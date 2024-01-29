<?php

namespace App\Services\Dimona;

use App\Models\Dimona\DimonaErrorCode;
use App\Services\Employee\EmployeeContractService;
use App\Services\Planning\PlanningService;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Services\CompanyService;
use Illuminate\Support\Str;
use App\Models\Dimona\Dimona;

class DimonaSenderService
{

    public function __construct(
        protected CompanyService $companyService,
        protected PlanningService $planningService,
        protected EmployeeContractService $employeeContractService,
        protected RequestDimona $requestDimona,
    ) {
    }

    public function sendLongTermDimona($companyId, $employeeContractId)
    {
        DB::connection('tenant')->beginTransaction();
        try {
            setTenantDBByCompanyId($companyId);
            $dimona = ['unique_id' => Str::uuid()];
            $dimona['employee_contract_id'] = $employeeContractId;
            $dimona['type'] = 'long_term';
            $dimona['dimona_type'] = 'IN';
            $employeeContract = $this->employeeContractService->getEmployeeContractDetails(
                $employeeContractId,
                ['employeeType', 'longTermEmployeeContract', 'employeeProfile.user.userBasicDetails']
            );
            $this->createDimonaRecords($dimona);
            $this->setCompanyData($companyId, $dimona);
            $this->setEmployeeData($employeeContract->employeeProfile, $dimona);
            $this->setContractData($employeeContract, $dimona);
            DB::connection('tenant')->commit();
            $this->requestDimona->sendDimonaRequest($dimona, "/api/send-dimona");
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
        }
    }

    public function sendDimonaByPlan($companyId, $planId)
    {
        try {
            DB::connection('tenant')->beginTransaction();
            setTenantDBByCompanyId($companyId);
            $dimona = ['unique_id' => Str::uuid()];
            $dimona['plan_id'] = $planId;
            $dimona['type'] = 'plan';
            $dimona['dimona_type'] = 'IN';
            $dimonaDeclarations = $this->createDimonaRecords($dimona);
            $plan = $this->planningService->getPlanningById($planId);
            $this->setCompanyData($companyId, $dimona);
            $this->setEmployeeData($plan->employeeProfile, $dimona);
            $this->setPlanningData($planId, $dimona);
            $response = $this->requestDimona->sendDimonaRequest($dimona, '/api/send-planning-dimona');
            if (!$response) {
                $this->setDimonaRequestFailed($dimonaDeclarations);
            }
            DB::connection('tenant')->commit();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
        }
    }

    public function setDimonaRequestFailed($dimonaDeclarations)
    {
        $dimonaError = DimonaErrorCode::where('error_code', '00000-000')->first();
        $dimonaDeclarations->dimonaDeclarationErrors()->create([
            'dimona_error_code_id' => $dimonaError->id
        ]);
    }

    // public function check
    public function createDimonaRecords(&$dimona)
    {
        $dimonaRecord = Dimona::create([
            'type' => $dimona['type'],
        ]);
        $dimonaDeclarations = $dimonaRecord->dimonaDeclarations()->create(
            [
                'unique_id' => $dimona['unique_id'],
                'type'      => $dimona['dimona_type']
            ]
        );
        if ($dimona['type'] == 'plan') {
            $this->createPlanningDimonaRecords($dimonaRecord, $dimona['plan_id']);
        } elseif ($dimona['type'] == 'long_term') {
            $this->createLongTermDimonaRecords($dimonaRecord, $dimona['employee_contract_id']);
            // } elseif ($dimona['type'] == 'flex_check') {
        }
        return $dimonaDeclarations;
    }
    public function createPlanningDimonaRecords($dimonaRecord, $planId)
    {
        $dimonaRecord->planningDimona()->create(
            ['planning_id' => $planId]
        );
    }
    public function createLongTermDimonaRecords($dimonaRecord, $employeeContractId)
    {
        $dimonaRecord->longtermDimona()->create(
            ['employee_contract_id' => $employeeContractId]
        );
    }
    public function setEmployeeData($employeeProfile, &$dimona)
    {
        $dimona['employee'] = [
            'social_security_number' => getRSZNumberFormat($employeeProfile->user->social_security_number ?? '')
        ];
    }
    public function setPlanningData($planId, &$dimona)
    {
        $plan = $this->planningService->getPlanningById($planId);

        $plan->employeeType->dimonaConfig->dimonaType;
        $employeeType = $plan->employeeType->toArray() ?? [];

        //Employee type.
        if (count($employeeType)) {
            $dimona['declaration']['employee_type'] = $employeeType['name'];
            $dimona['declaration']['employee_type_code'] = $employeeType['dimona_code'] ?? '';
            $dimona['declaration']['employee_type_category'] = $employeeType['employee_type_category_id'];
            $dimona['declaration']['dimona_catagory'] = $employeeType['dimona_config']['dimona_type_id'] ?? '';
            $dimona['declaration']['start_date'] = date('Y-m-d', strtotime($plan->start_date_time));
            $dimona['declaration']['start_time'] = date('H:i', strtotime($plan->start_date_time));
            $dimona['declaration']['end_date'] = date('Y-m-d', strtotime($plan->end_date_time));
            $dimona['declaration']['end_time'] = date('H:i', strtotime($plan->end_date_time));
        }
    }
    public function setContractData($employeeContract, &$dimona)
    {
        $employeeContract->employeeType->dimonaConfig->dimonaType;
        $employeeType = $employeeContract->employeeType->toArray() ?? [];

        $dimona['declaration']['employee_type'] = $employeeType['name'];
        $dimona['declaration']['employee_type_code'] = $employeeType['dimona_code'] ?? '';
        $dimona['declaration']['employee_type_category'] = $employeeType['employee_type_category_id'];
        $dimona['declaration']['dimona_catagory'] = $employeeType['dimona_config']['dimona_type_id'] ?? '';
        $dimona['declaration']['start_date'] = $employeeContract['start_date'] ?? '';
        $dimona['declaration']['end_date'] = $employeeContract['end_date'] ?? null;
    }

    public function setCompanyData($companyId, &$dimona)
    {
        $companyData = $this->companyService->getCompanyById($companyId)->toArray();
        $dimona['employer'] = [
            'company_id'            => $companyData['id'],
            'company_name'          => $companyData['company_name'],
            'company_vat_number'    => getVatNumberFormat($companyData['vat_number']),
            'company_rsz_number'    => getRSZNumberFormat($companyData['rsz_number']),
            'company_username'      => $companyData['username'],
            'company_sender_number' => $companyData['sender_number'],
            'company_oauth'         => $companyData['oauth_key'] ?? 'self_service_expeditor_' . $companyData['sender_number'],
        ];
    }

    public function getDimaonStatusForCompany($company_id)
    {
        try {

            $employee_types = collect($this->employeeTypeRepository->getCompanyEmployeeTypes($company_id));
            $company_dimoan_emp_type_ids = $this->getAllEmployeeTypeIDsForCompany($company_id);
            return $employee_types->transform(function ($employee_type) use ($company_dimoan_emp_type_ids) {
                return [
                    'employee_type_id'   => $employee_type->id,
                    'employee_type_name' => $employee_type->name,
                    'status'             => in_array($employee_type->id, $company_dimoan_emp_type_ids),
                ];
            });

        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateEmpTypeDimoanConfigToCompany($company_id, $values)
    {
        try {
            DB::beginTransaction();

            $company = $this->companyService->getCompanyById($company_id);
            $employee_type_ids = $values['employee_type_ids'] ?? [];

            // Sync the holiday codes to the company
            $company->dimoanEmployeeTypes()->sync($employee_type_ids);

            $company->refresh();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getAllEmployeeTypeIDsForCompany($company_id)
    {
        $company = $this->companyService->getCompanyById($company_id);
        return $company->dimoanEmployeeTypes()->pluck('employee_types.id')->toArray();
    }


}

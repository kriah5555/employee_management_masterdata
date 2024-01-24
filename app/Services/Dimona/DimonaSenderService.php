<?php

namespace App\Services\Dimona;

use App\Services\Planning\PlanningService;
use Exception;
use App\Models\Company\Company;
use Illuminate\Support\Facades\DB;
use App\Services\CompanyService;
use Illuminate\Support\Str;
use App\Models\DimonaRequest\DimonaBase;

class DimonaSenderService
{

    public function __construct(
        protected CompanyService $companyService,
        protected PlanningService $planningService,
        protected RequestDimona $requestDimona,
    ) {
    }

    public function sendDimonaByPlan($companyId, $planId)
    {
        // try {
        setTenantDBByCompanyId($companyId);
        $dimona = ['unique_id' => Str::uuid()];
        $this->setCompanyData($companyId, $dimona);
        $this->setEmployeeAndPlanningData($planId, $dimona);
        DB::connection('tenant')->beginTransaction();
        $this->createDimonaRecords($dimona);
        $this->requestDimona->sendDimonaRequest($dimona);
        DB::connection('tenant')->commit();
        // } catch (Exception $e) {
        //     DB::connection('tenant')->rollback();
        //     dd('here');
        // }
    }
    public function createDimonaRecords(&$dimona)
    {
        $dimonaBaseRecord = DimonaBase::create([
            'unique_id'    => $dimona['unique_id'],
            'dimona_code'  => $dimona['employee_type_code'],
            'employee_id'  => $dimona['user_id'],
            'employee_rsz' => $dimona['social_security_number'],
            'status'       => 1,
        ]);

        $dimonaBaseRecord->planningDimona()->create(['planning_base_id' => $dimona['plan_id']]);
        $dimonaBaseRecord->dimonaDetails()->create(
            [
                'dimona_type'     => $dimona['type'],
                'start_date_time' => $dimona['start_date'] . ' ' . ($dimona['start_time'] ?? '00:00'),
                'end_date_time'   => $dimona['end_date'] . ' ' . ($dimona['end_time'] ?? '00:00'),
            ]
        );
    }
    public function setEmployeeAndPlanningData($planId, &$dimona)
    {
        $hours = null;
        $plan = $this->planningService->getPlanningById($planId);

        $employeeDetails = $plan->employeeProfile->toArray() ?? [];
        $plan->employeeType->dimonaConfig->dimonaType;
        $employeeType = $plan->employeeType->toArray() ?? [];

        // plan details.
        $dimona['plan_id'] = $planId;

        //Employee details.
        if (count($employeeDetails)) {
            $dimona['employee_name'] = $employeeDetails['full_name'];
            $dimona['user_id'] = $employeeDetails['user_id'];
            $dimona['social_security_number'] = getRSZNumberFormat($employeeDetails['user']['social_security_number'] ?? '');
        }

        //Employee type.
        if (count($employeeType)) {
            $dimona['employee_type'] = $employeeType['name'];
            $dimona['employee_type_code'] = $employeeType['dimona_code'] ?? '';
            $dimona['employee_type_category'] = $employeeType['employee_type_category_id'];
            $dimona['dimona_catagory'] = $employeeType['dimona_config']['dimona_type_id'] ?? '';
        }
        $dimona['type'] = 'IN';
        $hours = timeDifferenceinHours($plan['start_date_time'], $plan['end_date_time']);
        $dimona += [
            'start_date' => date('Y-m-d', strtotime($plan['start_date_time'])),
            'start_time' => date('H:i', strtotime($plan['start_date_time'])),
            'end_date'   => date('Y-m-d', strtotime($plan['end_date_time'])),
            'end_time'   => date('H:i', strtotime($plan['end_date_time'])),
            'hours'      => $hours,
        ];
    }

    public function setCompanyData($companyId, &$dimona)
    {
        $companyData = Company::where('id', $companyId)->get()->toArray();
        $companyData = reset($companyData);
        $dimona += [
            'company_id'         => $companyData['id'],
            'company_name'       => $companyData['company_name'],
            'company_vat_number' => getVatNumberFormat($companyData['vat_number']),
            'company_rsz_number' => getRSZNumberFormat($companyData['rsz_number']),
            'company_username'   => $companyData['username'],
            'company_oauth'      => $companyData['oauth_key'] ?? 'self_service_expeditor_' . $companyData['sender_number'],
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

            $company = Company::findOrFail($company_id);
            $employee_type_ids = $values['employee_type_ids'] ?? [];

            // Sync the holiday codes to the company
            $company->dimoanEmployeeTypes()->sync($employee_type_ids);

            $company->refresh();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getAllEmployeeTypeIDsForCompany($company_id)
    {
        $company = Company::findOrFail($company_id);
        return $company->dimoanEmployeeTypes()->pluck('employee_types.id')->toArray();
    }


}

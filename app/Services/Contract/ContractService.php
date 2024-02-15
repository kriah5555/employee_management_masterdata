<?php

namespace App\Services\Contract;

use Illuminate\Support\Facades\DB;
use App\Services\HttpRequestService;
use App\Models\Planning\PlanningBase;
use App\Services\Company\FileService;
use App\Models\Contract\ContractTemplate;
use App\Repositories\Company\CompanyRepository;
use App\Services\Employee\EmployeeIdCardService;
use App\Repositories\Planning\PlanningRepository;
use App\Services\Contract\ContractTemplateService;
use App\Models\Company\Employee\EmployeeContractFile;
use App\Repositories\Employee\EmployeeIdCardRepository;
use App\Models\Company\Contract\CompanyContractTemplate;
use App\Repositories\Employee\EmployeeContractRepository;
class ContractService
{
    public function __construct(
        protected FileService $fileService,
        protected EmployeeContractRepository $employeeContractRepository,
    )
    {

    }

    public function generateEmployeeContract($employee_profile_id, $contract_type_id = null, $contract_status, $plan_id = null, $company_id = '', $employee_signature = '', $employer_signature = '') 
    {
        try {
            if (!empty($company_id)) {
                setTenantDBByCompanyId($company_id);
            }

            DB::connection('tenant')->beginTransaction();
                $url  = env('CONTRACTS_URL') . config('contracts.GENERATE_CONTRACT_ENDPOINT');
                $body = ['body' => app(ContractTemplateService::class)->getContractTemplate($contract_type_id, $company_id, ''), 'employee_signature' => $employee_signature, 'employer_signature' => $employer_signature];

                if (!empty($body)) {
                    $response = makeApiRequest($url, 'POST', $body);

                    $file = $this->fileService->createFileData([
                        'file_name' => $response['file_name'],
                        'file_path' => $response['pdf_file_path']
                    ]);

                    $employee_contract_file = $this->createEmployeeContractFileData([
                        'file_id'              => $file->id,
                        'contract_status'      => $contract_status,
                        'employee_profile_id'  => $employee_profile_id,
                        'employee_contract_id' => $contract_type_id,
                        'planning_base_id'     => $plan_id,
                    ]);

                    if (!empty($plan_id)) {
                        PlanningBase::find($plan_id)->update(['contract_status' => $contract_status]);
                    }
                    
                } else {
                    throw new \Exception("Contract template not fount");
                }

            DB::connection('tenant')->commit();
            return $employee_contract_file;
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function signEmployeePlanContract($values)
    {
        try {
            DB::connection('tenant')->beginTransaction();

                if (isset($values['company_id'])) {
                    setTenantDBByCompanyId($values['company_id']);
                }
                $plan = app(PlanningRepository::class)->getPlanningById($values['plan_id']);
                
                $employee_contract_file = $this->generateEmployeeContract($plan->employee_profile_id, null, config('contracts.SIGNED'), $values['plan_id'], $values['company_id'] ?? '', $values['signature'], '');
                
            DB::connection('tenant')->commit();
            return $employee_contract_file;
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function createEmployeeContractFileData($values) 
    {
        if (!empty($values['contract_type_id'])) {
            $condition = ['employee_contract_id' => $values['contract_type_id']];
        } else {
            $condition = ['planning_base_id' => $values['planning_base_id']];
        } 
        EmployeeContractFile::where($condition)->update(['status' => false]);

        return  EmployeeContractFile::create($values);
    }

    public function getEmployeeContractFiles($employee_profile_id = '', $contract_status = '', $contract_type_id = '', $plan_id ='') # ['signed', 'unsigned']
    {
        try {
            return EmployeeContractFile::query()
                ->when(!empty($employee_profile_id), fn ($query) => $query->where('employee_profile_id', $employee_profile_id))
                ->when(!empty($employee_contract_id), fn ($query) => $query->where('contract_type_id', $employee_contract_id))
                ->when(!empty($contract_status), fn ($query) => $query->where('contract_status', $contract_status))
                ->when(!empty($plan_id), fn ($query) => $query->where('planning_base_id', $plan_id))
                ->where('status', true)
                ->get();
    
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getEmployeeDocuments($employee_profile_id) # will get all contracts and documents of employee
    {
        try {

            $employee_documents = $this->getEmployeeContractFiles($employee_profile_id);
            $return = $employee_documents->map(function ($contract) {

                $type = null;

                if ($contract->planning_base_id) {
                    $type = 'Plan contract';
                } elseif ($contract->contract_type_id) {
                    $type = 'long term contract';
                }

                return [
                    'file_id'   => $contract->files->id,
                    'file_name' => $contract->files->file_name,
                    'file_url'  => $contract->file_url,
                    'type'      => $type . ' (' . ($contract->contract_status == config('contracts.CONTRACT_STATUS_SIGNED') ? 'Signed' : 'Unsigned') . ')',
                ];
            });

            $employee_id_cards_details = app(EmployeeIdCardService::class)->getEmployeeIdCards($employee_profile_id);
            return $return->concat($employee_id_cards_details);
    
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}

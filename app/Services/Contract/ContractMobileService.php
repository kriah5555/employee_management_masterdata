<?php

namespace App\Services\Contract;

use Illuminate\Support\Facades\DB;
use App\Services\Contract\ContractService;
use App\Models\Company\Employee\EmployeeProfile;
use App\Repositories\Company\CompanyRepository;

class ContractMobileService
{
    public function __construct(
        protected ContractService $contractService, 
        protected CompanyRepository $companyRepository, 
    )
    {}


    public function getEmployeeContractFiles($company_ids, $user_id, $contract_status = '', $employee_contract_id = '', $plan_id ='') # ['signed', 'unsigned']
    {
        try {
            $response = [
                'signed_contracts'   => [],
                'unsigned_contracts' => [],
            ];
    
            foreach ($company_ids as $company_id) {
                setTenantDBByCompanyId($company_id);
    
                $employee_profile = EmployeeProfile::where('user_id', $user_id)->first();
    
                if (!empty($employee_profile)) {
    
                    $company       = $this->companyRepository->getCompanyById($company_id);
                    $company_name  = $company->company_name;
                    $company_image = $company->logo_file ? $company->logo_file->file_path : null; 
                    $contracts     = $this->contractService->getEmployeeContractFiles($employee_profile->id, $contract_status, $employee_contract_id, $plan_id);
    
                    $contracts->each(function ($contract) use(&$response, $company_name, $company_id, $company_image) {
                        $contractData = [
                            'contract_id'        => $company_id,
                            'company_name'       => $company_name,
                            'company_image'      => $company_image,
                            'location_id'        => $contract->plan ? $contract->plan->location_id : null,
                            'location_name'      => $contract->plan ? $contract->plan->location->location_name : null,
                            'function_id'        => $contract->plan ? $contract->plan->function_id : null,
                            'function_name'      => $contract->plan ? $contract->plan->functionTitle->name : null,
                            'contract_date'      => $contract->plan ? $contract->plan->plan_date : null,
                            'contract_pdf'       => $contract->file_url,
                            'plan_contract'      => $contract->plan ? true : false,
                            'long_term_contract' => $contract->employeeContract ? true : false,
                        ];
    
                        if ($contract->contract_status == 2) {
                            $response['unsigned_contracts'][] = $contractData;
                        } else {
                            $response['signed_contracts'][] = $contractData;
                        }
                    });
                }
            }
    
            return $response;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}

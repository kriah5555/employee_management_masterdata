<?php

namespace App\Services\Contract;

use Illuminate\Support\Facades\DB;
use App\Services\HttpRequestService;
use App\Models\Planning\PlanningBase;
use App\Services\Company\FileService;
use App\Models\Contract\ContractTemplate;
use App\Repositories\Company\CompanyRepository;
use App\Repositories\Planning\PlanningRepository;
use App\Models\Company\Employee\EmployeeContractFile;
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
    public function generateEmployeeContract($employee_profile_id, $employee_contract_id = null, $contract_status, $plan_id = null, $company_id = '') 
    {
        try {
            if (!empty($company_id)) {
                setTenantDBByCompanyId($company_id);
            }

            DB::connection('tenant')->beginTransaction();
                $url  = env('CONTRACTS_URL') . config('contracts.GENERATE_CONTRACT_ENDPOINT');
                $body = ['body' => $this->getEmployeeContractTemplate($employee_contract_id, $company_id, $plan_id)];

                // $body = [
                //     "body"               => "<ul><li>point</li><li>point2</li></ul><p>Hi<br><strong>Sunil</strong>,How are you?<br>Regards,Sunil, This is a sample contract<br>template text with spaces, tabs, and newlines.</p><p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p><p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {employee_signature} &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {employer_signature}</p>",
                //     "employee_signature" => "https://upload.wikimedia.org/wikipedia/commons/a/aa/Henry_Oaminal_Signature.png",
                //     "employer_signature" => "https://upload.wikimedia.org/wikipedia/commons/a/aa/Henry_Oaminal_Signature.png",
                // ];

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
                        'employee_contract_id' => $employee_contract_id,
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

    public function createEmployeeContractFileData($values) 
    {
        if (!empty($values['employee_contract_id'])) {
            $condition = ['employee_contract_id' => $values['employee_contract_id']];
        } else {
            $condition = ['planning_base_id' => $values['planning_base_id']];
        } 
        EmployeeContractFile::where($condition)->update(['status' => false]);

        return  EmployeeContractFile::create($values);
    }

    public function getEmployeeContractTemplate($employee_contract_id = '', $company_id = '', $plan_id = '')
    {
        try {
            $contract_template   = '';

            if (!empty($company_id)) { # if it is employee flow and he is trying to generate contract then there will br not tenant db set
                setTenantDBByCompanyId($company_id);
            }

            if (!empty($employee_contract_id)) { # for employee long term contracts
                $employeeContract     = $this->employeeContractRepository->getEmployeeContractById($employee_contract_id);
                $employee_type_id     = $employeeContract->employee_type_id;
                $employeeBasicDetails = $employeeContract->employeeBasicDetails->language;
            } else { # for planning contract
                $plan = app(PlanningRepository::class)->getPlanningById($plan_id);
                $employee_type_id     = $plan->employee_type_id;
                $employeeBasicDetails = $plan->employeeProfile->employeeBasicDetails;
            }   

            $language = $employeeBasicDetails->language ?? config('constants.DEFAULT_LANGUAGE');

            $employee_contract_template = CompanyContractTemplate::where([
                'employee_type_id' => $employee_type_id,
                'status'           => true,
            ])->get()->first();

            if (!$employee_contract_template) {
                $company = app(CompanyRepository::class)->getCompanyById(empty($company_id) ? getCompanyId() : $company_id, ['companySocialSecretaryDetails']);
                if ($company->companySocialSecretaryDetails && $company->companySocialSecretaryDetails->company_social_secretary_id) {
                    $company_social_secretary_id = $company->companySocialSecretaryDetails->social_secretary_id;
                    $employee_contract_template  = ContractTemplate::query()->with(['socialSecretary' => function ($socialSecretary) {
                        $socialSecretary->whereId('company_social_secretary_id');
                    }])
                    ->where(['employee_type_id', $employee_type_id])
                    ->get()->first();
                }
            }
            
            if (!$employee_contract_template) {
                $employee_contract_template = ContractTemplate::where('employee_type_id', $employee_type_id)->get()->first();
            }

            if ($employee_contract_template) {
                $contract_template = $employee_contract_template->getTranslation('body', $language);
            }
            
            return $contract_template;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getEmployeeContractFiles($employee_profile_id, $contract_status, $contract_id = '') # ['signed', 'unsigned']
    {
        try {
            return EmployeeContractFile::where([
                    'contract_status'     => $contract_status, 
                    'employee_profile_id' => $employee_profile_id,
                    'status'              => true
                ])
                ->with(['files'])
                ->get();

        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}

<?php

namespace App\Services\Contract;

use Illuminate\Support\Facades\DB;
use App\Services\HttpRequestService;
use App\Models\Contract\ContractTemplate;
use App\Models\Company\Contract\CompanyContractTemplate;
use App\Models\Company\Employee\EmployeeContractFile;
use App\Services\Company\FileService;

class ContractService
{
    public function __construct(
        protected FileService $fileService
    )
    {

    }
    public function generateEmployeeContract($employee_profile_id, $employee_contract_id, $contract_status) 
    {

        try {
            DB::connection('master')->beginTransaction();
                $url = env('CONTRACTS_URL') . config('contracts.GENERATE_CONTRACT_ENDPOINT');
                $body = [
                    "body"               => "<ul><li>point</li><li>point2</li></ul><p>Hi<br><strong>Sunil</strong>,How are you?<br>Regards,Sunil, This is a sample contract<br>template text with spaces, tabs, and newlines.</p><p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p><p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {employee_signature} &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {employer_signature}</p>",
                    "employee_signature" => "https://upload.wikimedia.org/wikipedia/commons/a/aa/Henry_Oaminal_Signature.png",
                    "employer_signature" => "https://upload.wikimedia.org/wikipedia/commons/a/aa/Henry_Oaminal_Signature.png",
                ];

                $response = makeApiRequest($url, 'POST', $body);

                $file = $this->fileService->createFileData([
                    'file_name' => $response['file_name'],
                    'file_path' => $response['pdf_file_path']
                ]);

                $employee_contract_file = EmployeeContractFile::create([
                    'file_id'              => $file->id,
                    'contract_status'      => $status,
                    'employee_profile_id'  => $employee_profile_id,
                    'employee_contract_id' => $employee_contract_id,
                    'contract_status'      => $status,
                ]);

            DB::connection('tenant')->commit();
            return $employee_contract_file;
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getEmployeeContractTemplate($employee_id, $contract_id)
    {

    }

    public function getEmployeeContract($employee_profile_id, $status, $contract_id = '') # ['signed', 'unsigned', 'approved']
    {
        
    }
}

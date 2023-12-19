<?php

namespace App\Services\Contract;

use Illuminate\Support\Facades\DB;
use App\Services\HttpRequestService;
use App\Models\Contract\ContractTemplate;
use App\Models\Company\Contract\CompanyContractTemplate;

class ContractService
{
    public function generateEmployeeContract() 
    {
        $url = env('CONTRACTS_URL') . "/create_contract";
        $body = [
            "body"               => "<ul><li>point</li><li>point2</li></ul><p>Hi<br><strong>Sunil</strong>,How are you?<br>Regards,Sunil, This is a sample contract<br>template text with spaces, tabs, and newlines.</p><p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p><p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {employee_signature} &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {employer_signature}</p>",
            "employee_signature" => "https://upload.wikimedia.org/wikipedia/commons/a/aa/Henry_Oaminal_Signature.png",
            "employer_signature" => "https://upload.wikimedia.org/wikipedia/commons/a/aa/Henry_Oaminal_Signature.png",
        ];

        $response = makeApiRequest($url, 'POST', $body);

        return makeApiRequest($url, 'POST', $body);
    }

    public function getEmployeeContractTemplate($employee_id, $contract_id)
    {

    }

    public function getEmployeeContract($employee_profile_id, $status, $contract_id = '') # ['signed', 'unsigned', 'approved']
    {
        
    }
}

<?php

namespace App\Services\Planning;

use App\Models\Planning\PlanningBase;
use App\Interfaces\Planning\PlanningInterface;
use App\Repositories\Planning\PlanningRepository;
use App\Services\WorkstationService;
use App\Services\EmployeeFunction\FunctionService;
use App\Models\Company\Workstation;
use App\Models\Company\Location;
use App\Models\Company\Company;
use App\Models\EmployeeType\EmployeeType;
use App\Models\EmployeeFunction\FunctionTitle;
use App\Services\Employee\EmployeeService;
use App\Services\Contract\ContractTemplateService;
use Exception;


class PlanningContractService implements PlanningInterface
{

    public function __construct(
        protected ContractTemplateService $contractTemplateService,
    ) {
    }

    public function getPlanningContractContract(PlanningBase $plan)
    {
        $dailyContract = $plan->employeeType()->whereHas('contractTypes', function ($query) {
            $query->where('contract_renewal_type_id', 1);
        })->get();
        if (!$dailyContract->isEmpty()) {
            $template = $this->contractTemplateService->getTemplateForEmployeeType($plan->employeeType);
            if ($template) {
                $tokenData = [
                    'employee_first_name' => $plan->employeeProfile->user->userBasicDetails->first_name,
                    'employee_last_name'  => $plan->employeeProfile->user->userBasicDetails->last_name
                ];
                $template = replaceContractTokens($template->toArray()['body']['nl'], $tokenData);
                $url = '/service/contracts/create_contract';
                // dd($url, "POST", ['body' => $template]);
                $files = microserviceRequest($url, "POST", ['body' => $template]);
                $contractPdfUrl = env('CONTRACTS_URL') . '/' . $files['pdf_file_path'];
                return $contractPdfUrl;
            } else {
                throw new Exception("Contract template not found");
            }
        }
    }
}

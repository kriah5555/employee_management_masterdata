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
            $query->where('contract_renewal_type_id', 2);
	})->get();
        if (!$dailyContract->isEmpty()) {
            $template = null;
            // $template = $this->contractTemplateService->getTemplateForEmployeeType($plan->employeeType); # un commend this when contract template flow changed
            if ($template) {
                $tokenData = [
                    'employee_first_name' => $plan->employeeProfile->user->userBasicDetails->first_name,
                    'employee_last_name'  => $plan->employeeProfile->user->userBasicDetails->last_name
                ];
                $template = replaceContractTokens($template->toArray()['body']['nl'], $tokenData);
                // $url = '/service/contracts/create_contract';
                // $files = microserviceRequest($url, "POST", ['body' => $template]);
                $url = env('CONTRACTS_URL') . '/create_contract';
                $files = makeApiRequest($url, 'POST', ['body' => $template]);
                return env('CONTRACTS_URL') . '/' . $files['pdf_file_path'];
            }
        }
    }
}

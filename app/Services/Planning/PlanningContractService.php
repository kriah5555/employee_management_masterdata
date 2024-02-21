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
use App\Services\Contract\ContractService;


class PlanningContractService implements PlanningInterface
{

    public function __construct(
        protected ContractTemplateService $contractTemplateService,
    ) {
    }

    public function getPlanningContractContract(PlanningBase $plan)
    {
        $plan_daily_renewal_contract = $plan->employeeType->contractTypes->where('contract_renewal_type_id', config('constants.DAILY_CONTRACT_RENEWAL_ID'))->first();

        if (!$plan_daily_renewal_contract) {
            if ($plan->contract_status != config('contracts.SIGNED') && empty($plan->contracts)) {
                $template = $plan_daily_renewal_contract ? $this->contractTemplateService->getContractTemplate($plan_daily_renewal_contract->id, '', $plan->employeeProfile->user->userBasicDetails->language): null;
    
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
            } else { # if contract signed return the signed contract only
                $plan_daily_renewal_contract = $plan->employeeType->contractTypes->where('contract_renewal_type_id', config('constants.DAILY_CONTRACT_RENEWAL_ID'))->first();
                $contract = $plan->contracts()->exists() ?  $plan->contracts->first() : app(ContractService::class)->generateEmployeeContract($plan->employee_profile_id, $plan_daily_renewal_contract->id, config('contracts.CONTRACT_STATUS_UNSIGNED'), $plan->id, getCompanyId()); # if contract exists use that else generate new contract and use that
                return env('CONTRACTS_URL') . '/' . $contract->files->file_path;
            }
        }
    }
}

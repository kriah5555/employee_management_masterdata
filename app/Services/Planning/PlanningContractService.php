<?php

namespace App\Services\Planning;

use App\Models\Planning\PlanningBase;
use App\Services\Contract\ContractTemplateService;
use Exception;
use App\Services\Contract\ContractService;


class PlanningContractService
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
        if (!$dailyContract->isEmpty() && $dailyContract->first()) {
            if ($plan->contract_status != config('contracts.SIGNED') && empty($plan->contracts)) {
                $template = $daily_contracts ? $this->contractTemplateService->getContractTemplate($dailyContract->first()->id, '', $plan->employeeProfile->user->userBasicDetails->language) : null;

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
                $contract = $plan->contracts()->exists() ? $plan->contracts->first() : app(ContractService::class)->generateEmployeeContract($plan->employee_profile_id, null, config('contracts.CONTRACT_STATUS_UNSIGNED'), $plan->id, getCompanyId()); # if contract exists use that else generate new contract and use that
                return env('CONTRACTS_URL') . '/' . $contract->files->file_path;
            }
        }
    }

}

<?php

namespace App\Rules\Planning;

use Closure;
use App\Repositories\Planning\PlanningRepository;
use Illuminate\Contracts\Validation\ValidationRule;

class PlanContractSignedRule implements ValidationRule
{

    public function __construct(protected $company_id = '') {

    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!empty($this->company_id)) {
            setTenantDBByCompanyId($this->company_id);
        }
        $plan = app(PlanningRepository::class)->getPlanningById($value);

        if ($plan->contract_status == config('contracts.SIGNED')) {
            $fail(t('Contract already signed.'));
        }
    }
}

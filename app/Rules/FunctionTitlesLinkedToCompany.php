<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use App\Models\EmployeeFunction\FunctionTitle;
use App\Models\Company\Company;

class FunctionTitlesLinkedToCompany implements Rule
{
    protected $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function passes($attribute, $value)
    {
        // Get the sectors associated with the company
        $sectorIds = Company::find($this->company_id)
            ->sectors()
            ->pluck('sectors.id')
            ->toArray();

        $functionTitle = FunctionTitle::find($value);
        return $functionTitle && $functionTitle->functionCategory && in_array($functionTitle->functionCategory->sector->id, $sectorIds);
    }

    public function message()
    {
        return 'The provided function titles are not linked to the same company.';
    }
}

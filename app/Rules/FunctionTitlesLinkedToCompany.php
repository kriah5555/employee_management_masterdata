<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use App\Models\EmployeeFunction\FunctionTitle;
use App\Models\Company;

class FunctionTitlesLinkedToCompany implements Rule
{
    protected $sectorIds;

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

            // Check if the provided function title is associated with the same sectors as the company's sectors
            $functionTitle = FunctionTitle::find($value);

            return $functionTitle && $functionTitle->functionCategory && in_array($functionTitle->functionCategory->sector->id, $sectorIds);
    }

    public function message()
    {
        return 'The provided function titles are not linked to the same company.';
    }
}

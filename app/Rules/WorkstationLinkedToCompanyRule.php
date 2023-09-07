<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Workstation;

class WorkstationLinkedToCompanyRule implements Rule
{
    protected $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function passes($attribute, $value)
    {
        $workstation = Workstation::find($value);
        return $workstation && $workstation->company == $this->company_id;
    }

    public function message()
    {
        return t("The :attribute is not linked to the specified company.");
    }
}

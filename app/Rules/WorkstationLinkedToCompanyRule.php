<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Company\Workstation;

class WorkstationLinkedToCompanyRule implements Rule
{
    protected $company_id;

    public function __construct()
    {
    }

    public function passes($attribute, $value)
    {
        $workstation = Workstation::find($value);
        return $workstation !== null && $workstation->exists();
    }

    public function message()
    {
        return t("The :attribute is not linked to the specified company.");
    }
}

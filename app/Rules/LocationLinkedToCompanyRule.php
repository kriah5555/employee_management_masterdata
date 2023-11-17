<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Company\Location;

class LocationLinkedToCompanyRule implements Rule
{
    protected $company_id;

    public function __construct()
    {
    }

    public function passes($attribute, $value)
    {
        $location = Location::find($value);
        return $location !== null && $location->exists();    
    }

    public function message()
    {
        return t("The :attribute is not linked to the specified company.");
    }
}

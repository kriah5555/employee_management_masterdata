<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Location;

class LocationLinkedToCompanyRule implements Rule
{
    protected $sectorIds;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function passes($attribute, $value)
    {
        $location = Location::find($value);
        return $location && $location->company == $this->company_id;
    }

    public function message()
    {
        return "The provided location is not linked to the specified company.";
    }
}
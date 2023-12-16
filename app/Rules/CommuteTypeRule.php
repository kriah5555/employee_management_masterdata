<?php

namespace App\Rules;

use Closure;
use App\Models\Company\Employee\CommuteType;
use Illuminate\Contracts\Validation\Rule;

class CommuteTypeRule implements Rule
{
    protected $company_id;

    public function __construct()
    {
    }

    public function passes($attribute, $value)
    {
        $location = CommuteType::find($value);
        return $location !== null && $location->exists();    
    }

    public function message()
    {
        return t("The :attribute is invalid.");
    }
}
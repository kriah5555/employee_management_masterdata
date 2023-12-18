<?php

namespace App\Rules;

use Closure;
use App\Rules\CommuteTypeRule;
use Illuminate\Support\Facades\Validator;
use App\Rules\LocationLinkedToCompanyRule;
use Illuminate\Contracts\Validation\ValidationRule;

class EmployeeCommuteDetailsRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $rules = [
            "location_id"     =>  ['bail', 'integer', 'required', new LocationLinkedToCompanyRule()],
            "commute_type_id" => ['bail', 'integer', 'required', new CommuteTypeRule()],
            "distance"        => "required|digits_between:1,5", 
        ];

        $location_ids = collect($value)->pluck('location_id')->toArray();

        if (count($location_ids) != count(array_unique($location_ids))) {
            $fail('Location ids cannot repeat');
        }


        foreach ($value as $index => $employee_commute_detail) {
            $validator = Validator::make($employee_commute_detail, $rules);

            if ($validator->fails()) {
                foreach ($validator->errors()->toArray() as $errors) {
                    foreach ($errors as $error) {
                        $fail("Error at :attribute.$index : $error");
                    }
                }
            }
        }   
    }
}

<?php

namespace App\Http\Requests\Employee;

use App\Http\Requests\ApiRequest;
use App\Rules\EmployeeCommuteDetailsRule;


class EmployeeCommuteDetailsRequest extends ApiRequest
{

    public function rules(): array
    {
        $rules = [
            'employee_commute_details' => ['bail', 'nullable', 'array', new EmployeeCommuteDetailsRule()],
        ];

        return $rules;
    }
}

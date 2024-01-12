<?php

namespace App\Http\Requests\Dimona;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;

class EmployeeTypeDimoanConfigurationRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'employee_type_ids' => 'bail|nullable|array',
            'employee_type_ids.*' => [
                'bail',
                'integer',
                Rule::exists('master.employee_types', 'id')->where('status', 1),
            ]
        ];
    }
}

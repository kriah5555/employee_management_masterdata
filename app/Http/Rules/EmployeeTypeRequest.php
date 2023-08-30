<?php

namespace App\Http\Rules;

use App\Http\Rules\ApiRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class EmployeeTypeRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'                        => 'required|string|max:255',
            'description'                 => 'nullable|string|max:255',
            'status'                      => 'required|boolean',
            'employee_type_category_id' => [
                'required',
                'integer',
                Rule::exists('employee_type_categories', 'id'),
            ],
            // 'contract_type_id' => [
            //     'required',
            //     Rule::exists('contract_types', 'id'),
            // ],
            'contract_types'     => 'nullable|array',
            'contract_types.*'   => [
                'integer',
                Rule::exists('contract_types', 'id'),
            ],
            'dimona_type_id' => [
                'required',
                'integer',
                Rule::exists('dimona_types', 'id'),
            ],
        ];

    }
    public function messages()
    {
        return [
            'name.required'      => t('Employee type name is required.'),
            'name.string'        => 'Employee type must be a string.',
            'name.max'           => 'Employee type cannot be greater than 255 characters.',
            'description.string' => 'Description must be a string.',
            'description.max'    => 'Description cannot be greater than 255 characters.',
            'status.boolean'     => 'Status must be a boolean value.'
        ];
    }
}

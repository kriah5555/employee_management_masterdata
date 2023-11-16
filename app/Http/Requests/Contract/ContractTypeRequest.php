<?php

namespace App\Http\Requests\Contract;

use App\Http\Requests\ApiRequest;
use App\Services\Contract\ContractTypeService;
use Illuminate\Validation\Rule;

class ContractTypeRequest extends ApiRequest
{
    public function __construct(ContractTypeService $contractTypeService)
    {
        $this->contractTypeService = $contractTypeService;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'                     => 'required|string|max:255',
            'description'              => 'nullable|string',
            'status'                   => 'required|boolean',
            'contract_renewal_type_id' => [
                'required',
                'integer',
                Rule::exists('contract_renewal_types', 'id'),
            ],
        ];

    }
    public function messages()
    {
        return [
            'name.required'                     => 'Employee type name is required.',
            'name.string'                       => 'Employee type must be a string.',
            'name.max'                          => 'Employee type cannot be greater than 255 characters.',
            'description.string'                => 'Description must be a string.',
            'description.max'                   => 'Description cannot be greater than 255 characters.',
            'status.boolean'                    => 'Status must be a boolean value.',
            'contract_renewal_type_id.required' => 'Please select renewal type',
            'contract_renewal_type_id'          => 'Invalid renewal type',
        ];
    }
}
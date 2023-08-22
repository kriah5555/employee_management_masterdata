<?php

namespace App\Http\Rules\Contracts;

use App\Http\Rules\ApiRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use App\Services\Contracts\ContractTypeService;

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
        $renewalOptions = $this->contractTypeService->getContractRenewalOptions();
        $renewalOptionKeys = array_keys($renewalOptions);
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'renewal' => [
                'required',
                'in:' . implode(',', $renewalOptionKeys),
            ],
        ];

    }
    public function messages()
    {
        return [
            'name.required'      => 'Employee type name is required.',
            'name.string'        => 'Employee type must be a string.',
            'name.max'           => 'Employee type cannot be greater than 255 characters.',
            'description.string' => 'Description must be a string.',
            'description.max'    => 'Description cannot be greater than 255 characters.',
            'status.boolean'     => 'Status must be a boolean value.'
        ];
    }
}

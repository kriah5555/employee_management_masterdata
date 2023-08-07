<?php

namespace App\Http\Rules;

use Illuminate\Validation\Rule;
use App\Rules\AddressRule;
use App\Rules\LocationRule;
use App\Rules\WorkstationRule;
class CompanyRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'status'       => 'required|boolean',
            'sectors'      => 'required|array',
            'address'      => ['required', new AddressRule()],
            'locations'    => ['nullable', 'array', new LocationRule()],
            'workstations' => ['nullable', 'array', new WorkstationRule()],
            'sectors.*'    => [
                Rule::exists('sectors', 'id'),
            ],
        ];
    }
    
    public function messages()
    {

        return [
            'company_name.required' => 'Company name is required.',
            'company_name.string'   => 'Company name must be a string.',
            'company_name.max'      => 'Company name cannot be greater than 255 characters.',

            'status.required' => 'Status is required.',
            'status.boolean'  => 'Status must be a boolean value.',

            // 'logo.required' => 'Logo is required.',
            // 'logo.string'   => 'Logo must be a string.',
            // 'logo.max'      => 'Logo cannot be greater than 255 characters.',

            'sector_id.required' => 'Sector ID is required.',
            'sector_id.array'    => 'Sector ID must be an array.',
            'sector_id.*.exists' => 'One or more selected sector IDs are invalid.',
        ];
    }
}

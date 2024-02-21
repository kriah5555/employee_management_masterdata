<?php

namespace App\Http\Requests\EmployeeType;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;
use App\Rules\HexColor;
use App\Models\Contract\ContractType;

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
            'name'                      => 'required|string|max:255',
            'description'               => 'nullable|string',
            'status'                    => 'required|boolean',
            'dimona_code'               => '',
            'employee_type_category_id' => [
                'required',
                'integer',
                Rule::exists('employee_type_categories', 'id'),
            ],
            'contract_types'            => 'nullable|array',
            'contract_types.*'          => [
                'bail',
                'integer',
                Rule::exists('contract_types', 'id'),
            ],
            'dimona_type_id'            => [
                'integer',
                Rule::exists('dimona_types', 'id'),
            ],
            'icon_color'                => [
                'required',
                new HexColor
            ],
            'start_in_past'             => 'required|boolean',
            'counters'                  => 'required|boolean',
            'contract_hours_split'      => 'required|boolean',
            'leave_access'              => 'required|boolean',
            'holiday_access'            => 'required|boolean',
            'consecutive_days_limit'    => 'required|integer|min:1|max:7',
            'salary_type'               => [
                'required',
                Rule::in(array_keys(config('constants.SALARY_TYPES'))),
            ],
        ];

    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!empty($this->contract_types)) {
                $contract_types = ContractType::whereIn('id', $this->contract_types)
                ->where('contract_renewal_type_id', config('constants.DAILY_CONTRACT_RENEWAL_ID'))->get();
                if ($contract_types->count() > 1) {
                    $contract_type_names = $contract_types->pluck('name')->implode(', ');
                    $this->validator->errors()->add('contract_types', "cannot link more than one contract type with daily renewal periods '$contract_type_names'");
                };
            }
        });
    }

    public function messages()
    {
        return [
            'name.required'      => t('Employee type name is required.'),
            'name.string'        => 'Employee type must be a string.',
            'name.max'           => 'Employee type cannot be greater than 255 characters.',
            'description.string' => 'Description must be a string.',
            'description.max'    => 'Description cannot be greater than 255 characters.',
            'status.boolean'     => 'Status must be a boolean value.',
            'contract_types.*'   => 'Invalid contract type'
        ];
    }
}
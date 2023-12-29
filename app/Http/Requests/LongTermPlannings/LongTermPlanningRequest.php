<?php

namespace App\Http\Requests\LongTermPlannings;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;
use App\Rules\BelgiumCurrencyFormatRule;
use App\Services\Employee\EmployeeContractService;
use App\Services\Planning\LongTermPlanningService;

class LongTermPlanningRequest extends ApiRequest
{
    public function __construct(
        protected LongTermPlanningService $longTermPlanningService,
        protected EmployeeContractService $employeeContractService
    ) {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'employee_id'                  => [
                'required',
                'integer',
                Rule::exists('employee_profiles', 'id'),
            ],
            'repeating_week'               => 'required|integer',
            'location_id'                  => [
                'required',
                'integer',
                Rule::exists('locations', 'id'),
            ],
            'workstation_id'               => [
                'required',
                'integer',
                Rule::exists('workstations', 'id'),
            ],
            // 'function_id'                  => [
            //     'required',
            //     'integer',
            //     Rule::exists('master.function_titles', 'id'),
            // ],
            'plannings.*'                  => 'required|array',
            'plannings.*.*.day'            => 'required|integer|min:1|max:7',
            'plannings.*.*.start_time'     => 'required|date_format:H:i',
            'plannings.*.*.end_time'       => 'required|date_format:H:i',
            'plannings.*.*.contract_hours' => [
                'required',
                'string',
                new BelgiumCurrencyFormatRule
            ],
            'auto_renew'                   => 'required|boolean',
            'start_date'                   => [
                'bail',
                'required',
                'date_format:d-m-Y',
            ],
            'end_date'                     => 'after:start_date|date_format:d-m-Y',
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
            'status.boolean'     => 'Status must be a boolean value.',
            'contract_types.*'   => 'Invalid contract type'
        ];
    }


    protected function prepareForValidation()
    {
        // // Calculate a value and add it to the request
        // $calculatedValue = $this->calculateValue();
        // $this->merge(['calculated_value' => $calculatedValue]);
    }

    public function withValidator($validator)
    {
        // Additional custom validation logic
        $validator->after(function ($validator) {
            $this->validateDuration();
            $this->validateOverlap();
        });
    }

    protected function validateDuration()
    {
        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = $endDate ? date('Y-m-d', strtotime($endDate)) : date('Y-m-d', strtotime($startDate . '+1 year'));
        $employeeProfileId = $this->input('employee_id');
        $contract = $this->employeeContractService->checkContractExistForLongTermPlanning($employeeProfileId, $startDate, $endDate);
        if (!$contract) {
            $this->validator->errors()->add('start_date', "Employee doesn't have contract for selected dates");
        } else {
            $this->merge(['function_id' => $contract->employeeFunctionDetails->first()->function_id]);
        }
    }
    public function validateOverlap()
    {
        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = $endDate ? date('Y-m-d', strtotime($endDate)) : date('Y-m-d', strtotime($startDate . '+1 year'));
        $employeeProfileId = $this->input('employee_id');
        $locationId = $this->input('location_id');
        $longTermPlanning = $this->longTermPlanningService->getLongTermPlanningsByDate($employeeProfileId, $locationId, $startDate, $endDate);
        if (!$longTermPlanning->isEmpty()) {
            $longTermPlanningId = $this->route('long_term_planning');
            if (
                $this->method() != 'PUT' ||
                ($this->method() == 'PUT' &&
                    ($longTermPlanning->count() != 1 || $longTermPlanning->first()->id != $longTermPlanningId))
            ) {
                $this->validator->errors()->add('start_date', "Dates overlapping with other long term plannings");
            }
        }
        $renewingLongTermPlanning = $this->longTermPlanningService->getEmployeeRenewingLongTermPlanning($employeeProfileId, $locationId);
        if (!$renewingLongTermPlanning->isEmpty()) {
            $longTermPlanningId = $this->route('long_term_planning');
            if (
                $this->method() != 'PUT' ||
                ($this->method() == 'PUT' &&
                    ($longTermPlanning->count() != 1 || $longTermPlanning->first()->id != $longTermPlanningId))
            ) {
                $this->validator->errors()->add('auto_renew', "Cannot have more than one auto renewing long term plan");
            }
        }
    }
}

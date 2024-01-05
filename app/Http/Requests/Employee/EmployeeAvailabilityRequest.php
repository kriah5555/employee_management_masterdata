<?php

namespace App\Http\Requests\Employee;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

class EmployeeAvailabilityRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [];
        if ($this->isMethod('get')) {
            $rules = [
                'period' => 'required|regex:/^\d{2}-\d{4}$/',
            ];
            if ($this->routeIs('get-employee-availability-manager')) {
                $rules['employee_profile_id'] = [
                    'bail',
                    'required',
                    Rule::exists('employee_profiles', 'id')
                ];
            }
        } elseif ($this->isMethod('post')) {
            if ($this->routeIs('get-employee-availability')) {
                $rules = [
                    'period' => 'required|regex:/^\d{2}-\d{4}$/',
                ];
            } elseif ($this->routeIs('get-employee-availability-manager')) {
                $rules = [
                    'period'              => 'required|regex:/^\d{2}-\d{4}$/',
                    'employee_profile_id' => [
                        'bail',
                        'required',
                        Rule::exists('employee_profiles', 'id')
                    ]
                ];
            } else {
                $rules = [
                    'type'          => 'required|between:0,1|bail',
                    'remark'        => 'nullable|string',
                    'company_ids'   => 'required|array',
                    'company_ids.*' => [
                        'bail',
                        'required',
                        Rule::exists('master.companies', 'id')
                    ],
                    'dates'         => 'required|array',
                    'dates.*'       => 'date_format:' . config('constants.DEFAULT_DATE_FORMAT'),
                ];
            }
        } elseif ($this->isMethod('delete')) {
            $rules = [
                'id'         => [
                    'required',
                    Rule::exists('employee_availabilities', 'id'),
                ],
                'company_id' => [
                    'required',
                    Rule::exists('master.companies', 'id')
                ],
            ];
        }
        return $rules;

    }
    public function messages()
    {
        return [];
    }
}

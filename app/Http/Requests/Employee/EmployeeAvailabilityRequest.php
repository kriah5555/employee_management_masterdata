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
        } elseif ($this->isMethod('post')) {
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
        return $rules;

    }
    public function messages()
    {
        return [];
    }
}

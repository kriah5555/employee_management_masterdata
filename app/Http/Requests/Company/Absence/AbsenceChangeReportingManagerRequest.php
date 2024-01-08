<?php

namespace App\Http\Requests\Company\Absence;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use App\Rules\ResponsiblePersonExistsRule;
use App\Rules\AbsencePendingRule;

class AbsenceChangeReportingManagerRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $companyId = getCompanyId();
        return [
            'absence_id' => [
                'bail',
                'required',
                Rule::exists('tenant.absence', 'id'),
                new AbsencePendingRule()
            ],
            'manager_id' => [
                'bail',
                'required',
                'integer',
                new ResponsiblePersonExistsRule($companyId)
            ]
        ];
    }

    public function messages()
    {
        return [];
    }
}

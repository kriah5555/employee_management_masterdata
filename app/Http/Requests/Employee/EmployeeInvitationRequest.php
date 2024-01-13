<?php

namespace App\Http\Requests\Employee;

use App\Http\Requests\ApiRequest;
use App\Models\Company\Employee\EmployeeInvitation;
use App\Rules\User\GenderRule;

class EmployeeInvitationRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            if ($this->route()->getName() == 'validate-employee-invitations') {
                $rules = [
                    'token' => 'required|string|max:255',
                ];
            } elseif ($this->route()->getName() == 'employee-registration') {
                $rules = [
                    'token'             => 'required|string|max:255',
                    'first_name'        => 'required|string|max:255',
                    'last_name'         => 'required|string|max:255',
                    'gender_id'         => [
                        'bail',
                        'required',
                        'integer',
                        new GenderRule(),
                    ],
                    'date_of_birth'     => 'required|date_format:' . config('constants.DEFAULT_DATE_FORMAT'),
                    'street_house_no'   => 'required|string|max:255',
                    'postal_code'       => 'required|string|max:50',
                    'city'              => 'required|string|max:50',
                    'country'           => 'required|string|max:50',
                    'nationality'       => 'required|string|max:50',
                    'latitude'          => 'nullable|numeric',
                    'longitude'         => 'nullable|numeric',
                    'phone_number'      => 'required|string|max:20',
                    'email'             => 'required|email',
                    'account_number'    => 'nullable|string|max:255',
                    'language'          => ['required', 'string', 'in:' . implode(',', array_keys(config('constants.LANGUAGE_OPTIONS')))],
                    'marital_status_id' => [
                        'bail',
                        'required',
                        'integer',
                        // Rule::exists('marital_statuses', 'id'),
                    ],
                    'dependent_spouse'  => 'string|max:255',
                    'children'          => 'nullable|integer',
                ];
            } else {
                $rules = [
                    'first_name' => 'required|string|max:255',
                    'last_name'  => 'required|string|max:255',
                    'email'      => 'required|email',
                ];
            }
        }
        return $rules;

    }
    public function messages()
    {
        return [];
    }
    public function withValidator($validator)
    {
        // Additional custom validation logic
        $validator->after(function ($validator) {
            if ($this->route()->getName() == 'validate-employee-invitations' || $this->route()->getName() == 'employee-registration') {
                $token = $this->input('token');
                $token = decodeData($token);
                if ($token) {
                    $companyId = $token['company_id'];
                    $token = $token['token'];
                    setTenantDBByCompanyId($companyId);
                    $employeeInvitation = EmployeeInvitation::where('token', $token)->get()->first();
                    if ($employeeInvitation) {
                        if ($employeeInvitation->invitation_status == 1 && strtotime($employeeInvitation->expire_at) > strtotime(date('Y-m-d H:i'))) {
                            $this->merge(['employee_invitation' => $employeeInvitation]);
                            return;
                        } elseif (strtotime($employeeInvitation->expire_at) <= strtotime(date('Y-m-d H:i'))) {
                            $this->validator->errors()->add('token', "Link expired");
                        }
                    }
                }
                $this->validator->errors()->add('token', "Link invalid");
            }
        });
    }
}

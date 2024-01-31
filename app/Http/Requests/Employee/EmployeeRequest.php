<?php

namespace App\Http\Requests\Employee;

use App\Http\Requests\ApiRequest;
use App\Rules\DuplicateSocialSecurityNumberRule;
use App\Rules\ValidateLengthIgnoringSymbolsRule;
use App\Repositories\Employee\EmployeeProfileRepository;
use Illuminate\Validation\Rule;
use App\Rules\EmployeeContractDetailsRule;
use App\Rules\EmployeeFunctionDetailsRule;
use App\Rules\User\GenderRule;
use App\Rules\EmployeeCommuteDetailsRule;
use App\Rules\MealVoucherRule;
use App\Rules\BelgiumCurrencyFormatRule;
use App\Rules\ResponsiblePersonExistsRule;

class EmployeeRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('post') || $this->isMethod('put')) {
            $companyId = getCompanyId();

            $employeeId = $this->route('employee'); // Assuming your route parameter is named 'employee'

            $allowedLanguageValues = array_keys(config('constants.LANGUAGE_OPTIONS'));
            $rules = [
                'first_name'            => 'required|string|max:255',
                'last_name'             => 'required|string|max:255',
                'gender_id'             => [
                    'bail',
                    'required',
                    'integer',
                    new GenderRule(),
                ],
                'date_of_birth'         => 'required|date_format:' . config('constants.DEFAULT_DATE_FORMAT'),
                'responsible_person_id' => ['nullable', 'integer', new ResponsiblePersonExistsRule($companyId)],
                'street_house_no'       => 'required|string|max:255',
                'postal_code'           => 'required|string|max:50',
                'place_of_birth'        => 'required|string|max:50',
                'city'                  => 'required|string|max:50',
                'country'               => 'required|string|max:50',
                'nationality'           => 'required|string|max:50',
                'latitude'              => 'nullable|numeric',
                'longitude'             => 'nullable|numeric',
                'phone_number'          => 'required|string|max:20',
                'email'                 => 'required|email',
                'license_expiry_date'   => 'nullable|date_format:' . config('constants.DEFAULT_DATE_FORMAT'),
                'account_number'        => 'nullable|string|max:255',
                'language'              => ['required', 'string', 'in:' . implode(',', $allowedLanguageValues)],
                'marital_status_id'     => [
                    'bail',
                    'required',
                    'integer',
                    // Rule::exists('marital_statuses', 'id'),
                ],
                'dependent_spouse'      => 'string|max:255',
                'children'              => 'nullable|integer',
            ];
            if ($this->isMethod('post')) {
                $rules['fuel_card'] = 'nullable|boolean';
                $rules['company_car'] = 'nullable|boolean';
                $rules['extra_info'] = 'nullable|string';
                $rules['clothing_compensation'] = ['bail', 'nullable', new BelgiumCurrencyFormatRule()];
                $rules['social_secretary_number'] = 'nullable|string|max:255';
                $rules['contract_number'] = 'nullable|string|max:255';
                $rules['social_security_number'] = ['required', 'string', new ValidateLengthIgnoringSymbolsRule(11, 11, [',', '.', '-']), new DuplicateSocialSecurityNumberRule()];
                $rules['employee_contract_details'] = ['bail', 'required', 'array', new EmployeeContractDetailsRule()];
                $rules['employee_function_details'] = ['bail', 'required', 'array', new EmployeeFunctionDetailsRule()];
                $rules['employee_commute_details'] = ['bail', 'nullable', 'array', new EmployeeCommuteDetailsRule()];
                $rules['meal_voucher_id'] = ['bail', 'integer', 'nullable', new MealVoucherRule()];
            } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
                $rule['responsible_person_id'][] = Rule::notIn([$employeeId]);
                $rules['social_security_number'] = ['required', 'string', new ValidateLengthIgnoringSymbolsRule(11, 11, [',', '.', '-'])];
            }
        }
        return $rules;

    }
    public function messages()
    {
        return [];
    }
}

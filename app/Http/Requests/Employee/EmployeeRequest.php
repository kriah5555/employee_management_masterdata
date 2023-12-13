<?php

namespace App\Http\Requests\Employee;

use App\Http\Requests\ApiRequest;
use App\Rules\DuplicateSocialSecurityNumberRule;
use App\Rules\ValidateLengthIgnoringSymbolsRule;
use App\Repositories\Employee\EmployeeProfileRepository;
use Illuminate\Validation\Rule;
use App\Rules\CurrencyFormatRule;
use App\Rules\EmployeeContractDetailsRule;
use App\Rules\EmployeeFunctionDetailsRule;
use App\Services\EmployeeType\EmployeeTypeService;
use App\Services\EmployeeFunction\FunctionService;
use App\Rules\User\GenderRule;
use App\Rules\EmployeeCommuteDetailsRule;

class EmployeeRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $allowedLanguageValues = array_keys(config('constants.LANGUAGE_OPTIONS'));
        if ($this->isMethod('post') || $this->isMethod('put')) {
            $rules = [
                'first_name'          => 'required|string|max:255',
                'last_name'           => 'required|string|max:255',
                'gender_id'           => [
                    'bail',
                    'required',
                    'integer',
                    new GenderRule(),
                ],
                'date_of_birth'       => 'required|date',
                'place_of_birth'      => 'string|max:255',
                'street_house_no'     => 'required|string|max:255',
                'postal_code'         => 'required|string|max:50',
                'city'                => 'required|string|max:50',
                'country'             => 'required|string|max:50',
                'nationality'         => 'required|string|max:50',
                'latitude'            => 'nullable|numeric',
                'longitude'           => 'nullable|numeric',
                'phone_number'        => 'required|string|max:20',
                'email'               => 'required|email',
                'license_expiry_date' => 'nullable|date',
                'bank_account_number' => 'nullable|string|max:255',
                'language'            => ['required', 'string', 'in:' . implode(',', $allowedLanguageValues)],
                'marital_status_id'   => [
                    'bail',
                    'required',
                    'integer',
                    // Rule::exists('marital_statuses', 'id'),
                ],
                'dependent_spouse'    => 'string|max:255',
                'children'            => 'nullable|integer',
            ];
            if ($this->isMethod('post')) {
                $rules['meal_voucher_id'] = ['bail', 'integer'];
                $rules['fuel_card'] = 'required|boolean';
                $rules['company_car'] = 'required|boolean';
                $rules['extra_info'] = 'nullable|string';
                $rules['clothing_compensation'] = ['required', new CurrencyFormatRule];
                $rules['social_secretary_number'] = 'nullable|string|max:255';
                $rules['contract_number'] = 'nullable|string|max:255';
                $rules['social_security_number'] = ['required', 'string', new ValidateLengthIgnoringSymbolsRule(11, 11, [',', '.', '-']), new DuplicateSocialSecurityNumberRule()];
                $rules['employee_contract_details'] = ['bail', 'required', 'array', new EmployeeContractDetailsRule(app(EmployeeTypeService::class))];
                $rules['employee_function_details'] = ['bail', 'required', 'array', new EmployeeFunctionDetailsRule(app(EmployeeTypeService::class), app(FunctionService::class))];
            } elseif ($this->isMethod('put')) {
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

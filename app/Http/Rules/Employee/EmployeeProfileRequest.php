<?php

namespace App\Http\Rules\Employee;

use App\Http\Rules\ApiRequest;
use App\Rules\SocialSecurityNumberRule;
use App\Repositories\EmployeeProfileRepository;
use Illuminate\Validation\Rule;
use App\Rules\CurrencyFormatRule;

class EmployeeProfileRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $allowedLanguageValues = array_keys(config('constants.LANGUAGE_OPTIONS'));
        return [
            // 'first_name'             => 'required|string|max:255',
            // 'last_name'              => 'required|string|max:255',
            // 'date_of_birth'          => 'required|date',
            // 'gender_id'              => [
            //     'bail',
            //     'required',
            //     'integer',
            //     Rule::exists('genders', 'id'),
            // ],
            // 'email'                  => 'required|email',
            // 'phone_number'           => 'required|string|max:20',
            // 'social_security_number' => [
            //     'required',
            //     'string',
            //     'min:11',
            //     'max:11',
            //     new SocialSecurityNumberRule(new EmployeeProfileRepository)
            // ],
            // 'place_of_birth'         => 'string|max:255',
            // 'date_of_joining'        => 'required|date',
            // 'date_of_leaving'        => 'date',
            // 'language'               => ['required', 'string', 'in:' . implode(',', $allowedLanguageValues)],
            // 'marital_status_id'      => [
            //     'bail',
            //     'required',
            //     'integer',
            //     Rule::exists('marital_statuses', 'id'),
            // ],
            // 'dependent_spouse'       => 'string|max:255',
            // 'bank_account_number'    => 'nullable|string|max:255',
            // 'street_house_no'        => 'required|string|max:255',
            // 'postal_code'            => 'required|string|max:50',
            // 'city'                   => 'required|string|max:50',
            // 'country'                => 'required|string|max:50',
            // 'latitude'               => 'nullable|numeric',
            // 'longitude'              => 'nullable|numeric',
            // 'transport_id'           => [
            //     'bail',
            //     'required',
            //     'integer',
            //     Rule::exists('transports', 'id'),
            // ],
            // 'fuel_card'              => 'required|boolean',
            // 'company_car'            => 'required|boolean',
            // 'extra_info'             => 'nullable|string|max:2000',
            // 'clothing_compensation'  => ['required', new CurrencyFormatRule],
        ];

    }
    public function messages()
    {
        return [];
    }
}
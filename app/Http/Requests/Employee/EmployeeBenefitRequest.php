<?php

namespace App\Http\Requests\Employee;

use App\Http\Requests\ApiRequest;
use App\Rules\BelgiumCurrencyFormatRule;

class EmployeeBenefitRequest extends ApiRequest
{

    public function rules(): array
    {
       
        return [
            'meal_voucher_id'         => ['bail', 'integer'],
            'fuel_card'               => 'required|boolean',
            'company_car'             => 'required|boolean',
            'extra_info'              => 'nullable|string',
            'clothing_compensation'   => ['required', new BelgiumCurrencyFormatRule],
            'social_secretary_number' => 'nullable|string|max:255',
            'contract_number'         => 'nullable|string|max:255',
        ];
    }
}

<?php

namespace App\Http\Requests\Employee;

use App\Rules\MealVoucherRule;
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
            'clothing_compensation'   => ['nullable', new BelgiumCurrencyFormatRule],
            'social_secretary_number' => 'nullable|string|max:255',
            'contract_number'         => 'nullable|string|max:255',
            'meal_voucher_id'         => [
                'bail',
                'integer',
                'nullable',
                new MealVoucherRule()
            ],
        ];
    }
}

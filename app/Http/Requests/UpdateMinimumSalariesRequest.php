<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiRequest;
use App\Rules\BelgiumCurrencyFormatRule;
use App\Rules\MinimumSalariesLevelsRule;
use App\Rules\MinimumSalariesCategoriesRule;

class UpdateMinimumSalariesRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'salaries.*.level'                       => 'required|integer|min:0',
            'salaries.*.categories'                  => 'required|array',
            'salaries.*.categories.*.category'       => 'required|integer|min:1',
            'salaries'                               => ['required', 'array', new MinimumSalariesLevelsRule],
            'salaries.*.categories'                  => ['required', 'array', new MinimumSalariesCategoriesRule],
            'salaries.*.categories.*.minimum_salary' => ['required', new BelgiumCurrencyFormatRule],
        ];
    }
    public function messages()
    {
        return [];
    }
}

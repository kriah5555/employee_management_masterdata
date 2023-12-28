<?php

namespace App\Http\Requests\Planning;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use App\Rules\BelgiumCurrencyFormatRule;
use App\Rules\Planning\QRcodeRule;
use App\Rules\Planning\PlanStartQRExistRule;

class StartPlanByEmployeeRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'QR_code'    => ['bail', 'required', 'string', new QRcodeRule,],
            'start_time' => ['required', 'date_format:H:i', new PlanStartQRExistRule(request()->input('user_id'), request()->input('QR_code'))],
            'user_id'    => 'required|integer',
        ];
    }
    public function messages()
    {
        return [];
    }
}

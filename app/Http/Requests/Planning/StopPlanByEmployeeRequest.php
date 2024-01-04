<?php

namespace App\Http\Requests\Planning;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use App\Rules\BelgiumCurrencyFormatRule;
use App\Rules\Planning\QRcodeRule;
use App\Rules\Planning\PlanStopQRExistRule;

class StopPlanByEmployeeRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'QR_code'   => ['bail', 'required', 'string', new QRcodeRule,],
            'stop_time' => ['required', 'date_format:H:i', new PlanStopQRExistRule(request()->input('user_id'), request()->input('QR_code'))],
        ];
    }
    public function messages()
    {
        return [];
    }
}

<?php

namespace App\Http\Requests\Planning;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use App\Rules\BelgiumCurrencyFormatRule;
use App\Rules\Planning\QRcodeRule;
use App\Rules\Planning\PlanStopQRExistRule;
use Illuminate\Support\Facades\Auth;

class StopPlanByEmployeeRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'QR_code'   => ['bail', 'required', 'string', new QRcodeRule, new PlanStopQRExistRule(Auth::id(), date('H:i'))],
            // 'stop_time' => ['required', 'date_format:H:i', ],
        ];
    }
    public function messages()
    {
        return [];
    }
}

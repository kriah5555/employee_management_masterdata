<?php

namespace App\Http\Requests\Planning;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;

class PlanningBreakRequest extends ApiRequest
{
    public function rules(): array
    {
        $routeName = $this->route()->getName();
        $rules = [
            'plan_id' => [
                'required',
                'integer',
                Rule::exists('planning_base', 'id'),
            ],
        ];
        if ($routeName == 'start-break') {
            $rules['start_time'] = 'required|date_format:H:i';
        } elseif ($routeName == 'stop-break') {
            $rules['end_time'] = 'required|date_format:H:i';
        }
        return $rules;
    }
    public function messages()
    {
        return [
        ];
    }
}

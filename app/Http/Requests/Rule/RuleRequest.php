<?php

namespace App\Http\Requests\Rule;

use App\Http\Requests\ApiRequest;
use App\Rules\RulesValueRule;

class RuleRequest extends ApiRequest
{
    protected $sectorService;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'description' => 'required|string|max:255',
            'value'       => ['required', new RulesValueRule],
            'status'      => 'required|boolean',
        ];

    }
    public function messages()
    {
        return [
            'value.required' => 'Value is required.',
        ];
    }
}
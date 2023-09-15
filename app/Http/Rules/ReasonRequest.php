<?php

namespace App\Http\Rules;

use App\Services\WorkstationService;
use Illuminate\Validation\Rule;

class ReasonRequest extends ApiRequest
{
    public function rules() :array
    {
        $category = array_keys(config('constants.REASON_OPTIONS'));
        $reason_rules = [
            'name'     => 'required|string|max:255',
            'status'   => 'required|boolean',
            'category' => [
                'required',
                'string',
                Rule::in(array_keys(config('constants.REASON_OPTIONS')))
            ],
            'created_by' => 'nullable|integer',
            'updated_by' => 'nullable|integer',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            unset($reason_rules['category']);
        }
        return $reason_rules;
    }

    public function messages()
    {
        return [
            'category.required' => 'The category field is required.',
            'category.string'   => 'The category field must be a string.',
            'category.regex'    => 'The category field may only contain letters, numbers, hyphens, and underscores.',
            'category.unique'   => 'The category has already been taken.',
        ];
    }
}

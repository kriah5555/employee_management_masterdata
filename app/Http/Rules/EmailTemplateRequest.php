<?php

namespace App\Http\Rules;

use App\Services\WorkstationService;

class EmailTemplateRequest extends ApiRequest
{
    public function rules() :array
    {
        $email_template_rules = [
            'template_type' => 'required|string',
            'status'        => 'nullable|boolean',
            'body'          => 'required|array',
            'subject'       => 'nullable|array',
            'body.*'        => 'required|string',
            'subject.*'     => 'nullable|string',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            unset($email_template_rules['template_type']);
        }

        return $email_template_rules;
    }

    public function messages()
    {
        return [
            'template_type.required' => 'The template type is required.',
            'template_type.string'   => 'The template type must be a string.',
            
            'status.boolean'    => 'The status field must be a boolean.',
            
            'body.required'   => 'The body field is required.',
            'body.array'      => 'The body field must be an array.',
            'body.*.required' => 'The body translation is required.',
            'body.*.string'   => 'The body translation must be a string.',
            
            'subject.array'      => 'The subject field must be an array.',
            'subject.*.nullable' => 'The subject translation must be nullable.',
            'subject.*.string'   => 'The subject translation must be a string.',
        ];
    }
}

<?php

namespace App\Http\Rules\Email;

use App\Http\Rules\ApiRequest;
use Illuminate\Validation\Rule;

class EmailTemplateRequest extends ApiRequest
{
    public function rules() :array
    {
        $email_template_rules = [
            'status'        => 'nullable|boolean',
            'body'          => 'required|array',
            'subject'       => 'nullable|array',
            'body.*'        => 'nullable|string',
            'subject.*'     => 'nullable|string',
            'template_type' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9_\-]+$/',
                Rule::unique('email_templates', 'template_type'),
            ],
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            unset($email_template_rules['template_type']);
        }

        return $email_template_rules;
    }

    public function messages()
    {
        return [
            'template_type.required' => t('The template type is required.'),
            'template_type.string'   => t('The template type must be a string.'),
            'template_type.regex'    => t('The template type can only contain letters, digits, underscores, and hyphens.'),
            'template_type.unique'   => t('The selected template type already exists.'),
            
            'status.boolean'    => t('The status field must be a boolean.'),
            
            'body.required'   => t('The body field is required.'),
            'body.array'      => t('The body field must be an array.'),
            'body.*.required' => t('The body translation is required.'),
            'body.*.string'   => t('The body translation must be a string.'),
            
            'subject.array'      => t('The subject field must be an array.'),
            'subject.*.nullable' => t('The subject translation must be nullable.'),
            'subject.*.string'   => t('The subject translation must be a string.'),
        ];
    }
}

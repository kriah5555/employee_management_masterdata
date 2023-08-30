<?php

namespace App\Http\Rules\Translations;

use App\Http\Rules\ApiRequest;
use Illuminate\Validation\Rule;

class TranslationRequest extends ApiRequest
{
    public function rules() :array
    {
        return [
            'translations'          => 'nullable|array',
            'translations.*.key'    => 'required|string',
            'translations.*.text'   => 'required|array',
            'translations.*.text.*' => 'nullable|string'
        ];
    }

    public function messages()
    {
        return [
            'translations.array'             => t('The translations must be an array.'),
            'translations.*.key.required'    => t('Each translation must have a key.'),
            'translations.*.key.string'      => t('The key for each translation must be a string.'),
            'translations.*.text.required'   => t('Each translation must have text for at least one language.'),
            'translations.*.text.array'      => t('The text for each translation must be an array.'),
            'translations.*.text.*.nullable' => t('The text for each language in a translation is optional.'),
            'translations.*.text.*.string'   => t('The text for each language in a translation must be a string.'),
        ];
    }
}

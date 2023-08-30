<?php

use Spatie\TranslationLoader\LanguageLine;

if (!function_exists('api_response')) {
    function api_response($status, $message, $data = '', $server_error_status = '')
    {
        $return_data = [
            'success'  => $status,
            'message' => $message,
        ];

        if ($data) {
            $return_data['data'] = $data;
        }

        return $server_error_status ? response()->json($return_data, $server_error_status) : response()->json($return_data);
    }
}

if (!function_exists('hasDuplicates')) {
    function hasDuplicates(array $array): bool {
        $seen = [];
        foreach ($array as $number) {
            if (isset($seen[$number])) {
                return true; // Duplicate found
            }
            $seen[$number] = true;
        }
        return false; // No duplicates found
    }
}

if (!function_exists('t')) {
    function t($stringKey)
    {
        $locale = app()->getLocale();

        $translation = LanguageLine::where('key', $stringKey)->first();

        if ($translation && isset($translation->text[$locale])) {
            // dd($translation->toArray(), $translation->text[$locale]);
            return $translation->text[$locale];
        } else {
            // If translation doesn't exist create new entry
            $text = [];
            foreach (config('app.available_locales') as $locale) {
                $text[$locale] = $stringKey;
            }

            $translation = LanguageLine::firstOrNew([
                'key' => $stringKey,
            ]);

            $translation->text = $text;
            $translation->group = 'custoom';
            $translation->save();

            return $stringKey; // Return the original string if no translation exists
        }
    }

}
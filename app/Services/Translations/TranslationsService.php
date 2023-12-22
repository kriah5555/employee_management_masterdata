<?php

namespace App\Services\Translations;

use Illuminate\Support\Facades\DB;
use Spatie\TranslationLoader\LanguageLine;
use Illuminate\Support\Facades\File;
use App\Exceptions\ModelUpdateFailedException;

class TranslationsService
{

    public function __construct()
    {
    }

    public function extractTranslatableStrings()
    {
        $pattern = '/ t\((["\'])(.*?)\1\)/';
        $files = File::allFiles(app_path());

        foreach ($files as $file) {
            $contents = file_get_contents($file);
            preg_match_all($pattern, $contents, $matches);

            if (!empty($matches[2])) {
                foreach ($matches[2] as $stringKey) {
                    $text = [];

                    foreach (config('app.available_locales') as $locale) {
                        $text[$locale] = $stringKey;
                    }

                    $this->createTranslation($stringKey, $text);
                }
            }
        }
    }

    public function create($translations)
    {
        try {
            return DB::transaction(function () use ($translations) {
                $createdTranslations = [];
                foreach ($translations['translations'] as $key => $translationData) {
                    $group = 'custom';
                    $key = $translationData['key'];
                    $text = $translationData['text'];

                    $this->createTranslation($key, $text, $group);
                }
            });
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    protected function createTranslation($key, $text, $group = 'custom')
    {
        $translation = $this->model::firstOrNew([
            'key' => $key,
        ]);
        $translation->text = $text;
        $translation->group = $group;
        $translation->save();
    }

    public function index()
    {
        return LanguageLine::all();
    }

    public function getTranslation($strings)
    {
        $translations = [];
        if ($strings) {
            $availableLanguages = config('app.available_locales');

            foreach ($strings as $index => $string) {
                $translations[$index]['key'] = $string;
                foreach ($availableLanguages as $language) {
                    $translations[$index][$language] = t($string, $language);
                }
            }
        }
        return array_values($translations);
    }

    public function getTranslationById($id)
    {
        return LanguageLine::findOrFail($id);
    }
    public function update(LanguageLine $languageLine, $updatedValues)
    {
        $translationStrings = [
            'text' => [
                'en' => $updatedValues['en'] ?? '',
                'fr' => $updatedValues['fr'] ?? '',
                'nl' => $updatedValues['nl'] ?? '',
            ]
        ];
        if (
            $languageLine->update($translationStrings)
        ) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update employee type');
        }
    }
}

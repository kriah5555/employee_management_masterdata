<?php

namespace App\Http\Controllers\Translations;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Spatie\TranslationLoader\LanguageLine;
use App\Http\Controllers\Controller;

class TranslationController extends Controller
{
    public function extractTranslatableStrings()
    {
        app()->setLocale('nl');

        $pattern = '/__\((["\'])(.*?)\1/';

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

                    $translation = LanguageLine::firstOrNew([
                        'key' => $stringKey,
                    ]);

                    $translation->text = $text;
                    $translation->group = 'messages';
                    $translation->save();
                }
            }
        }
        return response()->json(['message' => 'Translations saved successfully', 'data' => $createdTranslations]);
    }

    public function index()
    {
        $translations = LanguageLine::all();
        return response()->json(['data' => $translations]);
    }

    public function store(Request $request)
    {
        $translations = $request->input('translations');

        $createdTranslations = [];
        foreach ($translations as $translationData) {
            
            $group = $translationData['group'];
            $key   = $translationData['key'];
            $text  = $translationData['text'];

            $translation = LanguageLine::firstOrNew([
                'key' => $key,
            ]);
            $translation->text  = $text;
            $translation->group = $group;
            $translation->save();

            $createdTranslations[] = $translation;
        }

        return response()->json(['message' => 'Translations saved successfully', 'data' => $createdTranslations]);
    }
    
    public function destroy($id)
    {
        $translation = LanguageLine::findOrFail($id);
        $translation->delete();

        return response()->json(['message' => 'Translation deleted successfully']);
    }
}

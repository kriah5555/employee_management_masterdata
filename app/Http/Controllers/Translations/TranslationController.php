<?php

namespace App\Http\Controllers\Translations;

use Spatie\TranslationLoader\LanguageLine;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Services\Translations\TranslationsService;
use App\Http\Requests\Translations\TranslationRequest;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public function __construct(protected TranslationsService $translation_service)
    {
    }

    public function extractTranslatableStrings()
    {
        $this->translation_service->extractTranslatableStrings();
        
        return returnResponse(
            [
                'success' => true,
                'message' => t('Translations saved successfully')
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function index($key = '')
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->translation_service->getAll(['key' => $key])
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function store(TranslationRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => t('Translations saved successfully'),
                'data'    => $this->translation_service->create($request->all())
            ],
            JsonResponse::HTTP_CREATED,
        );
    }
    
    public function destroy(LanguageLine $languageLine)
    {
        $languageLine->delete();
        return returnResponse(
            [
                'success' => true,
                'message' => t('Translation deleted successfully')
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function getStringTranslation(Request $request)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->translation_service->getTranslation($request->input('key'))
            ],
            JsonResponse::HTTP_OK,
        );
    }
}

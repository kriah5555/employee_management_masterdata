<?php

namespace App\Http\Controllers\Translations;

use Spatie\TranslationLoader\LanguageLine;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Services\Translations\TranslationsService;
use App\Http\Requests\Translations\TranslationRequest;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

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

    public function index()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->translation_service->index()
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                    'file'    => $e->getFile(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
    public function show($id)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->translation_service->getTranslationById($id)
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                    'file'    => $e->getFile(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function destroy(LanguageLine $languageLine)
    {
        try {
            $languageLine->delete();
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Translation deleted successfully')
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                    'file'    => $e->getFile(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function getStringTranslation(Request $request)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->translation_service->getTranslation($request->all())
            ],
            JsonResponse::HTTP_OK,
        );
    }
    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'text.en' => 'nullable|string',
                'text.nl' => 'nullable|string',
                'text.fr' => 'nullable|string',
            ];

            $validator = Validator::make(request()->all(), $rules);
            if ($validator->fails()) {
                return returnResponse(
                    [
                        'success' => true,
                        'message' => $validator->errors()->all()
                    ],
                    JsonResponse::HTTP_BAD_REQUEST,
                );
            }
            $translation = $this->translation_service->getTranslationById($id);
            $this->translation_service->update($translation, $validator->validated()['text']);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Translations updated',
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                    'file'    => $e->getFile(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

}

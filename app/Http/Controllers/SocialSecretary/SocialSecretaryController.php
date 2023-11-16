<?php

namespace App\Http\Controllers\SocialSecretary;

use App\Http\Requests\Holiday\HolidayCodesOfSocialSecretaryRequest;
use App\Http\Requests\SocialSecretary\SocialSecretaryRequest;
use App\Services\SocialSecretary\SocialSecretaryService;
use App\Models\SocialSecretary\SocialSecretary;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class SocialSecretaryController extends Controller
{
    protected $socialSecretaryService;

    public function __construct(SocialSecretaryService $socialSecretaryService)
    {
        $this->socialSecretaryService = $socialSecretaryService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->socialSecretaryService->getSocialSecretaries(),
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SocialSecretaryRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Social Secretary created successfully'),
                    'data'    => $this->socialSecretaryService->createSocialSecretary($request->validated())
                ],
                JsonResponse::HTTP_CREATED,
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->socialSecretaryService->getSocialSecretaryDetails($id)
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SocialSecretaryRequest $request, SocialSecretary $socialSecretary)
    {
        try {
            $this->socialSecretaryService->updateSocialSecretary($socialSecretary, $request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Social Secretary updated successfully')
                ],
                JsonResponse::HTTP_CREATED,
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SocialSecretary $socialSecretary)
    {
        try {
            $this->socialSecretaryService->deleteSocialSecretary($socialSecretary);
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Social Secretary deleted successfully'),
                ],
                JsonResponse::HTTP_CREATED,
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
    public function getSocialSecretaryHolidayConfiguration($socialSecretaryId)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->socialSecretaryService->getSocialSecretaryHolidayConfiguration($socialSecretaryId),
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
    public function updateSocialSecretaryHolidayConfiguration(HolidayCodesOfSocialSecretaryRequest $request)
    {
        try {
            $this->socialSecretaryService->updateSocialSecretaryHolidayConfiguration($request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Social secretary codes updated successfully',
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
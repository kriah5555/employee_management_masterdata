<?php

namespace App\Http\Controllers\SocialSecretary;

use App\Http\Rules\SocialSecretary\SocialSecretaryRequest;
use App\Services\SocialSecretary\SocialSecretaryService;
use App\Models\SocialSecretary\SocialSecretary;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class SocialSecretaryController extends Controller
{
    public function __construct(protected SocialSecretaryService $socialSecretaryService)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->socialSecretaryService->getAll(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => []
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SocialSecretaryRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => t('Social Secretary created successfully'),
                'data'    => $this->socialSecretaryService->create($request->validated())
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(SocialSecretary $socialSecretary)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $socialSecretary
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SocialSecretary $socialSecretary)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => ['details' => $socialSecretary]
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SocialSecretaryRequest $request, SocialSecretary $socialSecretary)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => t('Social Secretary updated successfully'),
                'data'    => $this->socialSecretaryService->update($socialSecretary, $request->validated())
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SocialSecretary $socialSecretary)
    {
        $socialSecretary->delete();
        return returnResponse(
            [
                'success' => true,
                'message' => t('Social Secretary deleted successfully'),
            ],
            JsonResponse::HTTP_CREATED,
        );
    }
}

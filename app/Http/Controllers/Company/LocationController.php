<?php

namespace App\Http\Controllers\Company;

use App\Services\Company\LocationService;
use App\Http\Requests\Company\LocationRequest;
use App\Models\Company\Location;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Exception;

class LocationController extends Controller
{
    public function __construct(protected LocationService $locationService)
    {
    }

    public function index()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->locationService->getLocations(),
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function locationWorkstations($location_id)
    {
        try {
            return response()->json([
                'success' => true,
                'data'    => $this->locationService->getLocationWorkstations($location_id),
            ]);
        } catch (Exception $e) {
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
    public function store(LocationRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Location created successfully'),
                    'data'    => $this->locationService->create($request->validated())
                ],
                JsonResponse::HTTP_CREATED,
            );
        } catch (Exception $e) {
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
    public function show(string $locationId)
    {
        try {
            return response()->json([
                'success' => true,
                'data'    => $this->locationService->getLocationById($locationId),
            ]);
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function create()
    {
        try {
            return response()->json([
                'success' => true,
                'data'    => $this->locationService->getOptionsToCreate(),
            ]);
        } catch (Exception $e) {
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
    public function update(LocationRequest $request, $id)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Location updated successfully'),
                    'data'    => $this->locationService->update($id, $request->validated()),
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function destroy(string $locationId)
    {
        $this->locationService->deleteLocation($locationId);
        return response()->json([
            'success' => true,
            'message' => t('Location deleted successfully')
        ]);
    }
}

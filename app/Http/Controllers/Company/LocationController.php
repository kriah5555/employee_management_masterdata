<?php

namespace App\Http\Controllers\Company;

use App\Services\LocationService;
use App\Http\Rules\LocationRequest;
use App\Models\Company\Location;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    protected $locationService;
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function index()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->locationService->getLocations(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LocationRequest $request)
    {
        $location = $this->locationService->create($request->validated());
        return returnResponse(
            [
                'success' => true,
                'message' => t('Location created successfully'),
                'data'    => $location
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $locationId)
    {
        return response()->json([
            'success' => true,
            'data'    => $this->locationService->getLocationById($locationId),
        ]);
    }

    public function create($company_id)
    {
        return response()->json([
            'success' => true,
            'data'    => $this->locationService->getOptionsToCreate($company_id),
        ]);
    }

    public function edit($location_id)
    {
        return response()->json([
            'success' => true,
            'data'    => $this->locationService->getOptionsToEdit($location_id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LocationRequest $request, Location $location)
    {
        $this->locationService->update($location, $request->validated());
        $location->refresh();
        return returnResponse(
            [
                'success' => true,
                'message' => t('Location updated successfully'),
                'data'    => $location,
            ],
            JsonResponse::HTTP_OK,
        );
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

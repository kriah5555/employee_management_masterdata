<?php

namespace App\Http\Controllers;

use App\Services\LocationService;
use App\Http\Rules\LocationRequest;
use App\Models\Location;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    public function __construct(protected LocationService $location_service)
    {
    }

    public function index($company_id, $status = 1)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->location_service->getAll(['company_id' => $company_id, 'status' => $status]),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LocationRequest $request)
    {
        $location = $this->location_service->create($request->validated());
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
    public function show(Location $location)
    {
        return response()->json([
            'success' => true,
            'data'    => $location,
        ]);
    }

    public function create($company_id)
    {
        return response()->json([
            'success' => true,
            'data'    => $this->location_service->getOptionsToCreate($company_id),
        ]);
    }

    public function edit($location_id)
    {
        return response()->json([
            'success' => true,
            'data'    => $this->location_service->getOptionsToEdit($location_id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LocationRequest $request, Location $location)
    {
        $this->location_service->update($location, $request->validated());
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

    public function destroy(Location $location)
    {
        $location->delete();
        return response()->json([
            'success' => true,
            'message' => t('Location deleted successfully')
        ]);
    }
}
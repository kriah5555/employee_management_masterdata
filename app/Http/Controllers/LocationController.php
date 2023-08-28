<?php

namespace App\Http\Controllers;

use App\Services\LocationService;
use App\Http\Rules\LocationRequest;
use App\Models\Location;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    protected $location_service;

    public function __construct(LocationService $location_service)
    {
        $this->location_service = $location_service;
    }

    // public function index()
    // {
    //     try {
    //         $data = $this->location_service->getAll();
    //         return response()->json([
    //             'success' => true,
    //             'data' => $data,
    //         ]);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => [$e->getMessage()],
    //         ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    //     }
    // }

    public function locations($company_id, $status = 1)
    {
        try {
            $data = $this->location_service->getAll(['company_id' => $company_id, 'status' => $status]);
            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            return returnResponse(
                [
                    'status'  => false,
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
            $location = $this->location_service->create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Location created successfully',
                'data'    => $location,
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return returnResponse(
                [
                    'status'  => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
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

    /**
     * Update the specified resource in storage.
     */
    public function update(LocationRequest $request, Location $location)
    {
        try {
            $this->location_service->update($location, $request->validated());
            $location->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully',
                'data'    => $location,
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return returnResponse(
                [
                    'status'  => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
    public function destroy(Location $location)
    {
        $location->delete();
        return response()->json([
            'success' => true,
            'message' => 'Location deleted successfully'
        ]);
    }
}
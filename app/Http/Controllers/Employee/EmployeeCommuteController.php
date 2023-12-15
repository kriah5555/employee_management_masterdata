<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Company\LocationService;
use App\Services\Employee\CommuteTypeService;
use App\Services\Employee\EmployeeCommuteService;
use App\Http\Requests\Employee\EmployeeCommuteDetailsRequest;

class EmployeeCommuteController extends Controller
{
    public function __construct(
        protected EmployeeCommuteService $employeeCommuteService,
    )
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => [
                        'locations'     => app(LocationService::class)->getActiveLocations(),
                        'commute_types' => app(CommuteTypeService::class)->getActiveCommuteTypes(),
                    ]
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $employee_profile_id)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employeeCommuteService->getEmployeeCommuteDetails($employee_profile_id)
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeCommuteDetailsRequest $request, string $employee_profile_id)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employeeCommuteService->updateEmployeeCommuteDetails($request->validated(), $employee_profile_id)
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

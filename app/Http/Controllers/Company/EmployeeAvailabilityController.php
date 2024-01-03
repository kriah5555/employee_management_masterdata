<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Company\EmployeeAvailabilityService;
use App\Http\Requests\Employee\EmployeeAvailabilityRequest;
use Illuminate\Support\Facades\Auth;

class EmployeeAvailabilityController extends Controller
{

    public function __construct(protected EmployeeAvailabilityService $employeeAvailabilityService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(EmployeeAvailabilityRequest $request)
    {
        try {
            $userId = Auth::guard('web')->user()->id;
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employeeAvailabilityService->getEmployeeAvailabilityForAllCompanies($userId, $request->get('period')),
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    "success" => false,
                    "message" => $e->getMessage(),
                ],
                HTTP_INTERNAL_SERVER_ERROR::HTTP_OK,
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeAvailabilityRequest $request)
    {
        try {
            $userId = Auth::guard('web')->user()->id;
            $this->employeeAvailabilityService->createAvailability($userId, $request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Availability created successfully',
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
    public function show(string $id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeAvailabilityRequest $request, $id)
    {
        try {
            return response()->json(
                [
                    'success' => true,
                    'message' => $this->employeeAvailabilityService->updateAvailability($request->validated(), $id)
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            return response()->json(
                [
                    'success' => true,
                    'message' => $this->employeeAvailabilityService->deleteAvailability($id)
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

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
            $userId = getActiveUser()->id;
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
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
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
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeAvailabilityRequest $request)
    {
        try {
            $this->employeeAvailabilityService->deleteAvailability($request->validated());
            return returnResponse(
                [
                    'success' => false,
                    'message' => 'Availability deleted',
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    "success" => false,
                    "message" => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function getEmployeeAvailability(EmployeeAvailabilityRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employeeAvailabilityService->getEmployeeAvailability($request->get('employee_profile_id'), $request->get('period')),
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    "success" => false,
                    "message" => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
    public function getWeeklyAvailability(EmployeeAvailabilityRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employeeAvailabilityService->getWeeklyAvailability($request->get('week'), $request->get('year')),
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    "success" => false,
                    "message" => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
    public function getWeeklyAvailabilityForEmployee(EmployeeAvailabilityRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employeeAvailabilityService->getWeeklyAvailabilityForEmployee($request->get('employee_profile_id'), $request->get('week'), $request->get('year')),
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    "success" => false,
                    "message" => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}

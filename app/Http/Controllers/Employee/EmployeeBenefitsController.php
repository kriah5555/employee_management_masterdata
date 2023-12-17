<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\MealVoucherService;
use App\Services\Employee\EmployeeBenefitService;
use App\Http\Requests\Employee\EmployeeBenefitRequest;

class EmployeeBenefitsController extends Controller
{
    public function __construct(
        protected EmployeeBenefitService $employeeBenefitService,
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
                        'meal_vouchers' => app(MealVoucherService::class)->getActiveMealVouchers(),
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
    }

    /**
     * Display the specified resource.
     */
    public function show(string $employee_id)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employeeBenefitService->getEmployeeBenefits($employee_id)
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
    public function update(EmployeeBenefitRequest $request, string $employee_profile_id)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Employee benefits updated successfully',
                    'data'    => $this->employeeBenefitService->updateEmployeeBenefits($request->validated(), $employee_profile_id),
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

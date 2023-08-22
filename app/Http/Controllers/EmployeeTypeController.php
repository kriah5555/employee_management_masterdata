<?php

namespace App\Http\Controllers;

use App\Models\EmployeeType\EmployeeType;
use App\Http\Rules\EmployeeTypeRequest;
use App\Services\EmployeeTypeService;
use Illuminate\Http\JsonResponse;

class EmployeeTypeController extends Controller
{
    protected $employeeTypService;

    public function __construct(EmployeeTypeService $employeeTypService)
    {
        $this->employeeTypService = $employeeTypService;
    }

    /**
     * API to get list of all employee types.
     */
    public function index()
    {
        try {
            return response()->json([
                'success' => true,
                'data'    => $this->employeeTypService->index()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API to get the details required for creating an employee type.
     */
    public function create()
    {
        try {
            return response()->json([
                'success' => true,
                'data'    => $this->employeeTypService->create()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API to create a new employee type.
     */
    public function store(EmployeeTypeRequest $request)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Employee type created successfully',
                'data'    => $this->employeeTypService->store($request->validated())
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API to get the employee type details.
     */
    public function show($id)
    {
        try {
            return response()->json([
                'success' => true,
                'data'    => $this->employeeTypService->show($id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * API to get all the details required for editing an employee type.
     */
    public function edit($id)
    {
        try {
            return response()->json([
                'success' => true,
                'data'    => $this->employeeTypService->edit($id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the existing employeee type.
     */
    public function update(EmployeeTypeRequest $request, EmployeeType $employeeType)
    {
        try {
            $this->employeeTypService->update($employeeType, $request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Employee type updated successfully',
                'data'    => $this->employeeTypService->update($employeeType, $request->validated()),
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete employee type.
     */
    public function destroy(EmployeeType $employeeType)
    {
        $employeeType->delete();
        return response()->json([
            'success' => true,
            'message' => 'Employee type deleted successfully'
        ]);
    }

}

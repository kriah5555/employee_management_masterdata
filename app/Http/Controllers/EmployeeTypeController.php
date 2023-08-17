<?php

namespace App\Http\Controllers;

use App\Models\EmployeeType\EmployeeType;
use App\Http\Rules\EmployeeTypeRequest;
use App\Services\EmployeeTypeService;
use Illuminate\Http\JsonResponse;
class EmployeeTypeController extends Controller
{
    protected $employee_type_service;

    public function __construct(EmployeeTypeService $employee_type_service)
    {
        $this->employee_type_service = $employee_type_service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->employee_type_service->getAllEmployeeTypes();
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeTypeRequest $request)
    {
        try {
            $employee_type = $this->employee_type_service->create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Employee type created successfully',
                'data' => $employee_type,
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sector = $this->employee_type_service->getEmployeeTypeDetails($id);
        return response()->json([
            'success' => true,
            'data' => $sector,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeTypeRequest $request, EmployeeType $employee_type)
    {
        try {
            $this->employee_type_service->update($employee_type, $request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Employee type updated successfully',
                'data' => $employee_type,
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeType $employee_type)
    {
        $employee_type->delete();
        return response()->json([
            'success' => true,
            'message' => 'Employee type deleted successfully'
        ]);
    }

    public function getEmployeeTypeOptions(EmployeeType $employee_type)
    {
        $data = $employee_type->getEmployeeTypeOptions();
        return api_response(200, 'Employee type options', $data);
    }

    public function create()
    {
        $data = $this->employee_type_service->getCreateEmployeeTypeOptions();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function edit($id)
    {
        $data = $this->employee_type_service->getCreateEmployeeTypeOptions();
        $data['details'] = $this->employee_type_service->getEmployeeTypeDetails($id);
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
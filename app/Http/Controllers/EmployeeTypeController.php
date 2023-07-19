<?php

namespace App\Http\Controllers;

use App\Models\EmployeeType;
use App\Http\Requests\EmployeeTypeRequest;
use Illuminate\Http\JsonResponse;

class EmployeeTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = EmployeeType::all();
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeTypeRequest $request)
    {
        try {
            $employee_type = EmployeeType::create($request->validated());
            $data = [
                'message' => 'Employee type created successfully',
                'data' => $employee_type,
            ];
            return response()->json($data);
        } catch (Exception $e) {
            $data = [
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ];
            return response()->json($data, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeType $employee_type)
    {
        return response()->json($employee_type);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeTypeRequest $request, EmployeeType $employee_type)
    {
        try {
            $employee_type->update($request->all());
            $data = [
                'message' => 'Employee type updated successfully',
                'data' => $employee_type,
            ];
            return response()->json($data);
        } catch (Exception $e) {
            $data = [
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ];
            return response()->json($data, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeType $employee_type)
    {
        $employee_type->delete();
        $data = [
            'message' => 'Employee type deleted'
        ];
        return response()->json($data);
    }
}
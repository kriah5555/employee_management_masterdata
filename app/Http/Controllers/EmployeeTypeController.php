<?php

namespace App\Http\Controllers;

use App\Models\EmployeeType;
use App\Http\Rules\EmployeeTypeRequest;
use Illuminate\Http\JsonResponse;
class EmployeeTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = EmployeeType::all();
        return api_response(true, 'Employee types received successfully', $data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeTypeRequest $request)
    {
        try {
            $employee_type = EmployeeType::create($request->validated());
            return api_response(true, 'Employee type created successfully', $employee_type, 201);
        } catch (Exception $e) {
            return api_response(true, 'Internal server error', $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeType $employee_type)
    {
        if (!$employee_type) {
            return api_response(false, 'Employee type not found', '', 404);
        }
        return api_response(true, 'Employee type received successfully', $employee_type, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeTypeRequest $request, EmployeeType $employee_type)
    {
        try {
            if (!$employee_type) {
                return api_response(false, 'Employee type not found', $employee_type, 404);
            }
            $employee_type->update($request->all());
            return api_response(true, 'Employee type updated successfully', $employee_type, 202);
        } catch (Exception $e) {
            return api_response(false, 'Internal server error', $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeType $employee_type)
    {
        if (!$employee_type) {
            return api_response(false, 'Employee type not found', '', 404);
        }
        $employee_type->delete();
        return api_response(true, 'Employee type deleted', '', 200);
    }
}
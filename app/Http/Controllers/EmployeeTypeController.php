<?php

namespace App\Http\Controllers;

use App\Models\EmployeeType\EmployeeType;
use App\Http\Rules\EmployeeTypeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

class EmployeeTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = EmployeeType::all();
        return api_response(200, 'Employee types received successfully', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeTypeRequest $request)
    {
        try {
            $employee_type = EmployeeType::create($request->validated());
            return api_response(201, 'Employee type created successfully', $employee_type);
        } catch (Exception $e) {
            return api_response(400, 'Internal server error', $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeType $employee_type)
    {
        return api_response(200, 'Employee type received successfully', $employee_type);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeTypeRequest $request, EmployeeType $employee_type)
    {
        try {
            $employee_type->update($request->all());
            return api_response(202, 'Employee type updated successfully', $employee_type);
        } catch (Exception $e) {
            return api_response(400, 'Internal server error', $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeType $employee_type)
    {
        $employee_type->delete();
        return api_response(204, 'Employee type deleted');
    }

    public function getEmployeeTypeOptions(EmployeeType $employee_type)
    {
        
        
        $result = $employee_type->all();

        return api_response(202, 'Employee type updated successfully', $result);
        exit;
    }
}
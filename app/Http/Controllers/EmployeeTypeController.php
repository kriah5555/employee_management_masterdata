<?php

namespace App\Http\Controllers;

use App\Models\EmployeeType;
use App\Http\Rules\EmployeeTypeRequest;
use Illuminate\Http\JsonResponse;
use App\Services\CommonServices;
use Illuminate\Support\Facades\App;

class EmployeeTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $api_service;

    public function __construct()
    {
        $this->api_service = App::make(CommonServices::class);
    }

    public function index()
    {
        $data = EmployeeType::all();
        return $this->api_service->api_response(200, 'Employee types received successfully', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeTypeRequest $request)
    {
        try {
            $employee_type = EmployeeType::create($request->validated());
            return $this->api_service->api_response(201, 'Employee type created successfully', $employee_type);
        } catch (Exception $e) {
            return $this->api_service->api_response(400, 'Internal server error', $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeType $employee_type)
    {
        return $this->api_service->api_response(200, 'Employee type received successfully', $employee_type);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeTypeRequest $request, EmployeeType $employee_type)
    {
        try {
            $employee_type->update($request->all());
            return $this->api_service->api_response(202, 'Employee type updated successfully', $employee_type);
        } catch (Exception $e) {
            return $this->api_service->api_response(400, 'Internal server error', $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeType $employee_type)
    {
        $employee_type->delete();
        return $this->api_service->api_response(204, 'Employee type deleted');
    }
}
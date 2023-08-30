<?php

namespace App\Http\Controllers\EmployeeType;

use App\Models\EmployeeType\EmployeeType;
use App\Http\Rules\EmployeeType\EmployeeTypeRequest;
use App\Services\EmployeeType\EmployeeTypeService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

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
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->employeeTypService->index()
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * API to get the details required for creating an employee type.
     */
    public function create()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->employeeTypService->create()
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * API to create a new employee type.
     */
    public function store(EmployeeTypeRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => 'Employee type created successfully',
                'data'    => $this->employeeTypService->store($request->validated())
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    /**
     * API to get the employee type details.
     */
    public function show($id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->employeeTypService->show($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }


    /**
     * API to get all the details required for editing an employee type.
     */
    public function edit($id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->employeeTypService->edit($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Update the existing employeee type.
     */
    public function update(EmployeeTypeRequest $request, EmployeeType $employeeType)
    {
        $this->employeeTypService->update($employeeType, $request->validated());
        return returnResponse(
            [
                'success' => true,
                'message' => 'Employee type updated successfully',
                'data'    => $this->employeeTypService->update($employeeType, $request->validated()),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Delete employee type.
     */
    public function destroy(EmployeeType $employeeType)
    {
        $employeeType->delete();
        return returnResponse(
            [
                'success' => true,
                'message' => 'Employee type deleted successfully'
            ],
            JsonResponse::HTTP_OK,
        );
    }

}
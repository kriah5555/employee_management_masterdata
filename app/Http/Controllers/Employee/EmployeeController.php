<?php

namespace App\Http\Controllers\Employee;

use App\Models\EmployeeType\EmployeeType;
use App\Http\Rules\Employee\CreateEmployeeRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\Employee\EmployeeService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * API to get list of all employee types.
     */
    public function index(string $companyId)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->employeeService->index($companyId)
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function create(string $companyId)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->employeeService->create($companyId)
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * API to get the details required for creating an employee type.
     */
    public function store(CreateEmployeeRequest $request, $company_id)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => 'Employee created successfully',
                'data'    => $this->employeeService->createNewEmployee($request->validated(), $company_id)
            ],
            JsonResponse::HTTP_OK,
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
                'data'    => $this->employeeService->show($id),
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
                'data'    => $this->employeeService->edit($id),
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

    public function getFunctionSalaryToCreateEmployee(Request $request)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->employeeService->getSalary($request->employee_type_id, $request->function_title_id, $request->experience_in_months)
            ],
            JsonResponse::HTTP_OK,
        );
    }

}
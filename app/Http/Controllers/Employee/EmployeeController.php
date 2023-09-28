<?php

namespace App\Http\Controllers\Employee;

use App\Models\EmployeeType\EmployeeType;
use App\Http\Rules\Employee\EmployeeProfileRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\Employee\EmployeeProfileService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $employeeProfileService;

    public function __construct(EmployeeProfileService $employeeProfileService)
    {
        $this->employeeProfileService = $employeeProfileService;
    }

    /**
     * API to get list of all employee types.
     */
    public function index(string $companyId)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->employeeProfileService->index($companyId)
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function create(string $companyId)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->employeeProfileService->create($companyId)
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * API to get the details required for creating an employee type.
     */
    public function store(EmployeeProfileRequest $request, $company_id)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => 'Employee created successfully',
                'data'    => $this->employeeProfileService->createNewEmployeeProfile($request->all(), $company_id)
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
                'data'    => $this->employeeProfileService->show($id),
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
                'data'    => $this->employeeProfileService->edit($id),
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
                'data'    => $this->employeeProfileService->getSalary($request->employee_type_id, $request->function_title_id, $request->experience_in_months)
            ],
            JsonResponse::HTTP_OK,
        );
    }

}
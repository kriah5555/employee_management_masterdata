<?php

namespace App\Http\Controllers\Employee;

use Exception;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Employee\EmployeeService;
use App\Services\EmployeeFunction\FunctionService;
use App\Services\Employee\EmployeeContractService;
use App\Http\Requests\Employee\EmployeeContractRequest;
use Illuminate\Http\Request;


class EmployeeContractController extends Controller
{

    public function __construct(
        protected EmployeeService $employeeService,
        protected EmployeeContractService $employeeContractService,
        protected CompanyService $companyService,
        protected FunctionService $functionService,
    ) {
    }

    public function store(EmployeeContractRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Employee contract created successfully',
                    'data'    => $this->employeeContractService->createEmployeeContract($request->all()),
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
     * API to get the employee type details.
     */
    public function show($employee_id)
    {
        try {

            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employeeContractService->getEmployeeContracts($employee_id)
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


    public function create()
    {
        try {
            $companyId = getCompanyId();
            return returnResponse(
                [
                    'success' => true,
                    'data'    => [
                        'employee_contract_options' => $this->companyService->getEmployeeContractOptionsForCreation($companyId),
                        'sub_types'                 => associativeToDictionaryFormat($this->employeeService->getSubTypeOptions(), 'key', 'value'),
                        'schedule_types'            => associativeToDictionaryFormat($this->employeeService->getScheduleTypeOptions(), 'key', 'value'),
                        'employment_types'          => associativeToDictionaryFormat($this->employeeService->getEmploymentTypeOptions(), 'key', 'value'),
                        'salary_types'              => associativeToDictionaryFormat($this->employeeService->getEmployeeSalaryTypeOptions(), 'key', 'value'),
                        'functions'                 => $this->functionService->getCompanyFunctionTitles(getCompanyId()),
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

    public function destroy($employee_contract_id)
    {
        try {
            $this->employeeContractService->deleteEmployeeContract($employee_contract_id);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Employee contracts deleted successfully'
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

    public function update(EmployeeContractRequest $request, $employee_contract_id)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Employee contract updated successfully',
                    'data'    => $this->employeeContractService->updateEmployeeContract($request->all(), $employee_contract_id),
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
     * API to get list of all employee types.
     */
    public function getActiveContractEmployees(Request $request)
    {
        $weekNumber = $request->input('week_number');
        $year = $request->input('year');
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employeeContractService->getActiveContractEmployeesByWeek($weekNumber, $year)
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
}

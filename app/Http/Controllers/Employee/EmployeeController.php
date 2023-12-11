<?php

namespace App\Http\Controllers\Employee;

use Exception;
use Illuminate\Http\Request;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\MealVoucherService;
use Illuminate\Support\Facades\Auth;
use App\Models\EmployeeType\EmployeeType;
use App\Services\Company\LocationService;
use Illuminate\Support\Facades\Validator;
use App\Services\Employee\EmployeeService;
use App\Services\Employee\CommuteTypeService;
use App\Http\Requests\Employee\EmployeeRequest;
use App\Models\Company\Employee\EmployeeProfile;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeePersonalDetailsRequest;

class EmployeeController extends Controller
{

    public function __construct(
        protected EmployeeService $employeeService,
        protected CompanyService $companyService,
        protected CommuteTypeService $commuteTypeService,
        protected MealVoucherService $mealVoucherService,
        protected LocationService $locationService,
    ) {
    }

    /**
     * API to get list of all employee types.
     */
    public function index()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employeeService->index()
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
     * API to get the details required for creating an employee type.
     */
    public function store(EmployeeRequest $request)
    {
        try {
            $companyId = getCompanyId();
            $this->employeeService->createNewEmployee($request->validated(), $companyId);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Employee created successfully'
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
    public function show($id)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employeeService->getEmployeeDetails($id),
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
     * Update the existing employeee type.
     */
    public function update(EmployeeRequest $request, $employeeProfileId)
    {
        try {
            $this->employeeService->update($employeeProfileId, $request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Employee profile updated successfully',
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


    public function updateEmployee(UpdateEmployeeRequest $request)
    {
        try {
            $companyId = getCompanyId();
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Employee updated successfully',
                    'data'    => $this->employeeService->updateEmployee($request->validated(), $companyId)
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
     * Delete employee type.
     */
    public function destroy(EmployeeType $employeeType)
    {
        try {
            $employeeType->delete();
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Employee type deleted successfully'
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

    public function getFunctionSalaryToCreateEmployee(Request $request)
    {
        try {
            $rules = [
                'employee_type_id'     => 'required|integer',
                'employee_subtype'     => 'nullable|string',
                'function_title_id'    => 'required|integer',
                'experience_in_months' => 'required|integer',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return returnResponse(
                    [
                        'success' => false,
                        'message' => $validator->errors()->first(),
                    ],
                    JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employeeService->getSalary($request->employee_type_id, $request->function_title_id, $request->experience_in_months, $request->employee_subtype)
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

    public function createEmployeeContract()
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
                        'functions'                 => $this->companyService->getFunctionsForCompany($this->companyService->getCompanyDetails($companyId)),
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
    public function createEmployeeContractFunctions()
    {
        try {
            $companyId = getCompanyId();
            return returnResponse(
                [
                    'success' => true,
                    'data'    => [
                        'functions' => $this->companyService->getFunctionsForCompany($this->companyService->getCompanyDetails($companyId)),
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

    public function createEmployeeCommute()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => [
                        'locations'            => $this->locationService->getActiveLocations(),
                        'commute_type_options' => $this->commuteTypeService->getActiveCommuteTypes(),
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

    public function createEmployeeBenefits()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => [
                        'meal_vouchers' => $this->mealVoucherService->getActiveMealVouchers(),
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

    public function updatePersonalDetails(UpdateEmployeePersonalDetailsRequest $request)
    {
        try {
            $this->employeeService->updatePersonalDetails($request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Employee personal details updated successfully'
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

    public function getEmployeeContracts($employeeId)
    {
        try {

            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employeeService->getEmployeeContracts($employeeId)
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

    public function getUserResponsibleCompanies()
    {
        try {

            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->employeeService->getResponsibleCompaniesForUser(Auth::user())
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
}

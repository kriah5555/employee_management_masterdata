<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\MealVoucherService;
use Illuminate\Support\Facades\Auth;
use App\Models\EmployeeType\EmployeeType;
use App\Services\Company\LocationService;
use App\Services\Employee\EmployeeService;
use App\Repositories\CommuteTypeRepository;
use App\Repositories\MealVoucherRepository;
use App\Services\Employee\CommuteTypeService;
use App\Http\Requests\Employee\CreateEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeePersonalDetailsRequest;

class EmployeeController extends Controller
{
    protected $employeeService;
    protected $companyService;
    protected $locationService;
    protected $commuteTypeService;
    protected $mealVoucherService;

    public function __construct(EmployeeService $employeeService, CompanyService $companyService, LocationService $locationService, CommuteTypeService $commuteTypeService, MealVoucherService $mealVoucherService)
    {
        $this->employeeService = $employeeService;
        $this->companyService = $companyService;
        $this->locationService = $locationService;
        $this->commuteTypeService = $commuteTypeService;
        $this->mealVoucherService = $mealVoucherService;
    }

    /**
     * API to get list of all employee types.
     */
    public function index()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->employeeService->index()
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * API to get the details required for creating an employee type.
     */
    public function store(CreateEmployeeRequest $request)
    {
        $companyId = getCompanyId();
        return returnResponse(
            [
                'success' => true,
                'message' => 'Employee created successfully',
                'data'    => $this->employeeService->createNewEmployee($request->validated(), $companyId)
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
                'data'    => $this->employeeService->getEmployeeDetails($id),
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


    public function updateEmployee(UpdateEmployeeRequest $request)
    {
        $companyId = getCompanyId();
        return returnResponse(
            [
                'success' => true,
                'message' => 'Employee updated successfully',
                'data'    => $this->employeeService->updateEmployee($request->validated(), $companyId)
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

    public function createEmployeeContract()
    {
        $companyId = getCompanyId();
        try {
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

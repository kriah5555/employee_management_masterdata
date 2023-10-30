<?php

namespace App\Http\Controllers\Employee;

use App\Models\EmployeeType\EmployeeType;
use App\Http\Rules\Employee\CreateEmployeeRequest;
use App\Repositories\CommuteTypeRepository;
use App\Repositories\Company\LocationRepository;
use App\Repositories\MealVoucherRepository;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\Employee\EmployeeService;
use App\Services\CompanyService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $employeeService;
    protected $companyService;
    protected $locationRepository;
    protected $commuteTypeRepository;
    protected $mealVoucherRepository;

    public function __construct(EmployeeService $employeeService, CompanyService $companyService, LocationRepository $locationRepository, CommuteTypeRepository $commuteTypeRepository, MealVoucherRepository $mealVoucherRepository)
    {
        $this->employeeService = $employeeService;
        $this->companyService = $companyService;
        $this->locationRepository = $locationRepository;
        $this->commuteTypeRepository = $commuteTypeRepository;
        $this->mealVoucherRepository = $mealVoucherRepository;
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
                'data'    => [
                    'commute_type_options'      => $this->commuteTypeRepository->getActiveCommuteTypes(),
                    'employee_contract_options' => $this->companyService->getEmployeeContractOptionsForCreation($companyId),
                    'locations'                 => $this->locationRepository->getActiveLocationsOfCompany($companyId),
                    'sub_types'                 => $this->employeeService->getSubTypeOptions(),
                    'schedule_types'            => $this->employeeService->getScheduleTypeOptions(),
                    'meal_vouchers'             => $this->mealVoucherRepository->getActiveMealVouchers(),
                    'employment_types'          => $this->employeeService->getEmploymentTypeOptions(),
                    'functions'                 => $this->companyService->getFunctionsForCompany($this->companyService->getCompanyDetails($companyId)),
                    'genders'                   => $this->employeeService->getGenders(),
                    'marital_statuses'          => $this->employeeService->getMaritalStatus(),
                    'dependent_spouse_options'  => $this->employeeService->getDependentSpouseOptions(),
                    'languages'                 => $this->employeeService->getLanguageOptions(),
                ]
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

    public function getOptionsForEmployeeContractCreation(string $companyId)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => [
                        'employee_contract_options' => $this->companyService->getEmployeeContractOptionsForCreation($companyId),
                        'sub_types'                 => associativeToDictionaryFormat($this->employeeService->getSubTypeOptions()),
                        'schedule_types'            => associativeToDictionaryFormat($this->employeeService->getScheduleTypeOptions()),
                        'employment_types'          => associativeToDictionaryFormat($this->employeeService->getEmploymentTypeOptions()),
                        'salary_types'              => associativeToDictionaryFormat($this->employeeService->getEmployeeSalaryTypeOptions()),
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
    public function getFunctionsForLinkingToEmployee(string $companyId)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => [
                        'functions' => collectionToValueLabelFormat($this->companyService->getFunctionsForCompany($this->companyService->getCompanyDetails($companyId))),
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

    public function getOptionsToUpdateEmployeeTransportDetails(string $companyId)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => [
                        'locations' => collectionToValueLabelFormat($this->locationRepository->getCompanyLocations($companyId)),
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

}

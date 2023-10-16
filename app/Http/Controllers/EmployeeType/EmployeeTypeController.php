<?php

namespace App\Http\Controllers\EmployeeType;

use App\Models\EmployeeType\EmployeeType;
use App\Http\Rules\EmployeeType\EmployeeTypeRequest;
use App\Services\EmployeeType\EmployeeTypeService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Contract\ContractTypeService;
use App\Services\Dimona\DimonaService;

class EmployeeTypeController extends Controller
{
    protected $employeeTypService;

    protected $contractTypeService;

    protected $dimonaService;

    public function __construct(EmployeeTypeService $employeeTypService, ContractTypeService $contractTypeService, DimonaService $dimonaService)
    {
        $this->employeeTypService = $employeeTypService;
        $this->contractTypeService = $contractTypeService;
        $this->dimonaService = $dimonaService;
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
                    'data'    => $this->employeeTypService->getEmployeeTypes()
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
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
    public function create()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => [
                        'employee_type_categories' => $this->employeeTypService->getEmployeeTypeCategories(),
                        'contract_types'           => $this->contractTypeService->getActiveContractTypes(),
                        'dimona_types'             => $this->dimonaService->getActiveDimonaTypes(),
                        'salary_type'              => $this->employeeTypService->getSalaryTypeOptions()
                    ]
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
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
     * API to create a new employee type.
     */
    public function store(EmployeeTypeRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Employee type created successfully',
                    'data'    => $this->employeeTypService->createEmployeeType($request->validated())
                ],
                JsonResponse::HTTP_CREATED,
            );
        } catch (\Exception $e) {
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
                    'data'    => $this->employeeTypService->getEmployeeTypeDetails($id),
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
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
    public function update(EmployeeTypeRequest $request, EmployeeType $employeeType)
    {
        try {
            $this->employeeTypService->update($employeeType, $request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Employee type updated successfully'
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
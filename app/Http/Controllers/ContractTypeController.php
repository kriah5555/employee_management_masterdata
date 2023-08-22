<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Contracts\ContractTypeService;
use App\Models\Contracts\ContractType;
use App\Http\Rules\Contracts\ContractTypeRequest;
use Illuminate\Http\JsonResponse;

class ContractTypeController extends Controller
{
    protected $contractTypeService;

    public function __construct(ContractTypeService $contractTypeService)
    {
        $this->contractTypeService = $contractTypeService;
    }

    /**
     * Returns a list of all contract types.
     */
    public function index()
    {
        $data = $this->contractTypeService->getAllContractTypes();
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function create()
    {
        $data = $this->contractTypeService->getCreateContractTypeOptions();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContractTypeRequest $request)
    {
        try {
            $employee_type = $this->contractTypeService->create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Employee type created successfully',
                'data' => $employee_type,
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sector = $this->contractTypeService->getContractTypeDetails($id);
        return response()->json([
            'success' => true,
            'data' => $sector,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeTypeRequest $request, EmployeeType $employee_type)
    {
        try {
            $this->employee_type_service->update($employee_type, $request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Employee type updated successfully',
                'data' => $employee_type,
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeType $employee_type)
    {
        $employee_type->delete();
        return response()->json([
            'success' => true,
            'message' => 'Employee type deleted successfully'
        ]);
    }

    public function getEmployeeTypeOptions(EmployeeType $employee_type)
    {
        $data = $employee_type->getEmployeeTypeOptions();
        return api_response(200, 'Employee type options', $data);
    }

    /**
     * Display the specified resource.
     */
    public function edit($id)
    {
        $data = $this->employee_type_service->create();
        $data['details'] = $this->employee_type_service->getEmployeeTypeDetails($id);
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Contract;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Contract\ContractService;
use App\Http\Requests\Contract\ContractRequest;

class ContractController extends Controller
{
    public function __construct(
        protected ContractService $contractService
    )
    {
        
    }

    public function index($employee_profile_id, $status)
    {
        try {
            $contract_status = config('contracts.'.strtoupper($status));
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->contractService->getEmployeeContractFiles($employee_profile_id, $contract_status),
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

    public function create()
    {
        
    }

    public function store(ContractRequest $request)
    {
        try {
            $contract_status = $this->getContractStatusByPath(request()->getPathInfo());
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->contractService->generateEmployeeContract($request->employee_profile_id, $request->employee_contract_id, $contract_status),
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

    private function getContractStatusByPath($path)
    {
        if (str_contains($path, "/contracts")) {
            return config('contracts.CONTRACT_STATUS_UNSIGNED');
        } elseif (str_contains($path, "/sign-contract")) {
            return config('contracts.CONTRACT_STATUS_SIGNED');
        }

        return ''; // Default if no match is found
    }

    /**
     * Display the specified resource.
     */
    public function show(string $employee_profile_id)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    // 'data'    => $this->contractService->getEmployeeContractFiles($employee_profile_id),
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    function generateContract(Request $request)
    {
        
    }
}

<?php

namespace App\Http\Controllers\Contract;

use App\Services\Contract\ContractTypeService;
use App\Services\Contract\ContractRenewalTypeService;
use App\Models\Contract\ContractType;
use App\Http\Rules\Contract\ContractTypeRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ContractTypeController extends Controller
{
    protected $contractTypeService;

    protected $contractRenewalTypeService;

    public function __construct(ContractTypeService $contractTypeService, ContractRenewalTypeService $contractRenewalTypeService)
    {
        $this->contractTypeService = $contractTypeService;
        $this->contractRenewalTypeService = $contractRenewalTypeService;
    }

    /**
     * Returns a list of all contract types.
     */
    public function index()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->contractTypeService->getContractTypes(),
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
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => [
                        'renewal_types' => $this->contractRenewalTypeService->getActiveContractRenewalTypes()
                    ],
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
     * Store a newly created resource in storage.
     */
    public function store(ContractTypeRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Contract type created successfully',
                    'data'    => $this->contractTypeService->createContractType($request->validated())
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
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->contractTypeService->getContractTypeDetails($id)
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
     * Update the specified resource in storage.
     */
    public function update(ContractTypeRequest $request, ContractType $contractType)
    {
        try {
            $this->contractTypeService->updateContractType($contractType, $request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => t('Contract type updated successfully'),
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
     * Remove the specified resource from storage.
     */
    public function destroy(ContractType $contractType)
    {
        try {
            $this->contractTypeService->deleteContractType($contractType);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Contract type deleted successfully'
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
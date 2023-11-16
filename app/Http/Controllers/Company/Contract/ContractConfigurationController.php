<?php

namespace App\Http\Controllers\Company\Contract;

use App\Models\Company\Contract\ContractConfiguration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Company\Contract\ContractConfigurationService;
use Illuminate\Http\JsonResponse;

class ContractConfigurationController extends Controller
{
    public function __construct(protected ContractConfigurationService $contractConfigurationService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->contractConfigurationService->getContractConfigurations(),
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->contractConfigurationService->updateContractConfigurations($request->all());
            return returnResponse(
                [
                    'success' => true,
                    'success' => 'Contract configuration updated successfully',
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
     * Display the specified resource.
     */
    public function show(ContractConfiguration $contractConfiguration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContractConfiguration $contractConfiguration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContractConfiguration $contractConfiguration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContractConfiguration $contractConfiguration)
    {
        //
    }
}

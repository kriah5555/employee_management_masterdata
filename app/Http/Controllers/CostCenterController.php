<?php

namespace App\Http\Controllers;

use App\Models\CostCenter;
use Illuminate\Http\Request;
use App\Services\CostCenterService;
use App\Http\Rules\CostCenterRequest;
use Illuminate\Http\JsonResponse;

class CostCenterController extends Controller
{    
    public function __construct(protected CostCenterService $costCenterService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->costCenterService->getAll(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($company_id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->costCenterService->getOptionsToCreate($company_id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CostCenterRequest $request)
    {
        $location = $this->costCenterService->create($request->validated());
        return returnResponse(
            [
                'success' => true,
                'message' => t('Cost center created successfully'),
                'data'    => $location
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(CostCenter $costCenter)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $location
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CostCenter $costCenter)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->workstation_service->getOptionsToEdit($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CostCenterRequest $request, CostCenter $costCenter)
    {
        $location = $this->costCenterService->create($request->validated());
        return returnResponse(
            [
                'success' => true,
                'message' => t('Cost center created successfully'),
                'data'    => $location
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CostCenter $costCenter)
    {
        $workstation->delete();
        return returnResponse(
            [
                'success' => true,
                'message' => t('Cost center created successfully'),
            ],
            JsonResponse::HTTP_CREATED,
        );
    }
}

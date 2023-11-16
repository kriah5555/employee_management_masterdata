<?php

namespace App\Http\Controllers\Company;

use App\Models\Company\CostCenter;
use App\Services\CostCenterService;
use App\Http\Requests\Company\CostCenterRequest;
use Illuminate\Http\JsonResponse;

class CostCenterController extends Controller
{    
    public function __construct(protected CostCenterService $costCenterService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index($company_id, $status = '')
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->costCenterService->getAll(['company_id' => $company_id, 'status' => $status, 'with' => ['workstationsValue', 'location']]),
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
                'data'    => $costCenter
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($costCenter_id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->costCenterService->getOptionsToEdit($costCenter_id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CostCenterRequest $request, CostCenter $costCenter)
    {
        $this->costCenterService->update($costCenter, $request->validated());
        return returnResponse(
            [
                'success' => true,
                'message' => t('Cost center updated successfully'),
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CostCenter $costCenter)
    {
        $costCenter->delete();
        return returnResponse(
            [
                'success' => true,
                'message' => t('Cost center Deleted successfully'),
            ],
            JsonResponse::HTTP_CREATED,
        );
    }
}

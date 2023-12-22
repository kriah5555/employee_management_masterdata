<?php

namespace App\Http\Controllers\Company;

use App\Models\Company\CostCenter;
use App\Services\CostCenterService;
use App\Http\Requests\Company\CostCenterRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

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
                'data'    => $this->costCenterService->getAll(['with' => ['workstations', 'location']]),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->costCenterService->getOptionsToCreate(request()->header('Company-Id')),
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
    public function show($cost_center_id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->costCenterService->getCostCenters($cost_center_id, ['location', 'workstations', 'employees.employeeBasicDetails'] ),
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CostCenterRequest $request, $id)
    {
        $this->costCenterService->update($id, $request->validated());
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
    public function destroy($id)
    {
        $this->costCenterService->deleteCostCenter($id);
        return returnResponse(
            [
                'success' => true,
                'message' => t('Cost center Deleted successfully'),
            ],
            JsonResponse::HTTP_CREATED,
        );
    }
}

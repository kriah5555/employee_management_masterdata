<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MinimumSalary;
use App\Services\SectorSalaryService;
use App\Services\SectorService;
use App\Http\Rules\UpdateMinimumSalariesRequest;

class SalaryController extends Controller
{
    protected $sectorSalaryService;
    protected $sectorService;

    public function __construct(SectorSalaryService $sectorSalaryService, SectorService $sectorService)
    {
        $this->sectorSalaryService = $sectorSalaryService;
        $this->sectorService = $sectorService;
    }

    /**
     * Display a listing of the resource.
     */
    public function getMinimumSalaries($id)
    {
        try {
            $data = $this->sectorSalaryService->getMinimumSalariesBySectorId($id);
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function updateMinimumSalaries(UpdateMinimumSalariesRequest $request, $id)
    {
        print_r($request->validated());exit;
        try {
            $data = $this->sectorSalaryService->getMinimumSalariesBySectorId($id);
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

<?php

namespace App\Http\Controllers\Sector;

use App\Services\Sector\SectorSalaryService;
use App\Services\Sector\SectorService;
use App\Http\Rules\UpdateMinimumSalariesRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

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
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->sectorSalaryService->getMinimumSalariesBySectorId($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }
    /**
     * Display a listing of the resource.
     */
    public function updateMinimumSalaries(UpdateMinimumSalariesRequest $request, $id)
    {
        $this->sectorSalaryService->updateMinimumSalaries($id, $request->validated()['salaries']);
        return returnResponse(
            [
                'success' => true,
                'message' => 'Minimum salaries updated'
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function addIncrementToMinimumSalaries($sector_id, $increment_coefficient)
    {
        try {
            $this->sectorSalaryService->incrementMinimumSalaries($sector_id, $increment_coefficient);
            return response()->json([
                'success' => true,
                'message' => 'Minimum salaries updated'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function undoIncrementedMinimumSalaries($sector_id)
    {
        try {
            $status = $this->sectorSalaryService->undoIncrementedMinimumSalaries($sector_id);

            $data = [];
            if ($status == 'success' || empty($status)) {
                $message = 'Minimum reverted successfully';
            } else {
                $message = $status;
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
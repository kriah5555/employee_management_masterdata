<?php

namespace App\Http\Controllers;

use App\Models\Sector\Sector;
use App\Models\Sector\SectorSalaryConfig;
use App\Models\Sector\SectorSalarySteps;
use App\Models\EmployeeType\EmployeeType;
use App\Http\Rules\SectorRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\SectorService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SectorController extends Controller
{
    protected $sectorService;

    public function __construct(SectorService $sectorService)
    {
        $this->sectorService = $sectorService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = $this->sectorService->getAllSectors();
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
     * Store a newly created resource in storage.
     */
    public function store(SectorRequest $request)
    {
        try {
            $sector = $this->sectorService->createNewSector($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Sector created successfully',
                'data' => $sector,
            ]);
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
        $sector = $this->sectorService->getSectorDetails($id);
        try {
            return response()->json([
                'success' => true,
                'data' => $sector,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function edit($id)
    {
        try {
            $data = $this->sectorService->getCreateSectorOptions();
            $data['details'] = $this->sectorService->getSectorDetails($id);
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
     * Update the specified resource in storage.
     */
    public function update(SectorRequest $request, Sector $sector)
    {
        try {
            $this->sectorService->updateSector($sector, $request->validated());
            $sector->refresh();
            return response()->json([
                'success' => true,
                'message' => 'Sector updated successfully',
                'data' => $sector,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sector $sector)
    {
        $sector->delete();
        return response()->json([
            'success' => true,
            'message' => 'Sector deleted successfully'
        ]);
    }
    public function getMinimumSalaries($id)
    {
        try {
            $sector = $this->sectorService->getSectorById($id);
            // $data = $this->sectorSalaryService->getMinimumSalaries($sector);
            return response()->json([
                'success' => true,
                'data' => $sector,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function create()
    {
        $data = $this->sectorService->getCreateSectorOptions();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}

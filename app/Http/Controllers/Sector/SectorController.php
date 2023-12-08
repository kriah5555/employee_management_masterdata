<?php

namespace App\Http\Controllers\Sector;

use App\Models\Sector\Sector;
use App\Http\Requests\Sector\SectorRequest;
use App\Services\CompanyService;
use App\Services\EmployeeType\EmployeeTypeService;
use App\Services\Sector\SectorService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SectorController extends Controller
{
    protected $sectorService;

    protected $employeeTypeService;
    public function __construct(SectorService $sectorService, EmployeeTypeService $employeeTypeService, CompanyService $companyService)
    {
        $this->sectorService = $sectorService;
        $this->employeeTypeService = $employeeTypeService;
        $this->companyService = $companyService;
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
                    'data'    => $this->sectorService->getSectors(),
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
        return returnResponse(
            [
                'success' => true,
                'data'    => [
                    'employee_types' => $this->employeeTypeService->getActiveEmployeeTypes()
                ]
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SectorRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => 'Sector created successfully',
                'data'    => $this->sectorService->createSector($request->validated())
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->sectorService->getSectorDetails($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SectorRequest $request, Sector $sector)
    {
        $this->sectorService->updateSector($sector, $request->validated());
        $sector->refresh();
        return returnResponse(
            [
                'success' => true,
                'message' => 'Sector updated successfully',
                'data'    => $sector,
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sector $sector)
    {
        $this->sectorService->deleteSector($sector);
        return returnResponse(
            [
                'success' => true,
                'message' => 'Sector deleted successfully'
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function getFunctionTitles(Request $request)
    {
        try {
            $this->validate($request, [
                'sector_ids'   => 'required|array',
                'sector_ids.*' => [
                    'integer',
                    Rule::exists('sectors', 'id'),
                ],
            ]);
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->sectorService->getSectorFunctionTitles($request->input('sector_ids')),
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
    public function getCompanyLinkedFunctions()
    {
        try {
            $companyId = getCompanyId();
            return returnResponse(
                [
                    'success' => true,
                    'data'    => [
                        'functions' => $this->companyService->getFunctionsForCompany($this->companyService->getCompanyDetails($companyId)),
                    ]
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

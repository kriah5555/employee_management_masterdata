<?php

namespace App\Http\Controllers\EmployeeFunction;

use App\Models\EmployeeFunction\FunctionCategory;
use App\Http\Requests\FunctionCategoryRequest;
use Illuminate\Http\JsonResponse;
use App\Services\EmployeeFunction\FunctionService;
use App\Http\Controllers\Controller;
use App\Services\Sector\SectorService;

class FunctionCategoryController extends Controller
{
    protected $functionService;
    protected $sectorService;
    public function __construct(FunctionService $functionService, SectorService $sectorService)
    {
        $this->functionService = $functionService;
        $this->sectorService = $sectorService;
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
                    'data'    => $this->functionService->getFunctionCategories(),
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
    public function store(FunctionCategoryRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Function category created successfully',
                    'data'    => $this->functionService->createFunctionCategory($request->validated()),
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
                    'data'    => $this->functionService->getFunctionCategoryDetails($id),
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
    public function update(FunctionCategoryRequest $request, FunctionCategory $functionCategory)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Function category updated successfully',
                    'data'    => $this->functionService->updateFunctionCategory($functionCategory, $request->validated()),
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
    public function destroy(FunctionCategory $functionCategory)
    {
        try {
            $this->functionService->deleteFunctionCategory($functionCategory);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Function category deleted successfully'
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
                        'sectors' => $this->sectorService->getActiveSectors()
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
}
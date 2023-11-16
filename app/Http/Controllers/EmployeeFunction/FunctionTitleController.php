<?php

namespace App\Http\Controllers\EmployeeFunction;

use App\Models\EmployeeFunction\FunctionTitle;
use App\Http\Requests\FunctionTitleRequest;
use Illuminate\Http\JsonResponse;
use App\Services\EmployeeFunction\FunctionService;
use App\Http\Controllers\Controller;

class FunctionTitleController extends Controller
{
    protected $functionService;
    public function __construct(FunctionService $functionService)
    {
        $this->functionService = $functionService;
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
                    'data'    => $this->functionService->getFunctionTitles(),
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
    public function store(FunctionTitleRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Function created successfully',
                    'data'    => $this->functionService->storeFunctionTitle($request->validated()),
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
                    'data'    => $this->functionService->getFunctionTitleDetails($id),
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
    public function update(FunctionTitleRequest $request, FunctionTitle $functionTitle)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Function updated successfully',
                    'data'    => $this->functionService->updateFunctionTitle($functionTitle, $request->validated()),
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
    public function destroy(FunctionTitle $functionTitle)
    {
        try {
            $this->functionService->deleteFunctionTitle($functionTitle);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Function deleted successfully'
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
                        'function_categories' => $this->functionService->getActiveFunctionCategories()
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
<?php

namespace App\Http\Controllers\EmployeeFunction;

use App\Models\EmployeeFunction\FunctionCategory;
use App\Http\Rules\FunctionCategoryRequest;
use Illuminate\Http\JsonResponse;
use App\Services\EmployeeFunction\FunctionService;
use App\Http\Controllers\Controller;

class FunctionCategoryController extends Controller
{
    public function __construct(protected FunctionService $functionService)
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
                'data'    => $this->functionService->indexFunctionCategories(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FunctionCategoryRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => 'Function category created successfully',
                'data'    => $this->functionService->storeFunctionCategories($request->validated()),
            ],
            JsonResponse::HTTP_CREATED,
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
                'data'    => $this->functionService->showFunctionCategory($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FunctionCategoryRequest $request, FunctionCategory $functionCategory)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => 'Function category updated successfully',
                'data'    => $this->functionService->updateFunctionCategories($functionCategory, $request->validated()),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FunctionCategory $function_category)
    {
        $function_category->delete();
        return returnResponse(
            [
                'success' => true,
                'message' => 'Function category deleted successfully'
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function create()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->functionService->createFunctionCategory(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function edit($id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->functionService->editFunctionCategory($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }
}
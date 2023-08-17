<?php

namespace App\Http\Controllers;

use App\Models\Function\FunctionCategory;
use App\Http\Rules\FunctionCategoryRequest;
use Illuminate\Http\JsonResponse;
use App\Services\FunctionService;

class FunctionCategoryController extends Controller
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
        $data = FunctionCategory::all();
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FunctionCategoryRequest $request)
    {
        try {
            $function_category = FunctionCategory::create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Function category created successfully',
                'data' => $function_category,
            ], JsonResponse::HTTP_CREATED);
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
        try {
            $function_category = $this->functionService->getFunctionCategoryDetails($id);
            return response()->json([
                'success' => true,
                'data' => $function_category,
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
    public function update(FunctionCategoryRequest $request, FunctionCategory $function_category)
    {
        try {
            $function_category->update($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Function category updated successfully',
                'data' => $function_category,
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FunctionCategory $function_category)
    {
        $function_category->delete();
        return response()->json([
            'success' => true,
            'message' => 'Function category deleted successfully'
        ]);
    }

    public function create()
    {
        $data = $this->functionService->getCreateFunctionCategoryOptions();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function edit($id)
    {
        try {
            $data = $this->functionService->getCreateFunctionCategoryOptions();
            $data['details'] = $this->functionService->getFunctionCategoryDetails($id);
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

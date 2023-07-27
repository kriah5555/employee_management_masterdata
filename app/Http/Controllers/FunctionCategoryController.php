<?php

namespace App\Http\Controllers;

use App\Models\FunctionCategory;
use Illuminate\Http\Request;
use App\Http\Rules\FunctionCategoryRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

class FunctionCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = FunctionCategory::all();
        return response()->json($data);
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
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(FunctionCategory $function_category)
    {
        return response()->json($function_category);
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
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
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
        ], JsonResponse::HTTP_OK);
    }
}

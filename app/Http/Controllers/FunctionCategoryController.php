<?php

namespace App\Http\Controllers;

use App\Models\FunctionCategory;
use App\Http\Rules\FunctionCategoryRequest;
use Illuminate\Http\JsonResponse;
class FunctionCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return api_response(true, 'Function categories received successfully', FunctionCategory::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FunctionCategoryRequest $request)
    {
        try {
            $function = FunctionCategory::create($request->validated());
            return api_response(true, 'Function category created successfully', $function, 201);
        } catch (Exception $e) {
            return api_response(false, 'Internal server error', $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(FunctionCategory $function_category)
    {
        if (!$function_category) {
            return api_response(false, 'Function category not found', '', 404);
        }
        return api_response(true, 'Function category received successfully', $function_category, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FunctionCategoryRequest $request, FunctionCategory $function_category)
    {
        try {
            if (!$function_category) {
                return api_response(false, 'Function category not found', '', 404);
            }
            $function_category->update($request->all());
            return api_response(true, 'Function category updated successfully', $function_category, 202);
        } catch (Exception $e) {
            return api_response(true, 'Internal server error', $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FunctionCategory $function_category)
    {   
        if (!$function_category) {
            return api_response(false, 'Function category data not found', '', 404);
        }
        $function_category->delete();
        return api_response(true, 'Function category deleted', '', 204);
    }
}

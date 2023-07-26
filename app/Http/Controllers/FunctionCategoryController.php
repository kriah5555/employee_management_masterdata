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
        return api_response(200, 'Function categories received successfully', FunctionCategory::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FunctionCategoryRequest $request)
    {
        try {
            $function = FunctionCategory::create($request->validated());
            return api_response(201, 'Function category created successfully', $function);
        } catch (Exception $e) {
            return api_response(400, 'Internal server error', $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return api_response(200, 'Function category received successfully', FunctionCategory::find($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FunctionCategoryRequest $request, $id)
    {
        try {
            $function_category = FunctionCategory::find($id);
            if ($function_category) {
                return api_response(404, 'Function category data not found');
            }
            $function_category->update($request->all());
            return api_response(202, 'Function category updated successfully', $function_category);
        } catch (Exception $e) {
            return api_response(400, 'Internal server error', $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {   
        $function_category = FunctionCategory::find($id);
        if (!$function_category) {
            return api_response(404, 'Function category data not found');
        }
        $function_category->delete();
        return api_response(204, 'Function category deleted');
    }
}

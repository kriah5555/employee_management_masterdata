<?php

namespace App\Http\Controllers;

use App\Models\FunctionCategory;
use Illuminate\Http\Request;
use App\Http\Requests\FunctionCategoryRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

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
            $function = FunctionCategory::create($request->validated());
            $data = [
                'message' => 'Function category created successfully',
                'data' => $function,
            ];
            return response()->json($data);
        } catch (Exception $e) {
            $data = [
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ];
            return response()->json($data, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(FunctionCategory $function_title)
    {
        return response()->json($function_title);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FunctionCategoryRequest $request, FunctionCategory $function_title)
    {
        try {
            $function_title->update($request->all());
            $data = [
                'message' => 'Function category updated successfully',
                'data' => $function_title,
            ];
            return response()->json($data);
        } catch (Exception $e) {
            $data = [
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ];
            return response()->json($data, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FunctionCategory $function_title)
    {
        $function_title->delete();
        $data = [
            'message' => 'Function deleted'
        ];
        return response()->json($data);
    }
}

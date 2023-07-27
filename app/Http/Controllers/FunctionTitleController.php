<?php

namespace App\Http\Controllers;

use App\Models\FunctionTitle;
use Illuminate\Http\Request;
use App\Http\Rules\FunctionTitleRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

class FunctionTitleController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = FunctionTitle::all();
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FunctionTitleRequest $request)
    {
        try {
            $function = FunctionTitle::create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Function created successfully',
                'data' => $function,
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
    public function show(FunctionTitle $function_title)
    {
        return response()->json($function_title);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FunctionTitleRequest $request, FunctionTitle $function_title)
    {
        try {
            $function_title->update($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Function updated successfully',
                'data' => $function_title,
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
    public function destroy(FunctionTitle $function_title)
    {
        $function_title->delete();
        return response()->json([
            'success' => true,
            'message' => 'Function deleted successfully'
        ], JsonResponse::HTTP_OK);
    }
}

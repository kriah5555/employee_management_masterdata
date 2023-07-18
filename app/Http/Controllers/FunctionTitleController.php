<?php

namespace App\Http\Controllers;

use App\Models\FunctionTitle;
use Illuminate\Http\Request;
use App\Http\Requests\FunctionTitle\CreateFunctionTitleRequest;
use App\Http\Requests\FunctionTitle\UpdateFunctionTitleRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

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
    public function store(CreateFunctionTitleRequest $request)
    {
        try {
            $function = FunctionTitle::create($request->validated());
            $data = [
                'message' => 'Function created successfully',
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
    public function show(FunctionTitle $function_title)
    {
        return response()->json($function_title);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFunctionTitleRequest $request, FunctionTitle $function_title)
    {
        try {
            $function_title->update($request->all());
            $data = [
                'message' => 'Function updated successfully',
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
    public function destroy(FunctionTitle $function_title)
    {
        $function_title->delete();
        $data = [
            'message' => 'Function deleted'
        ];
        return response()->json($data);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\FunctionTitle;
use App\Http\Rules\FunctionTitleRequest;
use Illuminate\Http\JsonResponse;
class FunctionTitleController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return api_response(true, 'Function titles received successfully', FunctionTitle::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FunctionTitleRequest $request)
    {
        try {
            return api_response(true, 'Function created successfully', FunctionTitle::create($request->validated()), 201);
        } catch (Exception $e) {
            return api_response(false, 'Internal server error', $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(FunctionTitle $function_title)
    {
        if (!$function_title) {
            return api_response(false, 'Function title not found', $function_title, 404);
        }
        return api_response(true, 'Function title received successfully', $function_title, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FunctionTitleRequest $request, FunctionTitle $function_title)
    {
        try {
            if (!$function_title) {
                return api_response(fasle, 'Function title not found', '', 404);
            }
            $function_title->update($request->all());
            return api_response(true, 'Function updated successfully', $function_title, 202);
        } catch (Exception $e) {
            return api_response(false, 'Internal server error', $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FunctionTitle $function_title)
    {
        if (!$function_title) {
            return api_response(false, 'Function title not found', '', 404);
        }
        $function_title->delete();
        return api_response(true, 'Function title deleted', '', 204);
    }
}

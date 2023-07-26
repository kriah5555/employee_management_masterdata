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
        return api_response(200, 'Function titles received successfully', FunctionTitle::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FunctionTitleRequest $request)
    {
        try {
            return api_response(201, 'Function created successfully', FunctionTitle::create($request->validated()));
        } catch (Exception $e) {
            return api_response(400, 'Internal server error', $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(FunctionTitle $function_title)
    {
        return api_response(200, 'Function title received successfully', $function_title);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FunctionTitleRequest $request, FunctionTitle $function_title)
    {
        try {
            $function_title->update($request->all());
            return api_response(202, 'Function updated successfully', $function_title);
        } catch (Exception $e) {
            return api_response(400, 'Internal server error', $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FunctionTitle $function_title)
    {
        $function_title->delete();
        return api_response(204, 'Function title deleted');
    }
}

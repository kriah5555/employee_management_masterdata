<?php

namespace App\Http\Controllers;

use App\Models\Function\FunctionTitle;
use App\Http\Rules\FunctionTitleRequest;
use Illuminate\Http\JsonResponse;
use App\Services\FunctionService;

class FunctionTitleController extends Controller
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
        $data = FunctionTitle::all();
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
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
            return returnResponse(
                [
                    'status'  => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $function_title = $this->functionService->getFunctionTitleDetails($id);
            return response()->json([
                'success' => true,
                'data' => $function_title,
            ]);
        } catch (Exception $e) {
            return returnResponse(
                [
                    'status'  => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
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
            return returnResponse(
                [
                    'status'  => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
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
        ]);
    }

    public function create()
    {
        $data = $this->functionService->getCreateFunctionTitleOptions();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function edit($id)
    {
        try {
            $data = $this->functionService->getCreateFunctionTitleOptions();
            $data['details'] = $this->functionService->getFunctionTitleDetails($id);
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return returnResponse(
                [
                    'status'  => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

}

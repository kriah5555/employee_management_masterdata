<?php

namespace App\Http\Controllers\EmployeeFunction;

use App\Models\EmployeeFunction\FunctionTitle;
use App\Http\Rules\FunctionTitleRequest;
use Illuminate\Http\JsonResponse;
use App\Services\FunctionService;
use App\Http\Controllers\Controller;

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
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->functionService->indexFunctionTitles(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FunctionTitleRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => 'Function created successfully',
                'data'    => $this->functionService->storeFunctionTitle($request->validated()),
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->functionService->showFunctionTitle($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FunctionTitleRequest $request, FunctionTitle $functionTitle)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => 'Function updated successfully',
                'data'    => $this->functionService->updateFunctionTitle($functionTitle, $request->validated()),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FunctionTitle $function_title)
    {
        $function_title->delete();
        return returnResponse(
            [
                'success' => true,
                'message' => 'Function deleted successfully'
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function create()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->functionService->createFunctionTitle(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function edit($id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->functionService->editFunctionTitle($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }

}
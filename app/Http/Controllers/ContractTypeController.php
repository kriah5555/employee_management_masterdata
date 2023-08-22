<?php

namespace App\Http\Controllers;

use App\Services\Contracts\ContractTypeService;
use App\Models\Contracts\ContractType;
use App\Http\Rules\Contracts\ContractTypeRequest;
use Illuminate\Http\JsonResponse;

class ContractTypeController extends Controller
{
    protected $contractTypeService;

    public function __construct(ContractTypeService $contractTypeService)
    {
        $this->contractTypeService = $contractTypeService;
    }

    /**
     * Returns a list of all contract types.
     */
    public function index()
    {
        try {
            return response()->json([
                'success' => true,
                'data'    => $this->contractTypeService->index()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create()
    {
        try {
            return response()->json([
                'success' => true,
                'data'    => $this->contractTypeService->create()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContractTypeRequest $request)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Employee type created successfully',
                'data' => $this->contractTypeService->store($request->validated()),
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            return response()->json([
                'success' => true,
                'data'    => $this->contractTypeService->show($id)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function edit($id)
    {
        $data = $this->contractTypeService->create();
        $data['details'] = $this->contractTypeService->show($id);
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(ContractTypeRequest $request, ContractType $contractType)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Employee type updated successfully',
                'data' => $this->contractTypeService->update($contractType, $request->validated()),
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContractType $contractType)
    {
        try {
            $contractType->delete();
            return response()->json([
                'success' => true,
                'message' => 'Employee type deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

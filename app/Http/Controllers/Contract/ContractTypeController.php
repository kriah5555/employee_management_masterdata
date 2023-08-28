<?php

namespace App\Http\Controllers\Contract;

use App\Services\Contract\ContractTypeService;
use App\Models\Contract\ContractType;
use App\Http\Rules\Contract\ContractTypeRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

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
        return returnResponse(
            [
                'status' => true,
                'data'   => $this->contractTypeService->index(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    public function create()
    {
        return returnResponse(
            [
                'status' => true,
                'data'   => $this->contractTypeService->create(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContractTypeRequest $request)
    {
        return returnResponse(
            [
                'status'  => true,
                'message' => 'Contract type created successfully',
                'data'    => $this->contractTypeService->store($request->validated()),
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
                'data'    => $this->contractTypeService->show($id)
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Display the specified resource.
     */
    public function edit($id)
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->contractTypeService->edit($id),
            ],
            JsonResponse::HTTP_OK,
        );
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(ContractTypeRequest $request, ContractType $contractType)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => 'Contract type updated successfully',
                'data'    => $this->contractTypeService->update($contractType, $request->validated()),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContractType $contractType)
    {
        $contractType->delete();
        return returnResponse(
            [
                'success' => true,
                'message' => 'Contract type deleted successfully'
            ],
            JsonResponse::HTTP_OK,
        );
    }
}

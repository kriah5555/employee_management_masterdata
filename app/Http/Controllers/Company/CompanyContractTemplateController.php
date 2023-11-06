<?php

namespace App\Http\Controllers\Company;

use App\Models\Contract\ContractTemplate;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Rules\Contract\ContractTemplateRequest;
use App\Services\Company\CompanyContractTemplateService;

class CompanyContractTemplateController extends Controller
{
    public function __construct(protected CompanyContractTemplateService $contractTemplateService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->contractTemplateService->getAll(['with' => ['employeeType']]),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->contractTemplateService->getOptionsToCreate(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContractTemplateRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => 'Contract template created successfully',
                'data'    => $this->contractTemplateService->create($request->validated()),
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
                'data'    => $this->contractTemplateService->get($id, ['company', 'employeeType', 'company', 'socialSecretary'])
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContractTemplateRequest $request, $id)
    {
        $contractTemplate = ContractTemplate::findOrFail($id);
        return returnResponse(
            [
                'success' => true,
                'message' => t('Contract template updated successfully'),
                'data'    => $this->contractTemplateService->update($contractTemplate, $request->validated()),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContractTemplate $contractTemplate)
    {
        $contractTemplate->delete();
        return returnResponse(
            [
                'success' => true,
                'message' => 'Contract template deleted successfully'
            ],
            JsonResponse::HTTP_OK,
        );
    }
}

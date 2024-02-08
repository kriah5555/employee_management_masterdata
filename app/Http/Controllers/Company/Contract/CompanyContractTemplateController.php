<?php

namespace App\Http\Controllers\Company\Contract;

use App\Models\Company\Contract\CompanyContractTemplate;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\ContractTemplateRequest;
use App\Services\Company\Contract\CompanyContractTemplateService;
use App\Services\Contract\ContractTypeService;
use App\Http\Resources\Contract\CompanyContractTemplateResource;

class CompanyContractTemplateController extends Controller
{
    public function __construct(
        protected CompanyContractTemplateService $companyContractTemplateService,
        protected ContractTypeService $contractTypeService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => CompanyContractTemplateResource::collection($this->companyContractTemplateService->getAll(['with' => ['contractType']])),
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
                'data'    => [
                    'contract_types' => $this->contractTypeService->getActiveContractTypes(),
                    'tokens'         => array_merge(
                        config('tokens.EMPLOYEE_TOKENS'),
                        config('tokens.COMPANY_TOKENS'),
                        config('tokens.CONTRACT_TOKENS'),
                        config('tokens.ATTACHMENT_TOKENS'),
                        config('tokens.SIGNATURE_TOKENS'),
                        config('tokens.FLEX_SALARY_TOKENS'),
                        config('tokens.ADDITIONAL_TOKENS'),
                        config('tokens.PLANNING_TOKENS'),
                    ),
                ],
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
                'data'    => $this->companyContractTemplateService->create($request->validated()),
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
                'data'    => new CompanyContractTemplateResource($this->companyContractTemplateService->get($id, ['contractType']))
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContractTemplateRequest $request, $id)
    {
        $companyContractTemplate = CompanyContractTemplate::findOrFail($id);
        return returnResponse(
            [
                'success' => true,
                'message' => t('Contract template updated successfully'),
                'data'    => $this->companyContractTemplateService->update($companyContractTemplate, $request->validated()),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $companyContractTemplate = CompanyContractTemplate::findOrFail($id);
        $companyContractTemplate->delete();
        return returnResponse(
            [
                'success' => true,
                'message' => 'Contract template deleted successfully'
            ],
            JsonResponse::HTTP_OK,
        );
    }
}

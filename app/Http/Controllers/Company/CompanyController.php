<?php

namespace App\Http\Controllers\Company;

use App\Models\Company\Company;
use App\Http\Requests\Company\CompanyRequest;
use App\Http\Requests\Company\CompanyAdditionalDetailsRequest;
use App\Services\Company\CompanyService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Sector\SectorService;
use App\Services\SocialSecretary\SocialSecretaryService;
use App\Services\Interim\InterimAgencyService;
use Exception;

class CompanyController extends Controller
{
    protected $companyService;
    protected $sectorService;
    protected $socialSecretaryService;
    protected $interimAgencyService;
    public function __construct(CompanyService $companyService, SectorService $sectorService, SocialSecretaryService $socialSecretaryService, InterimAgencyService $interimAgencyService)
    {
        $this->companyService = $companyService;
        $this->sectorService = $sectorService;
        $this->socialSecretaryService = $socialSecretaryService;
        $this->interimAgencyService = $interimAgencyService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->companyService->getCompaniesForOverview(),
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Company created. Please complete the additional details',
                    'data'    => $this->companyService->registerNewCompany($request->validated()),
                ],
                JsonResponse::HTTP_CREATED,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function storeAdditionalDetails(CompanyAdditionalDetailsRequest $request)
    {
        try {
            $this->companyService->companyAdditionalDetails(getCompanyId(), $request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Company additional details saved',
                ],
                JsonResponse::HTTP_CREATED,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
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
            return response()->json([
                'success' => true,
                'data'    => $this->companyService->getCompanyDetails($id),
            ]);
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRequest $request, Company $company)
    {
        try {
            $this->companyService->updateCompany($company, $request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Company updated successfully',
                ],
                JsonResponse::HTTP_CREATED,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        try {
            $this->companyService->deleteCompany($company);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Company deleted successfully'
                ],
                JsonResponse::HTTP_CREATED,
            );
        } catch (Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function create()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => [
                    'sectors'            => $this->sectorService->getActiveSectors(),
                    'social_secretaries' => $this->socialSecretaryService->getActiveSocialSecretaries(),
                    'interim_agencies'   => $this->interimAgencyService->getActiveInterimAgencies(),
                ],
            ],
            JsonResponse::HTTP_OK,
        );
    }
}

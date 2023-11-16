<?php

namespace App\Http\Controllers\Interim;

use App\Models\Interim\InterimAgency;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Interim\InterimAgencyRequest;
use App\Services\Interim\InterimAgencyService;
use App\Http\Controllers\Controller;

class InterimAgencyController extends Controller
{
    protected $interimAgencyService;
    protected $companyService;
    public function __construct(InterimAgencyService $interimAgencyService, CompanyService $companyService)
    {
        $this->interimAgencyService = $interimAgencyService;
        $this->companyService = $companyService;
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
                    'data'    => $this->interimAgencyService->getInterimAgencies(),
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => [
                        'companies' => $this->companyService->getCompanies()
                    ],
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
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
    public function store(InterimAgencyRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Interim agency created successfully',
                    'data'    => $this->interimAgencyService->createInterimAgency($request->validated()),
                ],
                JsonResponse::HTTP_CREATED,
            );
        } catch (\Exception $e) {
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
    public function show($interimAgencyId)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $this->interimAgencyService->getInterimAgencyDetails($interimAgencyId),
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
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
    public function update(InterimAgencyRequest $request, InterimAgency $interimAgency)
    {
        try {
            $this->interimAgencyService->updateInterimAgency($interimAgency, $request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Interim agency updated successfully',
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
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
    public function destroy(InterimAgency $interimAgency)
    {
        try {
            $this->interimAgencyService->deleteInterimAgency($interimAgency);
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'Interim agency deleted successfully'
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}

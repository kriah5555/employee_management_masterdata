<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Rules\CompanyRules;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
class CompanyController extends Controller
{
    protected $company_service;

    public function __construct(CompanyService $company_service)
    {
        $this->company_service = $company_service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = $this->company_service->getAllCompanies();
            return response()->json([
                'success' => true,
                'data' => $data,
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
    public function store(CompanyRules $request)
    {
        try {
            $company = $this->company_service->createNewCompany($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Company created successfully',
                'data' => $company,
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
    public function show(Company $company)
    {
        return response()->json([
            'success' => true,
            'data' => $company,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRules $request, Company $company)
    {
        try {
            $this->company_service->updateCompany($company, $request->all());
            $company->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Company updated successfully',
                'data' => $company,
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $company->delete();
        return response()->json([
            'success' => true,
            'message' => 'Company deleted successfully'
        ]);
    }
}

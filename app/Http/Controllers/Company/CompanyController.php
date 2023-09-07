<?php

namespace App\Http\Controllers\Company;

use App\Models\Company;
use App\Http\Rules\CompanyRequest;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;


class CompanyController extends Controller
{
    public function __construct(protected CompanyService $company_service)
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
                'data'    => $this->company_service->getAll(),
            ],
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request)
    {
        return returnResponse(
            [
                'success' => true,
                'message' => 'Company created successfully',
                'data'    => $this->company_service->create($request->validated()),
            ],
            JsonResponse::HTTP_CREATED,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        return response()->json([
            'success' => true,
            'data'    => $company,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRequest $request, Company $company)
    {
        try {
            $this->company_service->update($company, $request->all());
            $company->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Company updated successfully',
                'data'    => $company,
            ], JsonResponse::HTTP_CREATED);
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
        $company->delete();
        return response()->json([
            'success' => true,
            'message' => 'Company deleted successfully'
        ]);
    }

    public function create()
    {
        $data = $this->company_service->getCreateCompanyOptions();
        return response()->json([
            'success' => true,
            'data'    => $data
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Rules\CompanyRules;
class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return api_response(200, 'Commpanies received successfully', Company::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRules $request, Company $company)
    {
        try {
            $company = Company::create($request->all());
            $sectors = $request['sectors'];
            $company->sectors()->sync($sectors);
            $company->refresh();
            return api_response(true, 'Company created successfully', $company, 201);
        } catch (Exception $e) {
            return api_response(false, 'Internal server error', $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }    
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        if (!$company) {
            return api_response(false, 'Company not found', '', 404);
        }
        return api_response(true, 'Company received successfully', $company, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRules $request, Company $company)
    {
        try {
            if (!$company) {
                return api_response(404, 'company not found', '', 404);
            }
            if (isset($request['sectors'])) {
                $sectors = $request['sectors'];
            } else {
                $sectors = [];
            }
            $company->update($request->all());
            $company->sectors()->sync($sectors);
            $company->refresh();
            return api_response(false, 'company updated successfully', $company, 202);
        } catch (Exception $e) {
            return api_response(false, 'Internal server error', $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        if (!$company) {
            return api_response(false, 'Company data not found', '', 404);
        }
        $company->delete();
        return api_response(true, 'Company deleted', '', 204);
    }
}

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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CompanyRules $company)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRules $request, Company $company)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        //
    }
}

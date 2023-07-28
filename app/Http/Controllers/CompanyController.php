<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Files;
use App\Http\Rules\CompanyRules;
class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Company::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRules $request)
    {
        try {
            $request_data = $request->all();

            $filename = str_replace(' ', '_', $request_data['company_name']) . '_' . time() . '_' . $request->file('logo')->getClientOriginalName();
            $file = Files::create([
                'file_name' => $filename,
                'file_path' => $request->file('logo')->storeAs('company_logos', $filename)
            ]);
            $request_data['logo'] = $file->id;

            $company = Company::create($request_data);

            $sectors = $request['sectors'];
            $company->sectors()->sync($sectors);
            $company->refresh();

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
            if (isset($request['sectors'])) {
                $sectors = $request['sectors'];
            } else {
                $sectors = [];
            }

            $request_data = $request->all();

            // Replace spaces in the company name with underscores to form the filename.
            $filename = str_replace(' ', '_', $request_data['company_name']) . '_' . time() . '_' . $request->file('logo')->getClientOriginalName();
            $file = Files::create([
                'file_name' => $filename,
                'file_path' => $request->file('logo')->storeAs('company_logos', $filename)
            ]);
            $request_data['logo'] = $file->id;

            $company->update($request_data);
            $company->sectors()->sync($sectors);
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

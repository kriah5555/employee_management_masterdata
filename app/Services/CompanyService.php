<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Files;
class CompanyService
{
    public function getCompanyDetails($id)
    {
        return Company::findOrFail($id);
    }

    public function getAllCompanies()
    {
        return Company::all();
    }

    public function getActiveCompanies()
    {
        return Company::where('status', '=', true)->get();
    }

    public function createNewCompany($values)
    {
            try {
            DB::beginTransaction();
            $request_data = $values;
            if ($request_data['logo']) {
                $filename     = str_replace(' ', '_', $request_data['company_name']) . '_' . time() . '_' . $request_data['logo']->getClientOriginalName();
                $file         = Files::create([
                    'file_name' => $filename,
                    'file_path' => $request_data['logo']->storeAs('company_logos', $filename)
                ]);
                $request_data['logo'] = $file->id;
            }

            $company              = Company::create($request_data);
            $sectors              = $values['sectors'];
            $company->sectors()->sync($sectors);
            $company->refresh();

            DB::commit();
            return $company ;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateCompany(Company $company, $values)
    {
        try {
            DB::beginTransaction();
            if (isset($values['sectors'])) {
                $sectors = $values['sectors'];
            } else {
                $sectors = [];
            }
            $request_data = $values;

            if ($request_data['logo']) {
                // Remove the old logo if it exists
                if ($company->logo) {
                    $oldLogo = Files::find($company->logo);
                    if ($oldLogo) {
                        Storage::delete($oldLogo->file_path);
                        $oldLogo->delete();
                    }
                }
    
                // Store the new logo
                $filename = str_replace(' ', '_', $request_data['company_name']) . '_' . time() . '_' . $request_data['logo']->getClientOriginalName();
                $file = Files::create([
                    'file_name' => $filename,
                    'file_path' => $request_data['logo']->storeAs('company_logos', $filename)
                ]);
                $request_data['logo'] = $file->id;
            } else {
                // If no new logo provided, keep the existing logo
                unset($request_data['logo']);
            }

            $company->update($request_data);
            $company->sectors()->sync($sectors);
            $company->refresh();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getCompanyLogo(Company $company)
    {
        return $company->logo;
    }

    public function getCompanySectors(Company $company)
    {
        return $company->sectors;
    }
}

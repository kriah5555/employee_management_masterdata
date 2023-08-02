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
            $request_data         = $values;
            $request_data['logo'] = $request_data['logo'] ? self::addCompanyLogo($request_data) : '';
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
                $request_data['logo'] = self::addCompanyLogo($request_data, $company->id);
            } else {
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

    public function addCompanyLogo($request_data, $company_id = '')
    {
        if ($company_id) { # while updating
            $company = Company::find($company_id); // Corrected: Use $company_id instead of $id
            // Remove the old logo if it exists
            if ($company->logo) {
                $old_logo = Files::find($company->logo);
                if ($old_logo) {
                    Storage::delete($old_logo->file_path);
                    $old_logo->delete();
                }
            }
        }
        $filename = str_replace(' ', '_', $request_data['company_name']) . '_' . time() . '_' . $request_data['logo']->getClientOriginalName();
        $file     = Files::create([
            'file_name' => $filename,
            'file_path' => $request_data['logo']->storeAs('company_logos', $filename)
        ]);
        return $file->id;
    }
}

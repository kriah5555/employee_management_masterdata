<?php

namespace App\Services\Company;

use App\Interfaces\Services\Company\CompanyLogoServiceInterface;
use App\Models\Company\Company;
use Illuminate\Support\Facades\Storage;
use App\Models\Files;

class CompanyLogoService implements CompanyLogoServiceInterface
{
    public function __construct()
    {
    }


    public function updateCompanyLogoData(Company $company, $values)
    {
        $request_data = $values;

        if ($request_data['logo']) {
            $request_data['logo'] = self::addCompanyLogo($request_data, $company->id);
        } else {
            unset($request_data['logo']);
        }

        $company->update($request_data);
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
        $filename = str_replace(' ', '_', $request_data['company_name'] . '_' . time() . '_' . $request_data['logo']->getClientOriginalName());
        $file = Files::create([
            'file_name' => $filename,
            'file_path' => $request_data['logo']->storeAs('public/company_logos', $filename)
        ]);
        return $file->id;
    }
    public function getCompanyLogo(Company $company)
    {
        return $company->logo;
    }
}

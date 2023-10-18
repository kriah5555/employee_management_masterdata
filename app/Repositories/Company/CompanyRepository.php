<?php

namespace App\Repositories\Company;

use App\Interfaces\Company\CompanyRepositoryInterface;
use App\Models\Company;

class CompanyRepository implements CompanyRepositoryInterface
{
    public function getCompanies()
    {
        return Company::all();
    }
    public function getActiveCompanies()
    {
        return Company::where('status', '=', true)->get();
    }

    public function getCompanyById(string $companyId): Company
    {
        return Company::findOrFail($companyId);
    }

    public function deleteCompany(string $companyId)
    {
        Company::destroy($companyId);
    }

    public function createCompany(array $details): Company
    {
        return Company::create($details);
    }

    public function updateCompany(string $companyId, array $updatedDetails)
    {
        return Company::whereId($companyId)->update($updatedDetails);
    }
}
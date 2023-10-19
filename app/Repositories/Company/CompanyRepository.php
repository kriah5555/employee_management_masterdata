<?php

namespace App\Repositories\Company;

use App\Interfaces\Company\CompanyRepositoryInterface;
use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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

    public function getCompanyById(string $companyId, array $relations = []): Collection|Builder|Company
    {
        return Company::with($relations)->findOrFail($companyId);
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
<?php

namespace App\Repositories\Company;

use App\Interfaces\Company\CompanyRepositoryInterface;
use App\Models\Company\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;

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

    public function deleteCompany(Company $company)
    {
        if ($company->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete company');
        }
    }

    public function createCompany(array $details): Company
    {
        return Company::create($details);
    }

    public function updateCompany(Company $company, array $updatedDetails)
    {
        if ($company->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update company');
        }
    }
}

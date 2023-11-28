<?php

namespace App\Repositories\Company;

use App\Interfaces\Company\CompanyRepositoryInterface;
use App\Models\Company\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;
use App\Models\Tenant;

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

        try {
            $company = $this->getCompanyById($company->id);
            $tenant  = $this->getTenantByCompanyId($company->id);
    
            if ($tenant) {
                $tenant->delete();
            }

            if ($company->delete()) {
                return true;
            } else {
                throw new ModelDeleteFailedException('Failed to delete company');
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getTenantByCompanyId($companyId)
    {
        return Tenant::where('company_id', $companyId)->get()->first();
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

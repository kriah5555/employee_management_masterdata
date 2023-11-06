<?php

namespace App\Interfaces\Company;

use App\Models\Company\Company;

interface CompanyRepositoryInterface
{
    public function getCompanies();

    public function getActiveCompanies();

    public function getCompanyById(string $companyId);

    public function deleteCompany(Company $company);

    public function createCompany(array $details);

    public function updateCompany(Company $company, array $updatedDetails);
}

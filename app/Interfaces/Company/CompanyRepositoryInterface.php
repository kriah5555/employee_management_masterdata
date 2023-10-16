<?php

namespace App\Interfaces\Company;

interface CompanyRepositoryInterface
{
    public function getAllCompanies();

    public function getActiveCompanies();

    public function getCompanyById(string $companyId);

    public function deleteCompany(string $companyId);

    public function createCompany(array $details);

    public function updateCompany(string $companyId, array $updatedDetails);
}
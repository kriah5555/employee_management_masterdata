<?php

namespace App\Interfaces\Company;

use App\Models\Company\CompanySocialSecretaryDetails;
use App\Models\Company\Company;

interface CompanySocialSecretaryDetailsRepositoryInterface
{
    public function getCompanySocialSecretaryDetailsByCompany(Company $company);

    public function createCompanySocialSecretaryDetails(Company $company, array $details);

    public function updateCompanySocialSecretaryDetails(CompanySocialSecretaryDetails $companySocialSecretaryDetails, array $updatedDetails);
}

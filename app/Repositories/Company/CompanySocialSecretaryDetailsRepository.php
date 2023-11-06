<?php

namespace App\Repositories\Company;

use App\Interfaces\Company\CompanySocialSecretaryDetailsRepositoryInterface;
use App\Models\Company\Company;
use App\Models\Company\CompanySocialSecretaryDetails;
use App\Exceptions\ModelUpdateFailedException;

class CompanySocialSecretaryDetailsRepository implements CompanySocialSecretaryDetailsRepositoryInterface
{
    public function getCompanySocialSecretaryDetailsByCompany(Company $company)
    {
        return $company;
    }
    public function createCompanySocialSecretaryDetails(Company $company, array $details): CompanySocialSecretaryDetails
    {
        $details['company_id'] = $company->id;
        return CompanySocialSecretaryDetails::create($details);
    }

    public function updateCompanySocialSecretaryDetails(CompanySocialSecretaryDetails $companySocialSecretaryDetails, array $updatedDetails)
    {
        if ($companySocialSecretaryDetails->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update company social secretary details');
        }
    }
}

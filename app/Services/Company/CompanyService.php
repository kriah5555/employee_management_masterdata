<?php

namespace App\Services\Company;

use Illuminate\Support\Facades\DB;
use App\Models\Company\Company;
use App\Services\AddressService;
use App\Repositories\Company\CompanyRepository;
use App\Models\Tenant;
use App\Interfaces\Services\Company\CompanyServiceInterface;
use Exception;

use App\Services\Company\CompanyTenancyService;

class CompanyService implements CompanyServiceInterface
{

    public function __construct(
        protected CompanyRepository $companyRepository,
        protected AddressService $addressService,
        protected CompanyTenancyService $companyTenancyService,
        protected CompanyLogoService $companyLogoService
    ) {
    }

    public function getCompanies()
    {
        return $this->companyRepository->getCompanies();
    }
    public function getActiveCompanies()
    {
        return $this->companyRepository->getActiveCompanies();
    }

    public function registerNewCompany($values)
    {
        try {
            $company = DB::transaction(function () use ($values) {
                return $this->createCompany($values);
            });
            $this->companyTenancyService->createTenant($company);
            $company->address;
            return $company;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function companyAdditionalDetails($values)
    {
        dd($values);
        try {
            $company = DB::transaction(function () use ($values) {
                return $this->createCompany($values);
            });
            $this->companyTenancyService->createTenant($company);
            $company->address;
            return $company;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function createCompany($values)
    {
        $requestData = $values;
        $company_address = $this->addressService->createNewAddress($values['address']);
        $requestData['address_id'] = $company_address->id;
        $requestData['logo_id'] = isset($requestData['logo']) ? $this->companyLogoService->addCompanyLogo($requestData) : '';
        unset($requestData['social_Secretary_details'], $requestData['sectors'], $requestData['interim_agencies']);
        $company = $this->companyRepository->createCompany($requestData);
        if (isset($values['social_Secretary_details'])) {
            $company->companySocialSecretaryDetails()->create($values['social_Secretary_details']);
        }
        $company->sectors()->sync($values['sectors'] ?? []);
        $company->interimAgencies()->sync($values['interim_agencies'] ?? []);
        $company->refresh();
        return $company;
    }

    public function updateCompany($company, $values)
    {
        DB::beginTransaction();
        $this->addressService->updateAddress($company->address, $values['address']);

        if (isset($values['social_Secretary_details'])) {
            $company->companySocialSecretaryDetails()->updateOrCreate([], $values['social_Secretary_details']);
        }
        $company->sectors()->sync($values['sectors'] ?? []);
        $company->interimAgencies()->sync($values['interim_agencies'] ?? []);
        unset($values['social_Secretary_details'], $values['sectors'], $values['interim_agencies'], $values['address']);
        $this->companyRepository->updateCompany($company, $values);
        DB::commit();
    }

    public function deleteCompany($companyId)
    {
        $this->companyRepository->deleteCompany($companyId);
    }

    public function getCompanySectors(Company $company)
    {
        return $company->sectors;
    }

    public function getEmployeeContractOptionsForCreation(string $companyId)
    {
        $company = Company::with('sectors.employeeTypes.employeeTypeCategory')
            ->findOrFail($companyId);
        $employeeTypes = $company->sectors->flatMap(function ($sector) {
            return $sector->employeeTypes->map(function ($employeeType) {
                return $employeeType;
            });
        })->all();

        $employeeTypeCategoryOptions = $employeeTypeOptions = $employeeTypeCategoryConfig = [];
        foreach ($employeeTypes as $employeeType) {
            $employeeTypeCategoryOptions[$employeeType->employeeTypeCategory->id] = [
                'key'  => $employeeType->employeeTypeCategory->id,
                'name' => $employeeType->employeeTypeCategory->name
            ];
            $employeeTypeOptions[$employeeType->employeeTypeCategory->id][] = [
                'key'  => $employeeType->id,
                'name' => $employeeType->name
            ];
            $employeeTypeCategoryConfig[$employeeType->employeeTypeCategory->id] = [
                'sub_category_types' => $employeeType->employeeTypeCategory->sub_category_types,
                'schedule_types'     => $employeeType->employeeTypeCategory->schedule_types,
                'employment_types'   => $employeeType->employeeTypeCategory->employment_types,
            ];
        }
        return [
            'employee_type_categories'      => array_values($employeeTypeCategoryOptions),
            'employee_types'                => $employeeTypeOptions,
            'employee_type_category_config' => $employeeTypeCategoryConfig
        ];
    }

    public function getFunctionsForCompany(Company $company)
    {
        return $company->sectors->flatMap(function ($sector) {
            return $sector->functionCategories->flatMap(function ($functionCategory) {
                return $functionCategory->functionTitles;
            });
        });
    }

    public function getCompanyDetails($companyId): Company
    {
        return $this->companyRepository->getCompanyById($companyId, ['sectors', 'address', 'companySocialSecretaryDetails.socialSecretary', 'interimAgencies']);
    }

    public function getCompanyById($companyId): Company
    {
        return $this->companyRepository->getCompanyById($companyId);
    }

}

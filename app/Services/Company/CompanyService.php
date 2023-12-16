<?php

namespace App\Services\Company;

use App\Services\Employee\EmployeeService;
use Illuminate\Support\Facades\DB;
use App\Models\Company\Company;
use App\Services\AddressService;
use App\Repositories\Company\CompanyRepository;
use App\Interfaces\Services\Company\CompanyServiceInterface;
use Exception;

use App\Services\Company\CompanyTenancyService;

class CompanyService implements CompanyServiceInterface
{

    public function __construct(
        protected CompanyRepository $companyRepository,
        protected AddressService $addressService,
        protected CompanyTenancyService $companyTenancyService,
        protected CompanyLogoService $companyLogoService,
        protected CompanyLocationService $companyLocationService,
        protected CompanyWorkstationService $companyWorkstationService,
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

    public function companyAdditionalDetails($company_id, $values)
    {
        try {
            DB::connection('tenant')->beginTransaction();
                $responsible_persons_ids = [];
                foreach ($values['responsible_persons'] as $index => $responsiblePerson) {
                    $employee_service = app(EmployeeService::class);
                    $responsible_persons = $employee_service->createNewResponsiblePerson($responsiblePerson, $company_id);
                    $responsible_persons_ids[$index] = $responsible_persons->id;
                }
                $company = $this->getCompanyDetails($company_id);
                $location_ids = $this->companyLocationService->createCompanyLocations($values, $responsible_persons_ids); # add company locations
                $this->companyWorkstationService->createCompanyWorkstations($values, $location_ids, $company->id); # add workstations to location with function titles
            DB::connection('tenant')->commit();
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
        $company->companySocialSecretaryDetails()->create(
            [
                'social_secretary_id'     => $values['social_secretary_id'] ?? null,
                'social_secretary_number' => $values['social_secretary_number'] ?? null,
                'contact_email'           => $values['contact_email'] ?? null
            ]
        );
        $company->sectors()->sync($values['sectors'] ?? []);
        $company->interimAgencies()->sync($values['interim_agencies'] ?? []);
        return $company;
    }

    public function updateCompany($company, $values)
    {
        DB::beginTransaction();
        $this->addressService->updateAddress($company->address->id, $values['address']);
        $company->companySocialSecretaryDetails()->updateOrCreate(
            [
                'social_secretary_id'     => $values['social_secretary_id'],
                'social_secretary_number' => $values['social_secretary_number'],
                'contact_email'           => $values['contact_email']
            ]
        );
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

    public function getCompanyDetails($companyId): Company
    {
        return $this->companyRepository->getCompanyById($companyId, ['sectors', 'address', 'companySocialSecretaryDetails.socialSecretary', 'interimAgencies']);
    }

    public function getCompanyById($companyId): Company
    {
        return $this->companyRepository->getCompanyById($companyId);
    }

}

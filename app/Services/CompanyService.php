<?php

namespace App\Services;

use App\Repositories\Company\CompanySocialSecretaryDetailsRepository;
use App\Services\Employee\EmployeeService;
use Illuminate\Support\Facades\DB;
use App\Models\Company\Company;
use App\Models\Files;
use App\Services\AddressService;
use App\Services\Company\LocationService;
use App\Services\WorkstationService;
use App\Repositories\Company\CompanyRepository;
use App\Models\Tenant;
use App\Services\Email\MailService;

class CompanyService
{

    public function __construct(
        protected CompanyRepository $companyRepository,
        protected CompanySocialSecretaryDetailsRepository $companySocialSecretaryDetailsRepository,
        protected LocationService $locationService,
        protected AddressService $addressService,
        protected WorkstationService $workstationService,
        protected MailService $mailService,
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

    public function getTenantByCompanyId($companyId)
    {
        return $this->companyRepository->getTenantByCompanyId($companyId);
    }

    public function createNewCompany($values)
    {
        try {
            DB::connection('master')->beginTransaction();
            $company = $this->createCompany($values);
            DB::connection('master')->commit();
            $tenant = $this->createTenant($company);
            setTenantDB($tenant->id);

            DB::connection('tenant')->beginTransaction();
            foreach ($values['responsible_persons'] as $responsiblePerson) {
                $employee_service = app(EmployeeService::class);
                $employee_service->createNewResponsiblePerson($responsiblePerson, $company->id);
            }
            $location_ids = $this->createCompanyLocations($company, $values); # add company locations
            $this->createCompanyWorkstations($values, $location_ids, $company->id); # add workstations to location with function titles
            DB::connection('tenant')->commit();
            $this->mailService->sendCompanyCreationMail($company->id);
            return $company;
        } catch (\Exception $e) {
            DB::connection('master')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function createCompany($values)
    {
        return DB::transaction(function () use ($values) {
            $requestData = $values;
            $company_address = $this->addressService->createNewAddress($values['address']);
            $requestData['address'] = $company_address->id;
            $requestData['logo'] = isset($requestData['logo']) ? self::addCompanyLogo($requestData) : '';
            unset($requestData['social_Secretary_details'], $requestData['sectors'], $requestData['interim_agencies']);
            $company = $this->companyRepository->createCompany($requestData);
            if (isset($values['social_Secretary_details'])) {
                $company->companySocialSecretaryDetails()->create($values['social_Secretary_details']);
            }
            $company->sectors()->sync($values['sectors'] ?? []);
            $company->interimAgencies()->sync($values['interim_agencies'] ?? []);
            $company->refresh();
            return $company;
        });
    }

    public function createTenant($company)
    {
        $database_name = 'tenant_' . strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $company->company_name) . '_' . $company->id);
        makeTenantFolderPath($database_name);
        return Tenant::create([
            'tenancy_db_name' => $database_name,
            'database_name'   => $database_name,
            'company_id'      => $company->id,
        ]);
    }

    public function updateCompany($company, $values)
    {
        DB::beginTransaction();
        // $this->updateCompanyLogoData($company, $values);
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

    private function createCompanyLocations(Company $company, $values)
    {
        $location_ids = [];
        if (isset($values['locations'])) {
            foreach ($values['locations'] as $index => $location) {
                $location['company'] = $company->id;
                $location_ids[$index] = $this->locationService->create($location)->id;
            }
        }
        return $location_ids;
    }

    private function createCompanyWorkstations($values, $location_ids, $company_id)
    {
        if (!empty($location_ids) && isset($values['workstations'])) {
            foreach ($values['workstations'] as $workstation) {
                $workstation['locations'] = array_map(function ($value) use ($location_ids) {
                    return $location_ids[$value];
                }, $workstation['locations_index']);
                $workstation['company'] = $company_id;
                $this->workstationService->create($workstation, $company_id);
            }
        }
    }

    private function updateCompanyLogoData(Company $company, $values)
    {
        $request_data = $values;

        if ($request_data['logo']) {
            $request_data['logo'] = self::addCompanyLogo($request_data, $company->id);
        } else {
            unset($request_data['logo']);
        }

        $company->update($request_data);
    }

    public function getCompanyLogo(Company $company)
    {
        return $company->logo;
    }

    public function getCompanySectors(Company $company)
    {
        return $company->sectors;
    }

    public function addCompanyLogo($request_data, $company_id = '')
    {
        if ($company_id) { # while updating
            $company = Company::find($company_id); // Corrected: Use $company_id instead of $id
            // Remove the old logo if it exists
            if ($company->logo) {
                $old_logo = Files::find($company->logo);
                if ($old_logo) {
                    Storage::delete($old_logo->file_path);
                    $old_logo->delete();
                }
            }
        }
        $filename = str_replace(' ', '_', $request_data['company_name'] . '_' . time() . '_' . $request_data['logo']->getClientOriginalName());
        $file = Files::create([
            'file_name' => $filename,
            'file_path' => $request_data['logo']->storeAs(config('constants.COMPANY_LOGO_PATH'), $filename)
        ]);
        return $file->id;
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
            if ($employeeType->employeeTypeCategory->id == 1 && $employeeType->dimonaConfig->dimonaType->dimona_type_key == 'student') {
                $reserveHours = true;
            } else {
                $reserveHours = false;
            }
            $employeeTypeCategoryOptions[$employeeType->employeeTypeCategory->id] = [
                'key'  => $employeeType->employeeTypeCategory->id,
                'name' => $employeeType->employeeTypeCategory->name
            ];
            $employeeTypeOptions[$employeeType->employeeTypeCategory->id][$employeeType->id] = [
                'key'            => $employeeType->id,
                'name'           => $employeeType->name,
                'reserved_hours' => $reserveHours
            ];
            $employeeTypeCategoryConfig[$employeeType->employeeTypeCategory->id] = [
                'sub_category_types' => $employeeType->employeeTypeCategory->sub_category_types,
                'schedule_types'     => $employeeType->employeeTypeCategory->schedule_types,
                'employment_types'   => $employeeType->employeeTypeCategory->employment_types,
            ];
        }
        $employeeTypes = [];
        foreach ($employeeTypeOptions as $key => $value) {
            usort($value, function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
            $employeeTypes[$key] = array_values($value);
        }
        ksort($employeeTypeCategoryOptions);
        return [
            'employee_type_categories'      => array_values($employeeTypeCategoryOptions),
            'employee_types'                => $employeeTypes,
            'employee_type_category_config' => $employeeTypeCategoryConfig
        ];
    }

    public function getCompanyDetails($companyId)
    {
        return $this->companyRepository->getCompanyById($companyId, ['sectors', 'address', 'companySocialSecretaryDetails.socialSecretary', 'interimAgencies']);
    }

    public function getLocationsUnderCompany($companyId): Company
    {
        return Company::findOrFail($companyId);
    }

    public function getCompanyById($companyId): Company
    {
        return $this->companyRepository->getCompanyById($companyId);
    }
}

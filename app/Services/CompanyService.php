<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Company\Company;
use App\Models\Address;
use App\Models\Company\Location;
use App\Models\Workstation;
use App\Models\LocationRequest;
use App\Models\Files;
use App\Services\AddressService;
use App\Services\LocationService;
use App\Services\WorkstationService;
use App\Services\BaseService;
use App\Services\Sector\SectorService;
use App\Services\SocialSecretary\SocialSecretaryService;
use App\Repositories\Company\CompanyRepository;
use App\Services\Interim\InterimAgencyService;

class CompanyService
{
    protected $companyRepository;

    protected $addressService;
    protected $locationService;
    protected $workstationService;
    protected $sectorService;
    protected $socialSecretaryService;
    protected $interimAgencyService;
    protected $model;

    public function __construct(CompanyRepository $companyRepository, LocationService $locationService, AddressService $addressService, WorkstationService $workstationService)
    {
        $this->companyRepository = $companyRepository;
        $this->locationService = $locationService;
        $this->addressService = $addressService;
        $this->workstationService = $workstationService;
        $this->model = app(Company::class);
        $this->sectorService = app(SectorService::class);
        $this->socialSecretaryService = app(SocialSecretaryService::class);
        $this->interimAgencyService = app(InterimAgencyService::class);
    }

    public function getCompanies()
    {
        return $this->companyRepository->getCompanies();
    }
    public function getActiveCompanies()
    {
        return $this->companyRepository->getActiveCompanies();
    }

    public function createCompany($values)
    {
        DB::beginTransaction();
        $requestData = $values;
        $company_address = $this->addressService->createNewAddress($values['address']);
        $requestData['address'] = $company_address->id;
        $requestData['logo'] = isset($requestData['logo']) ? self::addCompanyLogo($requestData) : '';
        $company = $this->companyRepository->createCompany($requestData);
        $location_ids = $this->createCompanyLocations($company, $values); # add company locations
        $this->createCompanyWorkstations($values, $location_ids, $company->id); # add workstations to location with function titles
        $this->syncSectors($company, $values);
        $company->refresh();
        DB::commit();
        $company->createDatabaseTenancy();
        return $company;
    }

    public function updateCompany($company, $values)
    {
        DB::beginTransaction();
        // $this->updateCompanyLogoData($company, $values);
        $this->addressService->updateAddress($company->address, $values['address']);
        $this->syncSectors($company, $values);
        unset($values['address'], $values['sectors'], $values['responsible_persons'], $values['locations'], $values['workstations']);
        $this->companyRepository->updateCompany($company->id, $values);
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
                $this->workstationService->create($workstation);
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

    private function syncSectors(Company $company, $values)
    {
        if (isset($values['sectors'])) {
            $sectors = $values['sectors'];
        } else {
            $sectors = [];
        }

        $company->sectors()->sync($sectors);
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
            'file_path' => $request_data['logo']->storeAs('public/company_logos', $filename)
        ]);
        return $file->id;
    }

    public function getOptionsToCreate()
    {
        return [
            'sectors'            => $this->sectorService->getActiveSectors(),
            'social_secretaries' => $this->socialSecretaryService->getSocialSecretaryOptions(),
            'interim_agencies'   => $this->interimAgencyService->getInterimAgencyOptions(),
        ];
    }

    public function getOptionsToEdit($company_id)
    {
        $company_details = $this->model::with(['address', 'sectors', 'sectorsValue', 'logoFile'])->findOrFail($company_id);
        $options = $this->getOptionsToCreate();
        $options['details'] = $company_details;
        $options['details']['social_secretaries_value'] = $company_details->socialSecretaryValue();
        $options['details']['social_secretary_value'] = $company_details->socialSecretaryValue();
        $options['details']['interim_agency_value'] = $company_details->interimAgencyValue();
        unset($options['details']['socialSecretary'], $options['details']['sectors'], $options['details']['interimAgency']);

        return $options;
    }

    public function getCompanyOptions()
    {
        return $this->model::select(['id as value', 'company_name as label'])->get();
    }

    public function getAll(array $args = [])
    {
        if (isset($args['with'])) {
            return $this->model::with($args['with'])->get();
        } else {
            return $this->model::all();
        }
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

    public function getFunctionOptionsForCompany(Company $company)
    {
        return collectionToValueLabelFormat($this->getFunctionsForCompany($company));
    }

    public function getCompanyDetails($companyId): Company
    {
        return $this->companyRepository->getCompanyById($companyId, ['sectors', 'address']);
    }

    public function getLocationsUnderCompany($companyId): Company
    {
        return Company::findOrFail($companyId);
    }
}

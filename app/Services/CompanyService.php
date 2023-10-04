<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Address;
use App\Models\Location;
use App\Models\Workstation;
use App\Models\LocationRequest;
use App\Models\Files;
use App\Services\AddressService;
use App\Services\LocationService;
use App\Services\WorkstationService;
use App\Services\BaseService;
use App\Services\Sector\SectorService;

class CompanyService extends BaseService
{
    protected $sectorService;

    public function __construct(Company $company, SectorService $sectorService)
    {
        parent::__construct($company);
        $this->sectorService = $sectorService;
    }

    public function create($values)
    {
        try {
            return DB::transaction(function () use ($values) {
                $request_data = $values;
                $address_service = new AddressService();

                $company_address = $address_service->createNewAddress($values['address']);
                $request_data['address'] = $company_address->id;
                $request_data['logo'] = isset($request_data['logo']) ? self::addCompanyLogo($request_data) : '';
                $company = Company::create($request_data);
                $sectors = $values['sectors'];
                $location_ids = $this->createCompanyLocations($company, $values); # add company locations
                $this->createCompanyWorkstations($values, $location_ids, $company->id); # add workstations to location with function titles

                $this->syncSectors($company, $values);
                $company->refresh();
                return $company;
            });
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function update($company, $values)
    {
        try {
            DB::beginTransaction();
            $this->updateCompanyLogoData($company, $values);
            $address_service = new AddressService();
            $company_address = $address_service->updateAddress($company->address, $values['address']);
            unset($values['address']);
            $company->update($values);
            $this->syncSectors($company, $values);
            // $company->sectors()->sync($sectors);
            $company->refresh();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    private function createCompanyLocations(Company $company, $values)
    {
        $location_ids = [];
        if (isset($values['locations'])) {
            $location_service = new LocationService(new Location());
            foreach ($values['locations'] as $index => $location) {
                $location['company'] = $company->id;
                $location_ids[$index] = $location_service->create($location)->id;
            }
        }
        return $location_ids;
    }

    private function createCompanyWorkstations($values, $location_ids, $company_id)
    {
        $workstation_service = new WorkstationService(new Workstation());
        if (!empty($location_ids) && isset($values['workstations'])) {
            foreach ($values['workstations'] as $index => $workstation) {
                $workstation['locations'] = array_map(function ($value) use ($location_ids) {
                    return $location_ids[$value];
                }, $workstation['locations_index']);
                $workstation['company'] = $company_id;
                $workstation_service->create($workstation);
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
            'sectors' => $this->sectorService->getSectorOptions(),
        ];
    }

    public function getOptionsToEdit($company_id)
    {
        $company_details    = $this->get($company_id, ['address', 'sectors', 'sectorsValue', 'logoFile']);
        $options            = $this->getOptionsToCreate();
        $options['details'] = $company_details;
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

        $employeeTypeCategories = $company->sectors
            ->flatMap(function ($sector) {
                return $sector->employeeTypes->map(function ($employeeType) {
                    return [
                        'employee_type_category_id'   => $employeeType->employeeTypeCategory->id,
                        'employee_type_category_name' => $employeeType->employeeTypeCategory->name,
                        'employee_types'              => []
                    ];
                });
            });
        $employeeTypeCategories = $employeeTypeCategories->unique('employee_type_category_id')->values();

        $employeeTypesWithCategory = $company->sectors
            ->flatMap(function ($sector) {
                return $sector->employeeTypes->map(function ($employeeType) {
                    return [
                        'employee_type_id'          => $employeeType->id,
                        'employee_type'             => $employeeType->name,
                        'employee_type_category_id' => $employeeType->employeeTypeCategory->id,
                    ];
                });
            });
        $employeeTypesWithCategory = $employeeTypesWithCategory->groupBy('employee_type_category_id');
        $employeeTypesWithCategory = json_decode($employeeTypesWithCategory, true);
        $result = [];
        foreach ($employeeTypeCategories as $employeeTypeCategory) {
            $employeeTypeCategory['employee_types'] = $employeeTypesWithCategory[$employeeTypeCategory['employee_type_category_id']];
            $result[] = $employeeTypeCategory;
        }

        return $result;
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
        return Company::findOrFail($companyId);
    }
}
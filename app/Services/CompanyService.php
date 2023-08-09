<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Address;
use App\Models\LocationRequest;
use App\Models\Files;
use App\Services\AddressService;
use App\Services\LocationService;
use App\Services\WorkstationService;

class CompanyService
{
    public function getCompanyDetails($id)
    {
        return Company::findOrFail($id);
    }

    public function getAllCompanies()
    {
        return Company::all();
    }

    public function getActiveCompanies()
    {
        return Company::where('status', '=', true)->get();
    }

    public function createNewCompany($values)
    {
        try {
            DB::beginTransaction();
            $request_data    = $values;
            $address_service = new AddressService();
            
            $company_address         = $address_service->createNewAddress($values['address']);
            $request_data['address'] = $company_address->id;
            // $request_data['logo'] = $request_data['logo'] ? self::addCompanyLogo($request_data) : '';
            $company                 = Company::create($request_data);

            $sectors                 = $values['sectors'];
            
            
            $location_ids = $this->createCompanyLocations($company, $values); # add company locations
            
            $this->createCompanyWorkstations($values, $location_ids); # add workstations to location with function titles
            
            $this->syncSectors($company, $values);
            $company->refresh();
            DB::commit();
            return $company ;
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
            $location_service = new LocationService();
            foreach ($values['locations'] as $index => $location) {
                $location['company'] = $company->id;
                $location_ids[$index] = $location_service->createNewLocation($location)->id;
            }
        }
        return $location_ids;
    }

    private function createCompanyWorkstations($values, $location_ids)
    {
        $workstation_service = new WorkstationService();
        if (!empty($location_ids) && isset($values['workstations'])) {
            foreach ($values['workstations'] as $index => $workstation) {
                $workstation['locations'] = array_map(function ($value) use ($location_ids) {
                    return $location_ids[$value];
                }, $workstation['locations_index']);
                $workstation_service->createNewWorkstation($workstation);
            }
        }
    }

    public function updateCompany(Company $company, $values)
    {
        try {
            DB::beginTransaction();
            // $this->updateCompanyLodoData($company, $values);
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

    private function updateCompanyLodoData(Company $company, $values)
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
        $file     = Files::create([
            'file_name' => $filename,
            'file_path' => $request_data['logo']->storeAs('public/company_logos', $filename)
        ]);
        return $file->id;
    }
}

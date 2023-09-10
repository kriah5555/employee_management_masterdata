<?php

namespace App\Services;

use App\Models\Location;
use App\Models\Workstation;
use App\Services\AddressService;
use App\Rules\AddressRule;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Services\BaseService;
use App\Rules\WorkstationLinkedToCompanyRule;

class LocationService extends BaseService
{
    public function __construct(Location $location)
    {
        parent::__construct($location);
    }

    public function getAll(array $args = [])
    {
        return $this->model
            ->when(isset($args['status']) && $args['status'] !== 'all', fn($q) => $q->where('status', $args['status']))
            ->when(isset($args['company_id']), fn($q) => $q->where('company', $args['company_id']))
            ->when(isset($args['with']), fn($q) => $q->with($args['with']))
            ->with(['workstationsValues', 'address'])
            ->get();
    }

    public static function getLocationRules($for_company_creation = true)
    {
        $location_rules = [
            'location_name' => 'required|string|max:255',
            'address'       => ['required', new AddressRule()],
        ];

        if (!$for_company_creation) { # in company creation flow multi step form the workstation and locations are newly created and added so this ocndition will not be required
            $location_rules = self::addLocationCreationRules($location_rules);
        }
        return $location_rules;
    }

    public static function addLocationCreationRules($rules)
    {
        $rules['status']           = 'required|boolean';
        $rules['company']          = [
            'bail',
            'required',
            'integer',
            Rule::exists('companies', 'id')
        ];

        // $rules['workstations']     = 'nullable|array';
        // $rules['workstations.*'] = [
        //     'bail',
        //     'integer',
        //     Rule::exists('workstations', 'id'),
        //     new WorkstationLinkedToCompanyRule(request()->input('company')),
        // ];
        return $rules;
    }

    public function create($values)
    {
        try {
            DB::beginTransaction();
            $address           = new AddressService();
            $address           = $address->createNewAddress($values['address']);
            $values['address'] = $address->id;
            $location          = $this->model->create($values);
            // $workstations      = $values['workstations'] ?? [];
            // $location->workstations()->sync($workstations);
            DB::commit();
            return $location;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function update($location, $values)
    {
        try {
            DB::beginTransaction();
            $address = new AddressService();
            $address->updateAddress($location->address, $values['address']);
            // $workstations      = $values['workstations'] ?? [];
            // $location->workstations()->sync($workstations);
            unset($values['address']);
            unset($values['company']);
            $location->update($values);
            DB::commit();
            return $location;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getOptionsToCreate($company_id) 
    {
        return ['workstations' => Workstation::where('status', true)->where('company', $company_id)->get(['id as value', 'workstation_name as label'])];   
    }

    public function getOptionsToEdit($location_id) 
    {
        $location_details = $this->get($location_id, ['address']);
        $options = $this->getOptionsToCreate($location_details->company);
        $options['details'] = $location_details;
        return $options;
    }
}
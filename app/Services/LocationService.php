<?php

namespace App\Services;

use App\Models\Location;
use App\Services\AddressService;
use App\Rules\AddressRule;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Services\BaseService;

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
            ->get();    
    }

    public static function getLocationRules($for_company_creation = true) 
    {
        $location_rules = [
            'location_name' => 'required|string|max:255',
            'address'       => ['required', new AddressRule()],
            'company'       => [
                $for_company_creation ? 'nullable' : 'required',
                Rule::exists('companies', 'id')
            ],
        ];

        if (!$for_company_creation) {
            $location_rules['status'] = 'required|boolean';
        }
        return $location_rules;
    }

    public function create($values)
    {
        try {
            DB::beginTransaction();
            $address           = new AddressService();
            $address           = $address->createNewAddress($values['address']);
            $values['address'] = $address->id;
            $location          = $this->model->create($values);
            DB::commit();
            return $location ;
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
}

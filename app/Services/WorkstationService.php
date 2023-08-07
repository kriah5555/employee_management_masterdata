<?php

namespace App\Services;

use App\Models\Workstation;
use App\Services\AddressService;
use App\Rules\AddressRule;
use Illuminate\Validation\Rule;

class WorkstationService
{
    public static function getWorkstationRules($for_company_creation = true) 
    {
        $rules = [
            'workstation_name'  => 'required|string|max:255',
            'sequence_number'   => 'required|integer',
            'status'            => 'required|boolean',
            'function_titles'   => 'required|array',
            'function_titles.*' => [
                Rule::exists('function_titles', 'id'),
            ],
        ];

        if ($for_company_creation) {
            $rules = self::addCompanyCreationRules($rules);
        } else {
            $rules = self::addWorkstationCompanyCreationRules($rules);
        }

        return $rules;
    }

    private static function addCompanyCreationRules($rules)
    {
        $rules['locations_index']   = 'required|array';
        $rules['locations_index.*'] = 'integer';
        return $rules;
    }

    private static function addWorkstationCompanyCreationRules($rules)
    {
        $rules['locations']   = 'required|array';
        $rules['locations.*'] = [
            Rule::exists('locations', 'id')
        ];
        return $rules;
    }
    
    public function getWorkstationDetails($id)
    {
        return Workstation::findOrFail($id);
    }

    public function createNewWorkstation($values)
    {
        try {   
            $locations       = $values['locations'] ?? [];
            $function_titles = $values['function_titles'] ?? [];
            unset($values['locations']);
            unset($values['locations_index']);
            
            $workstation = Workstation::create($values);
            $workstation->locations()->sync($locations);
            $workstation->functionTitles()->sync($function_titles);
            
            return $workstation;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}

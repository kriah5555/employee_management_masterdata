<?php

namespace App\Services;

use App\Models\Workstation;
use App\Services\AddressService;
use App\Rules\AddressRule;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Rules\FunctionTitlesLinkedToSectorRule;

class WorkstationService
{
    public function getAllWorkstations()
    {
        return Workstation::all();
    }

    public function getActiveWorkstation()
    {
        return Workstation::where('status', '=', true)->get();
    }

    public function getWorkstationDetails($id)  
    {
        return Workstation::findOrFail($id);
    }

    public static function getWorkstationRules($for_company_creation = true) 
    {
        $rules = [
            'workstation_name'  => 'required|string|max:255',
            'sequence_number'   => 'required|integer',
            'status'            => 'required|boolean',
            'function_titles'   => 'nullable|array',
            'function_titles.*' => [
                Rule::exists('function_titles', 'id'),
                new FunctionTitlesLinkedToSectorRule(request()->input('sectors'))
            ],
        ];

        if ($for_company_creation) {    
            $rules = self::addCompanyCreationRules($rules);
        } else {
            $rules = self::addWorkstationRules($rules);
        }

        return $rules;
    }

    private static function addCompanyCreationRules($rules)
    {
        $rules['locations_index']   = 'required|array';
        $rules['locations_index.*'] = 'integer';
        return $rules;
    }

    private static function addWorkstationRules($rules)
    {
        $rules['locations']   = 'nullable|array';
        $rules['locations.*'] = [
            Rule::exists('locations', 'id')
        ];
        $rules['company']     = [
            'required', 
            'integer', 
            Rule::exists('companies', 'id')
        ];
        return $rules;
    }

    public function createNewWorkstation($values)
    {
        try {   
            DB::beginTransaction();
            $locations       = $values['locations'] ?? [];
            $function_titles = $values['function_titles'] ?? [];
            unset($values['locations']);
            unset($values['locations_index']);
            
            $workstation = Workstation::create($values);
            $workstation->locations()->sync($locations);
            $workstation->functionTitles()->sync($function_titles);
            DB::commit();
            
            return $workstation;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateWorkstation(Workstation $workstation, $values) 
    {
        try {
            DB::beginTransaction();
            $locations       = $values['locations'] ?? [];
            $function_titles = $values['function_titles'] ?? [];
            $workstation->locations()->sync($locations);
            $workstation->functionTitles()->sync($function_titles);
            unset($values['locations']);
            unset($values['function_titles']);

            $workstation->update($values);
            DB::commit();
            return $workstation;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }
}

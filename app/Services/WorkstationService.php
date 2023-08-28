<?php

namespace App\Services;

use App\Models\Workstation;
use App\Services\AddressService;
use App\Rules\AddressRule;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Rules\FunctionTitlesLinkedToSectorRule;
use App\Rules\FunctionTitlesLinkedToCompany;
use App\Rules\LocationLinkedToCompanyRule;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;

class WorkstationService extends BaseService
{
    protected $workstation;

    public function __construct(Workstation $workstation)
    {
        parent::__construct($workstation);
    }

    public function getAll(array $args = [])
    {
        // return $this->model
        //     ->when(isset($args['status']) && $args['status'] !== 'all', fn($q) => $q->where('status', $args['status']))
        //     ->when(isset($args['company_id']), fn($q) => $q->where('company', $args['company_id']))
        //     ->when(isset($args['with']), fn($q) => $q->with($args['with']))
        //     ->get();

        return $this->model
            ->when(isset($args['status']) && $args['status'] !== 'all', function (Builder $q) use ($args) {
                $q->where('status', $args['status']);
            })
            ->when(isset($args['company_id']), function (Builder $q) use ($args) {
                $q->where('company', $args['company_id']);
            })
            ->when(isset($args['with']), function (Builder $q) use ($args) {
                $q->with($args['with']);
            })
            ->when(isset($args['location_id']), function (Builder $q) use ($args) {
                $q->whereHas('locations', function (Builder $subQ) use ($args) {
                    $subQ->where('locations.id', $args['location_id']);
                });
            })
            ->get();
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
        $rules['locations_index'] = 'required|array';
        $rules['locations_index.*'] = 'integer';
        $rules['function_titles.*'][] = new FunctionTitlesLinkedToSectorRule(request()->input('sectors')); # to validate if the selected function title is linked to the sector selected
        return $rules;
    }

    private static function addWorkstationRules($rules)
    {
        $rules['locations'] = 'nullable|array';
        $rules['locations.*'] = [
            Rule::exists('locations', 'id'),
            new LocationLinkedToCompanyRule(request()->input('company'))
        ];

        $rules['company'] = [
            'required',
            'integer',
            Rule::exists('companies', 'id')
        ];
        $rules['function_titles.*'][] = new FunctionTitlesLinkedToCompany(request()->input('company')); # to validate if the selected function title is linked to the sector selected
        return $rules;
    }

    public function create($values)
    {
        try {
            DB::beginTransaction();

            $locations = $values['locations'] ?? [];
            $function_titles = $values['function_titles'] ?? [];

            unset($values['locations']);
            unset($values['locations_index']);

            $workstation = parent::create($values);

            // Attach locations and function titles
            $workstation->locations()->sync($locations);
            $workstation->functionTitles()->sync($function_titles);

            // Save the workstation
            $workstation->save();

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
            $locations = $values['locations'] ?? [];
            $function_titles = $values['function_titles'] ?? [];
            $workstation->locations()->sync($locations);
            $workstation->functionTitles()->sync($function_titles);
            unset($values['locations']);
            unset($values['function_titles']);
            unset($locations['company']);
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

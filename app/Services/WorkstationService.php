<?php

namespace App\Services;

use App\Models\Company\Workstation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Rules\FunctionTitlesLinkedToSectorRule;
use App\Rules\FunctionTitlesLinkedToCompany;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\Company\WorkstationRepository;
use App\Services\Company\LocationService;
use App\Services\EmployeeFunction\FunctionService;
use App\Rules\ExistsInMasterDatabaseRule;

class WorkstationService
{
    public function __construct(
        protected WorkstationRepository $workstationRepository,
        protected LocationService $locationService,
        protected FunctionService $functionService
    ) {
    }

    public function getWorkstationsOfCompany()
    {
        return $this->workstationRepository->getWorkstationsOfCompany();
    }

    public function getWorkstationById($workstation_id)
    {
        return $this->workstationRepository->getWorkstationById($workstation_id);
    }

    public function getWorkstationDetails($workstation_id)
    {
        $workstation_details = self::getWorkstationById($workstation_id);
        $function_titles = self::getWorkstationFunctions($workstation_details);
        $workstation_details = $workstation_details->toArray();
        $workstation_details['function_titles'] = $function_titles;
        return $workstation_details;
    }

    public function getWorkstationFunctions(Workstation $workstation)
    {
        $workstation = $workstation->toArray();
        $function_titles = collect($workstation['function_titles'])->pluck('function_title');
        return $function_titles;
    }

    public function getActiveWorkstationsOfCompany($workstation_id)
    {
        return $this->workstationRepository->getActiveWorkstationsOfCompany($workstation_id);
    }

    public function deleteWorkstation($workstation_id)
    {
        return $this->workstationRepository->deleteWorkstation($workstation_id);
    }

    public static function getWorkstationRules($for_company_creation = true)
    {
        $rules = [
            'workstation_name'  => 'required|string|max:255',
            'sequence_number'   => 'required|integer',
            'status'            => 'required|boolean',
            'function_titles'   => 'nullable|array',
            'function_titles.*' => [
                'bail',
                'integer',
                new ExistsInMasterDatabaseRule('function_titles'),
            ],
        ];

        if ($for_company_creation) { # company creation has multi step form with location and workstation include so this condition
            $rules = self::addCompanyCreationRules($rules);
        } else {
            $rules = self::addWorkstationCreationRules($rules);
        }

        return $rules;
    }

    private static function addCompanyCreationRules($rules)
    {
        $rules['locations_index'] = 'nullable|array';
        $rules['locations_index.*'] = 'integer';
        // $rules['function_titles.*'][] = new FunctionTitlesLinkedToSectorRule(request()->input('sectors')); # to validate if the selected function title is linked to the sector selected
        return $rules;
    }

    private static function addWorkstationCreationRules($rules)
    {
        $rules['locations'] = 'nullable|array';
        $rules['locations.*'] = [
            'integer',
            Rule::exists('locations', 'id'),
        ];
        $rules['function_titles.*'][] = new FunctionTitlesLinkedToCompany(request()->header('Company-Id')); # to validate if the selected function title is linked to the sector selected
        return $rules;
    }

    public function create($values)
    {
        try {
            DB::connection('tenant')->beginTransaction();

                $locations       = $values['locations'] ?? [];
                $function_titles = $values['function_titles'] ?? [];

                unset($values['locations'], $values['locations_index']);

            unset($values['locations'], $values['locations_index']);

            $workstation = $this->workstationRepository->createWorkstation($values);

            $workstation->locations()->sync($locations);
            // $workstation->functionTitles()->sync($function_titles);
            $workstation->linkFunctionTitles($function_titles);

            $workstation->save();

            DB::connection('tenant')->commit();

            return $workstation;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateWorkstation($workstation_id, $values)
    {
        try {
            DB::connection('tenant')->beginTransaction();

            $function_titles = $values['function_titles'] ?? [];
            $locations = $values['locations'] ?? [];

                $workstation = self::getWorkstationById($workstation_id);
                // $workstation->functionTitles()->sync($function_titles);
                $workstation->linkFunctionTitles($function_titles);

            $workstation->locations()->sync($locations);

            unset($values['function_titles']);
            $workstation->update($values);

            DB::connection('tenant')->commit();
            return $workstation;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getOptionsToCreate($company_id)
    {
        $function_titles = $this->functionService->getCompanyFunctionTitlesOptions($company_id);
        $locations = $this->locationService->getActiveLocations();

        return [
            'locations'       => $locations,
            'function_titles' => $this->functionService->getCompanyFunctionTitles($company_id),
        ];
    }

    public function getOptionsToEdit($workstation_id)
    {
        $workstation_details = $this->get($workstation_id, ['locations', 'functionTitles']);
        $options = $this->getOptionsToCreate($workstation_details->company);
        $options['details'] = $workstation_details;
        return $options;
    }
}

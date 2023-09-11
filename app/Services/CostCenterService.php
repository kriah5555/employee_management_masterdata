<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Services\BaseService;
use App\Models\CostCenter;
use App\Services\WorkstationService;
class CostCenterService extends BaseService
{
    protected $workstationService;

    public function __construct(CostCenter $costCenter)
    {
        parent::__construct($costCenter);
        $this->workstationService = app(WorkstationService::class);
        // $this->locationService    = app(LocationService::class);
    }

    public function create($values)
    {
        try {
            DB::beginTransaction();
                unset($values['company_id']);
                $costCenter   = $this->model->create($values);
                $workstations = $values['workstations'] ?? [];
                $costCenter->workstations()->sync($workstations);
            DB::commit();
            return $costCenter;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function update($costCenter, $values)
    {
        try {
            DB::beginTransaction();
                unset($values['company_id']);
                $costCenter   = $this->model->update($costCenter, $values);
                $workstations = $values['workstations'] ?? [];
                $costCenter->workstations()->sync($workstations);
            DB::commit();
            return $costCenter;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getOptionsToCreate($company_id)
    {
        $options = $this->workstationService->getOptionsToCreate($company_id);
        $options['workstations'] = [];
        unset($options['function_titles']);
        foreach ($options['locations'] as $option) {
            $workstations = $this->workstationService->locationService->get($option['value'], ['workstationsValues'])->toArray()['workstations_values'];
            $options['workstations'][$option['value']] = $workstations; // Use square brackets for assignment
        }
        return $options;
    }

    public function getOptionsToEdit($costCenterId)
    {
        $costCenter_details    = $this->get($costCenterId, ['workstationsValue', 'locationValue']);
        $options            = $this->getOptionsToCreate();
        $options['details'] = $costCenter_details;
        return $options;
    }
}
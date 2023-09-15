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

    public function getAll(array $args = [])
    {
        return $this->model
            ->when(isset($args['status']) && $args['status'] !== 'all', fn($q) => $q->where('status', $args['status']))
            ->when(isset($args['company_id']), function ($q) use ($args) {
                $q->whereHas('location', function ($locationSubQuery) use ($args) {
                    $locationSubQuery->where('company', $args['company_id']);
                });
            })
            ->when(isset($args['with']), fn($q) => $q->with($args['with']))
            ->get();
    }


    public function create($values)
    {
        try {
            DB::beginTransaction();
                unset($values['company_id']);
                $costCenter   = $this->model->create($values);
                $workstations = $values['workstations'] ?? [];
                $employees    = $values['employees'] ?? [];
                $costCenter->workstations()->sync($employees);
                $costCenter->employees()->sync($workstations);
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
                $workstations = $values['workstations'] ?? [];
                $employees    = $values['employees'] ?? [];
                $costCenter->workstations()->sync($workstations);
                $costCenter->employees()->sync($employees);
                $costCenter   = $costCenter->update($values);
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
        $costCenter_details = $this->get($costCenterId, ['workstationsValue','location']);
        $costCenter_details->locationValue;
        $options            = $this->getOptionsToCreate($costCenter_details->location->company);
        $options['details'] = $costCenter_details;
        return $options;
    }
}
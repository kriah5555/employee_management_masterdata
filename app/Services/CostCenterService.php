<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Services\BaseService;
use App\Models\Company\CostCenter;
use App\Services\WorkstationService;
use App\Services\Company\LocationService;
use App\Models\Company\Employee\EmployeeProfile;

class CostCenterService extends BaseService
{
    protected $workstationService;
    protected $employeeProfile;
    protected $locationService;

    public function __construct(CostCenter $costCenter)
    {
        parent::__construct($costCenter);
        $this->workstationService = app(WorkstationService::class);
        $this->employeeProfile    = app(EmployeeProfile::Class);
        $this->locationService    = app(LocationService::class);
    }

    public function getAll(array $args = [])
    {
        return $this->model
            ->when(isset($args['status']) && $args['status'] !== 'all', fn($q) => $q->where('status', $args['status']))
            ->when(isset($args['with']), fn($q) => $q->with($args['with']))
            ->get();
    }


    public function create($values)
    {
        try {
            DB::connection('tenant')->beginTransaction();
                $costCenter   = $this->model->create($values);
                $workstations = $values['workstations'] ?? [];
                $employees    = $values['employees'] ?? [];
                $costCenter->workstations()->sync($workstations);
                $costCenter->employees()->sync($employees);
            DB::connection('tenant')->commit();
            return $costCenter;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function update($costCenterId, $values)
    {
        try {
            DB::connection('tenant')->beginTransaction();
                $costCenter   = $this->model->find($costCenterId);
                $workstations = $values['workstations'] ?? [];
                $employees    = $values['employees'] ?? [];
                $costCenter->workstations()->sync($workstations);
                $costCenter->employees()->sync($employees);
                $costCenter   = $costCenter->update($values);
            DB::connection('tenant')->commit();
            return $costCenter;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getOptionsToCreate($company_id)
    {
        try {
            $options['locations'] = $this->locationService->getActiveLocations();
            foreach ($options['locations'] as $location) {
                $workstations = $this->locationService->getLocationWorkstations($location['id']);
                $options['workstations'][$location['id']] = $workstations;
            }
            $options['employees'] = [];
            return $options;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function deleteCostCenter($costCenterId)
    {
        try {
            $costCenter = $this->model->find($costCenterId);
            if ($costCenter) {
                $costCenter->delete();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
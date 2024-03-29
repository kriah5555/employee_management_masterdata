<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Company\CostCenter;
use App\Services\WorkstationService;
use App\Services\Company\LocationService;
use App\Services\Employee\EmployeeService;

class CostCenterService
{
    protected $workstationService;
    protected $employeeProfile;
    protected $locationService;

    public function __construct()
    {
        $this->workstationService = app(WorkstationService::class);
        $this->employeeService = app(EmployeeService::Class);
        $this->locationService = app(LocationService::class);
    }

    public function getCostCenters($id, $with = [])
    {
        if ($with) {
            return CostCenter::with($with)->findOrFail($id);
        } else {
            return CostCenter::findOrFail($id);
        }
    }

    public function getAll(array $args = [])
    {
        return CostCenter::when(isset($args['status']) && $args['status'] !== 'all', fn($q) => $q->where('status', $args['status']))
            ->when(isset($args['with']), fn($q) => $q->with($args['with']))
            ->get();
    }


    public function create($values)
    {
        try {
            DB::connection('tenant')->beginTransaction();
            $costCenter = CostCenter::create($values);
            $workstations = $values['workstations'] ?? [];
            $employees = $values['employees'] ?? [];
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
            $costCenter = CostCenter::find($costCenterId);
            $workstations = $values['workstations'] ?? [];
            $employees = $values['employees'] ?? [];
            $costCenter->workstations()->sync($workstations);
            $costCenter->employees()->sync($employees);
            $costCenter = $costCenter->update($values);
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
            $options['employees'] = $this->employeeService->getEmployeeOptions()['employees'];
            $employees = $this->employeeService->getEmployeeOptions()['employees'];
            $options['employees'] = [];
            foreach ($employees as $employee) {
                $options['employees'][] = [
                    'value' => $employee['employee_profile_id'],
                    'label' => $employee['full_name']
                ];
            }
            return $options;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function deleteCostCenter($costCenterId)
    {
        try {
            $costCenter = CostCenter::find($costCenterId);
            if ($costCenter) {
                $costCenter->delete();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}

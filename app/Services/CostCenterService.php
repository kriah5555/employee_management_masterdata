<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Services\BaseService;
use App\Models\Company\CostCenter;
use App\Services\WorkstationService;
use App\Models\Company\Employee\EmployeeProfile;
class CostCenterService extends BaseService
{
    protected $workstationService;
    protected $employeeProfile;

    public function __construct(CostCenter $costCenter)
    {
        parent::__construct($costCenter);
        $this->workstationService = app(WorkstationService::class);
        $this->employeeProfile    = app(EmployeeProfile::Class);
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
                $costCenter->workstations()->sync($workstations);
                $costCenter->employees()->sync($employees);
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

    function getEmployeeOptions($company_id) 
    {
        return $this->employeeProfile::where('status', true)
        ->where('company_id', $company_id)->select('id as value', DB::raw("CONCAT(first_name, ' ', last_name) as label"))
        ->get();
    }

    public function getOptionsToCreate($company_id)
    {
        try {
            $options = $this->workstationService->getOptionsToCreate($company_id);
            $options['workstations'] = [];
            unset($options['function_titles']);
            foreach ($options['locations'] as $option) {
                $workstations = $this->workstationService->locationService->get($option['value'], ['workstationsValues'])->toArray()['workstations_values'];
                $options['workstations'][$option['value']] = $workstations; // Use square brackets for assignment
            }
            $options['employees'] = $this->getEmployeeOptions($company_id);
            return $options;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getOptionsToEdit($costCenterId)
    {
        try {
            $costCenter_details = $this->get($costCenterId, ['workstationsValue','location', 'employeesValue']);
            $costCenter_details->locationValue;
            $options            = $this->getOptionsToCreate($costCenter_details->location->company);
            $options['details'] = $costCenter_details;
            return $options;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
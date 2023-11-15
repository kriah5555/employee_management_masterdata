<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Company\Location;
use App\Traits\UserAudit;
use App\Models\BaseModel;
use App\Models\Company\Employee\EmployeeProfile;

class CostCenter extends BaseModel
{
    protected $connection = 'tenant';
    
    use HasFactory, SoftDeletes, UserAudit;

    protected $table = 'cost_centers';

    protected static $sort = ['cost_center_number'];

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'cost_center_number',
        'location_id',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function workstations()
    {
        return $this->belongsToMany(Workstation::class, 'cost_center_workstations');
    }

    public function workstationsValue()
    {
        return $this->belongsToMany(Workstation::class, 'cost_center_workstations')
            ->where('workstations.status', true)
            ->select('workstations.id as value', 'workstations.workstation_name as label');
    }

    public function locationValue()
    {
        return $this->belongsTo(Location::class, 'location_id')
            ->where('status', true)
            ->select('id as value', 'location_name as label');
    }

    public function employees()
    {
        return $this->belongsToMany(EmployeeProfile::class, 'cost_center_employees', 'cost_centers_id', 'employee_profile_id');
    }

    public function employeesValue()
    {
        return $this->belongsToMany(EmployeeProfile::class, 'cost_center_employees', 'cost_centers_id', 'employee_profile_id')
            ->select('employee_profiles.id as value')
            ->selectRaw("CONCAT(employee_profiles.first_name, ' ', employee_profiles.last_name) as label");
    }
}

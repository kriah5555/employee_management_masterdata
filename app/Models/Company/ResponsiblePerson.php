<?php

namespace App\Models\Company;

use App\Models\BaseModel;
use App\Models\Company\Employee\EmployeeProfile;
use App\Traits\UserAudit;
use App\Models\Company\Workstation;

class ResponsiblePerson extends BaseModel
{
    use UserAudit;

    protected $connection = 'tenant';

    protected static $sort = ['location_name'];


    protected $table = "location_responsible_persons";

    protected $hidden = ['pivot'];

    protected $primaryKey = 'id'; // Corrected property name

    protected $fillable = [
        'location_id',
        'employee_profile_id',
    ];

    public function employeeProfile()
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id');
    }

    public function workstationsValues()
    {
        return $this->belongsToMany(Workstation::class, 'locations_to_workstations')
            ->select('workstations.id as value', 'workstations.workstation_name as label')
            ->where('workstations.status', true);
    }

    public function location_id()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

}

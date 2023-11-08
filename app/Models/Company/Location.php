<?php

namespace App\Models\Company;

use App\Models\BaseModel;
use App\Traits\UserAudit;
use App\Models\Company\CompanyAddress;
use App\Models\Company\Company;
use App\Models\Company\Workstation;

class Location extends BaseModel
{
    use UserAudit;

    protected $connection = 'tenant';

    protected static $sort = ['location_name'];
    
    protected $columnsToLog = [
        'location_name',
        'status',
        'address'
    ];


    protected $table = "locations";

    protected $hidden = ['pivot'];

    protected $primaryKey = 'id'; // Corrected property name

    protected $fillable = [
        'location_name',
        'status',
        'address'
    ];

    public function workstations()
    {
        return $this->belongsToMany(Workstation::class, 'locations_to_workstations');
    }

    public function workstationsValues()
    {
        return $this->belongsToMany(Workstation::class, 'locations_to_workstations')
            ->select('workstations.id as value', 'workstations.workstation_name as label')
            ->where('workstations.status', true);
    }

    public function address()
    {
        return $this->belongsTo(CompanyAddress::class, 'address');
    }
}
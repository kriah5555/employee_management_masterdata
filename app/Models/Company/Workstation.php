<?php

namespace App\Models\Company;

use App\Models\Company\Location;
use App\Models\Company\WorkstationToFunctions;
use App\Models\BaseModel;
use App\Models\Company\CostCenter;

class Workstation extends BaseModel
{
    protected static $sort = ['sequence_number'];

    protected $connection = 'tenant';

    protected $hidden = ['pivot'];

    protected $table = 'workstations';

    protected $primaryKey = 'id';

    protected $fillable = [
        'workstation_name',
        'sequence_number',
        'status',
    ];

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'locations_to_workstations', 'workstation_id', 'location_id');
    }

    public function functionTitles()
    {
        return $this->hasMany(WorkstationToFunctions::class, 'workstation_id')->with('functionTitle');
    }

    public function linkFunctionTitles($function_title_ids)
    {
        WorkstationToFunctions::where('workstation_id', $this->id)->delete();

        foreach ($function_title_ids as $function_title_id) {
            WorkstationToFunctions::create([
                'workstation_id'    => $this->id,
                'function_title_id' => $function_title_id,
            ]);
        }

    }
    public function costCenters()
    {
        return $this->belongsToMany(CostCenter::class, 'cost_center_workstations', 'workstation_id', 'cost_center_id');
    }
}

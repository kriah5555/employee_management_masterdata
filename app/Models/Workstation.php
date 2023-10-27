<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use App\Models\EmployeeFunction\FunctionTitle;
use App\Models\Location;
use App\Models\Company\Company;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workstation extends Model
{
    use HasFactory, SoftDeletes;

    protected $hidden = ['pivot'];
    
    protected $table = 'workstations';

    protected $primaryKey = 'id';

    protected $fillable = [
        'workstation_name',
        'sequence_number',
        'status',
        'company',
    ];

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'locations_to_workstations', 'workstation_id', 'location_id');
    }

    public function locationsValue()
    {
        return $this->belongsToMany(Location::class, 'locations_to_workstations', 'workstation_id', 'location_id')
        ->select('locations.id as value', 'locations.location_name as label');
    }

    public function functionTitles()
    {
        return $this->belongsToMany(FunctionTitle::class, 'workstation_to_funcitons', 'workstation_id', 'function_title_id');
    }

    public function functionTitlesValue()
    {
        return $this->belongsToMany(FunctionTitle::class, 'workstation_to_funcitons', 'workstation_id', 'function_title_id')
        ->select('function_titles.id as value', 'function_titles.name as label');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use App\Models\FunctionTitle;
use App\Models\Location;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workstation extends Model
{
    use HasFactory, SoftDeletes;

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
        return $this->belongsToMany(FunctionTitle::class, 'workstation_to_funcitons', 'workstation_id', 'function_title_id');
    }

}

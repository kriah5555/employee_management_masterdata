<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Location;
use App\Traits\UserAudit;
use App\Models\BaseModel;

class CostCenter extends BaseModel
{
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

    
}

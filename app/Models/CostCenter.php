<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Location;
use App\Traits\UserAudit;

class CostCenter extends Model
{
    use HasFactory, SoftDeletes, UserAudit;

    protected $table = 'cost_centers';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'cost_center_number',
        'location_id',
        'status'
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

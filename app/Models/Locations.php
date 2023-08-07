<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
    use HasFactory;

    protected $table = "locations";

    protected $primanrkey = 'id';

    protected $fillable = [
        'location_name', 
        'status',
        'company',
        'address'
    ];

    protected $with = ['workstations'];
    
    public function workstations()
    {
        return $this->belongsToMany(Workstation::class, 'locations_to_workstations');
    }
}

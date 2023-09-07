<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Address;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "locations";

    protected $primanrkey = 'id';

    protected $fillable = [
        'location_name', 
        'status',
        'company',
        'address'
    ];

    protected $with = ['address', 'workstations'];
    
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
        return $this->belongsTo(Address::class, 'address');
    }
}

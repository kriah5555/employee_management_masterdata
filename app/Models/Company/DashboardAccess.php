<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DashboardAccess extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    protected $fillable = [
        'unique_key',
        'type',
        'validity',
        'location_id',
        'status',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}

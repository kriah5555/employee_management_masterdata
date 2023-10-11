<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DashboardAccess extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unique_key',
        'type',
        'validity',
        'company_location_id',
        'status',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'company_location_id')
            ->where('type', 'locations');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_location_id')
            ->where('type', 'companies');
    }
}

<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DashboardAccess extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'master';
    protected $table = 'dashboard_access';
    protected $primary_key = 'id';

    protected $fillable = [
        'access_key',
        'type', # [1 => 'company', 2 => 'location']
        'company_id',
        'location_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}

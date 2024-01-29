<?php

namespace App\Models\Planning;

use App\Models\Planning\PlanningBase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Company\Employee\EmployeeProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeSwitchPlanning extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'request_from',
        'request_to',
        'plan_id',
        'request_status', # 1 => request pending # 2 => request approved
        'status',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function requestFrom()
    {
        return $this->belongsTo(EmployeeProfile::class, 'request_from');
    }

    public function requestTo()
    {
        return $this->belongsTo(EmployeeProfile::class, 'request_to');
    }

    public function plan()
    {
        return $this->belongsTo(PlanningBase::class, 'plan_id');
    }
}

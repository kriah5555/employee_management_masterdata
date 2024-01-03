<?php

namespace App\Models\Company\Employee;

use App\Models\BaseModel;
use App\Models\Planning\PlanningBase;
use App\Traits\UserAudit;
use App\Models\User\User;
use App\Models\User\UserBasicDetails;
use App\Models\Company\Absence\Absence;
use App\Models\Company\Employee\EmployeeCommute;
use App\Models\Company\Employee\EmployeeContract;
use App\Models\Company\Employee\EmployeeBenefits;

class EmployeeProfile extends BaseModel
{
    use UserAudit;

    protected $connection = 'tenant';

    protected $columnsToLog = [
        'user_id',
        'status',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_profiles';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;


    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'status',
    ];


    protected $appends = ['full_name'];

    public function getFullNameAttribute()
    {
        if ($this->user) {
            return $this->user->userBasicDetails->first_name . ' ' . $this->user->userBasicDetails->last_name;
        }

        return '';
    }

    public function user()
    {
        return $this->belongsTo(User::class)
            ->where('status', true);
    }

    public function employeeContracts()
    {
        return $this->hasMany(EmployeeContract::class);
    }
    public function employeeSocialSecretaryDetails()
    {
        return $this->hasOne(EmployeeSocialSecretaryDetails::class);
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function employeeBenefits()
    {
        return $this->hasOne(EmployeeBenefits::class);
    }

    public function employeeCommute()
    {
        return $this->hasMany(EmployeeCommute::class);
    }

    public function employeeBasicDetails()
    {
        return $this->belongsTo(UserBasicDetails::class, 'user_id', 'user_id');
    }

    public function getEmployeeProfileByUserId($userId)
    {
        return $this->where('user_id', $userId)
            ->where('status', true)
            ->get();
    }
    public function planningsForDate($date)
    {
        return $this->hasMany(PlanningBase::class)
            ->where('start_date_time', '>=', date('Y-m-d 00:00:00', strtotime($date)))
            ->where('start_date_time', '<=', date('Y-m-d 23:59:59', strtotime($date)))
            ->get();
    }
    public function availabilityForDate($date)
    {
        return $this->hasMany(PlanningBase::class)
            ->where('start_date_time', '>=', date('Y-m-d 00:00:00', strtotime($date)))
            ->where('start_date_time', '<=', date('Y-m-d 23:59:59', strtotime($date)))
            ->get();
    }
}

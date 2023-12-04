<?php

namespace App\Models\Company\Employee;

use App\Models\BaseModel;
use App\Traits\UserAudit;
use App\Models\User\User;
use App\Models\Company\Employee\EmployeeContract;
use App\Models\Company\Absence\Absence;
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
}

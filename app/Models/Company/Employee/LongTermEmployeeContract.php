<?php

namespace App\Models\Company\Employee;

use App\Models\BaseModel;
use App\Traits\UserAudit;
use App\Models\Company\Employee\EmployeeContract;

class LongTermEmployeeContract extends BaseModel
{
    use UserAudit;

    protected $connection = 'tenant';

    protected $columnsToLog = [
        'employee_contract_id',
        'sub_type',
        'schedule_type',
        'employment_type',
        'weekly_contract_hours',
        'work_days_per_week',
        'status',
        'dimona_period_id'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'long_term_employee_contract';

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
        'deleted_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_contract_id',
        'sub_type',
        'schedule_type',
        'employment_type',
        'weekly_contract_hours',
        'work_days_per_week',
        'status',
        'dimona_period_id'
    ];

    protected $appends = ['formatted_weekly_contract_hours'];


    public function getFormattedWeeklyContractHoursAttribute()
    {
        return formatToEuropeHours($this->weekly_contract_hours);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($longTermEmployeeContract) {
            $longTermEmployeeContract->weekly_contract_hours = formatToCommonHours($longTermEmployeeContract->weekly_contract_hours);
        });
    }

    public function employeeContract()
    {
        return $this->belongsTo(EmployeeContract::class);
    }
}

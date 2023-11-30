<?php

namespace App\Models;

use App\Models\Sector\SectorSalarySteps;
use App\Models\BaseModel;
use App\Traits\UserAudit;

class MinimumSalary extends BaseModel
{
    use UserAudit;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sector_minimum_salary';

    protected static $sort = ['category_number'];
    protected $columnsToLog = [
        'sector_salary_steps_id',
        'category_number',
        'hourly_minimum_salary',
        'monthly_minimum_salary'
    ];

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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sector_salary_steps_id',
        'category_number',
        'hourly_minimum_salary',
        'monthly_minimum_salary',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function sectorSalaryStep()
    {
        return $this->belongsTo(SectorSalarySteps::class);
    }

    public function setHourlyMinimumSalaryAttribute($value)
    {
        $this->attributes['hourly_minimum_salary'] = formatToNumber($value);
    }

    public function setMonthlyMinimumSalaryAttribute($value)
    {
        $this->attributes['monthly_minimum_salary'] = formatToNumber($value);
    }
}

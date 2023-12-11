<?php

namespace App\Models\Sector;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EmployeeType\EmployeeType;
use App\Models\Sector\SectorSalaryConfig;
use App\Models\Sector\SectorAgeSalary;
use App\Models\EmployeeFunction\FunctionCategory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\Sector\SectorSalarySteps;
use App\Models\BaseModel;
use App\Models\Company\Company;

class Sector extends BaseModel
{
    protected $connection = 'master';

    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sectors';

    protected $hidden = ['pivot'];

    protected static $sort = ['name'];

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
        'name',
        'paritair_committee',
        'description',
        'category',
        'night_hour_start_time',
        'night_hour_end_time',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function getNightHourStartTimeAttribute($value)
    {
        return $value ? date('H:i', strtotime($value)) : null;
    }

    public function getNightHourEndTimeAttribute($value)
    {
        return $value ? date('H:i', strtotime($value)) : null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['name', 'paritair_committee', 'description', 'category'])
        ->logOnlyDirty(['name', 'paritair_committee', 'description', 'category'])
        ->dontSubmitEmptyLogs();
    }

    public function employeeTypes()
    {
        return $this->belongsToMany(EmployeeType::class, 'sector_to_employee_types');
    }

    public function salaryConfig()
    {
        return $this->hasOne(SectorSalaryConfig::class);
    }

    public function sectorAgeSalary()
    {
        return $this->hasMany(SectorAgeSalary::class);
    }

    // protected static function booted()
    // {
    //     static::addGlobalScope('sort', function ($query) {
    //         $query->orderBy('name', 'asc');
    //     });
    // }

    public function isDeleted(): bool
    {
        return $this->deleted_at !== null;
    }

    public function isActive(): bool
    {
        return $this->status;
    }
    public function functionCategories()
    {
        return $this->hasMany(FunctionCategory::class)->where('status', true);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'sector_to_company');
    }

    public function employeeTypesValue()
    {
        return $this->belongsToMany(EmployeeType::class, 'sector_to_employee_types')->select(['employee_type_id as value', 'name as label']);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\Sector\SectorSalarySteps;

class MinimumSalary extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sector_minimum_salary';

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
        'sector_salary_step_id',
        'category_number',
        'salary',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['sector_salary_step_id', 'category_number', 'salary'])
        ->logOnlyDirty(['sector_salary_step_id', 'category_number', 'salary'])
        ->dontSubmitEmptyLogs();
    }

    protected static function booted()
    {
        static::addGlobalScope('sort', function ($query) {
            $query->orderBy('category_number', 'asc');
        });
    }
    public function sectorSalaryStep()
    {
        return $this->belongsTo(SectorSalarySteps::class);
    }
}

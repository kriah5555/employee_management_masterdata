<?php

namespace App\Models\Sector;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EmployeeType\EmployeeType;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Sector\Sector;
use App\Models\MinimumSalary;
use App\Models\BaseModel;
class SectorSalarySteps extends BaseModel
{
    protected $connection = 'master';

    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sector_salary_steps';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected static $sort = ['level'];

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
        'sector_salary_config_id',
        'level',
        'from',
        'to',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function salarySteps()
    {
        return $this->hasMany(SectorSalaryConfig::class);
    }
    
    public function minimumSalary()
    {
        return $this->hasMany(MinimumSalary::class);
    }

    // protected static function booted()
    // {
    //     static::addGlobalScope('sort', function ($query) {
    //         $query->orderBy('level', 'asc');
    //     });
    // }
}

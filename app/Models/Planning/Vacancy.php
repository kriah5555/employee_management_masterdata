<?php

namespace App\Models\Planning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Company\Location;
use App\Models\Company\Workstation;
use App\Models\EmployeeFunction\FunctionTitle;
use App\Models\Planning\{
    VacancyEmployeeTypes,
    VacancyFunctions,
    VacancyPostEmployees,
    VacancyRepeat
};

class Vacancy extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vacancies';

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
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
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
        'location_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'vacancy_count',
        'approval_type',
        'extra_info',
        'status',
        'repeat_type',
        'created_by',
        'updated_by',
        'workstation_id',
        'function_id'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function workstations()
    {
        return $this->belongsTo(Workstation::class, 'workstation_id');
    }

    public function functions()
    {
        return $this->belongsTo(FunctionTitle::class, 'function_id', 'id');
    }

    public function employeeTypes()
    {
        return $this->hasMany(VacancyEmployeeTypes::class);
    }

    public function vacancyPostEmployees()
    {
        return $this->hasMany(VacancyPostEmployees::class);
    }

    public function getVacancy()
    {
        return $this->with(
            [
                'location',
                'workstations',
                'functions',
                'employeeTypes.employeeType',
                'vacancyPostEmployees.employeeProfile.employeeBasicDetails'
            ]);
    }

}

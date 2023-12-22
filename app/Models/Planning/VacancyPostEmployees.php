<?php

namespace App\Models\Planning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Company\Employee\EmployeeProfile;

class VacancyPostEmployees extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vacancy_post_employee';

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
        'vacancy_id',
        'employee_profile_id',
        'request_status',
        'request_by',
        'responded_by',
        'request_at',
        'responded_at',
        'status',
        'plan_id',
        'vacancy_date'
    ];

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function employeeProfile()
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id', 'id');
    }

    public function plan()
    {
        return $this->belongsTo(PlanningBase::class, 'plan_id');
    }
}

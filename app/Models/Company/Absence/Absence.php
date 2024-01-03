<?php

namespace App\Models\Company\Absence;

use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use App\Models\Company\Absence\AbsenceDates;
use App\Models\Company\Absence\AbsenceHours;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Company\Employee\EmployeeProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absence extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    protected $table = 'absence';

    protected $fillable = [
        'absence_type', # [1 => Holiday, 2 -> Leave]
        'duration_type', #  [1 => 'First half',2 => 'Second half',3 => 'Multiple codes',4 => 'Multiple codes first half',5 => 'Multiple codes half',6 => 'First and second half', # will have two holiday codes, 7 => 'Multiple dates', # will have two holiday codes],
        'absence_status', # [1 => pending, 2 => approved, 3 => Rejected, 4 => Cancelled, 5 => approved but requested for cancellation]
        'employee_profile_id',
        'manager_id',
        'applied_date',
        'reason',
        'status',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($absence) {
            $absence->applied_date = now()->toDateString();
        });

        // Listen for the 'deleting' event
        static::deleting(function ($absence) {
            // Delete related AbsenceHours
            $absence->absenceHours()->delete();

            // Delete related AbsenceDates
            $absence->absenceDates()->delete();
        });
    }

    public function employee()
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id');
    }

    public function manager()
    {
        return $this->belongsTo(EmployeeProfile::class, 'manager_id');
    }

    public function absenceDates()
    {
        return $this->hasOne(AbsenceDates::class);
    }

    public function absenceHours()
    {
        return $this->hasMany(AbsenceHours::class);
    }

    public function getAppliedDateAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }
}

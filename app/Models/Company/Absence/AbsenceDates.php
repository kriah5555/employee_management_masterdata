<?php

namespace App\Models\Company\Absence;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Company\Absence\Absence;

class AbsenceDates extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    protected $table = 'absence_dates';

    protected $casts = [
        'dates' => 'json', // Cast the salary_data column to JSON
    ];

    protected $fillable = [
        'absence_id',
        'dates',
        'dates_type', # [1 =>  multiple dates, 2 => from and to date]
        'status',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = ['absence_dates_array'];

    public function absence()
    {
        return $this->belongsTo(Absence::class, 'absence_id');
    }

    public function getAbsenceDatesArrayAttribute() # will return the all dates which absence is applied
    {
        $customDates = json_decode($this->getAttribute('dates'), true);
        if ($this->dates_type == 2) { # will have from and to date
            return getDatesArray($customDates['from_date'], $customDates['to_date']);
        } else {
            return $customDates;
        }
    }
}

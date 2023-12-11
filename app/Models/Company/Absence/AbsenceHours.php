<?php

namespace App\Models\Company\Absence;

use App\Models\BaseModel;
use App\Services\DateService;
use App\Models\Holiday\HolidayCode;
use App\Models\Company\Absence\Absence;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsenceHours extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    protected $table = 'absence_hours';

    protected $fillable = [
        'holiday_code_id',
        'absence_id',
        'hours',
        'duration_type',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function absence()
    {
        return $this->belongsTo(Absence::class, 'absence_id');
    }

    public function holidayCode()
    {
        return $this->belongsTo(HolidayCode::class, 'holiday_code_id');
    }

    public function dateArrayFormat($datesArray)
    {
        $dates = ["from_date" => "01-11-2023", "to_date" => "05-11-2023"];
        if (isset($dates['from_date']) && isset($dates['to_date'])) {
            $datesArray = (new DateService())->getDatesArray($dates['from_date'], $dates['to_date']);
        } else {
            return $datesArray;
        }
    }
}

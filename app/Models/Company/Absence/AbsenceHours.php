<?php

namespace App\Models\Company\Absence;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}

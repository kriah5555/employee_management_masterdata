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
    
}

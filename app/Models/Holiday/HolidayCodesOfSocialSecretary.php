<?php

namespace App\Models\Holiday;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Holiday\HolidayCodes;
use App\Models\BaseModel;

class HolidayCodesOfSocialSecretary extends BaseModel
{
    protected $connection = 'master';

    use HasFactory;

    protected $table = 'holiday_codes_of_social_secretary';

    protected $fillable = [
        'holiday_code_id',
        'social_secretary_id',
        'social_secretary_code',
        'status',
        'created_by',
        'updated_by'
    ];

    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function holidayCode()
    {
        return $this->belongsTo(HolidayCode::class, 'holiday_code_id');
    }
}
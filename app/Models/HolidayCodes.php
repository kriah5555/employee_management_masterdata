<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HolidayCodes extends Model
{
    use HasFactory;

    protected $table = 'holiday_codes';

    protected $fillable = [
        'holiday_code_name',
        'internal_code',
        'description',
        'holiday_type',
        'count_type',
        'icon_type',
        'consider_plan_hours_in_week_hours',
        'employee_category',
        'contract_type',
        'carry_forword'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];
}

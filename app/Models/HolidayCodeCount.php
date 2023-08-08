<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HolidayCodes;
use Illuminate\Database\Eloquent\SoftDeletes;

class HolidayCodeCount extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'holiday_code_count';
    protected $fillable = ['count', 'holiday_code_id'];

    // Define the relationship with the 'companies' table
    
    
    public function holidayCodes()
    {
        return $this->belongsTo(HolidayCodes::class, 'holiday_code_id');
    }
}
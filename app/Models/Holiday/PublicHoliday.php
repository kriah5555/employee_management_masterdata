<?php

namespace App\Models\Holiday;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
use App\Models\Company\Company;
use Illuminate\Support\Carbon;

class PublicHoliday extends BaseModel
{

    protected $connection = 'master';

    protected static $sort = ['name', 'date'];

    // protected $dateFormat = 'd-m-Y'; // Use 'd-m-Y' format for the date attribute

    protected $table = 'public_holidays';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'date',
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

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($publicHoliday) {
            $publicHoliday->date = date('Y-m-d', strtotime($publicHoliday->date));
        });
    }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_public_holidays', 'public_holiday_id', 'company_id');
    }

    public function companiesValue()
    {
        return $this->belongsToMany(Company::class, 'company_public_holidays', 'public_holiday_id', 'company_id');
    }
}

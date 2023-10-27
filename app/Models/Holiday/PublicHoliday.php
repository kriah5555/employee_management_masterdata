<?php

namespace App\Models\Holiday;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
use App\Models\Company\Company;
class PublicHoliday extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected static $sort = ['name', 'date'];

    protected $dateFormat = 'd-m-Y'; // Use 'd-m-Y' format for the date attribute

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

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_public_holidays', 'public_holiday_id', 'company_id');
    }

    public function companiesValue()
    {
        return $this->belongsToMany(Company::class, 'company_public_holidays', 'public_holiday_id', 'company_id')->select(['companies.id as value', 'companies.company_name as label']);
    }
}

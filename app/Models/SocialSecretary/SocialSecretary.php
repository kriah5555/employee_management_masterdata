<?php

namespace App\Models\SocialSecretary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use App\Models\Holiday\HolidayCodesOfSocialSecretary;
use App\Models\Company\Company;

class SocialSecretary extends BaseModel
{
    protected $connection = 'master';

    use HasFactory, SoftDeletes;

    protected $table = 'social_secretaries';

    protected $fillable = [
        'name',
        'daily_registration',
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

    public function SocialSecretaryHolidayCodes() // Rename the function
    {
        return $this->hasMany(HolidayCodesOfSocialSecretary::class, 'social_secretary_id');
    }
}

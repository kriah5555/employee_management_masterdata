<?php

namespace App\Models\Company;

use App\Models\Sector\Sector;
use App\Models\Files;
use App\Models\Address;
use App\Models\Company\Location;
use App\Models\Holiday\HolidayCode;
use App\Models\Interim\InterimAgency;
use App\Models\Company\CompanySocialSecretaryDetails;
use App\Models\BaseModel;
use App\Traits\UserAudit;
use App\Models\Tenant;

class Company extends BaseModel
{
    use UserAudit;
    protected static $sort = ['company_name'];
    protected $columnsToLog = [
        'company_name',
        'address',
        'employer_id',
        'sender_number',
        'rsz_number',
        'social_secretary_id',
        'interim_agency_id',
        'oauth_key',
        'username',
        'email',
        'phone',
        'logo',
        'status',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $connection = 'master';

    protected $table = 'companies';

    protected $hidden = ['pivot'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'company_name',
        'address',
        'employer_id',
        'sender_number',
        'rsz_number',
        'oauth_key',
        'username',
        'email',
        'phone',
        'logo',
        'status',
        'created_by',
        'updated_by'
    ];

    public function address()
    {
        return $this->belongsTo(Address::class, 'address');
    }

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function sectors()
    {
        return $this->belongsToMany(Sector::class, 'sector_to_company');
    }

    public function sectorsValue()
    {
        return $this->belongsToMany(Sector::class, 'sector_to_company', 'company_id', 'sector_id')
            ->select('sectors.id as value', 'sectors.name as label');
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'company_to_locations');
    }

    public function holidayCodes()
    {
        return $this->belongsToMany(HolidayCode::class, 'company_holiday_codes', 'company_id', 'holiday_code_id')
            ->where('holiday_codes.status', true);
    }

    public function companySocialSecretaryDetails()
    {
        return $this->hasOne(CompanySocialSecretaryDetails::class);
    }

    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }

    public function interimAgencies()
    {
        return $this->belongsToMany(InterimAgency::class, 'company_interim_agency', 'company_id', 'interim_agency_id');
    }

    public function logoFile()
    {
        return $this->belongsTo(Files::class, 'logo'); # php artisan storage:link  -> need to create symbolic link between storage and public folder
    }

    # to link all the holiday codes to company when any new company are created
    protected static function boot()
    {
        parent::boot();

        static::created(function ($company) {
            $holiday_codes = HolidayCode::all()->pluck('id');
            $company->holidayCodes()->sync($holiday_codes);
        });
    }

    # can call this externally and link the holiday codes
    public function linkHolidayCodes()
    {
        $holiday_codes = HolidayCode::all()->pluck('id');
        $this->holidayCodes()->sync($holiday_codes);
    }

    // public function toArray()
    // {
    //     $array = parent::toArray();
    //     // $array['logo'] = $this->logoFile; // Append the logoFile relationship data
    //     return $array;
    // }


}
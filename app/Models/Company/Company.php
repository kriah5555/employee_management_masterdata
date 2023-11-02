<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sector\Sector;
use App\Models\Files;
use App\Models\Address;
use App\Models\Company\Location;
use App\Models\Holiday\HolidayCode;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\SocialSecretary\SocialSecretary;
use App\Models\Interim\InterimAgency;
use App\Models\Company\CompanySocialSecretaryDetails;


class Company extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
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
        'social_secretary_id',
        'interim_agency_id',
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

    public function socialSecretaryValue()
    {
        if ($this->socialSecretary) {
            return [
                'level' => $this->socialSecretary->id,
                'value' => $this->socialSecretary->name,
            ];
        } else {
            return null;
        }
    }

    public function interimAgency()
    {
        return $this->belongsTo(InterimAgency::class, 'interim_agency_id');
    }

    public function interimAgencyValue()
    {
        if ($this->interimAgency) {
            return [
                'level' => $this->interimAgency->id,
                'value' => $this->interimAgency->name,
            ];
        } else {
            return null;
        }
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

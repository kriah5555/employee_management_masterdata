<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sector\Sector;
use App\Models\Files;
use App\Models\Address;
use App\Models\Location;
use App\Models\Holiday\HolidayCodes;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'social_secretary_number',
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

    // protected $with = ['sectors', 'address'];

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
        return $this->belongsToMany(HolidayCodes::class, 'company_holiday_codes', 'company_id', 'holiday_code_id')
                    ->where('holiday_codes.status', true);
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
            // Find all active holiday codes (status = 1)
            // $activeHolidayCodes = HolidayCodes::where('status', 1)->pluck('id');

            $holiday_codes = HolidayCodes::all()->pluck('id');
            
            // Sync the active holiday codes with the company
            $company->holidayCodes()->sync($holiday_codes);
        });
    }

    # can call this externally and link the holiday codes
    public function linkHolidayCodes()
    {
        $holiday_codes = HolidayCodes::all()->pluck('id');
        $this->holidayCodes()->sync($holiday_codes);
    }

    // public function toArray()
    // {
    //     $array = parent::toArray();
    //     // $array['logo'] = $this->logoFile; // Append the logoFile relationship data
    //     return $array;
    // }


}

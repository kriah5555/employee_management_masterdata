<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sector;
use App\Models\Files;
use App\Models\Address;
use App\Models\Locations;
class Company extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';

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
        // 'logo', 
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

    protected $with = ['sectors','address'];
    public function sectors()
    {
        return $this->belongsToMany(Sector::class, 'sector_to_company');
    }

    public function locations()
    {
        return $this->belongsToMany(Locations::class, 'company_to_locations');
    }

    // public function logoFile()
    // {
    //     return $this->belongsTo(Files::class, 'logo');
    // }
    
    public function toArray()
    {
        $array = parent::toArray();
        // $array['logo'] = $this->logoFile; // Append the logoFile relationship data
        return $array;
    }


}
    
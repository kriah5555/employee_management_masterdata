<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sector;
use App\Models\Files;
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
        'street',
        'postal_code',
        'city',
        'country',
        'customer_type',
        'status',
        'logo',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $with = ['sectors'];
    public function sectors()
    {
        return $this->belongsToMany(Sector::class, 'sector_to_company');
    }

    // Define the one-to-one relationship for the logo attribute
    public function logoFile()
    {
        return $this->belongsTo(Files::class, 'logo');
    }
    
    // Override toArray method to include the logoFile relationship in the JSON response
    public function toArray()
    {
        $array = parent::toArray();
        $array['logo'] = $this->logoFile; // Append the logoFile relationship data
        return $array;
    }
}
    
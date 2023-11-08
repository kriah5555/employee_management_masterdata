<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Address;

class CompanyAddress extends Model
{
    protected $connection = 'tenant';

    use HasFactory, SoftDeletes;

    protected $table = 'address'; 

    protected $primaryKey = 'id';

    protected $fillable = [
        "street_house_no",
        "postal_code",
        "city",
        "country",
        "status",
        "latitude",
        "longitude"
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

}

<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

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

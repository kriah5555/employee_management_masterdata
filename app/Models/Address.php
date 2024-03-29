<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    protected $connection = 'master';

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

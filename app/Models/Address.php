<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $table = 'address';

    protected $primaryKey = 'id';

    protected $fillable = [
        "street",
        "house_no",
        "postal_code",
        "city",
        "country",
        "status"
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];
}

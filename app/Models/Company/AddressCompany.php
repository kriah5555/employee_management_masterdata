<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Address;

class AddressCompany extends Address
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

}

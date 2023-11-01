<?php

namespace App\Models\Interim;

use App\Models\Company\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
use App\Models\Address;

class InterimAgency extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected static $sort = ['name'];

    protected $fillable = [
        'name',
        'email',
        'employer_id',
        'sender_number',
        'username',
        'joint_commissioner_number',
        'rsz_number',
        'status',
        'address',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function address()
    {
        return $this->belongsTo(Address::class, 'address');
    }
    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }
}

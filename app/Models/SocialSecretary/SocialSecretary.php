<?php

namespace App\Models\SocialSecretary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class SocialSecretary extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'social_secretaries';

    protected $fillable = [
        'name',
        'status',
        'created_by',
        'updated_by'
    ];  

    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}

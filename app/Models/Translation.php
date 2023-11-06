<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $connection = 'master';

    use HasFactory;

    protected $table = 'translations';

    protected $primaryKey = 'id';
    
    protected $fillable = ['locale', 'group', 'key', 'value'];

}

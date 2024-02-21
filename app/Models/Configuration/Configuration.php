<?php

namespace App\Models\Configuration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class Configuration extends BaseModel
{
    use HasFactory;

    protected $connection = 'master';

    protected $table = 'configurations';

    protected $primaryKey = 'id';

    protected $hidden = ['pivot'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    protected $fillable = [
        'key',
        'value',
    ];
}

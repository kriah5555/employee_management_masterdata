<?php

namespace App\Models\Dimona;

use App\Models\BaseModel;
use App\Traits\UserAudit;

class DimonaErrorCode extends BaseModel
{
    use UserAudit;

    protected $connection = 'master';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dimona_error_codes';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'error_code',
        'description',
    ];
    protected static $sort = ['error_code'];

    protected $columnsToLog = ['error_code', 'description'];
}

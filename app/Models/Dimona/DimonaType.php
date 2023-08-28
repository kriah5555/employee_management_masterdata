<?php

namespace App\Models\Dimona;

use App\Models\BaseModel;

class DimonaType extends BaseModel
{

    protected static $sort = ['name'];

    protected $columnsToLog = ['name', 'dimona_type_key', 'status'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dimona_types';

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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'dimona_type_key',
        'status',
        'created_by',
        'updated_by'
    ];

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
}
<?php

namespace App\Models\EmployeeFunction;

use App\Models\Sector\Sector;
use App\Models\BaseModel;
use App\Models\EmployeeFunction\FunctionTitle;

class FunctionCategory extends BaseModel
{
    protected static $sort = ['name'];
    protected $columnsToLog = ['name', 'description', 'category', 'sector_id', 'status'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'function_category';

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

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sector_id',
        'name',
        'description',
        'category',
        'status',
        'created_by',
        'updated_by',
    ];

    public function sector()
    {
        return $this->belongsTo(Sector::class)->withTrashed();
    }
    
    public function sectorValue()
    {
        return $this->belongsTo(Sector::class, 'sector_id')
            ->select('id as value', 'name as label')
            ->where('status', true);
    }

    public function functionTitles()
    {
        return $this->hasMany(FunctionTitle::class)->where('status', true);
    }
}
<?php

namespace App\Models\EmployeeFunction;

use App\Models\EmployeeFunction\FunctionCategory;
use App\Models\BaseModel;

class FunctionTitle extends BaseModel
{
    protected static $sort = ['name'];
    protected $columnsToLog = ['name', 'description', 'function_code', 'function_category_id', 'status'];
    protected $connection = 'master';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'function_titles';

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
        'name',
        'function_code',
        'description',
        'status',
        'function_category_id',
        'created_by',
        'updated_by',
    ];

    public function functionCategory()
    {
        return $this->belongsTo(FunctionCategory::class)->withTrashed();
    }

    public function functionCategoryValue()
    {
        return $this->belongsTo(FunctionCategory::class, 'function_category_id')
            ->select('id as value', 'name as label')
            ->where('status', true);
    }
}

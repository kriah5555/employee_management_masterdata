<?php

namespace App\Models\EmployeeType;

use App\Models\BaseModel;

class EmployeeTypeCategory extends BaseModel
{
    protected static $sort = ['sort_order', 'name'];

    protected $columnsToLog = ['sort_order', 'name', 'description', 'status', 'sub_category_types', 'schedule_types', 'employment_types'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_type_categories';

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
        'description',
        'status',
        'sort_order',
        'sub_category_types',
        'schedule_types',
        'employement_type',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'created_at'
    ];

    public function employeeTypes()
    {
        return $this->hasMany(EmployeeType::class)
            ->where('status', true);
    }
}
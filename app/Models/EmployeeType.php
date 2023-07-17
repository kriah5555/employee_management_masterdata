<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeType extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_types';

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
        'key',
        'description',
        'status'
    ];

    // protected $with = ['nullColumns'];

    // public function nullColumns()
    // {
    //     return $this->withDefault();
    // }

    // public function getEmployeeTypeName()
    // {
    //     return 'Some value';
    // }

    // public function setEmployeeTypeName($value)
    // {
    //     $this->attributes['name'] = strtolower($value);
    // }
}

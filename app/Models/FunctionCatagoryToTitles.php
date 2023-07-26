<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FunctionCatagoryToTitles extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'function_catagory_to_titles';

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
        'id',
        'function_title_id',
        'function_catagory_id',
        'status',
        'created_by',
        'updated_by',
    ];

    public function createFunctionCatagory()
    {
        
    }

    public function EditFunctionCatagory()
    {

    }

    public function deleteFunctionCatagory()
    {

    }

    protected $casts = [
        'status' => 'boolean',
    ];

    public function getStatusAttribute($value)
    {
        return $value ? 'active' : 'inactive';
    }
}

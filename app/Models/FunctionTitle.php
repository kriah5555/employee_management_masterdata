<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FunctionTitle extends Model
{
    use HasFactory;

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
    protected $primaryKey = 'function_title_id';

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
        'function_title_id',
        'function_title_name',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    public function createFunctionTitle()
    {
        
    }

    public function updateFunctionTitle()
    {

    }

    public function deleteFunctionTitle()
    {

    }

    public function archiveFunctionTitle()
    {

    }
}

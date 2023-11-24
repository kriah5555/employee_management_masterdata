<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\EmployeeFunction\FunctionTitle;

class WorkstationToFunctions extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    use HasFactory, SoftDeletes;

    protected $table = 'workstation_to_functions';

    protected $primaryKey = 'workstation_id';

    protected $fillable = [
        'workstation_id',
        'function_title_id',
        'created_by',
        'updated_by',
    ];

    // public function workstation()
    // {
    //     return $this->belongsTo(Workstation::class, 'workstation_id');
    // }

    public function functionTitle()
    {
        return $this->belongsTo(FunctionTitle::class, 'function_title_id');
    }

}

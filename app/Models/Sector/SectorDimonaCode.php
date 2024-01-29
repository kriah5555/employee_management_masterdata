<?php

namespace App\Models\Sector;

use App\Models\EmployeeType\EmployeeType;
use App\Models\BaseModel;
use App\Models\Sector\Sector;

class SectorDimonaCode extends BaseModel
{
    protected $connection = 'master';

    protected $table = 'sector_dimona_codes';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'sector_id',
        'employee_type_id',
        'dimona_code'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sector_id');
    }

    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class, 'employee_type_id');
    }
}

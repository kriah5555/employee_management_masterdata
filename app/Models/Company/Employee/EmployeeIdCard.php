<?php

namespace App\Models\Company\Employee;

use App\Models\BaseModel;
use App\Models\Planning\Files;
use Illuminate\Support\Facades\Storage;
use App\Models\Company\Employee\EmployeeProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeIdCard extends BaseModel
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $table = 'employee_id_cards';

    protected $fillable = [
        'employee_profile_id',
        'file_id',
        'type', # [1 => Id front, 2 => Id back]
        'status',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $appends = ['file_url'];

    public function getFileUrlAttribute()
    {
        return  asset('storage/'. $this->files->file_path);
    }

    public function files()
    {
        return $this->belongsTo(Files::class, 'file_id');
    }

    public function employeeProfile()
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id');
    }
}

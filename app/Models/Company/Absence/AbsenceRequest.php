<?php

namespace App\Models\Company\Absence;

use App\Models\BaseModel;
use App\Models\Planning\Files;
use App\Models\Planning\PlanningBase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsenceRequest extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $table = 'absence_requests';

    protected $fillable = [
        'plan_id',
        'reason',
        'status',
    ];

    public function files()
    {
        return $this->belongsToMany(Files::class, 'absence_requests_files', 'absence_request_id', 'file_id');
    }
}

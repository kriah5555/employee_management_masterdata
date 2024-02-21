<?php

namespace App\Models\Company\Absence;

use App\Models\BaseModel;
use App\Models\Planning\Files;
use App\Models\Planning\PlanningBase;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsenceRequest extends BaseModel
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

    public function plan()
    {
        return $this->belongsTo(PlanningBase::class, 'plan_id');
    }

    protected $appends = ['file_urls'];

    public function getFileUrlsAttribute()
    {
        $urls = [];
        if ($this->files) {
            foreach ($this->files as $file) {
                $urls[] = secure_asset('storage/tenants/'.$file->file_path);
            }
        }
        return $urls;
    }
}

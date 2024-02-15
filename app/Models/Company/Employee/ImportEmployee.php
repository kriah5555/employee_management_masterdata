<?php

namespace App\Models\Company\Employee;

use App\Models\BaseModel;
use App\Models\Planning\Files;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ImportEmployee extends BaseModel
{

    protected $connection = 'tenant';

    protected $table = 'import_employee';

    protected $fillable = [
        'file_id',
        'import_status', # 1 => pending 2 => completed
        'imported_date', 
        'feedback_file_id',
        'status',
        'created_by',
        'updated_by',
    ];
    public function file()
    {
        return $this->belongsTo(Files::class, 'file_id'); # php artisan storage:link  -> need to create symbolic link between storage and public folder
    }

    public function feedbackFile()
    {
        return $this->belongsTo(Files::class, 'feedback_file_id'); # php artisan storage:link  -> need to create symbolic link between storage and public folder
    }

    protected $appends = ['file_url', 'feedback_file_url'];

    public function getFileUrlAttribute()
    {
        return secure_asset('storage/tenants/'.$this->file->file_path);
    }

    public function getFeedbackFileUrlAttribute()
    {
        return $this->feedbackFile ? secure_asset($this->feedbackFile->file_path) : null;
    }
}

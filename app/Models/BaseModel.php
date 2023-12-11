<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\DatabaseFieldDateFormat;

class BaseModel extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, DatabaseFieldDateFormat;

    protected static $sort = [];

    protected $columnsToLog = [];

    protected $hidden = ['pivot'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->columnsToLog)
            ->logOnlyDirty($this->columnsToLog)
            ->dontSubmitEmptyLogs();
    }

    public function isDeleted(): bool
    {
        return $this->deleted_at !== null;
    }

    public function isActive(): bool
    {
        return $this->status;
    }

    protected static function booted()
    {
        static::addGlobalScope('sort', function ($query) {
            foreach (static::$sort as $name) {
                $query->orderBy($name, 'asc');
            }
        });
    }
    protected static function allActive()
    {
        return parent::where('status', true)->get();
    }

    protected $apiValues = [];

    public function toApiReponseFormat()
    {
        $data = $this->toArray();
        $values = [];
        foreach ($this->apiValues as $key) {
            if (array_key_exists($key, $data)) {
                $values[$key] = $data[$key];
            }
        }
        return $values;
    }
    protected $dates = [];
}

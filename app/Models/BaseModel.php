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
    use HasFactory, SoftDeletes, LogsActivity;

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

    public function toApiResponseFormat()
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
    protected $dateFields = [];
    protected $dateTimeFields = [];

    protected static function boot()
    {
        parent::boot();

        // Registering the creating event
        static::creating(function ($model) {
            $model->formatDateFields();
        });

        // Registering the updating event
        static::updating(function ($model) {
            $model->formatDateFields();
        });
    }

    protected function formatDateFields()
    {
        foreach ($this->dateFields as $field) {
            if ($this->$field) {
                $this->$field = Carbon::createFromFormat('Y-m-d', $this->$field)->format('Y-m-d');
            }
        }
        foreach ($this->dateTimeFields as $field) {
            if ($this->$field) {
                $this->$field = Carbon::createFromFormat('Y-m-d H:i:s', $this->$field)->format('Y-m-d');
            }
        }
    }
}

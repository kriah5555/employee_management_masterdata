<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Sector\SectorSalaryConfig;

class MinimumSalaryBackup extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sector_minimum_salary_backup';

    protected $fillable = [
        'sector_salary_config_id',
        'category',
        'revert_count',
        'salary_type',
        'salary_data',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'salary_data' => 'json', // Cast the salary_data column to JSON
    ];

    public function SectorSalaryConfig()
    {
        return $this->belongsTo(SectorSalaryConfig::class, 'sector_salary_config_id');
    }

    protected static function booted()
    {
        static::creating(function ($backup) {
            // Increment the revert_count based on the sector_salary_config_id and insertion order
            $latestBackup = static::where('sector_salary_config_id', $backup->sector_salary_config_id)
                ->orderBy('revert_count', 'desc')
                ->first();

            if ($latestBackup) {
                $backup->revert_count = $latestBackup->revert_count + 1;
            } else {
                $backup->revert_count = 1;
            }
        });
    }
}

<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company\Company;

class CompanyDatabase extends Model
{
    use HasFactory;

    protected $table = 'company_database';

    protected $primaryKey = 'id';

    protected $fillable = [
        'database_name',
        'status',
        'company_id',
    ];

    public $timestamps = true;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}

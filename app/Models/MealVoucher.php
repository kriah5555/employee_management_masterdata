<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Traits\UserAudit;

class MealVoucher extends BaseModel
{
    protected $connection = 'master';

    use UserAudit;

    protected static $sort = ['sort_order', 'name'];
    
    protected $columnsToLog = [
        'sort_order',
        'name',
        'status',
        'amount',
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'meal_vouchers';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;


    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sort_order',
        'name',
        'status',
        'amount',
    ];

    protected $appends = ['amount_formatted'];

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = formatToNumber($value);
    }

    public function getAmountFormattedAttribute()
    {
        return formatToEuropeCurrency($this->amount);
    }
}

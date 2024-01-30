<?php

namespace App\Models\Dimona;

use App\Models\BaseModel;
use App\Models\Planning\TimeRegistration;
use App\Models\Dimona\Dimona;

class DimonaDeclaration extends BaseModel
{

    protected $connection = 'tenant';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dimona_declarations';

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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unique_id',
        'dimona_id',
        'type',
        'dimona_declartion_status',
    ];
    public function dimonaDeclarationErrors()
    {
        return $this->hasMany(DimonaDeclarationError::class, 'dimona_declaration_id');
    }
    public function timeRegistrations()
    {
        return $this->belongsToMany(TimeRegistration::class, 'dimona_declaration_time_registration');
    }

    public function dimona()
    {
        return $this->belongsTo(Dimona::class, 'dimona_id');
    }
}

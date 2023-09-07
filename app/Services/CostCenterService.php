<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Services\BaseService;
use App\Models\CostCenter;

class CostCenterService extends BaseService
{
    protected $sectorService;

    public function __construct(CostCenter $costCenter)
    {
        parent::__construct($costCenter);
    }
}
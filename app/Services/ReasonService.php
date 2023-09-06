<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Reason;

class ReasonService extends BaseService
{
    public function __construct(Reason $reason)
    {
        parent::__construct($reason);
    }
}

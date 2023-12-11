<?php

namespace App\Services\Planning;

use App\Models\Planning\{PlanningBase};
use App\Interfaces\Planning\PlanningCreateEditInterface;

class PlanningCreateEditService implements PlanningCreateEditInterface
{
    public function __construct(protected PlanningBase $planningBase) {

    }
}

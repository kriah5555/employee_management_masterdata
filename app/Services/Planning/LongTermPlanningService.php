<?php

namespace App\Services\Planning;

use App\Services\Planning\PlanningService;
use App\Models\Planning\LongTermPlanning;
use App\Models\Planning\LongTermPlanningTimings;


class LongTermPlanningService
{

    public function __construct(
        protected PlanningService $planningService,
    ) {

    }

}

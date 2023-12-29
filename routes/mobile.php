<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Planning\
{
    PlanningMobileController,
};

Route::post('get-employee-week-planning', [PlanningMobileController::class, 'getWeeklyPlanning']);


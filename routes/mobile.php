<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Planning\
{
    PlanningMobileController,
};

Route::post('get-employee-week-planning', [PlanningMobileController::class, 'getEmployeeWeeklyPlanning']);

Route::post('get-employee-dates-planning', [PlanningMobileController::class, 'getEmployeeDatesPlanning']);

Route::post('get-employee-worked-hours', [PlanningMobileController::class, 'getEmployeeWorkedHours']);


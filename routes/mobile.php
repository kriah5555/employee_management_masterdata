<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Planning\
{
    PlanningMobileController,
    PlanningStartStopController,
};
use App\Http\Middleware\InitializeTenancy;
use App\Http\Middleware\SetActiveUser;

Route::middleware([SetActiveUser::class])->group(function () {

    Route::get('get-employee-planning-status', [PlanningMobileController::class, 'getEmployeePlanningStatus']);

    Route::post('get-employee-week-planning', [PlanningMobileController::class, 'getEmployeeWeeklyPlanning']);

    Route::post('get-employee-dates-planning', [PlanningMobileController::class, 'getEmployeeDatesPlanning']);


    Route::controller(PlanningStartStopController::class)->group(function () {

        Route::post('start-plan-by-employee', 'startPlanByEmployee');

        Route::post('stop-plan-by-employee', 'stopPlanByEmployee');

    });

    Route::middleware([InitializeTenancy::class])->group(function () {
    });

});

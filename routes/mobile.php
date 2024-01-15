<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Planning\
{
    PlanningMobileController,
    PlanningController,
    PlanningStartStopController,
};
use App\Http\Controllers\{
    Contract\ContractMobileController
};
use App\Http\Middleware\SetActiveUser;
use App\Http\Middleware\InitializeTenancy;


Route::middleware([SetActiveUser::class])->group(function () {

    Route::controller(PlanningMobileController::class)->group(function () {

        Route::get('get-employee-planning-status', 'getEmployeePlanningStatus');

        Route::post('get-employee-week-planning', 'getEmployeeWeeklyPlanning');

        Route::post('get-employee-dates-planning', 'getEmployeeDatesPlanning');

        Route::post('get-employee-worked-hours', 'getEmployeeWorkedHours');

        Route::post('sign-plan-contract-employee', 'signPlanningContract');

    });

    Route::controller(ContractMobileController::class)->group(function () {

        Route::post('employee-sign-plan-contract', 'employeeSignPlanContract');

        Route::get('get-employee-contracts', 'index');
    });

    Route::controller(PlanningStartStopController::class)->group(function () {

        Route::post('start-plan-by-employee', 'startPlanByEmployee');
        
        Route::post('stop-plan-by-employee', 'stopPlanByEmployee');

        Route::post('stop-forgot-plan-by-employee', 'stopForgotPlanByEmployee');
        
    });

    Route::middleware([InitializeTenancy::class])->group(function () {

        Route::get('get-employees-to-switch-plan', [PlanningMobileController::class, 'getEmployeesToSwitchPlan']);

        Route::post('get-day-planning', [PlanningController::class, 'getDayPlanningMobile'])->name('get-day-planning-mobile');

    });

});

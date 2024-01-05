<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Planning\
{
    PlanningMobileController,
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

        // Route::post('get-employee-plan-contract/{user_id}', 'getEmployeePlanContract');

        Route::get('get-employee-contracts', 'index');
    });

    Route::controller(PlanningStartStopController::class)->group(function () {

        Route::post('start-plan-by-employee', 'startPlanByEmployee');
        
        Route::post('stop-plan-by-employee', 'stopPlanByEmployee');
        
    });

    Route::middleware([InitializeTenancy::class])->group(function () {

        Route::get('get-employees-to-switch-plan', [PlanningMobileController::class, 'getEmployeesToSwitchPlan']);

    });

});

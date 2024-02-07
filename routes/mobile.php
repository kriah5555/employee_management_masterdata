<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Planning\
{
    PlanningController,
    PlanningBreakController,
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

        Route::get('get-employee-contracts', 'index'); # employee flow

        Route::get('manager-get-employee-contracts/{employee_profile_id}', 'index')->name('manager-get-employee-contracts'); # manager flow
    });

    Route::controller(PlanningStartStopController::class)->group(function () {

        Route::post('start-plan-by-employee', 'startPlanByEmployee');

        Route::post('stop-plan-by-employee', 'stopPlanByEmployee');

        Route::post('stop-forgot-plan-by-employee', 'stopForgotPlanByEmployee');

    });

    Route::middleware([InitializeTenancy::class])->group(function () {

        Route::get('get-employees-to-switch-plan', [PlanningMobileController::class, 'getEmployeesToSwitchPlan']);

        Route::post('get-day-planning', [PlanningController::class, 'getDayPlanningMobile'])->name('get-day-planning-mobile');

        Route::post('get-day-plans-manager', [PlanningMobileController::class, 'getDayPlansManager']);

        Route::post('get-plannings-to-start-stop', [PlanningStartStopController::class, 'getDayPlanningToStartAndStop']); # manager flow

    });

    Route::controller(PlanningBreakController::class)->group(function () {

        Route::post('start-break-by-employee', 'startBreakByEmployee')->name('start-break-by-employee');

        Route::post('stop-break-by-employee', 'stopBreakByEmployee')->name('stop-break-by-employee');

    });
    
});

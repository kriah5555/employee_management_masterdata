<?php

use App\Http\Controllers\Planning\UurroosterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Planning\
{
    EventDetailsController,
    LongTermPlanningController,
    PlanningController,
    PlanningCreateEditController,
    PlanningStartStopController,
    TimeRegistrationController,
    VacancyController
};
use App\Http\Controllers\Planning\PlanningShiftController;
use App\Http\Middleware\InitializeTenancy;
use App\Http\Middleware\SetActiveUser;
use App\Http\Controllers\Dimona\DimonaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

#Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
#    return $request->user();
#});
Route::get('/check', function () {
    return "Echo";
});

Route::middleware([InitializeTenancy::class, SetActiveUser::class])->group(function () {

    Route::controller(PlanningController::class)
        // ->middleware(['initialize-tenancy'])
        ->prefix('planning')
        ->group(function () {

            $planningResources = [
                ['path' => 'get-planning-options', 'function' => 'getPlanningOverviewOptions'],
                ['path' => 'get-monthly-planning', 'function' => 'getMonthlyPlanning'],
                ['path' => 'get-week-planning', 'function' => 'getWeeklyPlanning'],
                ['path' => 'get-day-planning', 'function' => 'getDayPlanning'],
                ['path' => 'get-planning-create', 'function' => 'planningCreateOptions'],
            ];

            foreach ($planningResources as $api) {
                Route::POST($api['path'], $api['function']);
            }

        });


    Route::controller(PlanningController::class)->group(function () {
        
        Route::post('get-week-planning-employee', 'getWeeklyPlanningForEmployee')->name('week-planning-employee');

        Route::get('get-employee-day-planning/{employee_profile_id}', 'getEmployeeDayPlanning');

        Route::get('planning-details/{plan_id}', 'getPlanDetails');
        
        Route::post('get-plans-for-absence', 'getPlansForAbsence');
    });




    Route::controller(PlanningCreateEditController::class)->group(function () {

        Route::post('get-employee-plan-creation-options', 'create');

        Route::post('save-plans', 'savePlans');

        Route::delete('delete-plan/{plan_id}', 'destroy');

        Route::post('delete-week-plans', 'deleteWeekPlans');

    });

    Route::controller(PlanningStartStopController::class)->group(function () {

        Route::get('start-plan-options', 'startPlanOptions');

        Route::post('start-plan-by-manager', 'startPlanByManager');

        Route::post('stop-plan-by-manager', 'stopPlanByManager');

    });


    Route::post('/vacancy/respond-to-vacancy', [VacancyController::class, 'respondToVacancy']);
    Route::post('uurrooster', [UurroosterController::class, 'getUurroosterData']);
    Route::post('store-planning-shifts', [PlanningShiftController::class, 'storePlanningShifts']);
    Route::get('employee-long-term-plannings/{employee_id}', [LongTermPlanningController::class, 'getEmployeeLongTermPlannings']);
    Route::post('create-shift-plan', [PlanningShiftController::class, 'createShiftPlan']);
    $resources = [
        'long-term-planning' => [
            'controller' => LongTermPlanningController::class,
            'methods'    => ['show', 'create', 'store', 'update', 'destroy']
        ],
        'vacancy'            => [
            'controller' => VacancyController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
    ];
    foreach ($resources as $uri => ['controller' => $controller, 'methods' => $methods]) {
        Route::resource($uri, $controller)->only($methods);
    }
    Route::controller(DimonaController::class)->group(function () {
        Route::get('/dimona-test-plan/{planId}', [DimonaController::class, 'sendDimonaByPlan']);
        Route::get('/send-dimona/{planId}', [DimonaController::class, 'sendDimonaByPlan']);
        Route::get('/dimona-test-contract/{dimonaType}/{employeeContract}', [DimonaController::class, 'sendDimonaByEmployeeContract']);
        Route::get('/dimona-test-plan', [DimonaController::class, 'sendDimonaByPlan']);
    });
});
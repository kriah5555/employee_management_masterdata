<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Planning\
{
    EventDetailsController,
    LongtermPlanningController,
    PlanningController,
    PlanningCreateEditController,
    PlanningStartStopController,
    TimeRegistrationController,
    VacancyController
};
use App\Http\Middleware\InitializeTenancy;
use App\Http\Middleware\SetActiveUser;

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

        
    Route::get('get-employee-day-planning/{employee_profile_id}', [PlanningController::class, 'getEmployeeDayPlanning']);

    Route::controller(PlanningCreateEditController::Class)->group(function () {
        
        Route::post('get-employee-plan-creation-options', 'create');

        Route::post('save-plans', 'savePlans');

        Route::delete('delete-plan/{plan_id}', 'destroy');

        Route::post('delete-week-plans', 'deleteWeekPlans');

    });

    Route::post('start-plan-by-manager', [PlanningStartStopController::class, 'startPlanByManager']);
    Route::get('planning-details/{plan_id}', [PlanningController::class, 'getPlanDetails']);
    Route::resource('vacancy', VacancyController::class)->only(['index', 'show', 'create', 'store', 'update', 'destroy']);
});

// Route::controller(VacancyController::class)
// ->middleware(['initialize-tenancy'])
// ->prefix('vacancy')
// ->group(function () {
//     $apiList = [
//         ['path' => 'options', 'function' => 'create'],
//         ['path' => 'get-all-vacancies', 'function' => 'index'],
//         ['path' => 'create', 'function' => 'store'],
//         ['path' => 'get-vacancy/{vacancy}', 'function' => 'show'],
//         ['path' => 'update/{vacancy}', 'function' => 'update'],
//         ['path' => 'delete/{vacancy}', 'function' => 'destory'],
//     ];
//     foreach ($apiList as $api) {
//         Route::POST($api['path'], $api['function']);
//     }
// });

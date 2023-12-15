<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Planning\
{
    EventDetailsController,
    LongtermPlanningController,
    PlanningController,
    PlanningCreateEditController,
    TimeRegistrationController
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
        ->middleware(['initialize-tenancy'])
        ->prefix('planning')
        ->group(function () {

            $planningResouces = [
                ['path' => 'get-planning-options', 'function' => 'getPlanningOverviewOptions'],
                ['path' => 'get-monthly-planning', 'function' => 'getMonthlyPlanning'],
                ['path' => 'get-week-planning', 'function' => 'getWeeklyPlanning'],
                ['path' => 'get-day-planning', 'function' => 'getDayPlanning'],
                ['path' => 'get-planning-create', 'function' => 'planningCreateOptions']
            ];

            foreach ($planningResouces as $api) {
                Route::POST($api['path'], $api['function']);
            }
        });
    Route::post('get-employee-plan-creation-options', [PlanningCreateEditController::class, 'create']);
    Route::post('save-plans', [PlanningCreateEditController::class, 'savePlans']);
    Route::delete('delete-plan/{plan_id}', [PlanningCreateEditController::class, 'destroy']);
    Route::post('delete-week-plans', [PlanningCreateEditController::class, 'deleteWeekPlans']);
});

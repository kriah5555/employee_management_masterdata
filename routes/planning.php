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
Route::get('/check', function() {
    return "Echo";
});

Route::controller(PlanningController::class)
    ->middleware(['initialize-tenancy'])
    ->prefix('planning')
    ->group(function() {

        $planningResouces = [
            ['path' => 'get-planning-options', 'function' => 'getPlanningOverviewOptions'],
            ['path' => 'get-monthly-planning', 'function' => 'getMonthlyPlanning'],
            ['path' => 'get-week-planning', 'function' => 'getWeeklyPlanning'],
            ['path' => 'get-day-planning', 'function' => 'getDayPlanning'],
            ['path' => 'get-employee', 'function' => 'getEmployeeList'],
            ['path' => 'get-planning-create', 'function' => 'planningCreateOptions']
        ];

        foreach ($planningResouces as $api) {
            Route::POST($api['path'], $api['function']);
        }
});

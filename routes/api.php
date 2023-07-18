<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeTypeController;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\FunctionTitleController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('employee-types', EmployeeTypeController::class);

Route::resource('sectors', SectorController::class);

Route::resource('function-titles', FunctionTitleController::class);
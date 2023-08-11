<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeTypeController;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\FunctionTitleController;
use App\Http\Controllers\FunctionCategoryController;

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

/*
 Status codes 
    200 => make ok
    201 => created
    202 => updated
    204 => request successfull no data to return (deleted)

    400 => request cant be processed
    422 => Invalid date has been sent (validation error)
    401 => unauthorized
    404 => request dosent exists (data not found)
    405 => http request get, put not allowed
    403 => forbidden no authentication
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'service-registry'], function () {
    // Your API routes
});

Route::resource('employee-types', EmployeeTypeController::class);
Route::get('get-type-options', [EmployeeTypeController::class, 'getEmployeeTypeOptions']);


Route::resource('sectors', SectorController::class);

Route::resource('function-titles', FunctionTitleController::class);

Route::resource('function-categories', FunctionCategoryController::class);
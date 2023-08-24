<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeTypeController;
use App\Http\Controllers\HolidayCodesController;
use App\Http\Controllers\FunctionTitleController;
use App\Http\Controllers\FunctionCategoryController;
use App\Http\Controllers\HolidayCodeCountController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\WorkstationController;
use App\Http\Controllers\EmailTemplateApiController;

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

    500 =>  indicates that the server encountered an unexpected condition that prevented it from fulfilling the request
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'service-registry'], function () {
    // Your API routes
});

Route::get('get-type-options', [EmployeeTypeController::class, 'getEmployeeTypeOptions']);

Route::resource('employee-types', EmployeeTypeController::class)->withTrashed(['show']);

Route::resource('sectors', SectorController::class)->withTrashed(['show']);

Route::resource('function-titles', FunctionTitleController::class)->withTrashed(['show']);

Route::resource('function-categories', FunctionCategoryController::class)->withTrashed(['show']);

Route::resource('companies', CompanyController::class);

Route::resource('holiday-codes', HolidayCodesController::class);

Route::resource('holiday-code-count', HolidayCodeCountController::class);

Route::get('get-minimum-salaries/{id}', [SalaryController::class, 'getMinimumSalaries']);

Route::post('update-minimum-salaries/{id}', [SalaryController::class, 'updateMinimumSalaries']);

Route::middleware('validate.api.token')->group(function () {
    Route::get('/testing', function () {
        return response()->json([
            'message' => 'Test API.'
        ]);
      });
});
Route::resource('locations', LocationController::class);

Route::get('company/locations/{company_id}/{status}', [LocationController::class, 'locations'])->where('status', '^(0|1|all)$');

Route::resource('workstations', WorkstationController::class);

Route::get('company/workstations/{company_id}/{status}', [WorkstationController::class, 'companyWorkstations'])->where('status', '^(0|1|all)$');

Route::get('location/workstations/{location_id}/{status}', [WorkstationController::class, 'locationWorkstations'])->where('status', '^(0|1|all)$');

Route::resource('email-templates', EmailTemplateApiController::class);

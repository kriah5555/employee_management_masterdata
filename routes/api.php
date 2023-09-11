<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sector\SectorController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\EmployeeType\EmployeeTypeController;
use App\Http\Controllers\HolidayCode\HolidayCodesController;
use App\Http\Controllers\EmployeeFunction\FunctionTitleController;
use App\Http\Controllers\EmployeeFunction\FunctionCategoryController;
use App\Http\Controllers\HolidayCode\HolidayCodeCountController;
use App\Http\Controllers\Sector\SalaryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\WorkstationController;
use App\Http\Controllers\Email\EmailTemplateApiController;
use App\Http\Controllers\Translations\TranslationController;
use App\Http\Controllers\Contract\ContractTypeController;
use App\Http\Controllers\Rule\RuleController;
use App\Http\Controllers\ReasonController;

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
$integerRule = '[0-9]+'; # allow only integer values
$statusRule = '^(0|1|all)$'; # allow only 0 1 0r all values
$numericWithOptionalDecimalRule = '[0-9]+(\.[0-9]+)?'; # allow only numeric and decimla values

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'service-registry'], function () {
    // Your API routes
});

Route::resources([
    'employee-types'      => EmployeeTypeController::class,
    'sectors'             => SectorController::class,
    'function-titles'     => FunctionTitleController::class,
    'function-categories' => FunctionCategoryController::class,
    'companies'           => CompanyController::class,
    'holiday-codes'       => HolidayCodesController::class,
    'holiday-code-count'  => HolidayCodeCountController::class,
    'email-templates'     => EmailTemplateApiController::class,
    'contract-types'      => ContractTypeController::class,
    'workstations'        => WorkstationController::class,
    'locations'           => LocationController::class,
    'reasons'             => ReasonController::class,
]);

Route::controller(TranslationController::class)->group(function () {

    Route::post('/extract-translatable-strings', 'extractTranslatableStrings');

    Route::get('/translations/{key?}', 'index');

    Route::post('/translations', 'store');

    Route::post('/translate', 'getStringTranslation');
});

Route::controller(SalaryController::class)->group(function () use ($integerRule, $numericWithOptionalDecimalRule) {

    Route::get('get-minimum-salaries/{id}', 'getMinimumSalaries');

    Route::post('add-coefficient-minimum-salaries/{id}/{increment_coefficient}', 'addIncrementToMinimumSalaries')->where(['id' => $integerRule, 'increment_coefficient' => $numericWithOptionalDecimalRule]);

    Route::post('undo-coefficient-minimum-salaries/{sector_id}', 'undoIncrementedMinimumSalaries')->where(['sector_id' => $integerRule]);

    Route::post('update-minimum-salaries/{id}', 'updateMinimumSalaries')->where(['id' => $integerRule]);
});

Route::controller(LocationController::class)->group(function () use ($statusRule) {

    Route::get('locations/{company_id}/{status}', 'index')->where('status', $statusRule);

    Route::get('/locations/create/{company_id}', 'create');
});

Route::controller(WorkstationController::class)->group(function () use ($statusRule, $integerRule) {

    Route::get('company-workstations/{company_id}/{status}', 'companyWorkstations')->where('status', $statusRule);

    Route::get('location-workstations/{location_id}/{status}', 'locationWorkstations')->where(['status' => $statusRule, 'location_id' => $integerRule]);

    Route::post('undo-coefficient-minimum-salaries/{sector_id}', [SalaryController::class, 'undoIncrementedMinimumSalaries'])->where(['sector_id' => $integerRule]);

    Route::post('update-minimum-salaries/{id}', [SalaryController::class, 'updateMinimumSalaries'])->where(['id' => $integerRule]);

    Route::resource('locations', LocationController::class);

    Route::get('company/locations/{company_id}/{status}', [LocationController::class, 'locations'])->where('status', $statusRule);

    Route::resource('workstations', WorkstationController::class);

    Route::get('company/workstations/{company_id}/{status}', [WorkstationController::class, 'companyWorkstations'])->where('status', $statusRule);

    Route::resource('email-templates', EmailTemplateApiController::class);

    Route::post('/extract-translatable-strings', [TranslationController::class, 'extractTranslatableStrings']);

    Route::get('/translations/{key?}', [TranslationController::class, 'index']);

    Route::post('/translations', [TranslationController::class, 'store']);

    Route::post('/translate', [TranslationController::class, 'getStringTranslation']);

    Route::resource('rules', RuleController::class)->only(['index', 'show', 'edit', 'update']);

    Route::get('workstations/create/{company_id}', 'create');
});

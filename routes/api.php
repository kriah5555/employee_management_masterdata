<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sector\SectorController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\EmployeeType\EmployeeTypeController;
use App\Http\Controllers\HolidayCode\HolidayCodesController;
use App\Http\Controllers\HolidayCode\HolidayCodeConfigController;
use App\Http\Controllers\EmployeeFunction\FunctionTitleController;
use App\Http\Controllers\EmployeeFunction\FunctionCategoryController;
// use App\Http\Controllers\HolidayCode\HolidayCodeCountController;
use App\Http\Controllers\HolidayCode\EmployeeHolidayCountController;
use App\Http\Controllers\Sector\SalaryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\WorkstationController;
use App\Http\Controllers\Email\EmailTemplateApiController;
use App\Http\Controllers\Translations\TranslationController;
use App\Http\Controllers\Contract\ContractTypeController;
use App\Http\Controllers\Rule\RuleController;
use App\Http\Controllers\ReasonController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\GenderController;
use App\Http\Controllers\Employee\MaritalStatusController;
use App\Http\Controllers\CostCenterController;
use App\Http\Controllers\SocialSecretary\SocialSecretaryController;
use App\Http\Controllers\HolidayCode\HolidayCodesOfSocialSecretaryController;

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
    'email-templates'     => EmailTemplateApiController::class,
    'contract-types'      => ContractTypeController::class,
    'workstations'        => WorkstationController::class,
    'locations'           => LocationController::class,
    'social-secretary'    => SocialSecretaryController::class,
]);

Route::resource('rules', RuleController::class)->only(['index', 'show', 'edit', 'update']);

Route::resource('holiday-code-config', HolidayCodeConfigController::class)->only(['edit', 'update']);

Route::resource('employee-holiday-count', EmployeeHolidayCountController::class)->only(['edit', 'store', 'show']);

Route::resource('social-secretary-holiday-codes', HolidayCodesOfSocialSecretaryController::class)->only(['edit', 'store']);

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

    Route::post('undo-coefficient-minimum-salaries/{sector_id}', 'undoIncrementedMinimumSalaries')->where(['sector_id' => $integerRule]);

    Route::post('update-minimum-salaries/{id}', 'updateMinimumSalaries')->where(['id' => $integerRule]);

});

Route::controller(LocationController::class)->group(function () use ($statusRule) {

    Route::get('locations/{company_id}/{status}', 'index')->where('status', $statusRule);

    Route::get('/locations/create/{company_id}', 'create');

    Route::resource('locations', LocationController::class);

    Route::get('company/locations/{company_id}/{status}', 'locations')->where('status', $statusRule);
});

Route::controller(WorkstationController::class)->group(function () use ($statusRule, $integerRule) {

    Route::get('company-workstations/{company_id}/{status}', 'companyWorkstations')->where(['status' => $statusRule, 'company_id' => $integerRule]);

    Route::get('location-workstations/{location_id}/{status}', 'locationWorkstations')->where(['status' => $statusRule, 'location_id' => $integerRule]);

    Route::get('company/workstations/{company_id}/{status}', 'companyWorkstations')->where('status', $statusRule);

    Route::get('workstations/create/{company_id}', 'create');
});

Route::group(['middleware' => 'setactiveuser'], function () {

    Route::resource('employees', EmployeeController::class)->only(['show', 'edit']);

    Route::controller(EmployeeController::class)->group(function () {

        Route::get('/employees/get-company-employees/{company_id}', 'index');

        Route::get('/employees/create/{company_id}', 'create');

        Route::post('/employees/store/{company_id}', 'store');

        Route::post('/employees-get-function-salary', 'getFunctionSalaryToCreateEmployee');
    });

    Route::resource('genders', GenderController::class)->only(['index', 'store', 'show', 'edit', 'update', 'destroy']);

    Route::resource('marital-statuses', MaritalStatusController::class)->only(['index', 'store', 'show', 'edit', 'update', 'destroy']);

});

Route::controller(CostCenterController::class)->group(function () use ($statusRule, $integerRule) {

    Route::resource('cost-center', CostCenterController::class)->where(['status' => $statusRule, 'company_id' => $integerRule])->except(['index', 'create']);

    Route::get('cost-center/{company_id}/{status}', 'index')->where(['status' => $statusRule, 'company_id' => $integerRule]);

    Route::get('cost-center/create/{company_id}', 'create')->where('company_id', $integerRule);
});

Route::controller(ReasonController::class)->group(function () use ($statusRule, $integerRule) {

    Route::resource('reasons', ReasonController::class)->except(['index']);

    Route::get('reasons-list/{status}/{category?}', 'index')->where('status', $statusRule);

});

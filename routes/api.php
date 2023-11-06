<?php

use App\Http\Controllers\MealVoucherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sector\SectorController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\EmployeeType\EmployeeTypeController;
use App\Http\Controllers\Holiday\HolidayCodeController;
use App\Http\Controllers\Holiday\HolidayCodeConfigController;
use App\Http\Controllers\EmployeeFunction\FunctionTitleController;
use App\Http\Controllers\EmployeeFunction\FunctionCategoryController;
use App\Http\Controllers\Holiday\EmployeeHolidayCountController;
use App\Http\Controllers\Sector\SalaryController;
use App\Http\Controllers\Company\LocationController;
use App\Http\Controllers\Company\WorkstationController;
use App\Http\Controllers\Email\EmailTemplateApiController;
use App\Http\Controllers\Translations\TranslationController;
use App\Http\Controllers\Contract\ContractTypeController;
use App\Http\Controllers\Contract\ContractTemplateController;
use App\Http\Controllers\Rule\RuleController;
use App\Http\Controllers\ReasonController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Company\CostCenterController;
use App\Http\Controllers\SocialSecretary\SocialSecretaryController;
use App\Http\Controllers\Employee\CommuteTypeController;
use App\Http\Controllers\Holiday\PublicHolidayController;
use App\Http\Controllers\Interim\InterimAgencyController;

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
    'companies'       => CompanyController::class,
    'email-templates' => EmailTemplateApiController::class,
    'workstations'    => WorkstationController::class,
    'public-holidays' => PublicHolidayController::class,
]);

Route::resource('holiday-code-config', HolidayCodeConfigController::class)->only(['edit', 'update', 'create']);

Route::resource('employee-holiday-count', EmployeeHolidayCountController::class)->only(['edit', 'store', 'show']);

Route::controller(TranslationController::class)->group(function () {

    Route::post('/extract-translatable-strings', 'extractTranslatableStrings');

    Route::get('/translations/{key?}', 'index');

    Route::post('/translations', 'store');

    Route::post('/translate', 'getStringTranslation');

});

Route::controller(ContractTemplateController::class)->group(function () use ($integerRule) {

    Route::resource('contract-templates', ContractTemplateController::class)->except(['edit']);

    Route::get('company-contract-templates/{company_id}', 'index');

});



Route::controller(WorkstationController::class)->group(function () use ($statusRule, $integerRule) {

    Route::get('company-workstations/{company_id}/{status}', 'companyWorkstations')->where(['status' => $statusRule, 'company_id' => $integerRule]);

    Route::get('location-workstations/{location_id}/{status}', 'locationWorkstations')->where(['status' => $statusRule, 'location_id' => $integerRule]);

    Route::get('company/workstations/{company_id}/{status}', 'companyWorkstations')->where('status', $statusRule);

    Route::get('workstations/create/{company_id}', 'create');
});

Route::controller(CostCenterController::class)->group(function () use ($statusRule, $integerRule) {

    Route::resource('cost-center', CostCenterController::class)->where(['status' => $statusRule, 'company_id' => $integerRule])->except(['index', 'create']);

    Route::get('cost-center/{company_id}/{status}', 'index')->where(['status' => $statusRule, 'company_id' => $integerRule]);

    Route::get('cost-center/create/{company_id}', 'create')->where('company_id', $integerRule);
});


Route::group(['middleware' => 'setactiveuser'], function () use ($integerRule, $statusRule) {
    $resources = [
        'contract-types'      => [
            'controller' => ContractTypeController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'employee-types'      => [
            'controller' => EmployeeTypeController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'sectors'             => [
            'controller' => SectorController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'function-categories' => [
            'controller' => FunctionCategoryController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'function-titles'     => [
            'controller' => FunctionTitleController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'reasons'             => [
            'controller' => ReasonController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'holiday-codes'       => [
            'controller' => HolidayCodeController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'rules'               => [
            'controller' => RuleController::class,
            'methods'    => ['index', 'show', 'edit', 'update']
        ],
        'social-secretary'    => [
            'controller' => SocialSecretaryController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'interim-agencies'    => [
            'controller' => InterimAgencyController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'companies'           => [
            'controller' => CompanyController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
    ];
    foreach ($resources as $uri => ['controller' => $controller, 'methods' => $methods]) {
        Route::resource($uri, $controller)->only($methods);
    }
    Route::get('get-minimum-salaries/{sector_id}', [SalaryController::class, 'getOptionsForEmployeeContractCreation']);

    Route::get('get-function-category-options-by-sector/{sector_id}', [SectorController::class, 'getOptionsForEmployeeContractCreation']);

    Route::controller(SalaryController::class)->group(function () use ($integerRule) {

        Route::get('monthly-minimum-salaries/{sector_id}/get', 'getMinimumSalaries')->where(['sector_id' => $integerRule]);

        Route::post('monthly-minimum-salaries/{sector_id}/update', 'updateMinimumSalaries')->where(['sector_id' => $integerRule]);

        Route::post('monthly-minimum-salaries/{sector_id}/undo', 'undoIncrementedMinimumSalaries')->where(['sector_id' => $integerRule]);

        Route::get('hourly-minimum-salaries/{sector_id}/get', 'getMinimumSalaries');

        Route::post('hourly-minimum-salaries/{sector_id}/undo', 'undoIncrementedMinimumSalaries')->where(['sector_id' => $integerRule]);

        Route::post('hourly-minimum-salaries/{sector_id}/update', 'updateMinimumSalaries')->where(['id' => $integerRule]);
    });

    Route::controller(ReasonController::class)->group(function () {

        Route::get('reasons-list/{category?}', 'getReasonsList');

    });
    Route::controller(SocialSecretaryController::class)->group(function () use ($integerRule) {

        Route::get('social-secretary-holiday-configuration/{social_secretary_id}', 'getSocialSecretaryHolidayConfiguration')->where(['sector_id' => $integerRule]);

        Route::put('social-secretary-holiday-configuration', 'updateSocialSecretaryHolidayConfiguration')->where(['sector_id' => $integerRule]);
    });


    Route::resource('commute-types', CommuteTypeController::class)->only(['index', 'store', 'show', 'edit', 'update', 'destroy']);

    Route::resource('meal-vouchers', MealVoucherController::class)->only(['index', 'store', 'show', 'edit', 'update', 'destroy']);

    Route::group(['middleware' => 'initialize-tenancy'], function () {
        $resources = [
            'locations' => [
                'controller' => LocationController::class,
                'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
            ],
            'employees' => [
                'controller' => EmployeeController::class,
                'methods'    => ['index', 'show', 'store', 'update', 'destroy']
            ],
        ];
        foreach ($resources as $uri => ['controller' => $controller, 'methods' => $methods]) {
            Route::resource($uri, $controller)->only($methods);
        }
        Route::get('employee-contract/create', [EmployeeController::class, 'createEmployeeContract']);
        Route::get('employee-commute/create', [EmployeeController::class, 'createEmployeeCommute']);
        Route::get('employee-benefits/create', [EmployeeController::class, 'createEmployeeBenefits']);
    });
    // Route::controller(EmployeeController::class)->group(function () {

    //     Route::get('/employees', 'index');

    //     Route::get('/employees/create/{company_id}', 'create');

    //     Route::post('/employees/store/{company_id}', 'store');

    //     Route::post('/employees-get-function-salary', 'getFunctionSalaryToCreateEmployee');
    // });
});

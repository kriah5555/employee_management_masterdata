<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sector\SectorController;
use App\Http\Controllers\Holiday\HolidayCodeController;
use App\Http\Controllers\Holiday\HolidayCodeConfigController;
use App\Http\Controllers\Holiday\EmployeeHolidayCountController;
use App\Http\Controllers\Sector\SalaryController;
use App\Http\Controllers\Email\EmailTemplateApiController;
use App\Http\Controllers\Translations\TranslationController;
use App\Http\Controllers\Contract\ContractTypeController;
use App\Http\Controllers\Contract\ContractTemplateController;
use App\Http\Controllers\Rule\RuleController;
use App\Http\Controllers\ReasonController;
use App\Http\Controllers\SocialSecretary\SocialSecretaryController;
use App\Http\Controllers\Holiday\PublicHolidayController;
use App\Http\Controllers\Interim\InterimAgencyController;
use App\Http\Controllers\NotificationController\NotificationController;

use App\Http\Controllers\Company\{
    CompanyController,
    LocationController,
    CostCenterController,
    WorkstationController,
    AvailabilityController,
    Absence\LeaveController,
    Absence\HolidayController,
    Contract\ContractConfigurationController,
    Contract\CompanyContractTemplateController,
};

use App\Http\Controllers\Employee\{
    EmployeeController,
    CommuteTypeController,
    EmployeeTypeController,
    FunctionTitleController,
    FunctionCategoryController,
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
Route::get('/testing', function () {
    return response()->json([
        'message' => 'Masterdata'
    ]);
});

Route::group(['middleware' => 'service-registry'], function () {
    // Your API routes
});

Route::group(['middleware' => 'setactiveuser'], function () use ($integerRule) {
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
            'methods'    => ['index', 'show', 'store', 'update', 'destroy']
        ],
        'interim-agencies'    => [
            'controller' => InterimAgencyController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'companies'           => [
            'controller' => CompanyController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'commute-types'       => [
            'controller' => CommuteTypeController::class,
            'methods'    => ['index', 'store', 'show', 'edit', 'update', 'destroy']
        ],
        'meal-vouchers'       => [
            'controller' => MealVoucherController::class,
            'methods'    => ['index', 'store', 'show', 'edit', 'update', 'destroy']
        ],
        'email-templates'     => [
            'controller' => EmailTemplateApiController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'public-holidays'     => [
            'controller' => PublicHolidayController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'contract-templates'  => [
            'controller' => ContractTemplateController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'holiday-code-config' => [
            'controller' => HolidayCodeConfigController::class,
            'methods'    => ['edit', 'update', 'create']
        ],
    ];
    foreach ($resources as $uri => ['controller' => $controller, 'methods' => $methods]) {
        Route::resource($uri, $controller)->only($methods);
    }

    Route::get('convert-pdf-to-html', [ContractTemplateController::class, 'convertPDFHtml']);

    Route::controller(TranslationController::class)->group(function () {

        Route::post('/extract-translatable-strings', 'extractTranslatableStrings');

        Route::get('/translations/{key?}', 'index');

        Route::post('/translations', 'store');

        Route::post('/translate', 'getStringTranslation');

    });

    Route::get('get-minimum-salaries/{sector_id}', [SalaryController::class, 'getOptionsForEmployeeContractCreation']);

    Route::get('get-function-category-options-by-sector/{sector_id}', [SectorController::class, 'getOptionsForEmployeeContractCreation']);

    Route::post('get-sector-function-titles', [SectorController::class, 'getFunctionTitles']);

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

    Route::group(['middleware' => 'initialize-tenancy'], function () use ($integerRule) {

        Route::controller(HolidayController::class)->group(function () use ($integerRule) {

            Route::resource('holidays', HolidayController::class)->except(['edit', 'index']);

            Route::get('employee-holidays/{employee_id}/{status}', [HolidayController::class, 'employeeHolidays'])
                ->where(['status' => '(approve|cancel|pending|reject|request_cancel)']); # for employee flow

            Route::get('holidays-list/{status}', [HolidayController::class, 'index'])
                ->where(['status' => '(approve|cancel|pending|reject|request_cancel)']); # for managers flow

            Route::post('holidays-status/{holiday_id}/{status}', 'updateHolidayStatus')
                ->where(['status' => '(approve|cancel|request_cancel|reject)']); # fro all to update status of absence
        });

        Route::controller(LeaveController::class)->group(function () use ($integerRule) {

            Route::resource('leaves', LeaveController::class)->except(['edit', 'index']);

            Route::get('leaves-list/{status}', [LeaveController::class, 'index'])
                ->where(['status' => '(approve|cancel)']); # to get leaves list

            Route::post('leaves-status/{leave_id}/{status}', 'updateLeaveStatus')
                ->where(['status' => '(cancel)']);
        });

        Route::controller(LocationController::class)->group(function () use ($integerRule) {

            Route::get('location-workstations/{location_id}', 'locationWorkstations')->where(['location_id' => $integerRule]);

        });

        $resources = [
            'locations'    => [
                'controller' => LocationController::class,
                'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
            ],
            'workstations' => [
                'controller' => WorkstationController::class,
                'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
            ],
            'cost-centers' => [
                'controller' => CostCenterController::class,
                'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
            ],
        ];

        foreach ($resources as $uri => ['controller' => $controller, 'methods' => $methods]) {
            Route::resource($uri, $controller)->only($methods);
        }

        Route::controller(CompanyContractTemplateController::class)->group(function () {

            Route::resource('company-contract-templates', CompanyContractTemplateController::class)->except(['edit']);

        });

        Route::resource('employee-holiday-count', EmployeeHolidayCountController::class)->only(['edit', 'store', 'show']);

        Route::resource('employees', EmployeeController::class)->only(['index', 'show', 'store', 'update', 'destroy']);

        Route::resource('contract-configuration', ContractConfigurationController::class)->only(['index', 'store']);

        Route::get('employee-contract/create', [EmployeeController::class, 'createEmployeeContract']);
        Route::get('employee-commute/create', [EmployeeController::class, 'createEmployeeCommute']);
        Route::get('employee-benefits/create', [EmployeeController::class, 'createEmployeeBenefits']);
        Route::get('employee/update-personal-details', [EmployeeController::class, 'updatePersonalDetails']);
        Route::get('employees/contracts/{employeeId}', [EmployeeController::class, 'getEmployeeContracts']);

        Route::post('/create-availability', [AvailabilityController::class, 'createAvailability']);
        Route::get('/get-availability', [AvailabilityController::class, 'avilableDateAndNOtAvailableDates']);
        Route::put('/update-availability/{id}', [AvailabilityController::class, 'updateAvailability']);
        Route::delete('/delete-availabiility', [AvailabilityController::class, 'deleteAvailability']);
        Route::post('company-additional-details', [CompanyController::class, 'storeAdditionalDetails']);

    });
    Route::get('user/responsible-companies', [EmployeeController::class, 'getUserResponsibleCompanies']);



    Route::put('employee-update', [EmployeeController::class, 'updateEmployee']);

    Route::post('/send-notification', [NotificationController::class, 'sendNotification']);
});

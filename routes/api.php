<?php

use App\Http\Controllers\Dimona\DimonaErrorCodeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\Company\{
    CompanyController,
    EmployeeAvailabilityController,
};

use App\Http\Controllers\Holiday\{
    HolidayCodeController,
    PublicHolidayController,
    HolidayCodeConfigController,
};

use App\Http\Controllers\Employee\{
    EmployeeController,
    CommuteTypeController,
};

use App\Http\Controllers\{
    MealVoucherController,
    ReasonController,
    Parameter\ParameterController,
    Sector\SectorController,
    Sector\SalaryController,
    Contract\ContractTypeController,
    Interim\InterimAgencyController,
    Email\EmailTemplateApiController,
    Translations\TranslationController,
    EmployeeType\EmployeeTypeController,
    Contract\ContractTemplateController,
    EmployeeFunction\FunctionTitleController,
    SocialSecretary\SocialSecretaryController,
    EmployeeFunction\FunctionCategoryController,
    NotificationController\NotificationController,
    Dimona\EmployeeTypeDimoanConfigurationController,
    Configuration\FlexSalaryController,
};

use App\Http\Controllers\Employee\{
    EmployeeInvitationController,
    ImportEmployeeController
};

use App\Http\Controllers\Planning\VacancyController;
use App\Models\User\CompanyUser;
use Illuminate\Support\Facades\DB;

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

/**
 * Crons
 */
Route::get('/schedule-run', function () {
    Artisan::command('schedule:run');
});


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
        'contract-types'              => [
            'controller' => ContractTypeController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'employee-types'              => [
            'controller' => EmployeeTypeController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'sectors'                     => [
            'controller' => SectorController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'function-categories'         => [
            'controller' => FunctionCategoryController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'function-titles'             => [
            'controller' => FunctionTitleController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'reasons'                     => [
            'controller' => ReasonController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'holiday-codes'               => [
            'controller' => HolidayCodeController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'parameters'                  => [
            'controller' => ParameterController::class,
            'methods'    => ['show', 'edit', 'update']
        ],
        'social-secretary'            => [
            'controller' => SocialSecretaryController::class,
            'methods'    => ['index', 'show', 'store', 'update', 'destroy']
        ],
        'interim-agencies'            => [
            'controller' => InterimAgencyController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'companies'                   => [
            'controller' => CompanyController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'commute-types'               => [
            'controller' => CommuteTypeController::class,
            'methods'    => ['index', 'store', 'show', 'edit', 'update', 'destroy']
        ],
        'meal-vouchers'               => [
            'controller' => MealVoucherController::class,
            'methods'    => ['index', 'store', 'show', 'edit', 'update', 'destroy']
        ],
        'email-templates'             => [
            'controller' => EmailTemplateApiController::class,
            'methods'    => ['index', 'show', 'create', 'edit', 'store', 'update', 'destroy']
        ],
        'public-holidays'             => [
            'controller' => PublicHolidayController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'contract-templates'          => [
            'controller' => ContractTemplateController::class,
            'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
        ],
        'holiday-code-config'         => [
            'controller' => HolidayCodeConfigController::class,
            'methods'    => ['show', 'update']
        ],
        'employee-type-dimona-config' => [
            'controller' => EmployeeTypeDimoanConfigurationController::class,
            'methods'    => ['show', 'update']
        ],
        'availability'                => [
            'controller' => EmployeeAvailabilityController::class,
            'methods'    => ['store']
        ],
        'dimona-error-codes'          => [
            'controller' => DimonaErrorCodeController::class,
            'methods'    => ['index', 'update']
        ],
    ];
    foreach ($resources as $uri => ['controller' => $controller, 'methods' => $methods]) {
        Route::resource($uri, $controller)->only($methods);
    }
    Route::post('get-availability', [EmployeeAvailabilityController::class, 'index'])->name('get-employee-availability');

    Route::delete('availability', [EmployeeAvailabilityController::class, 'destroy'])->name('delete-employee-availability');

    Route::post('convert-pdf-to-html', [ContractTemplateController::class, 'convertPDFHtml']);

    Route::controller(TranslationController::class)->group(function () {

        Route::post('/extract-translatable-strings', 'extractTranslatableStrings');

        Route::resource('translations', TranslationController::class)->only(['show', 'index', 'update']);
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

        Route::post('salary-increment-calculation', 'salaryIncrementCalculation');
    });

    Route::controller(ReasonController::class)->group(function () {

        Route::get('reasons-list/{category?}', 'getReasonsList');
    });

    Route::controller(SocialSecretaryController::class)->group(function () use ($integerRule) {

        Route::get('social-secretary-holiday-configuration/{social_secretary_id}', 'getSocialSecretaryHolidayConfiguration')->where(['sector_id' => $integerRule]);

        Route::put('social-secretary-holiday-configuration', 'updateSocialSecretaryHolidayConfiguration')->where(['sector_id' => $integerRule]);
    });

    Route::get('user/responsible-companies', [CompanyController::class, 'getUserResponsibleCompanies']);

    Route::post('/user-details', [EmployeeController::class, 'getUserDetails']);

    Route::post('employee-update', [EmployeeController::class, 'updateEmployee']);
    Route::post('update-employee-personal-details', [EmployeeController::class, 'updateEmployeePersonal']);
    Route::post('update-employee-address-details', [EmployeeController::class, 'updateEmployeeAddress']);

    Route::get('/send-notification', [NotificationController::class, 'sendNotification']);

    Route::get('get-employee-companies', [EmployeeController::class, 'getEmployeeCompanies']);

    Route::post('/vacancy/apply-vacancy', [VacancyController::class, 'applyVacancy']);

    Route::post('/vacancy/employee', [VacancyController::class, 'getEmployeeJobsOverview']);

    Route::post('get-default-parameters', [ParameterController::class, 'getDefaultParameters'])->name('get-default-parameters');

    Route::put('update-default-parameter/{parameter_id}', [ParameterController::class, 'updateDefaultParameter'])->name('update-default-parameter');

    Route::post('get-parameters', [ParameterController::class, 'getParameters'])->name('get-parameters');

    Route::put('update-parameter/{parameter_id}', [ParameterController::class, 'updateParameter'])->name('update-parameter');

    Route::post('flex-salary', [FlexSalaryController::class, 'createOrUpdateFlexSalary']);
    Route::get('flex-salary/{key}', [FlexSalaryController::class, 'getFlexSalaryByKey']);
});
Route::post('/translate', [TranslationController::class, 'getStringTranslation']);

Route::post('validate-employee-invitations', [EmployeeInvitationController::class, 'validateEmployeeInvitation'])->name('validate-employee-invitations');

Route::post('employee-registration', [EmployeeInvitationController::class, 'employeeRegistration'])->name('employee-registration');

Route::get('import-employee-sample-file', [ImportEmployeeController::class, 'downloadImportEmployeeSampleFile']);

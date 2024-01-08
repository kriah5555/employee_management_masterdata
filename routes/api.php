<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Company\{
    CompanyController,
    EmployeeAvailabilityController,
};

use App\Http\Controllers\Holiday\{
    HolidayCodeController,
    PublicHolidayController,
    HolidayCodeConfigController,
    EmployeeHolidayCountController,
};

use App\Http\Controllers\Employee\{
    EmployeeController,
    CommuteTypeController,
    EmployeeAccessController,
    ResponsiblePersonController,
};

use App\Http\Controllers\{
    MealVoucherController,
    ReasonController,
    Rule\RuleController,
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
};

use App\Http\Controllers\Planning\VacancyController;

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
            'methods'    => ['index', 'show', 'create', 'edit', 'store', 'update', 'destroy']
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
            'methods'    => ['show', 'update']
        ],
        'availability'        => [
            'controller' => EmployeeAvailabilityController::class,
            'methods'    => ['store']
        ],
    ];
    foreach ($resources as $uri => ['controller' => $controller, 'methods' => $methods]) {
        Route::resource($uri, $controller)->only($methods);
    }
    Route::post('get-availability', [EmployeeAvailabilityController::class, 'index'])->name('get-employee-availability');

    Route::post('convert-pdf-to-html', [ContractTemplateController::class, 'convertPDFHtml']);

    Route::controller(TranslationController::class)->group(function () {

        Route::post('/extract-translatable-strings', 'extractTranslatableStrings');

        Route::resource('translations', TranslationController::class)->only(['show', 'index', 'update']);

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

    Route::get('user/responsible-companies', [EmployeeController::class, 'getUserResponsibleCompanies']);

    Route::get('/user-details', [EmployeeController::class, 'getUserDetails']);

    Route::post('employee-update', [EmployeeController::class, 'updateEmployee']);

    Route::get('/send-notification', [NotificationController::class, 'sendNotification']);

    Route::get('get-employee-companies', [EmployeeController::class, 'getEmployeeCompanies']);

    Route::post('/vacancy/apply-vacancy', [VacancyController::class, 'applyVacancy']);

    Route::post('/vacancy/employee', [VacancyController::class, 'getEmployeeJobsOverview']);
});

use App\Models\User\CompanyUser;
use Illuminate\Support\Facades\DB;

Route::get('/script', function () {
    DB::connection('master')->beginTransaction();
    $results = DB::connection('userdb')
        ->table('model_has_roles')
        ->select('*')  // You can replace '*' with specific column names if needed
        ->where('model_type', '=', "App\Models\User\CompanyUser")
        ->get();
    foreach ($results as $val) {
        if (in_array($val->role_id, [4, 5, 6, 7, 8, 9])) {
            if ($val->role_id == 4) {
                $role = 'customer_admin';
            } elseif ($val->role_id == 5) {
                $role = 'hr_manager';
            } elseif ($val->role_id == 6) {
                $role = 'manager';
            } elseif ($val->role_id == 7) {
                $role = 'planner';
            } elseif ($val->role_id == 8) {
                $role = 'staff';
            } elseif ($val->role_id == 9) {
                $role = 'employee';
            }
            $companyUser = CompanyUser::find($val->model_id);
            if ($companyUser) {
                $companyUser->assignRole($role);
            }
        }
    }
    DB::connection('master')->commit();
    return response()->json([
        'message' => 'Done'
    ]);
});

;

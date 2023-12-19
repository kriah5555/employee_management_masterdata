<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\InitializeTenancy;
use App\Http\Middleware\SetActiveUser;

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

use App\Http\Controllers\Holiday\{
    EmployeeHolidayCountController,
};

use App\Http\Controllers\Employee\{
    EmployeeController,
    EmployeeAccessController,
    EmployeeCommuteController,
    EmployeeContractController,
    EmployeeBenefitsController,
    ResponsiblePersonController,
};

use App\Http\Controllers\{
    Sector\SectorController,
    Contract\ContractController
};
use App\Http\Controllers\ReasonController;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

$integerRule = '[0-9]+'; # allow only integer values

Route::middleware([InitializeTenancy::class])->group(function () use ($integerRule) {

    Route::middleware([SetActiveUser::class])->group(function () use ($integerRule) {

        Route::get('/testing-tenant', function () {
            return response()->json([
                'message' => 'Masterdata tenant'
            ]);
        });

        Route::controller(HolidayController::class)->group(function () {

            Route::resource('holidays', HolidayController::class)->except(['edit', 'index']);

            Route::get('employee-holidays/{employee_id}/{status}', [HolidayController::class, 'employeeHolidays'])
                ->where(['status' => '(approve|cancel|pending|reject|request_cancel)']); # for employee flow

            Route::get('holidays-list/{status}', [HolidayController::class, 'index'])
                ->where(['status' => '(approve|cancel|pending|reject|request_cancel)']); # for managers flow

            Route::post('holidays-status/{holiday_id}/{status}', 'updateHolidayStatus')
                ->where(['status' => '(approve|cancel|request_cancel|reject)']); # fro all to update status of absence
        });

        Route::controller(LeaveController::class)->group(function () {

            Route::resource('leaves', LeaveController::class)->except(['edit', 'index']);

            Route::get('leaves-list/{status}', [LeaveController::class, 'index'])
                ->where(['status' => '(approve|pending)']); # to get leaves list

            Route::post('leaves-status/{leave_id}/{status}', 'updateLeaveStatus')
                ->where(['status' => '(cancel)']);
        });

        Route::controller(LocationController::class)->group(function () use ($integerRule) {

            Route::get('location-workstations/{location_id}', 'locationWorkstations')->where(['location_id' => $integerRule]);

        });

        $resources = [
            'locations'                  => [
                'controller' => LocationController::class,
                'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
            ],
            'workstations'               => [
                'controller' => WorkstationController::class,
                'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
            ],
            'cost-centers'               => [
                'controller' => CostCenterController::class,
                'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
            ],
            'company-contract-templates' => [
                'controller' => CompanyContractTemplateController::class,
                'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
            ],
            'employees'                  => [
                'controller' => EmployeeController::class,
                'methods'    => ['index', 'show', 'store', 'update', 'destroy']
            ],
            'employee-contracts'         => [
                'controller' => EmployeeContractController::class,
                'methods'    => ['show', 'store', 'update', 'destroy', 'create']
            ],
            'employee-benefits'          => [
                'controller' => EmployeeBenefitsController::class,
                'methods'    => ['show', 'update', 'create']
            ],
            'employee-commute'           => [
                'controller' => EmployeeCommuteController::class,
                'methods'    => ['show', 'update', 'create']
            ],
        ];

        // Route:resource('contracts', ContractController::class);
        
        Route::resource('contracts', ContractController::class)->only(['show', 'store']);

        Route::post('sign-contract', [ContractController::class, 'store']);

        foreach ($resources as $uri => ['controller' => $controller, 'methods' => $methods]) {
            Route::resource($uri, $controller)->only($methods);
        }

        Route::resource('employee-holiday-count', EmployeeHolidayCountController::class)->only(['edit', 'store', 'show']);

        Route::resource('contract-configuration', ContractConfigurationController::class)->only(['index', 'store']);

        Route::resource('employee-access', EmployeeAccessController::class)->only(['create']);

        Route::resource('responsible-persons', ResponsiblePersonController::class)->except(['edit']);

        Route::controller(EmployeeController::class)->group(function () {

            Route::post('employee-function-salary-option', 'getFunctionSalaryToCreateEmployee');

            Route::get('employee/update-personal-details', 'updatePersonalDetails');

            Route::put('employee-update', 'updateEmployee');

        });

        Route::controller(EmployeeContractController::class)->group(function () {

            Route::post('get-active-contract-employees', 'getActiveContractEmployees');

        });

        Route::post('employee-availability/{user_id}', [AvailabilityController::class, 'createAvailability']);

        Route::get('/get-availability', [AvailabilityController::class, 'availableDateAndNOtAvailableDates']);

        Route::put('/update-availability/{id}', [AvailabilityController::class, 'updateAvailability']);

        Route::delete('/delete-availability', [AvailabilityController::class, 'deleteAvailability']);

        Route::post('company-additional-details', [CompanyController::class, 'storeAdditionalDetails']);

        Route::get('get-company-linked-functions', [SectorController::class, 'getCompanyLinkedFunctions']);

        Route::controller(ReasonController::class)->group(function () {

            Route::get('start-plan-reasons', 'getStartPlanReasons');

            Route::get('stop-plan-reasons', 'getStopPlanReasons');
        });
    });
});

<?php

declare(strict_types=1);

use App\Http\Controllers\Company\DashboardAccessController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\InitializeTenancy;
use App\Http\Middleware\SetActiveUser;

use App\Http\Controllers\Company\{
    CompanyController,
    LocationController,
    CostCenterController,
    WorkstationController,
    Absence\LeaveController,
    Absence\HolidayController,
    Absence\AbsenceController,
    Contract\ContractConfigurationController,
    Contract\CompanyContractTemplateController,
    EmployeeAvailabilityController,
};

use App\Http\Controllers\Holiday\{
    EmployeeHolidayCountController,
};

use App\Http\Controllers\Employee\{
    EmployeeController,
    EmployeeAccessController,
    EmployeeIdCardController,
    EmployeeCommuteController,
    EmployeeContractController,
    EmployeeBenefitsController,
    ResponsiblePersonController,
    EmployeeSignatureController,
    EmployeeInvitationController,
};

use App\Http\Controllers\{
    Sector\SectorController,
    Contract\ContractController,
    Parameter\ParameterController,
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

Route::middleware([SetActiveUser::class])->group(function () use ($integerRule) {

    Route::middleware([InitializeTenancy::class])->group(function () use ($integerRule) {

        Route::get('/testing-tenant', function () {
            return response()->json([
                'message' => 'Masterdata tenant'
            ]);
        });

        Route::controller(HolidayController::class)->group(function () {

            Route::resource('holidays', HolidayController::class)->except(['edit']);

            Route::post('holidays-change-reporting-manager', [HolidayController::class, 'changeHolidayManager']);

            Route::get('employee-holidays', [HolidayController::class, 'employeeHolidays']);

            Route::get('holidays-list/{status}', [HolidayController::class, 'index'])
                ->where(['status' => '(approve|cancel|pending|reject)']); # for managers flow

            Route::post('holidays-list-manager-mobile', [HolidayController::class, 'getAllHolidaysForMobile']);

            Route::post('holidays-status', 'updateHolidayStatus');

            Route::post('employee-apply-holidays-mobile', [HolidayController::class, 'store'])->name('employee-apply-holidays-mobile');

            Route::post('manager-add-employee-holiday-mobile', [HolidayController::class, 'store'])->name('manager-add-employee-holiday-mobile');

        });

        Route::controller(LeaveController::class)->group(function () {

            Route::resource('leaves', LeaveController::class)->except(['edit']);

            Route::post('leaves-change-reporting-manager', [LeaveController::class, 'changeLeaveManager']);

            Route::get('leaves-list/{status}', [LeaveController::class, 'index'])
                ->where(['status' => '(approve|pending)']); # to get leaves list

            Route::post('leaves-list-manager-mobile', [LeaveController::class, 'getAllLeavesForMobile']);

            Route::post('leaves-status', 'updateLeaveStatus');

            Route::post('add-leave', [LeaveController::class, 'addLeave'])->name('add-leave'); # add as manager

            Route::put('update-leave/{id}', [LeaveController::class, 'update'])->name('update-leave'); # add and update as manager

            Route::post('employee-shift-leave', [LeaveController::class, 'addLeave'])->name('employee-shift-leave'); # apply and update as employee

            Route::post('shift-leave', [LeaveController::class, 'addLeave'])->name('shift-leave'); # apply and update as employee
        });

        Route::controller(LocationController::class)->group(function () use ($integerRule) {

            Route::get('location-workstations/{location_id}', 'locationWorkstations')->where(['location_id' => $integerRule]);
            Route::get('get-locations-list', 'getLocationsList');

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
                'methods'    => ['index', 'showyteyyd', 'create', 'store', 'update', 'destroy']
            ],
            'company-contract-templates' => [
                'controller' => CompanyContractTemplateController::class,
                'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
            ],
            'employees'                  => [
                'controller' => EmployeeController::class,
                'methods'    => ['index', 'show', 'store', 'update', 'destroy']
            ],
            'employee-id-card'           => [
                'controller' => EmployeeIdCardController::class,
                'methods'    => ['store', 'show']
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
            'employee-invitations'       => [
                'controller' => EmployeeInvitationController::class,
                'methods'    => ['store']
            ],
        ];

        Route::controller(ContractController::class)->group(function () {

            Route::post('contracts', 'store');

            Route::post('employee-sign-plan-contract', 'employeeSignPlanContract');

            Route::get('get-employee-plan-contract/{plan_id}', 'getEmployeePlanContract');

            Route::get('get-employee-contracts/{employee_id}/{status}', 'index')
                ->where(['status' => '(signed|unsigned)']);

            Route::get('get-employee-documents/{employee_profile_id}', 'getEmployeeDocuments');

        });

        foreach ($resources as $uri => ['controller' => $controller, 'methods' => $methods]) {
            Route::resource($uri, $controller)->only($methods);
        }

        Route::resource('employee-holiday-count', EmployeeHolidayCountController::class)->only(['edit', 'store', 'show']);

        Route::resource('contract-configuration', ContractConfigurationController::class)->only(['index', 'store']);

        Route::resource('employee-access', EmployeeAccessController::class)->only(['show', 'update']);

        Route::resource('responsible-persons', ResponsiblePersonController::class)->except(['edit']);

        Route::resource('employee-signature', EmployeeSignatureController::class)->only(['store']);

        Route::get('employee-signature', [EmployeeSignatureController::class, 'show']);

        Route::get('responsible-persons-list', [ResponsiblePersonController::class, 'getResponsiblePersonList']);

        Route::get('employee-holiday-count-overview/{employee_profile_id}', [EmployeeHolidayCountController::class, 'getEmployeeHolidayCountOverview']);

        Route::controller(EmployeeController::class)->group(function () {

            Route::post('employee-function-salary-option', 'getFunctionSalaryToCreateEmployee');

            Route::get('employee/update-personal-details', 'updatePersonalDetails');

            Route::get('employee-list', 'getEmployeeList');

        });

        Route::controller(EmployeeContractController::class)->group(function () {

            Route::post('get-active-contract-employees', 'getActiveContractEmployees');

        });

        Route::post('company-additional-details', [CompanyController::class, 'storeAdditionalDetails']);

        Route::get('get-company-linked-functions', [SectorController::class, 'getCompanyLinkedFunctions']);

        Route::controller(ReasonController::class)->group(function () {

            Route::get('start-plan-reasons', 'getStartPlanReasons');

            Route::get('stop-plan-reasons', 'getStopPlanReasons');
        });

        Route::controller(EmployeeAvailabilityController::class)->group(function () {

            Route::get('get-employee-availability', 'getEmployeeAvailability')->name('get-employee-availability-manager');

            Route::post('get-employee-availability', 'getEmployeeAvailability')->name('get-employee-availability-manager');

            Route::post('get-weekly-availability', 'getWeeklyAvailability')->name('get-weekly-availability');

            Route::post('get-weekly-availability-for-employee', 'getWeeklyAvailabilityForEmployee')->name('get-weekly-availability-for-employee');

            Route::delete('availability', 'destroy')->name('delete-availability');
        });

        Route::get('get-manage-parameter-options', [ParameterController::class, 'getManageParameterOptions'])->name('get-manage-parameter-options');

        Route::post('get-company-parameters', [ParameterController::class, 'getCompanyParameters'])->name('get-company-parameters');

        Route::controller(AbsenceController::class)->group(function () {

            Route::post('get-absence-details-for-week', 'getAbsenceDetailsForWeek');

        });

        Route::put('update-company-parameter/{parameter_name}', [ParameterController::class, 'updateCompanyParameter'])->name('update-company-parameters');
        Route::get('get-company-employees', [EmployeeController::class, 'getCompanyEmployees']);
        Route::get('get-dashboard-access-key-for-company', [DashboardAccessController::class, 'getDashboardAccessKeyForCompany']);
        Route::get('get-dashboard-access-key-for-location/{location_id}', [DashboardAccessController::class, 'getDashboardAccessKeyForLocation']);
        Route::get('validate-company-dashboard-access-key/{access_key}', [DashboardAccessController::class, 'validateCompanyDashboardAccessKey']);
        Route::get('validate-location-dashboard-access-key/{access_key}', [DashboardAccessController::class, 'validateLocationDashboardAccessKey']);
        Route::delete('revoke-dashboard-access-key/{access_key}', [DashboardAccessController::class, 'revokeDashboardAccessKey']);
    });

    Route::controller(LeaveController::class)->group(function () {
    
        Route::post('employee-shift-leave', [LeaveController::class, 'addLeave'])->name('employee-shift-leave'); # apply and update as employee
    
        Route::get('responsible-persons-for-chat', [ResponsiblePersonController::class, 'getResponsiblePersonListForChat']);

    });
});


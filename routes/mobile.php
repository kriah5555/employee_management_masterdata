<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SetActiveUser;
use App\Http\Middleware\InitializeTenancy;

use App\Http\Controllers\Employee\{
    EmployeeMobileController,
};

Route::middleware([InitializeTenancy::class, SetActiveUser::class])->group(function () {

    Route::controller(EmployeeMobileController::class)->group(function () {

        Route::get('employee-list', 'getEmployeeList');

    });

});

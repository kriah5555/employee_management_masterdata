<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SetActiveUser;
use App\Http\Middleware\InitializeTenancy;


Route::middleware([InitializeTenancy::class, SetActiveUser::class])->group(function () {


});

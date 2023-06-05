<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/testing', function () {
  return response()->json([
      'message' => 'Test API.'
  ]);
});

Route::prefix('v1')->group(function () {
    // Routes for API version 1
    Route::get('users', 'UserController@index');
    // ...
});

Route::prefix('v2')->group(function () {
    // Routes for API version 2
    Route::get('users', 'UserController@indexV2');
    // ...
});
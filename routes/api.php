<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\LoginAPIController;
use App\Http\Controllers\API\LocationAPIController;
use App\Http\Controllers\API\BasecampAPIController;
use App\Http\Controllers\API\JobAPIController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/login-mobile', [LoginAPIController::class, 'loginMobile'])->name('login-mobile');

Route::group(['middleware' => ['jwt.auth']], function() {
    Route::post('/get-user', [LoginAPIController::class, 'getUser'])->name('get-user');
    Route::prefix('location')->name('location')->group(function () {
        Route::get('/get', [LocationAPIController::class, 'getLocation'])->name('get');
    });
    Route::prefix('basecamp')->name('basecamp')->group(function () {
        Route::get('/get', [BasecampAPIController::class, 'getBasecamp'])->name('get');
    });
    Route::prefix('job')->name('job')->group(function () {
        Route::get('/get', [JobAPIController::class, 'getJob'])->name('get');
    });
});

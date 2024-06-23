<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\WorkingOrderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [LoginController::class, 'login'])->name('login');
Route::post('actionlogin', [LoginController::class, 'actionlogin'])->name('actionlogin');

Route::get('home', [HomeController::class, 'index'])->name('home')->middleware('auth');
Route::get('actionlogout', [LoginController::class, 'actionlogout'])->name('actionlogout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/
Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

/*
|--------------------------------------------------------------------------
| FORMS
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| ADMIN PANEL
|--------------------------------------------------------------------------
*/

// STEFAN
    // WORKING ORDER
    Route::get('form/input/working-order', [WorkingOrderController::class, 'index'])->name('form/input/working-order')->middleware('auth');



// THESAR
    // ERMOPLOYEE
        // DASHBOARD
        Route::get('masters/employee/index', [EmployeeController::class, 'index'])->name('masters/employee/index')->middleware('auth');
        Route::post('master/employee/dashboard-data', [EmployeeController::class, 'data'])->name('master/employee/dashboard-data');

    // DEPARTMENT
        // DASHBOARD
        Route::get('masters/department/index', [DepartmentController::class, 'index'])->name('masters/department/index')->middleware('auth');
        Route::post('masters/department/dashboard-data', [DepartmentController::class, 'data'])->name('masters/department/dashboard-data')->middleware('auth');
        Route::get('masters/department/export-excel', [DepartmentController::class, 'exportExcel'])->name('masters/department/export-excel')->middleware('auth');
        Route::get('masters/department/import-excel', [DepartmentController::class, 'importExcel'])->name('masters/department/import-excel')->middleware('auth');
        Route::get('masters/department/create-new', [DepartmentController::class, 'createNew'])->name('masters/department/create-new')->middleware('auth');
        // FORM INPUT
        Route::post('masters/department/create-new/create', [DepartmentController::class, 'submitData'])->name('masters/department/create-new/create')->middleware('auth');
        // UPLOAD
        Route::get('masters/department/download-template', [DepartmentController::class, 'downloadDepartmentTemplate'])->name('masters/department/download-template')->middleware('auth');
        Route::post('masters/department/upload', [DepartmentController::class, 'uploadDepartment'])->name('masters/department/upload')->middleware('auth');
        Route::post('masters/department/display-upload', [DepartmentController::class, 'displayUploadDepartment'])->name('masters/department/display-upload')->middleware('auth');
    
    // LOCATION
        // DASNBOARD
        Route::get('masters/location/index', [LocationController::class, 'index'])->name('masters/location/index')->middleware('auth');

    // DEVICE
        // DASNBOARD
        Route::get('masters/device/index', [DeviceController::class, 'index'])->name('masters/device/index')->middleware('auth');
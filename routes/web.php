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
Route::post('actionlogout', [LoginController::class, 'actionlogout'])->name('actionlogout');

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
Route::get('form-input/working-order/index', [WorkingOrderController::class, 'index'])->name('form-input.working-order.index')->middleware('auth');
Route::get('form-input/working-order/create', [WorkingOrderController::class, 'createNew'])->name('form-input.working-order.create')->middleware('auth');
Route::post('form-input/working-order/dashboard-data', [WorkingOrderController::class, 'data'])->name('form-input/working-order/dashboard-data')->middleware('auth');
Route::post('form-input/working-order/create-new', [WorkingOrderController::class, 'submitData'])->name('form-input.working-order.create-new')->middleware('auth');

Route::get('masters/employee/create-new', [EmployeeController::class, 'createNew'])->name('masters/employee/create-new')->middleware('auth');
Route::post('masters/employee/create-new/create', [EmployeeController::class, 'submitData'])->name('masters/employee/create-new/create')->middleware('auth');

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
    Route::post('masters/department/display-upload', [DepartmentController::class, 'displayUpload'])->name('masters/department/display-upload')->middleware('auth');

// LOCATION
// DASNBOARD
Route::get('masters/location/index', [LocationController::class, 'index'])->name('masters/location/index')->middleware('auth');
Route::post('masters/location/dashboard-data', [LocationController::class, 'data'])->name('masters/location/dashboard-data')->middleware('auth');
Route::get('masters/location/export-excel', [LocationController::class, 'exportExcel'])->name('masters/location/export-excel')->middleware('auth');
Route::get('masters/location/import-excel', [LocationController::class, 'importExcel'])->name('masters/location/import-excel')->middleware('auth');
Route::get('masters/location/create-new', [LocationController::class, 'createNew'])->name('masters/location/create-new')->middleware('auth');
// FORM INPUT
Route::post('masters/location/create-new/create', [LocationController::class, 'submitData'])->name('masters/location/create-new/create')->middleware('auth');
// DEVICE
// DASNBOARD
Route::get('masters/device/index', [DeviceController::class, 'index'])->name('masters/device/index')->middleware('auth');
Route::get('masters/role/index', [RoleController::class, 'index'])->name('masters/role/index')->middleware('auth');
Route::get('masters/job/index', [JobController::class, 'index'])->name('masters/job/index')->middleware('auth');

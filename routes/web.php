<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\EmployeeController;
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
// WORKING ORDER
Route::get('input-working-order', [WorkingOrderController::class, 'index'])->name('input-working-order')->middleware('auth');

/*
|--------------------------------------------------------------------------
| ADMIN PANEL
|--------------------------------------------------------------------------
*/
// ERMOPLOYEE
Route::get('employee-data', [EmployeeController::class, 'index'])->name('employee-data')->middleware('auth');
Route::post('employee-datatable', [EmployeeController::class, 'data'])->name('employee-datatable');

// DEPARTMENT
Route::get('department-data', [DepartmentController::class, 'index'])->name('department-data')->middleware('auth');
Route::post('department-data/department-datatable', [DepartmentController::class, 'data'])->name('department-data/department-datatable')->middleware('auth');
Route::get('department-data/export_excel', [DepartmentController::class, 'exportExcel'])->name('department-data/export_excel')->middleware('auth');
Route::get('department-data/import-excel', [DepartmentController::class, 'importExcel'])->name('department-data/import-excel')->middleware('auth');
Route::get('department-data/download-department-template', [DepartmentController::class, 'downloadDepartmentTemplate'])->name('department-data/download-department-template')->middleware('auth');
Route::post('department-data/upload-department', [DepartmentController::class, 'uploadDepartment'])->name('department-data/upload-department')->middleware('auth');
Route::post('department-data/display-upload-department', [DepartmentController::class, 'displayUploadDepartment'])->name('department-data/display-upload-department')->middleware('auth');

Route::get('create-new-department', [DepartmentController::class, 'createNew'])->name('create-new-department')->middleware('auth');
Route::post('create-new-department/create', [DepartmentController::class, 'submitData'])->name('create-new-department/create')->middleware('auth');


Route::get('location-data', [LocationController::class, 'index'])->name('location-data')->middleware('auth');
Route::get('device-data', [DeviceController::class, 'index'])->name('device-data')->middleware('auth');
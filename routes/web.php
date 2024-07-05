<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\BasecampController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\AutorisationController;
use App\Http\Controllers\WorkingOrderController;
use App\Http\Controllers\DeviceCategoryController;

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
Route::post('profile-user', [ProfileController::class, 'index'])->name('profile-user');

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
Route::get('form-input/working-order/getwonumber', [WorkingOrderController::class, 'getWONumber'])->name('form-input.working-order.getwonumber')->middleware('auth');
Route::get('form-input/working-order/getjobcategory', [WorkingOrderController::class, 'getJobCategory'])->name('form-input.working-order.getjobcategory')->middleware('auth');
Route::get('form-input/working-order/getdevicemodel', [WorkingOrderController::class, 'getDeviceModel'])->name('form-input.working-order.getdevicemodel')->middleware('auth');
Route::get('form-input/working-order/getdevicecode', [WorkingOrderController::class, 'getDeviceCode'])->name('form-input.working-order.getdevicecode')->middleware('auth');
Route::get('form-input/working-order/getdisturbancecategory', [WorkingOrderController::class, 'getDisturbanceCategory'])->name('form-input.working-order.getdisturbancecategory')->middleware('auth');
Route::post('form-input/working-order/dashboard-data', [WorkingOrderController::class, 'data'])->name('form-input.working-order.dashboard-data')->middleware('auth');
Route::post('form-input/working-order/create-new', [WorkingOrderController::class, 'submitData'])->name('form-input.working-order.create-new')->middleware('auth');
Route::get('form-input/working-order/detail/{id}', [WorkingOrderController::class, 'detail'])->name('form-input.working-order.detail')->middleware('auth');

//APPROVAL
Route::get('form-input/approval/index', [ApprovalController::class, 'index'])->name('form-input.approval.index')->middleware('auth');
Route::post('form-input/approval/dashboard-data', [ApprovalController::class, 'data'])->name('form-input.approval.dashboard-data')->middleware('auth');
Route::get('form-input/approval/detail/{id}', [ApprovalController::class, 'detail'])->name('form-input.approval.detail')->middleware('auth');
Route::post('form-input/approval/approve', [ApprovalController::class, 'approve'])->name('form-input.approval.approve')->middleware('auth');
Route::post('form-input/approval/notapprove', [ApprovalController::class, 'notApprove'])->name('form-input.approval.notapprove')->middleware('auth');
Route::post('form-input/approval/cancel', [ApprovalController::class, 'cancel'])->name('form-input.approval.cancel')->middleware('auth');


// THESAR
// ERMOPLOYEE
Route::get('masters/employee/index', [EmployeeController::class, 'index'])->name('masters/employee/index')->middleware('auth');
Route::post('master/employee/dashboard-data', [EmployeeController::class, 'data'])->name('master/employee/dashboard-data');
Route::post('master/employee/delete-data', [EmployeeController::class, 'deleteData'])->name('master/employee/delete-data');
Route::get('masters/employee/detail-data/{id}', [EmployeeController::class, 'detailData'])->name('masters/employee/detail-data')->middleware('auth');
Route::get('masters/employee/create-new', [EmployeeController::class, 'createNew'])->name('masters/employee/create-new')->middleware('auth');
Route::post('masters/employee/create-new/create', [EmployeeController::class, 'submitData'])->name('masters/employee/create-new/create')->middleware('auth');

// DEPARTMENT
Route::get('masters/department/index', [DepartmentController::class, 'index'])->name('masters/department/index')->middleware('auth');
Route::post('masters/department/dashboard-data', [DepartmentController::class, 'data'])->name('masters/department/dashboard-data')->middleware('auth');
Route::get('masters/department/export-excel', [DepartmentController::class, 'exportExcel'])->name('masters/department/export-excel')->middleware('auth');
Route::get('masters/department/import-excel', [DepartmentController::class, 'importExcel'])->name('masters/department/import-excel')->middleware('auth');
Route::get('masters/department/create-new', [DepartmentController::class, 'createNew'])->name('masters/department/create-new')->middleware('auth');
Route::post('masters/department/create-new/create', [DepartmentController::class, 'submitData'])->name('masters/department/create-new/create')->middleware('auth');
Route::get('masters/department/download-template', [DepartmentController::class, 'downloadDepartmentTemplate'])->name('masters/department/download-template')->middleware('auth');
Route::post('masters/department/upload', [DepartmentController::class, 'uploadDepartment'])->name('masters/department/upload')->middleware('auth');
Route::post('masters/department/display-upload', [DepartmentController::class, 'displayUpload'])->name('masters/department/display-upload')->middleware('auth');
Route::post('masters/department/delete-data', [DepartmentController::class, 'deleteData'])->name('masters/department/delete-data');
Route::get('masters/department/detail-data/{id}', [DepartmentController::class, 'detailData'])->name('masters/department/detail-data')->middleware('auth');

// LOCATION
Route::get('masters/location/index', [LocationController::class, 'index'])->name('masters/location/index')->middleware('auth');
Route::post('masters/location/dashboard-data', [LocationController::class, 'data'])->name('masters/location/dashboard-data')->middleware('auth');
Route::get('masters/location/export-excel', [LocationController::class, 'exportExcel'])->name('masters/location/export-excel')->middleware('auth');
Route::get('masters/location/import-excel', [LocationController::class, 'importExcel'])->name('masters/location/import-excel')->middleware('auth');
Route::get('masters/location/create-new', [LocationController::class, 'createNew'])->name('masters/location/create-new')->middleware('auth');
Route::post('masters/location/create-new/create', [LocationController::class, 'submitData'])->name('masters/location/create-new/create')->middleware('auth');
Route::post('masters/location/delete-data', [LocationController::class, 'deleteData'])->name('masters/location/delete-data');
Route::get('masters/location/detail-data/{id}', [LocationController::class, 'detailData'])->name('masters/location/detail-data')->middleware('auth');

// DEVICE
Route::get('masters/device/index', [DeviceController::class, 'index'])->name('masters/device/index')->middleware('auth');
Route::post('masters/device/dashboard-data', [DeviceController::class, 'data'])->name('masters/device/dashboard-data')->middleware('auth');
Route::get('masters/device/create-new', [DeviceController::class, 'createNew'])->name('masters/device/create-new')->middleware('auth');
Route::post('masters/device/create-new/create', [DeviceController::class, 'submitData'])->name('masters/device/create-new/create')->middleware('auth');
Route::post('masters/device/delete-data', [DeviceController::class, 'deleteData'])->name('masters/device/delete-data');
Route::get('masters/device/detail-data/{id}', [DeviceController::class, 'detailData'])->name('masters/device/detail-data')->middleware('auth');

// ROLE
Route::get('masters/role/index', [RoleController::class, 'index'])->name('masters/role/index')->middleware('auth');

// BASECAMP
Route::get('masters/basecamp/index', [BasecampController::class, 'index'])->name('masters/basecamp/index')->middleware('auth');
Route::post('masters/basecamp/dashboard-data', [BasecampController::class, 'data'])->name('masters/basecamp/dashboard-data')->middleware('auth');
Route::get('masters/basecamp/create-new', [BasecampController::class, 'createNew'])->name('masters/basecamp/create-new')->middleware('auth');
Route::post('masters/basecamp/create-new/create', [BasecampController::class, 'submitData'])->name('masters/basecamp/create-new/create')->middleware('auth');
Route::post('masters/basecamp/delete-data', [BasecampController::class, 'deleteData'])->name('masters/basecamp/delete-data');
Route::get('masters/basecamp/detail-data/{id}', [BasecampController::class, 'detailData'])->name('masters/basecamp/detail-data')->middleware('auth');

// JOB
Route::get('masters/job/index', [JobController::class, 'index'])->name('masters/job/index')->middleware('auth');
Route::post('masters/job/dashboard-data', [JobController::class, 'data'])->name('masters/job/dashboard-data')->middleware('auth');
Route::get('masters/job/create-new', [JobController::class, 'createNew'])->name('masters/location/create-new')->middleware('auth');
Route::post('masters/job/create-new/create', [JobController::class, 'submitData'])->name('masters/job/create-new/create')->middleware('auth');
Route::post('masters/job/delete-data', [JobController::class, 'deleteData'])->name('masters/job/delete-data');
Route::get('masters/job/detail-data/{id}', [JobController::class, 'detailData'])->name('masters/job/detail-data')->middleware('auth');

// DEVICE CATEGORY
Route::get('masters/device-category/index', [DeviceCategoryController::class, 'index'])->name('masters/device-category/index')->middleware('auth');
Route::post('masters/device-category/dashboard-data', [DeviceCategoryController::class, 'data'])->name('masters/device-category/dashboard-data')->middleware('auth');
Route::get('masters/device-category/create-new', [DeviceCategoryController::class, 'createNew'])->name('masters/device-category/create-new')->middleware('auth');
Route::post('masters/device-category/create-new/create', [DeviceCategoryController::class, 'submitData'])->name('masters/device-category/create-new/create')->middleware('auth');
Route::post('masters/device-category/delete-data', [DeviceCategoryController::class, 'deleteData'])->name('masters/device-category/delete-data');
Route::get('masters/device-category/detail-data/{id}', [DeviceCategoryController::class, 'detailData'])->name('masters/device-category/detail-data')->middleware('auth');

// otorisasi
Route::get('masters/autorisation/index', [AutorisationController::class, 'index'])->name('masters/autorisation/index')->middleware('auth');



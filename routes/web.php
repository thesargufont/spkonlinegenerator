<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TrialController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\BasecampController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EngineerController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\AutorisationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\WorkingOrderController;
use App\Http\Controllers\DeviceCategoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DetailChartController;


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

Route::get('profile-user', [ProfileController::class, 'index'])->name('profile-user');
Route::post('masters/profile-user/signature', [ProfileController::class, 'newSignature'])->name('masters/profile-user/signature')->middleware('auth');
Route::post('masters/profile-user/password', [ProfileController::class, 'newPassword'])->name('masters/profile-user/password')->middleware('auth');

Route::get('home', [HomeController::class, 'index'])->name('home')->middleware('auth');
Route::post('layout/get-notif', [HomeController::class, 'getNotif'])->name('layout/get-notif')->middleware('auth');

Route::post('dashboard/dashboard-data', [HomeController::class, 'data'])->name('dashboard.dashboard-data')->middleware('auth');

Route::get('actionlogout', [LoginController::class, 'actionlogout'])->name('actionlogout')->middleware('auth');

Route::get('notifications', [NotificationController::class, 'index'])->name('notifications')->middleware('auth');
Route::post('notifications/dashboard-data', [NotificationController::class, 'data'])->name('notifications/dashboard-data')->middleware('auth');

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
Route::get('form-input/working-order/cekdetail/{id}', [WorkingOrderController::class, 'checkDetail'])->name('form-input.working-order.cekdetail')->middleware('auth');
Route::get('form-input/working-order/detail/{id}', [WorkingOrderController::class, 'detail'])->name('form-input.working-order.detail')->middleware('auth');

//APPROVAL
Route::get('form-input/approval/index', [ApprovalController::class, 'index'])->name('form-input.approval.index')->middleware('auth');
Route::get('form-input/approval/getspknumber', [ApprovalController::class, 'getSPKNumber'])->name('form-input.approval.getspknumber')->middleware('auth');
Route::post('form-input/approval/dashboard-data', [ApprovalController::class, 'data'])->name('form-input.approval.dashboard-data')->middleware('auth');
Route::get('form-input/approval/detail/{id}', [ApprovalController::class, 'detail'])->name('form-input.approval.detail')->middleware('auth');
Route::post('form-input/approval/approve', [ApprovalController::class, 'approve'])->name('form-input.approval.approve')->middleware('auth');
Route::post('form-input/approval/notapprove', [ApprovalController::class, 'notApprove'])->name('form-input.approval.notapprove')->middleware('auth');
Route::post('form-input/approval/cancel', [ApprovalController::class, 'cancel'])->name('form-input.approval.cancel')->middleware('auth');
Route::get('form-input/approval/download/{id}', [ApprovalController::class, 'generatePDF'])->name('form-input.approval.download')->middleware('auth');

//ENGINEER
Route::get('form-input/engineer/index', [EngineerController::class, 'index'])->name('form-input.engineer.index')->middleware('auth');
Route::post('form-input/engineer/dashboard-data', [EngineerController::class, 'data'])->name('form-input.engineer.dashboard-data')->middleware('auth');
Route::get('form-input/engineer/detail/{id}', [EngineerController::class, 'detail'])->name('form-input.engineer.detail')->middleware('auth');
Route::post('form-input/engineer/submit', [EngineerController::class, 'submit'])->name('form-input.engineer.submit')->middleware('auth');
Route::get('form-input/engineer/download/{id}', [EngineerController::class, 'generateWord'])->name('form-input.engineer.download')->middleware('auth');
Route::get('form-input/engineer/downloadTrial/{id}', [TrialController::class, 'generatePDFEngineer'])->name('form-input.engineer.downloadTrial')->middleware('auth');

// THESAR
// ERMOPLOYEE
Route::get('masters/employee/index', [EmployeeController::class, 'index'])->name('masters/employee/index')->middleware('auth');
Route::post('master/employee/dashboard-data', [EmployeeController::class, 'data'])->name('master/employee/dashboard-data');
Route::post('master/employee/delete-data', [EmployeeController::class, 'deleteData'])->name('master/employee/delete-data');
Route::get('masters/employee/detail-data/{id}', [EmployeeController::class, 'detailData'])->name('masters/employee/detail-data')->middleware('auth');
Route::get('masters/employee/import-excel', [EmployeeController::class, 'importExcel'])->name('masters/employee/import-excel')->middleware('auth');
Route::get('masters/employee/download-template', [EmployeeController::class, 'downloadDepartmentTemplate'])->name('masters/employee/download-template')->middleware('auth');
Route::post('masters/employee/upload', [EmployeeController::class, 'uploadDepartment'])->name('masters/employee/upload')->middleware('auth');
Route::post('masters/employee/display-upload', [EmployeeController::class, 'displayUpload'])->name('masters/employee/display-upload')->middleware('auth');
Route::post('masters/employee/save-upload', [EmployeeController::class, 'saveUpload'])->name('masters/employee/save-upload')->middleware('auth');
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
Route::post('masters/department/save-upload', [DepartmentController::class, 'saveUpload'])->name('masters/department/save-upload')->middleware('auth');
Route::post('masters/department/delete-data', [DepartmentController::class, 'deleteData'])->name('masters/department/delete-data');
Route::get('masters/department/detail-data/{id}', [DepartmentController::class, 'detailData'])->name('masters/department/detail-data')->middleware('auth');

// LOCATION
Route::get('masters/location/index', [LocationController::class, 'index'])->name('masters/location/index')->middleware('auth');
Route::post('masters/location/dashboard-data', [LocationController::class, 'data'])->name('masters/location/dashboard-data')->middleware('auth');
Route::get('masters/location/export-excel', [LocationController::class, 'exportExcel'])->name('masters/location/export-excel')->middleware('auth');
Route::get('masters/location/import-excel', [LocationController::class, 'importExcel'])->name('masters/location/import-excel')->middleware('auth');
Route::get('masters/location/create-new', [LocationController::class, 'createNew'])->name('masters/location/create-new')->middleware('auth');
Route::post('masters/location/create-new/create', [LocationController::class, 'submitData'])->name('masters/location/create-new/create')->middleware('auth');
Route::get('masters/location/download-template', [LocationController::class, 'downloadDepartmentTemplate'])->name('masters/location/download-template')->middleware('auth');
Route::post('masters/location/upload', [LocationController::class, 'uploadDepartment'])->name('masters/location/upload')->middleware('auth');
Route::post('masters/location/display-upload', [LocationController::class, 'displayUpload'])->name('masters/location/display-upload')->middleware('auth');
Route::post('masters/location/save-upload', [LocationController::class, 'saveUpload'])->name('masters/location/save-upload')->middleware('auth');
Route::post('masters/location/delete-data', [LocationController::class, 'deleteData'])->name('masters/location/delete-data');
Route::get('masters/location/detail-data/{id}', [LocationController::class, 'detailData'])->name('masters/location/detail-data')->middleware('auth');

// DEVICE
Route::get('masters/device/index', [DeviceController::class, 'index'])->name('masters/device/index')->middleware('auth');
Route::post('masters/device/dashboard-data', [DeviceController::class, 'data'])->name('masters/device/dashboard-data')->middleware('auth');
Route::get('masters/device/create-new', [DeviceController::class, 'createNew'])->name('masters/device/create-new')->middleware('auth');
Route::post('masters/device/create-new/create', [DeviceController::class, 'submitData'])->name('masters/device/create-new/create')->middleware('auth');
Route::get('masters/device/import-excel', [DeviceController::class, 'importExcel'])->name('masters/device/import-excel')->middleware('auth');
Route::get('masters/device/download-template', [DeviceController::class, 'downloadDepartmentTemplate'])->name('masters/device/download-template')->middleware('auth');
Route::post('masters/device/upload', [DeviceController::class, 'uploadDepartment'])->name('masters/device/upload')->middleware('auth');
Route::post('masters/device/display-upload', [DeviceController::class, 'displayUpload'])->name('masters/device/display-upload')->middleware('auth');
Route::post('masters/device/save-upload', [DeviceController::class, 'saveUpload'])->name('masters/device/save-upload')->middleware('auth');
Route::post('masters/device/delete-data', [DeviceController::class, 'deleteData'])->name('masters/device/delete-data');
Route::get('masters/device/detail-data/{id}', [DeviceController::class, 'detailData'])->name('masters/device/detail-data')->middleware('auth');
Route::get('masters/device/edit-data/{id}', [DeviceController::class, 'editData'])->name('masters/device/edit-data')->middleware('auth');
Route::post('masters/device/update-data', [DeviceController::class, 'updateData'])->name('masters.device.update-data')->middleware('auth');

// ROLE
Route::get('masters/role/index', [RoleController::class, 'index'])->name('masters/role/index')->middleware('auth');

// BASECAMP
Route::get('masters/basecamp/index', [BasecampController::class, 'index'])->name('masters/basecamp/index')->middleware('auth');
Route::post('masters/basecamp/dashboard-data', [BasecampController::class, 'data'])->name('masters/basecamp/dashboard-data')->middleware('auth');
Route::get('masters/basecamp/create-new', [BasecampController::class, 'createNew'])->name('masters/basecamp/create-new')->middleware('auth');
Route::post('masters/basecamp/create-new/create', [BasecampController::class, 'submitData'])->name('masters/basecamp/create-new/create')->middleware('auth');
Route::get('masters/basecamp/import-excel', [BasecampController::class, 'importExcel'])->name('masters/basecamp/import-excel')->middleware('auth');
Route::get('masters/basecamp/download-template', [BasecampController::class, 'downloadDepartmentTemplate'])->name('masters/basecamp/download-template')->middleware('auth');
Route::post('masters/basecamp/upload', [BasecampController::class, 'uploadDepartment'])->name('masters/basecamp/upload')->middleware('auth');
Route::post('masters/basecamp/display-upload', [BasecampController::class, 'displayUpload'])->name('masters/basecamp/display-upload')->middleware('auth');
Route::post('masters/basecamp/save-upload', [BasecampController::class, 'saveUpload'])->name('masters/basecamp/save-upload')->middleware('auth');
Route::post('masters/basecamp/delete-data', [BasecampController::class, 'deleteData'])->name('masters/basecamp/delete-data');
Route::get('masters/basecamp/detail-data/{id}', [BasecampController::class, 'detailData'])->name('masters/basecamp/detail-data')->middleware('auth');

// JOB
Route::get('masters/job/index', [JobController::class, 'index'])->name('masters/job/index')->middleware('auth');
Route::post('masters/job/dashboard-data', [JobController::class, 'data'])->name('masters/job/dashboard-data')->middleware('auth');
Route::get('masters/job/create-new', [JobController::class, 'createNew'])->name('masters/location/create-new')->middleware('auth');
Route::post('masters/job/create-new/create', [JobController::class, 'submitData'])->name('masters/job/create-new/create')->middleware('auth');
Route::get('masters/job/import-excel', [JobController::class, 'importExcel'])->name('masters/job/import-excel')->middleware('auth');
Route::get('masters/job/download-template', [JobController::class, 'downloadDepartmentTemplate'])->name('masters/job/download-template')->middleware('auth');
Route::post('masters/job/upload', [JobController::class, 'uploadDepartment'])->name('masters/job/upload')->middleware('auth');
Route::post('masters/job/display-upload', [JobController::class, 'displayUpload'])->name('masters/job/display-upload')->middleware('auth');
Route::post('masters/job/save-upload', [JobController::class, 'saveUpload'])->name('masters/job/save-upload')->middleware('auth');
Route::post('masters/job/delete-data', [JobController::class, 'deleteData'])->name('masters/job/delete-data');
Route::get('masters/job/detail-data/{id}', [JobController::class, 'detailData'])->name('masters/job/detail-data')->middleware('auth');

// DEVICE CATEGORY
Route::get('masters/device-category/index', [DeviceCategoryController::class, 'index'])->name('masters/device-category/index')->middleware('auth');
Route::post('masters/device-category/dashboard-data', [DeviceCategoryController::class, 'data'])->name('masters/device-category/dashboard-data')->middleware('auth');
Route::get('masters/device-category/create-new', [DeviceCategoryController::class, 'createNew'])->name('masters/device-category/create-new')->middleware('auth');
Route::post('masters/device-category/create-new/create', [DeviceCategoryController::class, 'submitData'])->name('masters/device-category/create-new/create')->middleware('auth');
Route::get('masters/device-category/import-excel', [DeviceCategoryController::class, 'importExcel'])->name('masters/device-category/import-excel')->middleware('auth');
Route::get('masters/device-category/download-template', [DeviceCategoryController::class, 'downloadDepartmentTemplate'])->name('masters/device-category/download-template')->middleware('auth');
Route::post('masters/device-category/upload', [DeviceCategoryController::class, 'uploadDepartment'])->name('masters/device-category/upload')->middleware('auth');
Route::post('masters/device-category/display-upload', [DeviceCategoryController::class, 'displayUpload'])->name('masters/device-category/display-upload')->middleware('auth');
Route::post('masters/device-category/save-upload', [DeviceCategoryController::class, 'saveUpload'])->name('masters/device-category/save-upload')->middleware('auth');
Route::post('masters/device-category/delete-data', [DeviceCategoryController::class, 'deleteData'])->name('masters/device-category/delete-data');
Route::get('masters/device-category/detail-data/{id}', [DeviceCategoryController::class, 'detailData'])->name('masters/device-category/detail-data')->middleware('auth');

// otorisasi
Route::get('masters/autorisation/index', [AutorisationController::class, 'index'])->name('masters/autorisation/index')->middleware('auth');
Route::post('masters/autorisation/dashboard-data', [AutorisationController::class, 'data'])->name('masters/autorisation/dashboard-data')->middleware('auth');
Route::get('masters/autorisation/create-new', [AutorisationController::class, 'createNew'])->name('masters/autorisation/create-new')->middleware('auth');
Route::post('masters/autorisation/create-new/create', [AutorisationController::class, 'submitData'])->name('masters/autorisation/create-new/create')->middleware('auth');
Route::get('masters/autorisation/import-excel', [AutorisationController::class, 'importExcel'])->name('masters/autorisation/import-excel')->middleware('auth');
Route::get('masters/autorisation/download-template', [AutorisationController::class, 'downloadDepartmentTemplate'])->name('masters/autorisation/download-template')->middleware('auth');
Route::post('masters/autorisation/upload', [AutorisationController::class, 'uploadDepartment'])->name('masters/autorisation/upload')->middleware('auth');
Route::post('masters/autorisation/display-upload', [AutorisationController::class, 'displayUpload'])->name('masters/autorisation/display-upload')->middleware('auth');
Route::post('masters/autorisation/save-upload', [AutorisationController::class, 'saveUpload'])->name('masters/autorisation/save-upload')->middleware('auth');
Route::post('masters/autorisation/delete-data', [AutorisationController::class, 'deleteData'])->name('masters/autorisation/delete-data')->middleware('auth');
Route::get('masters/autorisation/detail-data/{id}', [AutorisationController::class, 'detailData'])->name('masters/autorisation/detail-data')->middleware('auth');
//Route::get('masters/autorisation/edit-data/{id}', [AutorisationController::class, 'editData'])->name('masters/autorisation/edit-data')->middleware('auth');
//Route::post('masters/autorisation/update-data', [AutorisationController::class, 'updateData'])->name('masters.autorisation.update-data')->middleware('auth');


// VINCENT
// REPORT
Route::get('reports/index', [ReportController::class, 'index'])->name('reports.index')->middleware('auth');
Route::get('reports/getDataFilter', [ReportController::class, 'getDataFilter'])->name('reports.getDataFilter')->middleware('auth');
Route::post('reports/getDataTable', [ReportController::class, 'dataTable'])->name('reports.getDataTable')->middleware('auth');
Route::get('report/export', [ReportController::class, 'downloadXLSX'])->name('report.export');
Route::get('report/cekdetail/{id}', [ReportController::class, 'checkDetail'])->name('report.cekdetail')->middleware('auth');
Route::get('report/detail/{id}', [ReportController::class, 'detail'])->name('report.detail')->middleware('auth');

// DASHBOARD TO WORKING ORDER
Route::get('form-input/working-order/show/{label}/{label_type}', [WorkingOrderController::class, 'show'])->name('form-input.working-order.show')->middleware('auth');

// DETAIL CHART
Route::get('detail_chart/index/{label}/{label_type}', [DetailChartController::class, 'index'])->name('detail_chart.index')->middleware('auth');
Route::post('detail_chart/dashboard-data', [DetailChartController::class, 'data'])->name('detail_chart.dashboard-data')->middleware('auth');
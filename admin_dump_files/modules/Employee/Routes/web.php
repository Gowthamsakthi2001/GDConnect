<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Http\Controllers\DepartmentController;
use Modules\Employee\Http\Controllers\DriverController;
use Modules\Employee\Http\Controllers\DriverPerformanceController;
use Modules\Employee\Http\Controllers\EmployeeController;
use Modules\Employee\Http\Controllers\LicenseTypeController;
use Modules\Employee\Http\Controllers\PositionController;

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

Route::prefix('admin')->as('admin.')->middleware('auth')->group(function () {

    Route::prefix('/position')->as('position.')->group(function () {
        Route::resource('/', PositionController::class)->except(['show'])->parameter('', 'position');
    });

    Route::prefix('/department')->as('department.')->group(function () {
        Route::resource('/', DepartmentController::class)->except(['show'])->parameter('', 'department');
    });

    Route::prefix('/license-type')->as('license_type.')->group(function () {
        Route::resource('/', LicenseTypeController::class)->except(['show'])->parameter('', 'license_type');
    });

    Route::prefix('/employee')->as('employee.')->group(function () {
        Route::resource('/', EmployeeController::class)->except(['show'])->parameter('', 'employee');
    });

    Route::prefix('/driver')->as('driver.')->group(function () {
        Route::prefix('/performance')->as('performance.')->group(function () {
            Route::resource('/', DriverPerformanceController::class)->except(['show'])->parameter('', 'performance');
        });

        Route::resource('/', DriverController::class)->except(['show'])->parameter('', 'driver');
    });
});

Route::prefix('admin/Green-Drive-Ev/employee-management')->as('admin.Green-Drive-Ev.employee_management.')->controller(EmployeeController::class)->middleware('auth')->group(function () {
    Route::get('/list', 'employee_lists')->name('employee_list');
    Route::get('/create', 'employee_create')->name('employee_create');
    Route::get('/job-status-update', 'job_status_update')->name('job_status_update');
    Route::get('/verify-list/{type}', 'export_employee_verify_list')->name('export_employee_verify_list');
    Route::get('login-logs/lists', 'employee_logs')->name('employee_logs');
    Route::get('login-logs/list/{id}', 'single_employee_log')->name('single_employee_log');
    Route::get('logs/export', 'export_employee_log_list')->name('export_employee_log_list');

});

<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\Http\Controllers\ReportController;

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

Route::prefix('admin/report')->as('admin.report.')->middleware('auth')->group(function () {
    Route::get('/live-orders', [ReportController::class, 'live_orders'])->name('live_orders');
    Route::get('/live-order-view/{id}', [ReportController::class, 'live_order_view'])->name('live_order_view');
    Route::get('/employee', [ReportController::class, 'employee_report'])->name('employee');
    Route::get('/driver', [ReportController::class, 'driver_report'])->name('driver');
    Route::get('/purchase', [ReportController::class, 'purchase_report'])->name('purchase');
    Route::get('/expense', [ReportController::class, 'expense_report'])->name('expense');
    Route::get('/maintenance', [ReportController::class, 'maintenance_report'])->name('maintenance');

    Route::prefix('admin/vehicle')->as('vehicle.')->group(function () {
        Route::get('/', [ReportController::class, 'vehicle_report'])->name('index');
        Route::get('/requisition', [ReportController::class, 'vehicle_requisition_report'])->name('requisition');
    });

    Route::prefix('admin/pickdrop')->as('pickdrop.')->group(function () {
        Route::get('/requisition', [ReportController::class, 'pickdrop_requisition_report'])->name('requisition');
    });

    Route::prefix('admin/refuel')->as('refuel.')->group(function () {
        Route::get('/requisition', [ReportController::class, 'refuel_requisition_report'])->name('requisition');
    });
});

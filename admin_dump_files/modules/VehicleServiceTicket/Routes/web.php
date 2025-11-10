<?php

use Illuminate\Support\Facades\Route;
use Modules\VehicleServiceTicket\Http\Controllers\VehicleServiceTicketController;

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

// Route::group([], function () {
//     Route::resource('vehicleserviceticket', VehicleServiceTicketController::class)->names('vehicleserviceticket');
// });


Route::prefix('/web/ticket-portal/')->as('admin.web.vehicle-ticket.')->controller(VehicleServiceTicketController::class)->group(function () {
    Route::get('/create-ticket', 'create_web_ticket')->name('create');
    Route::post('/store-ticket', 'new_ticket_create')->name('store');
});

Route::prefix('/customer/ticket-portal/')
    ->as('auth.customer.vehicle-ticket.')
    ->controller(VehicleServiceTicketController::class)
    ->middleware('auth:customer')
    ->group(function () {
        Route::get('/create-ticket', 'create_user_ticket')->name('create');
        Route::post('/store-ticket', 'new_ticket_create')->name('store');
    });


Route::prefix('/admin/ticket-management/')->as('admin.ticket_management.')->controller(VehicleServiceTicketController::class)->middleware('auth')->group(function () {
    Route::get('/list/{type}', 'ticket_list')->name('list');
    Route::get('/view-ticket/{id}', 'view_ticket')->name('view');
     Route::get('/export-ticket-management', 'export_ticket')->name('export_ticket');
});


Route::get('/admin/web/ticket-portal/submitted', [VehicleServiceTicketController::class,'form_subimit_welcome'])->name('vehicle.ticket.submitted');
<?php

use Illuminate\Support\Facades\Route;

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


Route::prefix('admin/Green-Drive-Ev/clients')->as('admin.Green-Drive-Ev.clients.')->controller(ClientsController::class)->middleware('auth')->group(function () { 
    Route::get('delete/{id}', 'delete_client')->name('delete');
    Route::get('edit/{id}', 'edit_client')->name('edit');
    Route::post('store', 'store')->name('store');  
    Route::post('update/{id}', 'update')->name('update');  
    Route::get('list', 'list')->name('list');      
    Route::get('create', 'index')->name('create');  // Lists all cities

});

Route::prefix('admin/Green-Drive-Ev/clients/hub')->as('admin.Green-Drive-Ev.clients.hub.')->controller(ClientsController::class)->middleware('auth')->group(function () { 
    Route::get('create', 'hub_create')->name('create');  //list of route for Hub
    Route::post('store', 'hub_store')->name('store');
    Route::get('list', 'hub_list')->name('list');  
    Route::get('status/{id}/{status}', 'hub_change_status')->name('status'); 
    Route::get('delete/{id}', 'delete_client_hub')->name('delete');
    Route::get('get-hub/{id}', 'get_client_hub')->name('get_client_hub');
    Route::post('update', 'client_hub_update')->name('update');  
});

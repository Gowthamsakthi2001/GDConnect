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

// Route::group([], function () {
//     Route::resource('riderbackgroundverification', RiderBackgroundVerificationController::class)->names('riderbackgroundverification');
// });


Route::prefix('admin/Green-Drive-Ev')->as('admin.Green-Drive-Ev.background_verification.')->controller(RiderBackgroundVerificationController::class)->middleware('auth')->group(function () {
    // Route::get('/recruiters', 'recruiter_list')->name('index');
    // Route::get('/recruiter/preview/{id}', 'recruiter_preview')->name('recruiter.preview');
});
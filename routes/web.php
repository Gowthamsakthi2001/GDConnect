<?php

use App\Http\Controllers\ArtisanHttpController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GetZoneController;
use App\Http\Controllers\LocalizationController;
use Modules\VehicleManagement\Http\Controllers\MobitraApiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
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
Route::get('/', [DashboardController::class, 'redirectToDashboard'])->name('home');
Route::post('rider-onboard-filter/data', [DashboardController::class, 'RiderOnboardfilterData'])->name('RiderOnboardfilter.data')->middleware('auth');
Route::post('filter/data', [DashboardController::class, 'filterData'])->name('filter.data')->middleware('auth'); //updated by Gowtham.S
Route::post('filter/hrdata', [DashboardController::class, 'filterhrData'])->name('filter.hrdata')->middleware('auth');
Route::get('/get-city-list/{state_id}', [DashboardController::class, 'getCities'])->name('global.get_cities')->middleware('auth');//updated by Gowtham.S
Route::get('/get-zone-list/{city_id}', [GetZoneController::class, 'getZones'])->name('global.get_zones');//updated by Gowtham.s - Zone Map
Route::get('/admin', [DashboardController::class, 'redirectToDashboard'])->middleware('auth');
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard')->middleware('auth');
Route::get('/admin/tracking', [MobitraApiController::class, 'mobitra_tracking'])->name('admin.tracking')->middleware('auth');
Route::get('lang/{lang}', [LocalizationController::class, 'switchLang'])->name('lang.switch');
Route::get('dev/artisan-http/storage-link', [ArtisanHttpController::class, 'storageLink'])->name('artisan-http.storage-link');


Route::get('/run-storage-link', function () {
    // Run the storage:link command using Artisan
    Artisan::call('storage:link');

    // Return a response after the command is executed
    return response()->json([
        'status' => 'Storage link created successfully!'
    ]);
});

Route::get('/deliveryman_suspend.php', function () {
    include base_path('deliveryman_suspend.php');
});

Route::get('/set-active-module/{id}', function($id) { //updated by Gowtham.S
    session(['active_module_id' => $id]);
    return response()->json(['success' => true]);
});


Route::get('/every_minute_autoload.php', function () {
    return response()->file(base_path('every_minute_autoload.php'));
});



Route::get('/log/export/', function (Request $request) { //without middleware create this route
    $from_date = $request->query('from_date');
    $to_date = $request->query('to_date');
    $id = $request->query('id');

    if (empty($from_date) || empty($to_date) || empty($id)) {
        return response()->json(['status' => false, 'message' => 'From date, to date, and ID fields are required']);
    }

    $query = "
        SELECT
            ev_delivery_man_logs.id,
            ev_delivery_man_logs.user_id,
            ev_delivery_man_logs.user_type,
            ev_delivery_man_logs.punched_in,
            ev_delivery_man_logs.punched_out,
            DATE(ev_delivery_man_logs.punched_in) AS date,
            TIME(ev_delivery_man_logs.punched_in) AS in_time,
            TIME(ev_delivery_man_logs.punched_out) AS out_time,
            CONCAT(
                TIMESTAMPDIFF(HOUR, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out), ' hours ',
                MOD(TIMESTAMPDIFF(MINUTE, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out), 60), ' minutes'
            ) AS total_time
        FROM ev_delivery_man_logs
        WHERE ev_delivery_man_logs.user_id = ?
        AND ev_delivery_man_logs.punched_in BETWEEN ? AND ?;
    ";

    $reports = DB::select($query, [$id, $from_date, $to_date]);

    return view('exports.user_log_export', compact('reports'));
});











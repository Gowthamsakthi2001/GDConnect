    <?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\EvDeliveryManController;
use App\Http\Controllers\Api\V1\EvDeliveryManAuthController;
use App\Http\Controllers\Api\V1\TimeManageController;
use App\Http\Controllers\Api\V1\AssetManagementContoller;
use App\Http\Controllers\Api\V1\EvLeaveManagementContoller;
use App\Http\Controllers\Api\V1\LiveOrderController;
use App\Http\Controllers\Api\V1\VehicleServiceTicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Grouping API routes for the Green Drive EV application
Route::group(['prefix' => 'v1', 'as' => 'v1.'], function () {
    
    Route::group(['prefix' => 'Green-Drive-Ev', 'as' => 'Green-Drive-Ev.'], function () {
        // Deliveryman Routes
        
        Route::get('app-validation/{app_mode}/{app_version}', [EvDeliveryManController::class, 'user_app_validation']);
        
         Route::group(['prefix' => 'Deliveryman', 'as' => 'Deliveryman.'], function () {
            
            Route::get('source', [EvDeliveryManController::class, 'getEvLeadSources'])->name('source');
            Route::get('city', [EvDeliveryManController::class, 'getEvCities'])->name('city');
            Route::post('get-area', [EvDeliveryManController::class, 'getArea'])->name('get-area');
            Route::get('list', [EvDeliveryManController::class, 'list'])->name('list');
            Route::get('profile/{id}', [EvDeliveryManController::class, 'profile'])->name('profile');
            Route::get('rider-type', [EvDeliveryManController::class, 'RiderType'])->name('rider-type');
            Route::get('zones', [EvDeliveryManController::class, 'zones'])->name('zones');
            Route::post('punch-out', [TimeManageController::class, 'punchOut'])->name('punch-out');
            Route::post('punch-in', [TimeManageController::class, 'punchIn'])->name('punch-in');
            Route::get('deliveryman-reports/{id}', [TimeManageController::class, 'deliveryman_reports'])->name('deliveryman-reports');
            Route::get('client-basedon-/dm-reports/{id}', [TimeManageController::class, 'client_basedon_dm_reports']);
            // Route::get('download', [EvDeliveryManController::class, 'downloadImage'])->name('download');
            Route::get('client/{id}', [EvDeliveryManController::class, 'client'])->name('client');
            Route::post('document-validation', [EvDeliveryManController::class, 'fetchChallanInfo']);
            Route::post('document-validation-test', [EvDeliveryManController::class, 'api_club_document_verify']);
            Route::get('log-info/{id}/{fliter_date}',[EvDeliveryManController::class, 'log_info']);
            
            Route::post('leave-request', [EvLeaveManagementContoller::class, 'new_leave_request']); //updated by Gowtham.s
            Route::post('permission-request', [EvLeaveManagementContoller::class, 'permission_new_request']);
            Route::get('leave-summary/{id}', [EvLeaveManagementContoller::class, 'leave_count_summary']);
            Route::get('leave-request-list/{id}', [EvLeaveManagementContoller::class, 'deliveryman_approve_reject_list']);
            Route::get('leave-pending-list/{id}', [EvLeaveManagementContoller::class, 'deliveryman_leave_pending_list']);
            Route::get('get-leave-dates/{id}', [EvLeaveManagementContoller::class, 'filter_leave_present']);
            Route::get('type-list-test', [EvLeaveManagementContoller::class, 'leave_type_list_test']);
        });
        
    
        // Deliveryman Authentication Routes
        Route::group(['prefix' => 'Deliveryman-auth', 'as' => 'Deliveryman-auth.'], function () {
            Route::post('store', [EvDeliveryManAuthController::class, 'store'])->name('store');
            Route::post('test-mail', [EvDeliveryManAuthController::class, 'registerRiderMail'])->name('test_mail');
            Route::post('check-process', [EvDeliveryManAuthController::class, 'check_process'])->name('check-process');
            Route::post('alternative-send-otp', [EvDeliveryManAuthController::class, 'alternative_send_otp'])->name('alternative_send_otp');
            Route::post('referal-person/send-otp', [EvDeliveryManAuthController::class, 'referal_person_send_otp'])->name('referalperson_send_otp');
            Route::post('register', [EvDeliveryManAuthController::class, 'register'])->name('register');
            Route::post('profile-update', [EvDeliveryManAuthController::class, 'profile_update'])->name('profile_update');
            Route::post('otp-verification', [EvDeliveryManAuthController::class, 'otp_verification'])->name('otp-verification');
            Route::post('approve-status', [EvDeliveryManAuthController::class, 'approved_status'])->name('approve-status');
            Route::post('rider_status', [EvDeliveryManAuthController::class, 'rider_status'])->name('rider_status');
            Route::get('active_inactive/{id}', [EvDeliveryManAuthController::class, 'active_inactive'])->name('active_inactive');
            Route::post('store/adhaar-no',[EvDeliveryManAuthController::class, 'store_adhaar_no']);
            Route::post('rider-info',[EvDeliveryManAuthController::class, 'rider_info'])->name('rider_info');
            Route::post('test-id-generate',[EvDeliveryManAuthController::class, 'test_id_generate']);//updated by Gowtham.s
            Route::get('adhoc-permanent-update',[EvDeliveryManAuthController::class, 'adhoc_permanent_autoload']);
            
        });
        
        Route::group(['prefix' => 'assetmanage', 'as' => 'assetmanage.'], function () {
            Route::get('asset/{Chassis_Serial_No}', [AssetManagementContoller::class, 'assetmanager'])->name('asset');
        });
        
        Route::group(['prefix' => 'leave-management', 'as' => 'leavemanagement.'], function () {
            Route::get('type-list', [EvLeaveManagementContoller::class, 'leave_type_list']);
             
        });
        
        Route::group(['prefix' => 'vehicle-service-ticket', 'as' => 'vehicle_ticket.'], function () {
            Route::post('create', [VehicleServiceTicketController::class, 'ticket_create']);
            Route::get('rider-tickets/{id}', [VehicleServiceTicketController::class, 'get_rider_tickets']);
        });
        
        
        Route::group(['prefix' => 'asset-management', 'as' => 'asset_management.'], function () {
            Route::group(['prefix' => 'qc', 'as' => 'quality_check.'], function () {
                Route::get('checkboxes/{vehicle_type}', [AssetManagementContoller::class, 'getCheckBoxLists']);
                Route::get('list/{dm_id}', [AssetManagementContoller::class, 'qc_lists']);
                Route::get('get-vehicle-types', [AssetManagementContoller::class, 'get_vehicle_types']);
                Route::get('get-vehicle-models', [AssetManagementContoller::class, 'get_vehicle_models']);
                Route::get('get-location-data', [AssetManagementContoller::class, 'get_location_data']);
                Route::get('view-quality-check/{id}', [AssetManagementContoller::class, 'view_quality_check']);
                Route::post('create-quality-check', [AssetManagementContoller::class, 'create']);
                Route::post('reinitiate-quality-check', [AssetManagementContoller::class, 'reinitiate_quality_check']);
                
                
            });
        });
        
        
        
    });
    
    
    Route::group(['prefix' => 'external'], function (){
        
        Route::group(['prefix' => 'orders'], function (){
            Route::post('create', [LiveOrderController::class, 'live_order_create']);
        });
    });
    
});


Route::post('/check-missing-chassis', function (Request $request) {
    // ✅ Validate input
    $validated = $request->validate([
        'location' => 'nullable',
        'zone_id' => 'nullable',
        'chassis_numbers' => 'required|array|min:1',
        'chassis_numbers.*' => 'string',
    ]);

    $chassisNumbers = $validated['chassis_numbers'];
    $location = $validated['location'];
    $zone_id = $validated['zone_id'] ?? null;

    // ✅ Find duplicates in input
    $duplicates = array_keys(array_filter(array_count_values($chassisNumbers), fn($count) => $count > 1));

    // Initialize arrays
    $found_location_1 = [];
    $found_other_locations_raw = [];
    $not_in_db = [];

    foreach (array_chunk($chassisNumbers, 500) as $chunk) {
        // 1️⃣ Find chassis matching the given location (+ zone if provided)
        $query1 = DB::table('vehicle_qc_check_lists')
            ->where('location', $location)
            ->whereIn('chassis_number', $chunk);

        if (!empty($zone_id)) {
            $query1->where('zone_id', $zone_id);
        }

        $existingLocation1 = $query1->pluck('chassis_number')->toArray();
        $found_location_1 = array_merge($found_location_1, $existingLocation1);

        // 2️⃣ Find chassis in DB but with different location or zone
        $query2 = DB::table('vehicle_qc_check_lists')
            ->select('chassis_number', 'location', 'zone_id')
            ->whereIn('chassis_number', $chunk);

        if (!empty($zone_id)) {
            $query2->where(function ($q) use ($location, $zone_id) {
                $q->where('location', '!=', $location)
                  ->orWhere('zone_id', '!=', $zone_id);
            });
        } else {
            $query2->where('location', '!=', $location);
        }

        $otherLocations = $query2->get()->toArray();
        $found_other_locations_raw = array_merge($found_other_locations_raw, $otherLocations);
    }

    // ✅ Group by (location, zone_id) and add count
    $grouped_other_locations = [];
    foreach ($found_other_locations_raw as $row) {
        $key = $row->location . '_' . $row->zone_id;
        if (!isset($grouped_other_locations[$key])) {
            $grouped_other_locations[$key] = [
                'location' => $row->location,
                'zone_id' => $row->zone_id,
                'chassis_numbers' => [],
                'count' => 0,
            ];
        }
        $grouped_other_locations[$key]['chassis_numbers'][] = $row->chassis_number;
        $grouped_other_locations[$key]['count']++;
    }

    // Convert associative array to indexed array
    $found_other_locations = array_values($grouped_other_locations);

    // 3️⃣ Find chassis not in DB at all
    $all_found_chassis = array_merge(
        $found_location_1,
        array_map(fn($row) => $row->chassis_number, $found_other_locations_raw)
    );

    $not_in_db = array_values(array_diff($chassisNumbers, $all_found_chassis));

    // ✅ Return JSON response
    return response()->json([
        'total_given' => count($chassisNumbers),
        'duplicate_chassis' => $duplicates,

        'found_location_count' => count($found_location_1),
        'found_location_chassis' => $found_location_1,

        'found_other_locations_grouped_count' => count($found_other_locations),
        'found_other_locations' => $found_other_locations, // grouped by location + zone_id + count

        'not_in_db_count' => count($not_in_db),
        'not_in_db_chassis' => $not_in_db,
    ]);
});

Route::post('/bulk-assign-vehicles', function (Request $request) {
    $validated = $request->validate([
        'chassis_numbers' => 'required|array|min:1',
        'chassis_numbers.*' => 'string'
    ]);

    $chassisNumbers = $validated['chassis_numbers'];
    $agentId = $request->agent_id; // system or logged-in user ID
     $customer_id = $request->customer_id;
     $customer_name = $request->customer_name;
     $id = $request->id;
    $results = [
        'success' => [],
        'failed' => [],
    ];

    foreach (array_chunk($chassisNumbers, 100) as $batch) {
        DB::beginTransaction();

        try {
            foreach ($batch as $chassisNumber) {
                $chassisNumber = trim($chassisNumber);

                // 1️⃣ Find vehicle
                $vehicle = DB::table('ev_tbl_asset_master_vehicles')->where('chassis_number', $chassisNumber)->first();
                if (!$vehicle) {
                    $results['failed'][] = [
                        'chassis_number' => $chassisNumber,
                        'error' => 'Vehicle not found',
                    ];
                    continue;
                }

                // 2️⃣ Check if already assigned
                $assigned = DB::table('b2b_tbl_vehicle_assignments')
                    ->where('asset_vehicle_id', $vehicle->id)
                    ->whereIn('status', ['running', 'assigned'])
                    ->exists();

                if ($assigned) {
                    $results['failed'][] = [
                        'vehicle_id' => $vehicle->id,
                        'chassis_number' => $chassisNumber,
                        'error' => 'Vehicle already assigned',
                    ];
                    continue;
                }

                // 3️⃣ Get next available pending vehicle request
                $vehicleRequest = DB::table('b2b_tbl_vehicle_requests')
                    ->where('status', 'pending')
                    ->where('created_by', $id)
                    ->orderBy('created_at', 'asc')
                    ->lockForUpdate()
                    ->first();

                if (!$vehicleRequest) {
                    $results['failed'][] = [
                        'chassis_number' => $chassisNumber,
                        'error' => 'No pending request found',
                    ];
                    continue;
                }

                // 4️⃣ Get rider details
                $rider = DB::table('b2b_tbl_riders')->where('id', $vehicleRequest->rider_id)->first();
                if (!$rider) {
                    $results['failed'][] = [
                        'chassis_number' => $chassisNumber,
                        'error' => 'Rider not found for request ' . $vehicleRequest->req_id,
                    ];
                    continue;
                }

                // 5️⃣ Assign the vehicle
                $assignmentId = DB::table('b2b_tbl_vehicle_assignments')->insertGetId([
                    'rider_id' => $rider->id,
                    'req_id' => $vehicleRequest->req_id,
                    'asset_vehicle_id' => $vehicle->id,
                    'handover_type' => 'vehicle',
                    'status' => 'running',
                    'assigned_agent_id' => $agentId,
                    'odometer_value' => 100,
                    'odometer_image' => "1759995351_68e765d784c47.jpg",
                    'vehicle_front' => "1759995351_68e765d7852b4.jpg",
                    'vehicle_back' => "1759995351_68e765d7852b4.jpg",
                    'vehicle_top' => "1759995351_68e765d7852b4.jpg",
                    'vehicle_bottom' => "1759995351_68e765d7852b4.jpg",
                    'vehicle_left' => "1759995351_68e765d7852b4.jpg",
                    'vehicle_right' => "1759995351_68e765d7852b4.jpg",
                    'vehicle_battery' => "1759995351_68e765d7852b4.jpg",
                    'vehicle_charger' => "1759995351_68e765d7852b4.jpg",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 6️⃣ Update inventory
                DB::table('asset_vehicle_inventories')
                    ->where('asset_vehicle_id', $vehicle->id)
                    ->update(['transfer_status' => 1,'updated_at' => now()]);

                // 7️⃣ Update vehicle master
                DB::table('ev_tbl_asset_master_vehicles')
                    ->where('id', $vehicle->id)
                    ->update([
                        'client' => $customer_id,
                        'vehicle_delivery_date' => now()->format('Y-m-d'),
                        'updated_at' => now()
                    ]);

                // 8️⃣ Update request as completed
                DB::table('b2b_tbl_vehicle_requests')
                    ->where('id', $vehicleRequest->id)
                    ->update([
                        'status' => 'completed',
                        'is_active' => 1,
                        'closed_by' => $agentId,
                        'completed_at' => now(),
                        'updated_at' => now()
                    ]);

                // 9️⃣ Log assignment
                DB::table('b2b_tbl_vehicle_assignment_logs')->insert([
                    'assignment_id' => $assignmentId,
                    'status' => 'running',
                    'remarks' => "Vehicle {$vehicle->permanent_reg_number} assigned to rider {$rider->name}",
                    'action_by' => $agentId,
                    'type' => 'agent',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
               
                $remarks = "Vehicle has been successfully assigned to {$customer_name} inventory status updated accordingly.";
                
                // 🔟 Log chassis
                DB::table('ev_tbl_chassis_transfer_logs')->insert([
                    'chassis_number' => $vehicle->chassis_number,
                    'vehicle_id' => $vehicle->id,
                    'status' => 'updated',
                    'remarks' => $remarks,
                    'created_by' => $agentId,
                    'type' => 'b2b-agent-app',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $results['success'][] = [
                    'chassis_number' => $chassisNumber,
                    'req_id' => $vehicleRequest->req_id,
                    'rider_id' => $rider->id,
                    'vehicle_id' => $vehicle->id,
                ];
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Bulk vehicle assignment failed: ' . $e->getMessage());

            foreach ($batch as $num) {
                $results['failed'][] = [
                    'chassis_number' => $num,
                    'error' => $e->getMessage(),
                ];
            }
        }
    }

    return response()->json([
        'status' => true,
        'summary' => [
            'total' => count($chassisNumbers),
            'success' => count($results['success']),
            'failed' => count($results['failed']),
        ],
        'results' => $results,
    ]);
});





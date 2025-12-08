<?php

namespace Modules\B2B\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\CustomHandler;
use Illuminate\Support\Facades\DB;
// use SimpleSoftwareIO\QrCode\Facades\QrCode; // Correct import
use Illuminate\Support\Facades\Auth;//updated by Mugesh.B
use Modules\B2B\Entities\B2BVehicleAssignment;
use Modules\B2B\Entities\B2BReturnRequest;
use Modules\B2B\Entities\B2BServiceRequest;
use Modules\B2B\Entities\B2BReportAccident;
use Modules\MasterManagement\Entities\EvTblAccountabilityType; //updated by Mugesh.B
use Modules\MasterManagement\Entities\CustomerLogin;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\AssetMaster\Entities\AssetVehicleInventory;
use Modules\B2B\Entities\B2BRecoveryRequest;
use Modules\B2B\Entities\B2BRider;
use Modules\City\Entities\City;
use Modules\B2B\Entities\B2BVehicleRequests;
use Modules\Zones\Entities\Zones;
use Modules\VehicleManagement\Entities\VehicleType; 
use Modules\AssetMaster\Entities\VehicleModelMaster;


class B2BDashboardController extends Controller
{
    
public function index(Request $request)
{
    $guard = Auth::guard('master')->check() ? 'master' : 'zone';
    $user  = Auth::guard($guard)->user();
    $customerId = $user->customer_id;
    $user->load('customer_relation');
    

    $accountability_Types = $user->customer_relation->accountability_type_id;

    // Make sure it's an array (sometimes could be stored as string or null)
    if (!is_array($accountability_Types)) {
        $accountability_Types = json_decode($accountability_Types, true) ?? [];
    }
    
    $accountability_Type = in_array(2, $accountability_Types) ? 2 : 1;
    
                
    $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
                ->where('city_id', $user->city_id)
                ->pluck('id');
    $today = now();
    $last30Days = $today->copy()->subDays(30);
    $prev30Days = $last30Days->copy()->subDays(30);
    
    
    // === Filter helper ===
    $applyFilter = function ($query, $modelType) use ($guard, $user, $customerLoginIds) {
        if ($modelType === 'rider') {
            // return $query->whereHas('customerLogin', function ($q) use ($guard, $user, $customerId) {
            //     $q->where('customer_id', $customerId);
            //     if ($guard === 'master') {
            //         $q->where('city_id', $user->city_id);
            //     } elseif ($guard === 'zone') {
            //         $q->where('city_id', $user->city_id)
            //           ->where('zone_id', $user->zone_id);
            //     }
            // });
            
            return $query->whereHas('VehicleRequest', function ($q) use ($user, $guard, $customerLoginIds) {
                    // Always filter by created_by if IDs exist
                    if ($customerLoginIds->isNotEmpty()) {
                        $q->whereIn('created_by', $customerLoginIds);
                    }
                
                    // Apply guard-specific filters
                    if ($guard === 'master') {
                        $q->where('city_id', $user->city_id);
                    }
                
                    if ($guard === 'zone') {
                        $q->where('city_id', $user->city_id)
                          ->where('zone_id', $user->zone_id);
                    }
                });
        } elseif ($modelType === 'assignment') {
            return $query->whereHas('VehicleRequest', function ($q) use ($user, $guard, $customerLoginIds) {
                    // Always filter by created_by if IDs exist
                    if ($customerLoginIds->isNotEmpty()) {
                        $q->whereIn('created_by', $customerLoginIds);
                    }
                
                    // Apply guard-specific filters
                    if ($guard === 'master') {
                        $q->where('city_id', $user->city_id);
                    }
                
                    if ($guard === 'zone') {
                        $q->where('city_id', $user->city_id)
                          ->where('zone_id', $user->zone_id);
                    }
                });
        } else {
            // Requests: assignment -> rider -> customerLogin
            return $query->whereHas('assignment.VehicleRequest', function ($q) use ($user, $guard, $customerLoginIds) {
                    // Always filter by created_by if IDs exist
                    if ($customerLoginIds->isNotEmpty()) {
                        $q->whereIn('created_by', $customerLoginIds);
                    }
                
                    // Apply guard-specific filters
                    if ($guard === 'master') {
                        $q->where('city_id', $user->city_id);
                    }
                
                    if ($guard === 'zone') {
                        $q->where('city_id', $user->city_id)
                          ->where('zone_id', $user->zone_id);
                    }
                });
        }
    };
    
    $total_vehicles = AssetMasterVehicle::whereHas('quality_check', function ($query) use ($user, $guard , $customerId , $accountability_Type) {
            if ($guard === 'master') {
                $query->where('location', $user->city_id);
            } elseif ($guard === 'zone') {
                $query->where('location', $user->city_id)
                      ->where('zone_id', $user->zone_id);
            }
            
            $query->where('accountability_type', $accountability_Type);
            $query->where('customer_id', $customerId);
        })
        ->count();
    

    $total_rfd = AssetVehicleInventory::where('transfer_status', 3)
        ->whereHas('assetVehicle', function ($query) use ($customerId, $user, $guard, $accountability_Type) {
            $query->whereHas('quality_check', function ($qc) use ($user, $guard, $customerId, $accountability_Type) {
                $qc->where('customer_id', $customerId);
    
                // Guard-based filtering
                if ($guard === 'master') {
                    $qc->where('location', $user->city_id);
                } elseif ($guard === 'zone') {
                    $qc->where('location', $user->city_id)
                       ->where('zone_id', $user->zone_id);
                }
    
                $qc->where('accountability_type', $accountability_Type);
            });
    
        
        })
        ->count();
    
    
    
    
    
    $totalZones = CustomerLogin::where('customer_id',$customerId)->where('type','zone')->count();
    
        $zoneIds = CustomerLogin::where('customer_id', $customerId)
            ->where('type', 'zone')
            ->pluck('zone_id');
        // print_r($zoneIds);exit;
        $zones = Zones::where('city_id', $user->city_id)->get();

    // === Vehicle Assignments ===
    $totalAssignedCurrent = $applyFilter(
        B2BVehicleAssignment::with('vehicle.quality_check')
            ->whereHas('vehicle.quality_check', function ($query) use ($customerId, $accountability_Type, $user, $guard) {
                // Filter by customer
                $query->where('customer_id', $customerId);
    
                // Filter by accountability type
                $query->where('accountability_type', $accountability_Type);
    
                // Optional: Guard-based filtering
                if ($guard === 'master') {
                    $query->where('location', $user->city_id);
                } elseif ($guard === 'zone') {
                    $query->where('location', $user->city_id)
                          ->where('zone_id', $user->zone_id);
                }
            }),
        'assignment'
    )
    ->where('status', 'running')
    ->count();

    // === Return Requests ===
  $totalReturnCurrent = $applyFilter(
        B2BReturnRequest::with('assignment.vehicle.quality_check')
            ->whereHas('assignment.vehicle.quality_check', function ($query) use ($customerId, $accountability_Type, $user, $guard) {
                // Filter by customer
                $query->where('customer_id', $customerId);
    
                // Filter by accountability type
                $query->where('accountability_type', $accountability_Type);
    
                // Optional: Guard-based filtering
                if ($guard === 'master') {
                    $query->where('location', $user->city_id);
                } elseif ($guard === 'zone') {
                    $query->where('location', $user->city_id)
                          ->where('zone_id', $user->zone_id);
                }
            }),
        'request'
    )
    ->where('status', 'closed')
    ->count();
    
    $totalReturnRequests = $applyFilter(
        B2BReturnRequest::with('assignment.vehicle.quality_check')
            ->whereHas('assignment.vehicle.quality_check', function ($query) use ($customerId, $accountability_Type, $user, $guard) {
                // Filter by customer
                $query->where('customer_id', $customerId);
    
                // Filter by accountability type
                $query->where('accountability_type', $accountability_Type);
    
                // Optional: Guard-based filtering
                if ($guard === 'master') {
                    $query->where('location', $user->city_id);
                } elseif ($guard === 'zone') {
                    $query->where('location', $user->city_id)
                          ->where('zone_id', $user->zone_id);
                }
            }),
        'request'
    )
    // ->where('status', 'opened')
    ->count();

    // === Service Requests ===
    $totalServiceCurrent = $applyFilter(
        B2BServiceRequest::with('assignment.vehicle.quality_check')
            ->whereHas('assignment.vehicle.quality_check', function ($query) use ($customerId, $accountability_Type, $user, $guard) {
                // Customer filter
                $query->where('customer_id', $customerId);
    
                // Accountability type filter
                $query->where('accountability_type', $accountability_Type);
    
            }) 
            ->where('status', '!=', 'closed'),
        'request'
    )->count();

    // === Accident Reports ===
    $totalAccidentCurrent = $applyFilter(
        B2BReportAccident::with('assignment.vehicle.quality_check')
            ->whereHas('assignment.vehicle.quality_check', function ($query) use ($customerId, $accountability_Type, $user, $guard) {
                // Filter by customer
                $query->where('customer_id', $customerId);
    
                // Filter by accountability type
                $query->where('accountability_type', $accountability_Type);
    
            }),
        'request'
    )->count();

    // === Recovery Requests ===
    $totalRecoveryCurrent = $applyFilter(
        B2BRecoveryRequest::with('assignment.vehicle.quality_check')
            ->whereHas('assignment.vehicle.quality_check', function ($query) use ($customerId, $accountability_Type, $user, $guard) {
                // Filter by customer
                $query->where('customer_id', $customerId);
    
                // Filter by accountability type
                $query->where('accountability_type', $accountability_Type);
            })
            ,
        'request'
    )->count();
    
    $totalRecovered = $applyFilter(
        B2BRecoveryRequest::with('assignment.vehicle.quality_check')
            ->whereHas('assignment.vehicle.quality_check', function ($query) use ($customerId, $accountability_Type, $user, $guard) {
                // Filter by customer
                $query->where('customer_id', $customerId);
    
                // Filter by accountability type
                $query->where('accountability_type', $accountability_Type);
            })
            ->where('status', 'closed'),
        'request'
    )->count();
    

    // === Active / Inactive Riders ===
    $totalActiveRider = $applyFilter(B2BRider::query(), 'rider')
        // ->whereBetween('created_at', [$last30Days, $today])
        ->whereHas('latestVehicleRequest', function ($q) use ($last30Days, $today, $accountability_Type) {
            $q->where('is_active', 1)
              ->where('account_ability_type', $accountability_Type);
        })
        ->count();
    
    $totalInactiveRider = $applyFilter(B2BRider::query(), 'rider')
        // ->whereBetween('created_at', [$prev30Days, $last30Days])
        ->where(function ($q) use ($accountability_Type) {
            $q->doesntHave('latestVehicleRequest') // riders with no request
              ->orWhereHas('latestVehicleRequest', function ($q2) use ($accountability_Type) {
                  $q2->where('is_active', 0)
                     ->where('account_ability_type', $accountability_Type);
              });
        })
        ->count();
        
        

    // === Return Reasons ===
    $returnReasons = ['Contract End', 'Performance Issue', 'Vehicle Issue', 'No Longer Needed'];
    $returnCounts = [];
    
    foreach ($returnReasons as $reason) {
        $count = $applyFilter(
            B2BReturnRequest::whereHas('assignment.vehicle.quality_check', function ($query) use ($customerId, $accountability_Type, $user, $guard) {
                // Filter by customer
                $query->where('customer_id', $customerId);
    
                // Filter by accountability type
                $query->where('accountability_type', $accountability_Type);
    
            }),
            'request'
        )
        ->where('return_reason', $reason)
        // ->where('status', 'closed')
        // ->whereBetween('created_at', [$last30Days, $today]) // optional
        ->count();
    
        $returnCounts[$reason] = $count;
    }
    
    // Access individual counts
    $contractEnd = $returnCounts['Contract End'];
    $performanceIssue = $returnCounts['Performance Issue'];
    $vehicleIssue = $returnCounts['Vehicle Issue'];
    $noLongerNeeded = $returnCounts['No Longer Needed'];
    
    

    // === Accident Types ===
    $accidentTypes = ['Collision', 'Fall', 'Fire', 'Other'];
    $accidentCounts = [];
    
    foreach ($accidentTypes as $type) {
        $count = $applyFilter(
            B2BReportAccident::whereHas('assignment.vehicle.quality_check', function ($query) use ($customerId, $accountability_Type, $user, $guard) {
                // Filter by customer
                $query->where('customer_id', $customerId);
    
                // Filter by accountability type
                $query->where('accountability_type', $accountability_Type);
    
            }),
            'request'
        )
        ->where('accident_type', $type)
        // ->where('status', 'closed')
        // ->whereBetween('created_at', [$last30Days, $today]) // optional
        ->count();
    
        $accidentCounts[$type] = $count;
    }
    
    // Access individual counts
    $collision = $accidentCounts['Collision'];
    $fall = $accidentCounts['Fall'];
    $fire = $accidentCounts['Fire'];
    $other = $accidentCounts['Other'];
    
    

    // === Service Request Chart Data ===
    $serviceStatuses = ['unassigned', 'inprogress','closed'];
    $serviceChartData = [];
    
     $year  = now()->year; 
     $labels = [];

    // Build labels only once
    for ($month = 1; $month <= 12; $month++) {
        $labels[] = \Carbon\Carbon::create($year, $month, 1)->format('M Y');
    }
    
    foreach ($serviceStatuses as $status) {
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $count = $applyFilter(
                B2BServiceRequest::whereHas('assignment.vehicle.quality_check', function ($query) use ($customerId, $accountability_Type, $user, $guard) {
                    // Filter by customer
                    $query->where('customer_id', $customerId);
    
                    // Filter by accountability type
                    $query->where('accountability_type', $accountability_Type);
                }),
                'request'
            )
            ->where('status', $status)
            // ->whereYear('created_at', now()->year)
            // ->whereMonth('created_at', $month)
            ->count();
            $monthlyData[] = $count;
        }

        // Assign colors (optional: match your previous chart)
        $colorMap = [
            'unassigned' => '#CAEDCE',
            'inprogress' => '#D8E4FE',
            'closed' => '#C0E0DF'
        ];

        $serviceChartData[] = [
            'label' => ucfirst(str_replace('_', ' ', $status)),
            'data' => $monthlyData,
            'borderColor' => $colorMap[$status],
            'backgroundColor' => $colorMap[$status],
            'fill' => false,
            'tension' => 0.4
        ];
    }

    $assigned_vehicles = [
        'current' => $totalAssignedCurrent,
        'previous' => 0,
        'change_percent' => 0,
    ];
    $return_requests = [
        'current' => $totalReturnCurrent,
        'previous' => 0,
        'change_percent' => 0,
    ];
    $service_requests = [
        'current' => $totalServiceCurrent,
        'previous' => 0,
        'change_percent' => 0,
    ];
    $recovery_requests = [
        'current' => $totalRecoveryCurrent,
        'previous' => 0,
        'change_percent' => 0,
    ];
    $accident_report = [
        'current' => $totalAccidentCurrent,
        'previous' => 0,
        'change_percent' => 0,
    ];
    
    $cities = City::where('status', 1)->where('id',$user->city_id)->get();
    // Return view with serviceChartData included
    
    $accountability_types = EvTblAccountabilityType::where('status', 1)
    ->whereIn('id',$accountability_Types)
    ->orderBy('id', 'desc')
    ->get();
    
    $vehicle_types = VehicleType::where('is_active', 1)->get();
    $vehicle_models = VehicleModelMaster::where('status', 1)->get();
    $vehicle_makes = VehicleModelMaster::where('status', 1)->distinct()->pluck('make');
    
    return view('b2b::dashboard', compact(
        'assigned_vehicles',
        'accountability_types',
        'accountability_Type',
        'return_requests',
        'service_requests',
        'recovery_requests',
        'accident_report',
        'total_rfd' ,
        'total_vehicles',
        'totalInactiveRider',
        'totalActiveRider',
        'totalReturnRequests',
        'contractEnd',
        'performanceIssue',
        'vehicleIssue',
        'noLongerNeeded',
        'totalZones',
        'other',
        'fire',
        'fall',
        'collision',
        'serviceChartData',
        'cities',
        'labels',
        'zones',
        'user',
        'vehicle_types',
        'vehicle_models',
        'vehicle_makes',
        'totalRecovered'
        
    ));
}

public function dashboard_data(Request $request)
{
    $guard = Auth::guard('master')->check() ? 'master' : 'zone';
    $user  = Auth::guard($guard)->user();
    $customerId = $user->customer_id;

    $user->load('customer_relation');

    // --- Normalize accountability types ---
    $accountability_Types = $user->customer_relation->accountability_type_id;

    if (!is_array($accountability_Types)) {
        $accountability_Types = json_decode($accountability_Types, true) ?? [];
    }

    // Default if 2 exists → use 2 else 1
    $defaultAccountabilityType = in_array(2, $accountability_Types) ? 2 : 1;

    // Use request value or fallback correctly
    $accountability_Type = $request->has('accountability_type')
        ? (array)$request->accountability_type
        : [$defaultAccountabilityType];

    // Convert string → int for safety
    $accountability_Type = array_map('intval', $accountability_Type);

    // --- Other filters ---
    $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
        ->where('city_id', $user->city_id)
        ->pluck('id');

    $city_id       = $request->city_id ?? null;
    $zone_id       = $request->has('zone_id') ? (array)$request->zone_id : [];
    $vehicle_type  = $request->has('vehicle_type') ? (array)$request->vehicle_type : [];
    $vehicle_model = $request->has('vehicle_model') ? (array)$request->vehicle_model : [];
    $vehicle_make  = $request->has('vehicle_make') ? (array)$request->vehicle_make : [];
    $from_date     = $request->from_date ?? null;
    $to_date       = $request->to_date ?? null;
    $quick_date_filter = $request->quick_date_filter ?? null;

    $today = now();

    // ===================== DATE RANGE ============================
    if ($quick_date_filter && $quick_date_filter != 'custom') {
        switch ($quick_date_filter) {
            case 'today':
                $start_date = $today->copy()->startOfDay();
                $end_date = $today->copy()->endOfDay();
                $interval = '2 hours';
                break;

            case 'week':
                $start_date = $today->copy()->startOfWeek();
                $end_date = $today->copy()->endOfWeek();
                $interval = '1 day';
                break;

            case 'month':
                $start_date = $today->copy()->startOfMonth();
                $end_date = $today->copy()->endOfMonth();
                $interval = '1 day';
                break;
            case 'last15days':
                $start_date =$today->copy()->subDays(14)->startOfDay(); // 15 days including today
                $end_date = $today->copy()->endOfDay();
                $interval = '1 day';
                break;
            case 'year':
                $start_date = $today->copy()->startOfYear();
                $end_date = $today->copy()->endOfYear();
                $interval = '1 month';
                break;

            default:
                $start_date = $today->copy()->subDays(30);
                $end_date = $today;
                $interval = '1 day';
                break;
        }
    } elseif ($from_date && $to_date) {
        $start_date = \Carbon\Carbon::parse($from_date)->startOfDay();
        $end_date   = \Carbon\Carbon::parse($to_date)->endOfDay();
        $interval = $start_date->diffInDays($end_date) <= 31 ? '1 day' : '1 month';
    } else {
        $minCreated = B2BRider::min('created_at');
        $start_date = $minCreated ? \Carbon\Carbon::parse($minCreated)->startOfDay() : now()->subYears(5);
        $end_date = now()->endOfDay();

        $diffYears = $start_date->diffInYears($end_date);
        $interval = $diffYears >= 1 ? '1 year' : '1 month';
    }

    $daysInRange = $start_date->diffInDays($end_date) + 1;
    $prev_end_date = $start_date->copy()->subDay();
    $prev_start_date = $prev_end_date->copy()->subDays($daysInRange - 1);

    // === Generate Labels / Period for charts ===
    $labels = [];
    $period = \Carbon\CarbonPeriod::create($start_date, $interval, $end_date);
    foreach ($period as $dt) {
        if ($interval === '2 hours') {
            $labels[] = $dt->format('H:i');
        } elseif ($interval === '1 day') {
            $labels[] = $dt->format('d M');
        } elseif ($interval === '1 month' || $interval === '1 year') {
            // for 1 month and 1 year, show month-year
            $labels[] = $dt->format('M Y');
        } else {
            $labels[] = $dt->format('d M');
        }
    }

    // ================== FILTER HELPER =====================
    $applyFilter = function ($query, $modelType)
        use (
            $guard, $user, $customerLoginIds, $customerId,
            $city_id, $zone_id,
            $accountability_Type,
            $vehicle_type, $vehicle_model, $vehicle_make
        ) {

        if ($modelType === 'rider') {
            return $query->whereHas('VehicleRequest', function ($q) use (
                $guard, $user, $customerLoginIds,
                $city_id, $zone_id, $accountability_Type,
                $vehicle_type, $vehicle_model, $vehicle_make
            ) {
                if ($customerLoginIds->isNotEmpty()) {
                    $q->whereIn('created_by', $customerLoginIds);
                }

                if ($city_id) {
                    $q->where('city_id', $city_id);
                } elseif ($guard === 'master') {
                    $q->where('city_id', $user->city_id);
                }

                if ($zone_id) {
                    $q->whereIn('zone_id', $zone_id);
                } elseif ($guard === 'zone') {
                    $q->where('zone_id', $user->zone_id)
                        ->where('city_id', $user->city_id);
                }

                // use consistent column name in requests (quality_check uses accountability_type)
                $q->whereIn('account_ability_type', $accountability_Type);

                // Vehicle Filters (via assignment -> vehicle -> quality_check)
                $q->whereHas('assignment.vehicle.quality_check', function ($qc) use ($vehicle_type, $vehicle_model, $vehicle_make) {
                    if ($vehicle_type)  $qc->whereIn('vehicle_type', $vehicle_type);
                    if ($vehicle_model) $qc->whereIn('vehicle_model', $vehicle_model);

                    $qc->whereHas('vehicle_model_relation', function ($vm) use ($vehicle_make) {
                        if ($vehicle_make) $vm->whereIn('make', $vehicle_make);
                    });
                });
            });
        }

        if ($modelType === 'assignment') {
            return $query->whereHas('vehicleRequest', function ($q) use (
                $guard, $user, $customerLoginIds, $city_id, $zone_id
            ) {
                if ($customerLoginIds->isNotEmpty()) {
                    $q->whereIn('created_by', $customerLoginIds);
                }

                if ($city_id) {
                    $q->where('city_id', $city_id);
                } elseif ($guard === 'master') {
                    $q->where('city_id', $user->city_id);
                }

                if ($zone_id) {
                    $q->whereIn('zone_id', $zone_id);
                } elseif ($guard === 'zone') {
                    $q->where('zone_id', $user->zone_id);
                }
            })
            ->whereHas('vehicle.quality_check', function ($qc) use (
                $customerId, $accountability_Type,
                $vehicle_type, $vehicle_model, $vehicle_make
            ) {
                $qc->where('customer_id', $customerId)
                   ->whereIn('accountability_type', $accountability_Type);

                if ($vehicle_type)  $qc->whereIn('vehicle_type', $vehicle_type);
                if ($vehicle_model) $qc->whereIn('vehicle_model', $vehicle_model);

                $qc->whereHas('vehicle_model_relation', function ($vm) use ($vehicle_make) {
                    if ($vehicle_make) $vm->whereIn('make', $vehicle_make);
                });
            });
        }

        // Default (request) case
        return $query->whereHas('assignment.vehicleRequest', function ($q) use (
            $guard, $user, $customerLoginIds, $city_id, $zone_id
        ) {
            if ($customerLoginIds->isNotEmpty()) {
                $q->whereIn('created_by', $customerLoginIds);
            }

            if ($city_id) {
                $q->where('city_id', $city_id);
            } elseif ($guard === 'master') {
                $q->where('city_id', $user->city_id);
            }

            if ($zone_id) {
                $q->whereIn('zone_id', $zone_id);
            } elseif ($guard === 'zone') {
                $q->where('zone_id', $user->zone_id);
            }
        })
        ->whereHas('assignment.vehicle.quality_check', function ($qc) use (
            $customerId, $accountability_Type,
            $vehicle_type, $vehicle_model, $vehicle_make
        ) {
            $qc->where('customer_id', $customerId)
               ->whereIn('accountability_type', $accountability_Type);

            if ($vehicle_type)  $qc->whereIn('vehicle_type', $vehicle_type);
            if ($vehicle_model) $qc->whereIn('vehicle_model', $vehicle_model);

            $qc->whereHas('vehicle_model_relation', function ($vm) use ($vehicle_make) {
                if ($vehicle_make) $vm->whereIn('make', $vehicle_make);
            });
        });
    };

    // -------------------- TOTAL VEHICLES --------------------
    $total_vehicles = AssetMasterVehicle::whereHas('quality_check', function ($query) use (
        $user, $zone_id, $guard, $customerId, $accountability_Type, $vehicle_model, $vehicle_type, $vehicle_make
    ) {
        if ($guard === 'master') {
            $query->where('location', $user->city_id);

            if (!empty($zone_id)) {
                $query->whereIn('zone_id', $zone_id);
            }
        } elseif ($guard === 'zone') {
            $query->where('location', $user->city_id)
                  ->where('zone_id', $user->zone_id);
        }

        $query->whereIn('accountability_type', $accountability_Type)
              ->where('customer_id', $customerId);

        if ($vehicle_type)  $query->whereIn('vehicle_type', $vehicle_type);
        if ($vehicle_model) $query->whereIn('vehicle_model', $vehicle_model);

        $query->whereHas('vehicle_model_relation', function ($vm) use ($vehicle_make) {
            if ($vehicle_make) $vm->whereIn('make', $vehicle_make);
        });
    })
    ->when(in_array(1, $accountability_Type) && !empty($zone_id), function ($query) use ($zone_id, $customerId) {
        $query->where('client', $customerId);
    })
    ->count();

    // -------------------- TOTAL RFD --------------------
    $total_rfd = AssetVehicleInventory::where('transfer_status', 3)
        ->whereHas('assetVehicle', function ($query) use (
            $customerId, $user, $guard, $accountability_Type, $zone_id, $vehicle_type, $vehicle_model, $vehicle_make
        ) {
            $query->whereHas('quality_check', function ($qc) use (
                $user, $zone_id, $guard, $customerId, $accountability_Type, $vehicle_type, $vehicle_model, $vehicle_make
            ) {
                $qc->where('customer_id', $customerId);

                if ($guard === 'master') {
                    $qc->where('location', $user->city_id);

                    if (!empty($zone_id)) {
                        $qc->whereIn('zone_id', $zone_id);
                    }
                } elseif ($guard === 'zone') {
                    $qc->where('location', $user->city_id)
                       ->where('zone_id', $user->zone_id);
                }

                $qc->whereIn('accountability_type', $accountability_Type);

                if ($vehicle_type)  $qc->whereIn('vehicle_type', $vehicle_type);
                if ($vehicle_model) $qc->whereIn('vehicle_model', $vehicle_model);

                $qc->whereHas('vehicle_model_relation', function ($vm) use ($vehicle_make) {
                    if ($vehicle_make) $vm->whereIn('make', $vehicle_make);
                });
            });

            $query->when(in_array(1, $accountability_Type) && !empty($zone_id), function ($q) use ($zone_id, $customerId) {
                $q->where('client', $customerId);
            });
        })
        ->count();

    // === Metrics ===
    $totalAssignedCurrent = $applyFilter(B2BVehicleAssignment::query(), 'assignment')
        ->where('status', 'running')->count();

    $totalReturnCurrent = $applyFilter(B2BReturnRequest::query(), 'request')
        ->whereBetween('created_at', [$start_date, $end_date])
        ->where('status', 'closed')
        ->count();

    $totalReturnRequests = $applyFilter(B2BReturnRequest::query(), 'request')
        ->whereBetween('created_at', [$start_date, $end_date])->count();

    $totalServiceCurrent = $applyFilter(B2BServiceRequest::query(), 'request')
        ->where('status', '!=', 'closed')
        ->whereBetween('created_at', [$start_date, $end_date])->count();

    $totalAccidentCurrent = $applyFilter(B2BReportAccident::query(), 'request')
        ->whereBetween('created_at', [$start_date, $end_date])->count();

    $totalRecoveryCurrent = $applyFilter(B2BRecoveryRequest::query(), 'request')
        ->whereBetween('created_at', [$start_date, $end_date])->count();

    $totalRecovered = $applyFilter(B2BRecoveryRequest::query(), 'request')
        ->where('status', 'closed')
        ->whereBetween('created_at', [$start_date, $end_date])->count();

    $totalActiveRider = $applyFilter(B2BRider::query(), 'rider')
        ->whereHas('latestVehicleRequest', function ($q) {
            $q->where('is_active', 1);
        })
        ->count();

    $totalInactiveRider = $applyFilter(B2BRider::query(), 'rider')
        ->where(function ($q) {
            $q->doesntHave('latestVehicleRequest')
              ->orWhereHas('latestVehicleRequest', function ($q2) {
                  $q2->where('is_active', 0);
              });
        })
        ->count();

    // Return Reasons
    $contractEnd = $applyFilter(B2BReturnRequest::query(), 'request')
        ->whereBetween('created_at', [$start_date, $end_date])->where('return_reason', 'Contract End')->count();
    $performanceIssue = $applyFilter(B2BReturnRequest::query(), 'request')
        ->whereBetween('created_at', [$start_date, $end_date])->where('return_reason', 'Performance Issue')->count();
    $vehicleIssue = $applyFilter(B2BReturnRequest::query(), 'request')
        ->whereBetween('created_at', [$start_date, $end_date])->where('return_reason', 'Vehicle Issue')->count();
    $noLongerNeeded = $applyFilter(B2BReturnRequest::query(), 'request')
        ->whereBetween('created_at', [$start_date, $end_date])->where('return_reason', 'No Longer Needed')->count();

    // Accident Types
    $collision = $applyFilter(B2BReportAccident::query(), 'request')
        ->whereBetween('created_at', [$start_date, $end_date])->where('accident_type', 'Collision')->count();
    $fall = $applyFilter(B2BReportAccident::query(), 'request')
        ->whereBetween('created_at', [$start_date, $end_date])->where('accident_type', 'Fall')->count();
    $fire = $applyFilter(B2BReportAccident::query(), 'request')
        ->whereBetween('created_at', [$start_date, $end_date])->where('accident_type', 'Fire')->count();
    $other = $applyFilter(B2BReportAccident::query(), 'request')
        ->whereBetween('created_at', [$start_date, $end_date])->where('accident_type', 'Other')->count();

    // === Service Chart Data ===
    $serviceStatuses = ['unassigned', 'inprogress', 'closed'];
    $serviceChartData = [];
    foreach ($serviceStatuses as $status) {
        $chartData = [];
        foreach ($period as $dt) {
            $query = $applyFilter(B2BServiceRequest::query(), 'request')->where('status', $status);

            if ($interval === '2 hours') {
                $chartData[] = $query->whereBetween('created_at', [$dt, $dt->copy()->addHours(2)])->count();
            } elseif ($interval === '1 day') {
                $chartData[] = $query->whereDate('created_at', $dt->toDateString())->count();
            } elseif ($interval === '1 month' || $interval === '1 year') {
                $chartData[] = $query->whereMonth('created_at', $dt->month)->whereYear('created_at', $dt->year)->count();
            } else {
                $chartData[] = $query->whereDate('created_at', $dt->toDateString())->count();
            }
        }
        $colorMap = ['unassigned' => '#CAEDCE', 'inprogress' => '#EECACB', 'closed' => '#C0E0DF'];
        $serviceChartData[] = [
            'label' => ucfirst(str_replace('_', ' ', $status)),
            'data' => $chartData,
            'borderColor' => $colorMap[$status],
            'backgroundColor' => $colorMap[$status],
            'fill' => false,
            'tension' => 0.4
        ];
    }

    return response()->json([
        'assigned_vehicles' => ['current' => $totalAssignedCurrent, 'previous' => 0, 'change_percent' => 0],
        'return_requests' => ['current' => $totalReturnCurrent, 'previous' => 0, 'change_percent' => 0],
        'service_requests' => ['current' => $totalServiceCurrent, 'previous' => 0, 'change_percent' => 0],
        'recovery_requests' => ['current' => $totalRecoveryCurrent, 'previous' => 0, 'change_percent' => 0],
        'accident_report' => ['current' => $totalAccidentCurrent, 'previous' => 0, 'change_percent' => 0],
        'totalActiveRider' => $totalActiveRider,
        'totalInactiveRider' => $totalInactiveRider,
        'contractEnd' => $contractEnd,
        'performanceIssue' => $performanceIssue,
        'totalReturnRequests' => $totalReturnRequests,
        'vehicleIssue' => $vehicleIssue,
        'noLongerNeeded' => $noLongerNeeded,
        'collision' => $collision,
        'fall' => $fall,
        'fire' => $fire,
        'other' => $other,
        'labels' => $labels,
        'serviceChartData' => $serviceChartData,
        'total_vehicles' => $total_vehicles,
        'total_rfd_vehicles' => $total_rfd,
        'totalRecovered' => $totalRecovered,
        'accountability_Type' => $accountability_Type
    ]);
}




// public function dashboard_data(Request $request)
// {
//     $guard = Auth::guard('master')->check() ? 'master' : 'zone';
//     $user  = Auth::guard($guard)->user();
//     $customerId = $user->customer_id;
    
    
//     $user->load('customer_relation');

//     $accountability_Types = $user->customer_relation->accountability_type_id;
    
//     // Make sure it's an array (sometimes could be stored as string or null)
//     if (!is_array($accountability_Types)) {
//         $accountability_Types = json_decode($accountability_Types, true) ?? [];
//     }
    
//     $defaultAccountabilityType = in_array(2, $accountability_Types) ? 2 : 1;
//     // Use request value if provided, otherwise use default
//     $accountability_Type = (array)$request->accountability_type ?? (array) $defaultAccountabilityType;

                
//     $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
//         ->where('city_id', $user->city_id)
//         ->pluck('id');
//     $city_id = $request->city_id ?? null;
//     $zone_id = (array) $request->zone_id ?? [];
//     $vehicle_type = (array) $request->vehicle_type ?? [];
//     $vehicle_model = (array) $request->vehicle_model ?? [];
//     $vehicle_make = (array) $request->vehicle_make ?? [];
//     $from_date = $request->from_date ?? null;
//     $to_date   = $request->to_date ?? null;
//     $quick_date_filter = $request->quick_date_filter ?? null;

//     $today = now();

//     // === Date Range Handling ===
//     if ($quick_date_filter && $quick_date_filter!='custom') {
//         switch ($quick_date_filter) {
//             case 'today':
//                 $start_date = $today->copy()->startOfDay();
//                 $end_date = $today->copy()->endOfDay();
//                 $interval = '2 hours';
//                 break;
//             case 'week':
//                 $start_date = $today->copy()->startOfWeek();
//                 $end_date = $today->copy()->endOfWeek();
//                 $interval = '1 day';
//                 break;
//             case 'month':
//                 $start_date = $today->copy()->startOfMonth();
//                 $end_date = $today->copy()->endOfMonth();
//                 $interval = '1 day';
//                 break;
//             case 'year':
//                 $start_date = $today->copy()->startOfYear();
//                 $end_date = $today->copy()->endOfYear();
//                 $interval = '1 month';
//                 break;
//             // case 'custom':
//             //     $start_date = Carbon::parse($from_date)->startOfDay();
//             //     $end_date   = Carbon::parse($to_date)->endOfDay();
//             //     $interval = $start_date->diffInDays($end_date) <= 31 ? '1 day' : '1 month';
//             //     break;
//             default:
//                 $start_date = $today->copy()->subDays(30);
//                 $end_date = $today;
//                 $interval = '1 day';
//                 break;
//         }
//     } elseif ($from_date && $to_date) {
//         $start_date = Carbon::parse($from_date)->startOfDay();
//         $end_date   = Carbon::parse($to_date)->endOfDay();
//         $interval = $start_date->diffInDays($end_date) <= 31 ? '1 day' : '1 month';
//     } else {
//         // $start_date = '';
//         // $end_date = '';
//         // $interval = '1 month';
        
//     $start_date = B2BRider::min('created_at') 
//                   ? Carbon::parse(B2BRider::min('created_at'))->startOfDay()
//                   : now()->subYears(5);

//     $end_date   = now()->endOfDay();

//     $diffYears = $start_date->diffInYears($end_date);

//     $interval = $diffYears >= 1 ? '1 year' : '1 month';
//     }

//     $daysInRange = $start_date->diffInDays($end_date) + 1;
//     $prev_end_date = $start_date->copy()->subDay();
//     $prev_start_date = $prev_end_date->copy()->subDays($daysInRange - 1);
    
//     // === Filter Helper ===
//     $applyFilter = function ($query, $modelType) use ($guard, $user, $customerLoginIds,$customerId, $city_id, $zone_id , $accountability_Type) {
//         if ($modelType === 'rider') {
//             return $query->whereHas('VehicleRequest', function ($q) use ($guard, $user, $customerLoginIds, $city_id, $zone_id , $accountability_Type) {
//                 if ($customerLoginIds->isNotEmpty()) {
//                         $q->whereIn('created_by', $customerLoginIds);
//                     }
//                 if ($city_id) $q->where('city_id', $city_id);
//                 elseif ($guard === 'master') $q->where('city_id', $user->city_id);
//                 if ($zone_id) $q->whereIn('zone_id', $zone_id);
//                 elseif ($guard === 'zone'){
//                     $q->where('city_id', $user->city_id);
//                     $q->where('zone_id', $user->zone_id);
//                 } 
                
//             if (!empty($accountability_Type)) {
//                 $q->whereIn('account_ability_type', $accountability_Type);
//             }
            
//             $q->whereHas('assignment.vehicle.quality_check',function ($p) use ($vehicle_type,$vehicle_model,$vehicle_make){
//                 if($vehicle_type) $p->whereIn('vehicle_type',$vehicle_type);
//                 if($vehicle_model) $p->whereIn('vehicle_model',$vehicle_model);
//                 $p->whereHas('vehicle_model_relation',function ($r) use ($vehicle_make){
//                     if($vehicle_make) $p->whereIn('make',$vehicle_make);
//                 } );
//             });
//             });
//         } elseif ($modelType === 'assignment') {
//             return $query->whereHas('vehicleRequest', function ($q) use ($guard, $user, $customerLoginIds, $city_id, $zone_id) {
//                 if ($customerLoginIds->isNotEmpty()) {
//                         $q->whereIn('created_by', $customerLoginIds);
//                     }
//                 if ($city_id) $q->where('city_id', $city_id);
//                 elseif ($guard === 'master') $q->where('city_id', $user->city_id);
//                 if ($zone_id) $q->whereIn('zone_id', $zone_id);
//                 elseif ($guard === 'zone') $q->where('zone_id', $user->zone_id);
//             }) ->whereHas('vehicle.quality_check', function ($q) use ($customerId, $accountability_Type) {
//             // QualityCheck filters
//             $q->where('customer_id', $customerId)
//               ->whereIn('accountability_type', $accountability_Type);
            
//             $q->whereHas('vehicle.quality_check',function ($p) use ($vehicle_type,$vehicle_model,$vehicle_make){
//                 if($vehicle_type) $p->whereIn('vehicle_type',$vehicle_type);
//                 if($vehicle_model) $p->whereIn('vehicle_model',$vehicle_model);
//                 $p->whereHas('vehicle_model_relation',function ($r) use ($vehicle_make){
//                     if($vehicle_make) $p->whereIn('make',$vehicle_make);
//                 } );
//             });
//         });
//         } else {
           
            
//             return $query->whereHas('assignment.vehicleRequest', function ($q) use ($guard, $user, $customerLoginIds, $city_id, $zone_id) {
//                 if ($customerLoginIds->isNotEmpty()) {
//                         $q->whereIn('created_by', $customerLoginIds);
//                     }
//                 if ($city_id) $q->where('city_id', $city_id);
//                 elseif ($guard === 'master') $q->where('city_id', $user->city_id);
//                 if ($zone_id) $q->whereIn('zone_id', $zone_id);
//                 elseif ($guard === 'zone') $q->where('zone_id', $user->zone_id);
//             }) 
//             ->whereHas('assignment.vehicle.quality_check', function ($q) use ($customerId, $accountability_Type) {
//             // QualityCheck filters
//             $q->where('customer_id', $customerId)
//               ->whereIn('accountability_type', $accountability_Type);
            
//             $q->whereHas('assignment.vehicle.quality_check',function ($p) use ($vehicle_type,$vehicle_model,$vehicle_make){
//                 if($vehicle_type) $p->whereIn('vehicle_type',$vehicle_type);
//                 if($vehicle_model) $p->whereIn('vehicle_model',$vehicle_model);
//                 $p->whereHas('vehicle_model_relation',function ($r) use ($vehicle_make){
//                     if($vehicle_make) $p->whereIn('make',$vehicle_make);
//                 } );
//             });
//         });
//         }
//     };

//     // === Generate Labels Dynamically ===
//     $labels = [];
//     $period = CarbonPeriod::create($start_date, $interval, $end_date);
//     foreach ($period as $dt) {
//         if ($interval === '2 hours') $labels[] = $dt->format('H:i');
//         elseif ($interval === '1 day') $labels[] = $dt->format('d M');
//         elseif ($interval === '1 month') $labels[] = $dt->format('M Y');
//     }
    
    
    
// // -------------------- TOTAL VEHICLES --------------------
// $total_vehicles = AssetMasterVehicle::whereHas('quality_check', function ($query) use ($user, $zone_id, $guard, $customerId, $accountability_Type,$vehicle_model,$vehicle_type,$vehicle_make) {
//     if ($guard === 'master') {
//         $query->where('location', $user->city_id);

//         if (!empty($zone_id)) {
//             $query->whereIn('zone_id', $zone_id);
//         }
//     } elseif ($guard === 'zone') {
//         $query->where('location', $user->city_id)
//               ->where('zone_id', $user->zone_id);
//     }

//     $query->whereIn('accountability_type', $accountability_Type)
//           ->where('customer_id', $customerId);
    
    
//     if($vehicle_type) $query->whereIn('vehicle_type',$vehicle_type);
//     if($vehicle_model) $query->whereIn('vehicle_model',$vehicle_model);
//     $query->whereHas('vehicle_model_relation',function ($r) use ($vehicle_make){
//                     if($vehicle_make) $p->whereIn('make',$vehicle_make);
//     } );
            
// })
// ->when(in_array(1, $accountability_Type) && !empty($zone_id), function ($query) use ($zone_id , $customerId) {
//     $query->where('client', $customerId);
// })
// ->count();


// // -------------------- TOTAL RFD --------------------
// $total_rfd = AssetVehicleInventory::where('transfer_status', 3)
//     ->whereHas('assetVehicle', function ($query) use ($customerId, $user, $guard, $accountability_Type, $zone_id) {
//         $query->whereHas('quality_check', function ($qc) use ($user, $zone_id, $guard, $customerId, $accountability_Type) {
//             $qc->where('customer_id', $customerId);

//             if ($guard === 'master') {
//                 $qc->where('location', $user->city_id);

//                 if (!empty($zone_id)) {
//                     $qc->whereIn('zone_id', $zone_id);
//                 }
//             } elseif ($guard === 'zone') {
//                 $qc->where('location', $user->city_id)
//                   ->where('zone_id', $user->zone_id);
//             }

//             $qc->whereIn('accountability_type', $accountability_Type);
           
//             if($vehicle_type) $qc->whereIn('vehicle_type',$vehicle_type);
//             if($vehicle_model) $qc->whereIn('vehicle_model',$vehicle_model);
//             $qc->whereHas('vehicle_model_relation',function ($p) use ($vehicle_make){
//                 if($vehicle_make) $p->whereIn('make',$vehicle_make);
//             } );
            
//         });

        
//         $query->when(in_array(1, $accountability_Type) && !empty($zone_id), function ($q) use ($zone_id , $customerId) {
//             $q->where('client', $customerId);
//         });
//     })
//     ->count();

    



//     // === Metrics ===
//     $totalAssignedCurrent = $applyFilter(B2BVehicleAssignment::query(), 'assignment')
//         // ->whereBetween('created_at', [$start_date, $end_date])
//         ->where('status','running')->count();

//     $totalReturnCurrent = $applyFilter(B2BReturnRequest::query(), 'request')
//         ->whereBetween('created_at', [$start_date, $end_date])->where('status','closed')->count();
        
//     $totalReturnRequests = $applyFilter(B2BReturnRequest::query(), 'request')
//         ->whereBetween('created_at', [$start_date, $end_date])->count();

//     $totalServiceCurrent = $applyFilter(B2BServiceRequest::query(), 'request')
//     ->where('status', '!=', 'closed')
//         ->whereBetween('created_at', [$start_date, $end_date])->count();

//     $totalAccidentCurrent = $applyFilter(B2BReportAccident::query(), 'request')
//         ->whereBetween('created_at', [$start_date, $end_date])->count();
    

//     $totalRecoveryCurrent = $applyFilter(B2BRecoveryRequest::query(), 'request')
//         // ->where('status', '!=', 'closed')
//         ->whereBetween('created_at', [$start_date, $end_date])->count();
    
//     $totalRecovered = $applyFilter(B2BRecoveryRequest::query(), 'request')
//         ->where('status', 'closed')
//         ->whereBetween('created_at', [$start_date, $end_date])->count();  

//     $totalActiveRider = $applyFilter(B2BRider::query(), 'rider')
//         ->whereHas('latestVehicleRequest', function ($q) use ($start_date,$end_date) {
//         $q->where('is_active', 1);
//     })
//     // ->whereBetween('created_at', [$start_date, $end_date])->where('status', 1)
//     ->count();
    
//     $totalInactiveRider = $applyFilter(B2BRider::query(), 'rider')
//     ->where(function ($q) {
//         $q->doesntHave('latestVehicleRequest') // riders with no request
//           ->orWhereHas('latestVehicleRequest', function ($q2) {
//               $q2->where('is_active', 0); // last request inactive
//           });
//     })
//     // ->whereBetween('created_at', [$start_date, $end_date])
//     ->count();

//     // Return Reasons
//     $contractEnd = $applyFilter(B2BReturnRequest::query(), 'request')->whereBetween('created_at', [$start_date, $end_date])->where('return_reason','Contract End')->count();
//     $performanceIssue = $applyFilter(B2BReturnRequest::query(), 'request')->whereBetween('created_at', [$start_date, $end_date])->where('return_reason','Performance Issue')->count();
//     $vehicleIssue = $applyFilter(B2BReturnRequest::query(), 'request')->whereBetween('created_at', [$start_date, $end_date])->where('return_reason','Vehicle Issue')->count();
//     $noLongerNeeded = $applyFilter(B2BReturnRequest::query(), 'request')->whereBetween('created_at', [$start_date, $end_date])->where('return_reason','No Longer Needed')->count();

//     // Accident Types
//     $collision = $applyFilter(B2BReportAccident::query(), 'request')->whereBetween('created_at', [$start_date, $end_date])->where('accident_type','Collision')->count();
//     $fall = $applyFilter(B2BReportAccident::query(), 'request')->whereBetween('created_at', [$start_date, $end_date])->where('accident_type','Fall')->count();
//     $fire = $applyFilter(B2BReportAccident::query(), 'request')->whereBetween('created_at', [$start_date, $end_date])->where('accident_type','Fire')->count();
//     $other = $applyFilter(B2BReportAccident::query(), 'request')->whereBetween('created_at', [$start_date, $end_date])->where('accident_type','Other')->count();

//     // === Service Chart Data ===
//     $serviceStatuses = ['unassigned' , 'inprogress' , 'closed'];
//     $serviceChartData = [];
//     foreach ($serviceStatuses as $status) {
//         $chartData = [];
//         foreach ($period as $dt) {
//             $query = $applyFilter(B2BServiceRequest::query(), 'request')->where('status', $status);

//             if ($interval === '2 hours') $chartData[] = $query->whereBetween('created_at', [$dt, $dt->copy()->addHours(2)])->count();
//             elseif ($interval === '1 day') $chartData[] = $query->whereDate('created_at', $dt->toDateString())->count();
//             elseif ($interval === '1 month') $chartData[] = $query->whereMonth('created_at', $dt->month)->whereYear('created_at', $dt->year)->count();
//         }
//         $colorMap = ['unassigned'=>'#CAEDCE','inprogress'=>'#EECACB','closed'=>'#C0E0DF'];
//         $serviceChartData[] = [
//             'label'=>ucfirst(str_replace('_',' ',$status)),
//             'data'=>$chartData,
//             'borderColor'=>$colorMap[$status],
//             'backgroundColor'=>$colorMap[$status],
//             'fill'=>false,
//             'tension'=>0.4
//         ];
//     }

//     return response()->json([
//         'assigned_vehicles'=>['current'=>$totalAssignedCurrent,'previous'=>0,'change_percent' => 0],
//         'return_requests'=>['current'=>$totalReturnCurrent,'previous'=>0,'change_percent' =>0],
//         'service_requests'=>['current'=>$totalServiceCurrent,'previous'=>0 ,'change_percent' =>0],
//         'recovery_requests'=>['current'=>$totalRecoveryCurrent,'previous'=>0 ,'change_percent' => 0],
//         'accident_report'=>['current'=>$totalAccidentCurrent,'previous'=>0, 'change_percent' => 0],
//         'totalActiveRider'=>$totalActiveRider,
//         'totalInactiveRider'=>$totalInactiveRider,
//         'contractEnd'=>$contractEnd,
//         'performanceIssue'=>$performanceIssue,
//         'totalReturnRequests'=>$totalReturnRequests,
//         'vehicleIssue'=>$vehicleIssue,
//         'noLongerNeeded'=>$noLongerNeeded,
//         'collision'=>$collision,
//         'fall'=>$fall,
//         'fire'=>$fire,
//         'other'=>$other,
//         'labels'=>$labels,
//         'serviceChartData'=>$serviceChartData,
//         'total_vehicles' =>$total_vehicles , 
//         'total_rfd_vehicles' =>$total_rfd,
//         'totalRecovered'=>$totalRecovered,
//         'accountability_Type' =>$accountability_Type
//     ]);
// }




public function admin_dashboard(){
    return view('b2b::admin_dashboard');
}


/**
 * Helper: Calculate percentage change
 */
private function calculatePercentageChange($previous, $current)
{
    if ($previous == 0) {
        return $current > 0 ? 100 : 0;
    }

    return round((($current - $previous) / $previous) * 100, 2);
}


    
}
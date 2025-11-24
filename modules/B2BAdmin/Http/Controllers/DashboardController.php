<?php

namespace Modules\B2BAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\B2B\Entities\B2BAgent;
use Modules\MasterManagement\Entities\CustomerLogin;
use Modules\AssetMaster\Entities\AssetVehicleInventory;
use Modules\B2B\Entities\B2BVehicleRequests;
use Modules\B2B\Entities\B2BReturnRequest;
use Modules\B2B\Entities\B2BRecoveryRequest;
use Modules\B2B\Entities\B2BReportAccident;
use Modules\B2B\Entities\B2BServiceRequest;
use Modules\B2B\Entities\B2BVehicleAssignment;
use Modules\City\Entities\City;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Modules\MasterManagement\Entities\EvTblAccountabilityType; // updated by logesh
use Modules\MasterManagement\Entities\CustomerMaster; //updated by logesh

class DashboardController extends Controller
{

public function index()
{
    // Basic counts
    

    // Current month range
    // $start_date = Carbon::now()->startOfMonth();
    // $end_date   = Carbon::now()->endOfMonth();
    
    $start_date = Carbon::now()->startOfYear();
    $end_date   = Carbon::now()->endOfYear();
            
    // $days_in_month = $start_date->daysInMonth;
    
    // $start_date_formatted = $start_date->format('M d, Y'); // e.g. Sep 01, 2025
    // $end_date_formatted   = $end_date->format('M d, Y'); 
    
    
    // // Previous month range
    // $prev_start_date = Carbon::now()->subMonth()->startOfMonth();
    // $prev_end_date   = Carbon::now()->subMonth()->endOfMonth();
    
    $days_in_month = $start_date->daysInMonth;
    
    // Formatted dates
    $start_date_formatted = $start_date->format('M d, Y'); // e.g. Jan 01, 2025
    $end_date_formatted   = $end_date->format('M d, Y');   // e.g. Dec 31, 2025
    
    // Previous year range
    $prev_start_date = Carbon::now()->subYear()->startOfYear(); // Jan 01, 2024
    $prev_end_date   = Carbon::now()->subYear()->endOfYear();
    
    $agent_count  = B2BAgent::where('role', 17)->where('status', 'Active')->whereBetween('created_at', [$start_date, $end_date])
                    ->count();
    $client_count = CustomerLogin::whereBetween('created_at', [$start_date, $end_date])
                    ->count();
                    
    // === Counts for current month ===
    $rfd_count_current = AssetVehicleInventory::where('transfer_status', 3)
    ->whereHas('assetVehicle', function ($q) use ($start_date, $end_date) {
        $q->whereBetween('created_at', [$start_date, $end_date]);
    })
    ->count();

    $deploy_count_current = B2BVehicleRequests::where('status', 'completed')
        ->where('is_active', 1)
        ->whereBetween('created_at', [$start_date, $end_date])
        ->count();

    $return_count_current = B2BReturnRequest::where('status', 'closed')
        ->whereBetween('created_at', [$start_date, $end_date])
        ->count();

    // === Counts for previous month ===
    $rfd_count_prev = AssetVehicleInventory::where('transfer_status', 3)
    ->whereHas('assetVehicle', function ($q) use ($prev_start_date, $prev_end_date) {
        $q->whereBetween('created_at', [$prev_start_date, $prev_end_date]);
    })
    ->count();

    $deploy_count_prev = B2BVehicleRequests::where('status', 'completed')
        ->where('is_active', 1)
        ->whereBetween('created_at', [$prev_start_date, $prev_end_date])
        ->count();

    $return_count_prev = B2BReturnRequest::where('status', 'closed')
        ->whereBetween('created_at', [$prev_start_date, $prev_end_date])
        ->count();

    // === Percentage change helper ===
    // $calculateChange = function ($prev, $current) {
    //     if ($prev == 0 && $current == 0) return 0;
    //     if ($prev == 0) return 100;
    //     return round((($current - $prev) / $prev) * 100, 2);
    // };

    // === Wrap up counts ===
    $rfd_count = [
        'current' => $rfd_count_current,
        'previous' => $rfd_count_prev,
        'change_percent' => $this->calculateChange($rfd_count_prev, $rfd_count_current),
    ];

    $deploy_count = [
        'current' => $deploy_count_current,
        'previous' => $deploy_count_prev,
        'change_percent' => $this->calculateChange($deploy_count_prev, $deploy_count_current),
    ];
  
    $return_count = [
        'current' => $return_count_current,
        'previous' => $return_count_prev,
        'change_percent' => $this->calculateChange($return_count_prev, $return_count_current),
    ];
    
    
    $zones = B2BVehicleAssignment::whereHas('vehicleRequest', function ($q) use ($start_date,$end_date) {
            $q->where('is_active', 1)->whereBetween('created_at', [$start_date, $end_date]);
        })
        ->with(['vehicleRequest.city'])
        ->get()
        ->groupBy(fn($assignment) => $assignment->vehicleRequest->city->id ?? 'unknown')
        ->map(function ($assignments) {
            $city = $assignments->first()->vehicleRequest->city ?? null;

            return [
                'city_id'   => $city?->id,
                'city_name' => $city?->city_name ?? 'Unknown',
                'vehicle_count' => $assignments->count(),
            ];
        })
        ->filter(fn($zone) => $zone['vehicle_count'] > 0) 
        ->values();
        
    $clientWiseDeploymentData = B2BVehicleAssignment::whereHas('vehicleRequest', function ($q) use ($start_date,$end_date) {
        $q->where('status', 'completed')->whereBetween('created_at', [$start_date, $end_date]);
        // $q->where('is_active', 1)->where('status', 'completed')->whereBetween('created_at', [$start_date, $end_date]);
    })
    ->with(['vehicleRequest.city', 'rider.customerLogin.customer_relation'])
    ->get()
    ->groupBy(fn($assignment) => $assignment->rider->customerLogin->customer_relation->trade_name ?? 'Unknown')
    ->map(function ($assignments) {
        $client = $assignments->first()->rider->customerLogin->customer_relation ?? null;

        return [
            'client_name'    => $client?->trade_name ?? 'Unknown',
            'vehicle_count'  => $assignments->count(),
        ];
    })
    ->filter(fn($item) => $item['vehicle_count'] > 0) // only include clients with vehicles
    ->values();
    
    // $generateDailyCounts = function ($model,$type=null) use ($start_date, $end_date, $days_in_month) {
    //     $counts = [];
    //     for ($day = 1; $day <= $days_in_month; $day++) {
    //         $date = $start_date->copy()->day($day);
    //         $query = $model::whereDate('created_at', $date);

    //         // Extra filter only for return
    //         if ($type === 'return') {
    //             $query->where('status', 'closed');
    //         }
    //         elseif ($type === 'service') {
    //             $query->where('status', 'closed');
    //         }
    //         elseif ($type === 'accident') {
    //             $query->where('status', 'claim_closed');
    //         }
    //         elseif ($type === 'recovery') {
    //             $query->where('status', 'closed');
    //         }
    //         $counts[] = $query->count();
    //     }
    //     return $counts;
    // };

    $generateMonthlyCounts = function ($model, $type = null) use ($start_date, $end_date) {
    $counts = [];

    // Loop through each month in the year
    $period = CarbonPeriod::create($start_date, '1 month', $end_date);

    foreach ($period as $date) {
        $query = $model::whereBetween('created_at', [
            $date->copy()->startOfMonth(),
            $date->copy()->endOfMonth()
        ]);

        // Extra filters
        if ($type === 'return') {
            $query->where('status', 'closed');
        } elseif ($type === 'service') {
            $query->where('status', 'closed');
        } elseif ($type === 'accident') {
            $query->where('status', 'claim_closed');
        } elseif ($type === 'recovery') {
            $query->where('status', 'closed');
        }

        $counts[] = $query->count();
    }

    return $counts;
};


    $serviceChartData  = $generateMonthlyCounts(B2BServiceRequest::class,'service');
    $returnChartData   = $generateMonthlyCounts(B2BReturnRequest::class,'return');
    $accidentChartData = $generateMonthlyCounts(B2BReportAccident::class,'accident');
    $recoveryChartData = $generateMonthlyCounts(B2BRecoveryRequest::class,'recovery');
    
        // === Service Requests ===
        $totalServiceCurrent = B2BServiceRequest::where('status', 'closed')->whereBetween('created_at', [$start_date, $end_date])->count();
        $totalServicePrev = B2BServiceRequest::where('status', 'closed')->whereBetween('created_at', [$prev_start_date, $prev_end_date])->count();
    
        // === Accident Reports ===
        $totalAccidentCurrent = B2BReportAccident::where('status', 'claim_closed')->whereBetween('created_at', [$start_date, $end_date])->count();
        $totalAccidentPrev = B2BReportAccident::where('status', 'claim_closed')->whereBetween('created_at', [$prev_start_date, $prev_end_date])->count();
    
        // === Recovery Requests ===
        $totalRecoveryCurrent = B2BRecoveryRequest::where('status', 'closed')->whereBetween('created_at', [$start_date, $end_date])->count();
        $totalRecoveryPrev = B2BRecoveryRequest::where('status', 'closed')->whereBetween('created_at', [$prev_start_date, $prev_end_date])->count();
        
        
        $service_count = [
            'current' => $totalServiceCurrent,
            'previous' => $totalServicePrev,
            'change_percent' => $this->calculateChange( $totalServicePrev,$totalServiceCurrent),
        ];
        
        $accident_count = [
            'current' => $totalAccidentCurrent,
            'previous' => $totalAccidentPrev,
            'change_percent' => $this->calculateChange( $totalAccidentPrev,$totalAccidentCurrent),
        ];
        
        $recovery_count = [
            'current' => $totalRecoveryCurrent,
            'previous' => $totalRecoveryPrev,
            'change_percent' => $this->calculateChange( $totalRecoveryPrev,$totalRecoveryCurrent),
        ];
        
        // print_r($service_count);exit;
        $cities = City::where('status', 1)->get();
        
        // $labels = [];
        //     for ($day = 1; $day <= $days_in_month; $day++) {
        //         $labels[] = $start_date->copy()->day($day)->format('M d'); // Example: "Sep 01"
        //     }
        
        $labels = [];
        $period = CarbonPeriod::create($start_date, '1 month', $end_date);
        
        foreach ($period as $date) {
            $labels[] = $date->format('M Y'); // Example: "Jan 2025"
        }
        
        $accountability_types = EvTblAccountabilityType::where('status', 1) //updated by logesh
                ->orderBy('id', 'desc')
                ->get();
                
        $customers = CustomerMaster::select('id','trade_name')->where('status', 1) //updated by logesh
                ->orderBy('id', 'desc')
                ->get();
                
    return view('b2badmin::dashboard', compact(
        'agent_count',
        'client_count',
        'start_date_formatted',
        'end_date_formatted',
        'rfd_count',
        'deploy_count',
        'return_count',
        'recovery_count',
        'service_count',
        'accident_count',
        'zones',
        'serviceChartData',
        'returnChartData',
        'accidentChartData',
        'recoveryChartData',
        'clientWiseDeploymentData',
        'cities',
        'labels',
        'accountability_types',
        'customers'
    ));
}


public function filter(Request $request)
{
        // === Base Query Filters (City, Zone) ===
    $city_id = $request->city_id ?? null;
    $zone_id = $request->zone_id ?? null;
    $accountability_type = $request->accountability_type ?? null;
    $customer_id = $request->customer_id ?? null;
    
    $recovery_status = !empty($request->recovery_status) ? $request->recovery_status : 'closed';
    $accident_status = !empty($request->accident_status) ? $request->accident_status : 'claim_closed';
    $return_status   = !empty($request->return_status)   ? $request->return_status   : 'closed';
    $service_status  = !empty($request->service_status)  ? $request->service_status  : 'closed';
    // === Date Filters ===
    
    // if(empty($request->quick_date_filter) && empty($request->from_date) && empty($request->to_date)){
    //   $request->quick_date_filter = 'year'; 
    // }
    if($customer_id){
        $accountability = CustomerMaster::where('id',$customer_id)->pluck('accountability_type_id');
    }
        if (!empty($request->quick_date_filter)) {
            switch ($request->quick_date_filter) {
                case 'today':
                    $start_date = Carbon::today();
                    $end_date   = Carbon::today();
                    $prev_start_date = Carbon::yesterday();
                    $prev_end_date   = Carbon::yesterday();
                    break;
                case 'week':
                    $start_date = Carbon::now()->startOfWeek();
                    $end_date   = Carbon::now()->endOfWeek();
                    $prev_start_date = Carbon::now()->subWeek()->startOfWeek();
                    $prev_end_date   = Carbon::now()->subWeek()->endOfWeek();
                    break;
                case 'month':
                    $start_date = Carbon::now()->startOfMonth();
                    $end_date   = Carbon::now()->endOfMonth();
                    $prev_start_date = Carbon::now()->subMonth()->startOfMonth();
                    $prev_end_date   = Carbon::now()->subMonth()->endOfMonth();
                    break;
                case 'year':
                    $start_date = Carbon::now()->startOfYear();
                    $end_date   = Carbon::now()->endOfYear();
                    $prev_start_date = Carbon::now()->subYear()->startOfYear();
                    $prev_end_date   = Carbon::now()->subYear()->endOfYear();
                    break;
                default:
                    $start_date = Carbon::now()->startOfMonth();
                    $end_date   = Carbon::now()->endOfMonth();
                    $prev_start_date = Carbon::now()->subMonth()->startOfMonth();
                    $prev_end_date   = Carbon::now()->subMonth()->endOfMonth();
            }
        } 
        elseif ($request->from_date && $request->to_date) {
    $start_date = Carbon::parse($request->from_date);
    $end_date   = Carbon::parse($request->to_date);

    $days_in_range = $start_date->diffInDays($end_date) + 1;

    $prev_end_date   = $start_date->copy()->subDay();
    $prev_start_date = $prev_end_date->copy()->subDays($days_in_range - 1);

    $labels = [];
    $intervals = [];

    if ($days_in_range <= 7) {
    for ($i = 0; $i < $days_in_range; $i++) {
        $date = $start_date->copy()->addDays($i);
        $labels[] = $date->format('D');
        $intervals[] = [$date->copy()->startOfDay(), $date->copy()->endOfDay()];
    }
    } elseif ($days_in_range <= 31) {
        for ($i = 0; $i < $days_in_range; $i++) {
            $date = $start_date->copy()->addDays($i);
            $labels[] = $date->format('M d');
            $intervals[] = [$date->copy()->startOfDay(), $date->copy()->endOfDay()];
        }
    } else {
        $period = CarbonPeriod::create($start_date->copy()->startOfMonth(), '1 month', $end_date->copy()->endOfMonth());
        foreach ($period as $date) {
            $labels[] = $date->format('M Y');
            $intervals[] = [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()];
        }
    }
}
        else {
           
                    $start_date = Carbon::now()->startOfYear();
                    $end_date   = Carbon::now()->endOfYear();
                    $prev_start_date = Carbon::now()->subYear()->startOfYear();
                    $prev_end_date   = Carbon::now()->subYear()->endOfYear();
                    
                    $labels = [];
                    $intervals = [];
                   
                    for ($i = 0; $i < 12; $i++) {
                        $from = $start_date->copy()->addMonths($i)->startOfMonth();
                        $to   = $from->copy()->endOfMonth();
                        $labels[] = $from->format('M'); // Jan, Feb, ...
                        $intervals[] = [$from, $to];
                    }
                 
        }


   
       
    // === Agent ===
    $agent_query = B2BAgent::where('role', 17)
        ->where('status', 'Active')
        ->whereBetween('created_at', [$start_date, $end_date]);

    if ($city_id) {
        $agent_query->where('city_id', $city_id);
    }
    if ($zone_id) {
        $agent_query->where('zone_id', $zone_id);
    }

    $agent_count = $agent_query->count();

    // === Client ===
    $client_query = CustomerLogin::whereBetween('created_at', [$start_date, $end_date]);

    if ($city_id) {
        $client_query->where('city_id', $city_id);
    }
    if ($zone_id) {
        $client_query->where('zone_id', $zone_id);
    }
    

    $client_count = $client_query->count();

    // === RFD ===
    $rfd_count_current = AssetVehicleInventory::where('transfer_status', 3)
        ->whereHas('assetVehicle', function ($q) use ($start_date, $end_date,$accountability_type,$customer_id) {
            $q->whereBetween('created_at', [$start_date, $end_date]);
            if ($accountability_type && $accountability_type == 1 && $customer_id) {
                $q->where('client', $customer_id);
            }
        })
        ->whereHas('assetVehicle.quality_check', function ($q) use ($city_id, $zone_id,$accountability_type,$customer_id) {
            if ($city_id) {
                $q->where('location', $city_id);
            }
            if ($zone_id) {
                $q->where('zone_id', $zone_id);
            }
            if ($accountability_type) {
                $q->where('accountability_type', $accountability_type);
            }
            
            if ($accountability_type && $accountability_type == 2 && $customer_id) {
                $q->where('customer_id', $customer_id);
            }elseif($customer_id){
                $q->where('customer_id', $customer_id);
            }
        })
    
        ->count();

    $rfd_count_prev = AssetVehicleInventory::where('transfer_status', 3)
        ->whereHas('assetVehicle', function ($q) use ($prev_start_date, $prev_end_date) {
            $q->whereBetween('created_at', [$prev_start_date, $prev_end_date]);
        })
        ->whereHas('assetVehicle.quality_check', function ($q) use ($city_id, $zone_id) {
            if ($city_id) {
                $q->where('location', $city_id);
            }
            if ($zone_id) {
                $q->where('zone_id', $zone_id);
            }
        })
        ->count();

    // === Deployment ===
    $deploy_current_query = B2BVehicleRequests::where('status', 'completed')
        // ->where('is_active', 1)
        ->whereBetween('created_at', [$start_date, $end_date]);
    if ($city_id) {
        $deploy_current_query->where('city_id', $city_id);
    }
    if ($zone_id) {
        $deploy_current_query->where('zone_id', $zone_id);
    }
    if ($accountability_type) {
        $deploy_current_query->where('account_ability_type', $accountability_type);
    }
    if ($customer_id ) {
        $deploy_current_query->whereHas('assignment.vehicleRequest.customerLogin', function($q) use ($customer_id) {
            if ($customer_id) $q->where('customer_id', $customer_id);
        });
    }
                    
    $deploy_count_current = $deploy_current_query->count();

    $deploy_prev_query = B2BVehicleRequests::where('status', 'completed')
        ->where('is_active', 1)
        ->whereBetween('created_at', [$prev_start_date, $prev_end_date]);
    if ($city_id) {
        $deploy_prev_query->where('city_id', $city_id);
    }
    if ($zone_id) {
        $deploy_prev_query->where('zone_id', $zone_id);
    }
    $deploy_count_prev = $deploy_prev_query->count();

    // === Return ===
    $return_count_current = B2BReturnRequest::where('status', $return_status)
        ->whereBetween('created_at', [$start_date, $end_date])
        ->whereHas('assignment.vehicleRequest', function ($q) use ($city_id, $zone_id,$accountability_type) {
            if ($city_id) {
                $q->where('city_id', $city_id);
            }
            if ($zone_id) {
                $q->where('zone_id', $zone_id);
            }
            if ($accountability_type) {
                $q->where('account_ability_type', $accountability_type);
            }
        })
        ->whereHas('assignment.vehicleRequest.customerLogin', function($q) use ($customer_id) {
            if ($customer_id) $q->where('customer_id', $customer_id);
            })                
                    
        ->count();

    $return_count_prev = B2BReturnRequest::where('status', $return_status)
        ->whereBetween('created_at', [$prev_start_date, $prev_end_date])
        ->whereHas('assignment.vehicleRequest', function ($q) use ($city_id, $zone_id) {
            if ($city_id) {
                $q->where('city_id', $city_id);
            }
            if ($zone_id) {
                $q->where('zone_id', $zone_id);
            }
        })
        ->count();

    // === Helper for change % ===
    // $calculateChange = function ($prev, $current) {
    //     if ($prev == 0 && $current == 0) return 0;
    //     if ($prev == 0) return 100;
    //     return round((($current - $prev) / $prev) * 100, 2);
    // };

    // === Wrap counts ===
    $rfd_count = [
        'current' => $rfd_count_current,
        'previous' => $rfd_count_prev,
        'change_percent' => $this->calculateChange($rfd_count_prev, $rfd_count_current),
    ];

    $deploy_count = [
        'current' => $deploy_count_current,
        'previous' => $deploy_count_prev,
        'change_percent' => $this->calculateChange($deploy_count_prev, $deploy_count_current),
    ];

    $return_count = [
        'current' => $return_count_current,
        'previous' => $return_count_prev,
        'change_percent' => $this->calculateChange($return_count_prev, $return_count_current),
    ];

    // === Zones List (with city & zone filter support) ===
    $zones = B2BVehicleAssignment::whereHas('vehicleRequest', function ($q) use ($city_id, $zone_id,$start_date, $end_date,$accountability_type) {
            $q->where('is_active', 1)->whereBetween('created_at', [$start_date, $end_date]);
            if ($city_id) {
                $q->where('city_id', $city_id);
            }
            if ($zone_id) {
                $q->where('zone_id', $zone_id);
            }
            if ($accountability_type) {
                $q->where('account_ability_type', $accountability_type);
            }
        })
        ->whereHas('vehicleRequest.customerLogin',function($q) use ($customer_id){
             $q->where('customer_id', $customer_id);
        })
        ->with(['vehicleRequest.city','vehicleRequest.customerLogin'])
        ->get()
        ->groupBy(fn($assignment) => $assignment->vehicleRequest->city->id ?? 'unknown')
        ->map(function ($assignments) {
            $city = $assignments->first()->vehicleRequest->city ?? null;
            return [
                'city_id'       => $city?->id,
                'city_name'     => $city?->city_name ?? 'Unknown',
                'vehicle_count' => $assignments->count(),
            ];
        })
        ->filter(fn($zone) => $zone['vehicle_count'] > 0)
        ->values();
    
    // $clientWiseDeploymentData = B2BVehicleAssignment::whereHas('vehicleRequest', function ($q) use ($city_id, $zone_id,$start_date, $end_date,$accountability_type,$customer_id) {
    //     $q->where('status', 'completed')
    //     ->where('is_active', 1)
    //     ->whereBetween('created_at', [$start_date, $end_date]);
    //     if($city_id){
    //         $q->where('city_id', $city_id); 
    //     }
    //     if($zone_id){
    //         $q->where('zone_id', $zone_id);
    //     }
    //     if($accountability_type){
    //         $q->where('account_ability_type', $accountability_type);
    //     }
    // })
    // ->whereHas('rider.customerLogin', function ($q) use ($customer_id){
    //     $q->where('customer_id', $customer_id);
    // })
    // ->with(['vehicleRequest.city', 'rider.customerLogin.customer_relation'])
    // ->get()
    // ->groupBy(fn($assignment) => $assignment->rider->customerLogin->customer_relation->trade_name ?? 'Unknown')
    // ->map(function ($assignments) {
    //     $client = $assignments->first()->rider->customerLogin->customer_relation ?? null;

    //     return [
    //         'client_name'    => $client?->trade_name ?? 'Unknown',
    //         'vehicle_count'  => $assignments->count(),
    //     ];
    // })
    // ->filter(fn($item) => $item['vehicle_count'] > 0) // only include clients with vehicles
    // ->values();
    
    $baseQuery = B2BVehicleAssignment::whereHas('vehicleRequest', function ($q) use ($city_id, $zone_id, $start_date, $end_date, $accountability_type) {
        $q->where('status', 'completed')
          ->where('is_active', 1)
          ->whereBetween('created_at', [$start_date, $end_date]);

        if ($city_id) {
            $q->where('city_id', $city_id);
        }
        if ($zone_id) {
            $q->where('zone_id', $zone_id);
        }

        // Only apply accountability_type filter if explicitly provided.
        if ($accountability_type) {
            $q->where('account_ability_type', $accountability_type);
        }
    });

    // Apply customer filter only when provided
    $baseQuery->when($customer_id, function ($q) use ($customer_id) {
        return $q->whereHas('rider.customerLogin', function ($qq) use ($customer_id) {
            $qq->where('customer_id', $customer_id);
        });
    });

    $assignments = $baseQuery->with(['vehicleRequest.city', 'rider.customerLogin.customer_relation'])->get();

    // helper for mapping type to label
    $typeLabel = function ($t) {
        return $t == 2 ? 'Fixed' : ($t == 1 ? 'Variable' : 'Unknown');
    };

    if ($customer_id) {
        // Group by accountability type (from vehicleRequest)
        $groupedByType = $assignments->groupBy(fn($a) => $a->vehicleRequest->account_ability_type ?? 0);

        if ($accountability_type) {
            // show only selected accountability type
            $clientWiseDeploymentData = collect([
                [
                    'client_name'   => $typeLabel((int)$accountability_type),
                    'vehicle_count' => $groupedByType->get((int)$accountability_type)?->count() ?? 0,
                ]
            ]);
        } else {
            // show both Fixed (1) and Variable (2)
            $clientWiseDeploymentData = collect([
                [
                    'client_name'   => 'Fixed',
                    'vehicle_count' => $groupedByType->get(2)?->count() ?? 0,
                ],
                [
                    'client_name'   => 'Variable',
                    'vehicle_count' => $groupedByType->get(1)?->count() ?? 0,
                ]
            ]);
        }
    } else {
        // original client-wise grouping (when no specific customer filter)
        $clientWiseDeploymentData = $assignments
            ->groupBy(fn($assignment) => $assignment->rider->customerLogin->customer_relation->trade_name ?? 'Unknown')
            ->map(function ($assignments) {
                $client = $assignments->first()->rider->customerLogin->customer_relation ?? null;

                return [
                    'client_name'    => $client?->trade_name ?? 'Unknown',
                    'vehicle_count'  => $assignments->count(),
                ];
            })
            ->filter(fn($item) => $item['vehicle_count'] > 0)
            ->values();
    }
    
    // === Dates for display ===
    $start_date_formatted = $start_date->format('M d, Y');
    $end_date_formatted   = $end_date->format('M d, Y');
    
       $filter = $request->get('quick_date_filter'); // today, week, month, year


if(!empty($filter)){
  if ($filter === 'today') {
    // Split the day into 12 slots (2-hour intervals)
    $startOfDay = now()->startOfDay();
    $labels = [];
    $intervals = [];
    for ($i = 0; $i < 24; $i += 2) {
        $from = $startOfDay->copy()->addHours($i);
        $to   = $from->copy()->addHours(2);
        $labels[] = $from->format('H:i') . '-' . $to->format('H:i');
        $intervals[] = [$from, $to];
    }

} elseif ($filter === 'week') {
    // Daily counts for 7 days
    $labels = [];
    $intervals = [];
    for ($i = 0; $i < 7; $i++) {
        $date = $start_date->copy()->addDays($i);
        $labels[] = $date->format('D'); // Mon, Tue, ...
        $intervals[] = [$date->copy()->startOfDay(), $date->copy()->endOfDay()];
    }

} elseif ($filter === 'year') {
    // Monthly counts for 12 months
    $labels = [];
    $intervals = [];
    $startOfYear = now()->startOfYear();
    for ($i = 0; $i < 12; $i++) {
        $from = $startOfYear->copy()->addMonths($i)->startOfMonth();
        $to   = $from->copy()->endOfMonth();
        $labels[] = $from->format('M'); // Jan, Feb, ...
        $intervals[] = [$from, $to];
    }


} else {
    // Default: month (day wise)
    $days_in_range = $start_date->diffInDays($end_date) + 1;
    $labels = [];
    $intervals = [];
    for ($i = 0; $i < $days_in_range; $i++) {
        $date = $start_date->copy()->addDays($i);
        $labels[] = $date->format('d M');
        $intervals[] = [$date->copy()->startOfDay(), $date->copy()->endOfDay()];
    }

   
}  
}


 $generateCounts = function ($model,$type) use ($intervals, $city_id, $zone_id,$customer_id,$accountability_type,$return_status,$service_status,$accident_status,$recovery_status) {
        $counts = [];
        foreach ($intervals as [$from, $to]) {
           
            $query = $model::whereBetween('created_at', [$from, $to]);
            if($type == 'return'){
              $query->where('status', $return_status); 
              
            }
            if($type == 'service'){
              $query->where('status', $service_status);  
            }
            if($type == 'accident'){
              $query->where('status', $accident_status);  
            }
            if($type == 'recovery'){
              $query->where('status', $recovery_status);  
            }
            if ($city_id || $zone_id || $accountability_type) {
                        $query->whereHas('assignment.vehicleRequest', function($q) use ($city_id, $zone_id,$accountability_type) {
                            if ($city_id) $q->where('city_id', $city_id);
                            if ($zone_id) $q->where('zone_id', $zone_id);
                            if ($accountability_type) $q->where('account_ability_type', $accountability_type);
                        });
                    }
            if ($customer_id ) {
                        $query->whereHas('assignment.vehicleRequest.customerLogin', function($q) use ($customer_id) {
                            if ($customer_id) $q->where('customer_id', $customer_id);
                        });
                    }
            $counts[] = $query->count();
        }
        return $counts;
    };
    

// === Chart Data ===
$serviceChartData  = $generateCounts(B2BServiceRequest::class,'service');
$returnChartData   = $generateCounts(B2BReturnRequest::class,'return');
$accidentChartData = $generateCounts(B2BReportAccident::class,'accident');
$recoveryChartData = $generateCounts(B2BRecoveryRequest::class,'recovery');

if(!empty($filter)){
  $labels = [];

switch ($filter) {
    case 'today': // 24 hours broken into 2-hour slots
        for ($i = 0; $i < 24; $i += 2) {
            $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00 - ' 
                      . str_pad($i + 2, 2, '0', STR_PAD_LEFT) . ':00';
        }
        break;

    case 'week': // 7 days
        $period = CarbonPeriod::create($start_date, $end_date);
        foreach ($period as $date) {
            $labels[] = $date->format('D'); // e.g. Mon, Tue
        }
        break;

    case 'month': // each day of month
        $period = CarbonPeriod::create($start_date, $end_date);
        foreach ($period as $date) {
            $labels[] = $date->format('M d'); // e.g. Sep 01
        }
        break;

    case 'year': // each month of year
        for ($m = 1; $m <= 12; $m++) {
            $labels[] = Carbon::create()->month($m)->format('M'); // Jan, Feb, ...
        }
        break;
}  
}

    // Render partials
    $metricsHtml = view('b2badmin::partials.metrics-cards', compact(
        'rfd_count',
        'deploy_count',
        'return_count',
        'client_count',
        'agent_count',
        'start_date_formatted',
        'end_date_formatted',
        'clientWiseDeploymentData'
    ))->render();

    $citiesHtml = view('b2badmin::partials.cities-cards', compact('zones'))->render();


    return response()->json([
        'metricsHtml' => $metricsHtml,
        'citiesHtml'  => $citiesHtml,
        'current_month' =>'From '.$start_date_formatted. ' To ' .$end_date_formatted,
        'agent_count' =>$agent_count,
        'client_count' =>$client_count,
        'deploymentData' => [
        'labels' => $clientWiseDeploymentData->pluck('client_name')->toArray(),
        'values' => $clientWiseDeploymentData->pluck('vehicle_count')->toArray(),
    ],
    'service_count'=>array_sum($serviceChartData)?? 0,
    'return_count' =>array_sum($returnChartData) ?? 0,
    'accident_count' =>array_sum($accidentChartData) ?? 0,
    'recovery_count' =>array_sum($recoveryChartData) ?? 0,
    'labels' =>$labels,
    'charts' => [
        'service'  => $serviceChartData,
        'return'   => $returnChartData,
        'accident' => $accidentChartData,
        'recovery' => $recoveryChartData,
    ]
    ]);
}


    private function resolveDates($request)
    {
        if ($request->quick_date_filter) {
            switch ($request->quick_date_filter) {
                case 'today':
                    $start_date = Carbon::today();
                    $end_date   = Carbon::today();
                    $prev_start_date = Carbon::yesterday();
                    $prev_end_date   = Carbon::yesterday();
                    break;
                case 'week':
                    $start_date = Carbon::now()->startOfWeek();
                    $end_date   = Carbon::now()->endOfWeek();
                    $prev_start_date = Carbon::now()->subWeek()->startOfWeek();
                    $prev_end_date   = Carbon::now()->subWeek()->endOfWeek();
                    break;
                case 'month':
                    $start_date = Carbon::now()->startOfMonth();
                    $end_date   = Carbon::now()->endOfMonth();
                    $prev_start_date = Carbon::now()->subMonth()->startOfMonth();
                    $prev_end_date   = Carbon::now()->subMonth()->endOfMonth();
                    break;
                case 'year':
                    $start_date = Carbon::now()->startOfYear();
                    $end_date   = Carbon::now()->endOfYear();
                    $prev_start_date = Carbon::now()->subYear()->startOfYear();
                    $prev_end_date   = Carbon::now()->subYear()->endOfYear();
                    break;
                default:
                    $start_date = Carbon::now()->startOfMonth();
                    $end_date   = Carbon::now()->endOfMonth();
                    $prev_start_date = Carbon::now()->subMonth()->startOfMonth();
                    $prev_end_date   = Carbon::now()->subMonth()->endOfMonth();
            }
        } elseif ($request->from_date && $request->to_date) {
            $start_date = Carbon::parse($request->from_date);
            $end_date   = Carbon::parse($request->to_date);
            $days = $start_date->diffInDays($end_date) + 1;
            $prev_start_date = $start_date->copy()->subDays($days);
            $prev_end_date   = $start_date->copy()->subDay();
        } else {
            $start_date = Carbon::now()->startOfYear();
            $end_date   = Carbon::now()->endOfYear();
            $prev_start_date = Carbon::now()->subYear()->startOfYear();
            $prev_end_date   = Carbon::now()->subYear()->endOfYear();
        }
        // print_r($start_date);exit;
        return compact('start_date', 'end_date', 'prev_start_date', 'prev_end_date');
    }

    /**
     * Helper to calculate percentage change
     */
    private function calculateChange($prev, $current)
{
    if ($prev == 0 && $current == 0) {
        return 0; // No change
    }

    if ($prev == 0 && $current > 0) {
        return 100; // Full growth from 0 to something
    }

    if ($prev > 0 && $current == 0) {
        return -100; // Complete drop
    }

    return round((($current - $prev) / $prev) * 100, 2);
}

    /**
     * Generic function to generate chart labels and counts
     */
    private function generateChartData($request,$filter, $start_date, $end_date, $model, $status = null, $city_id = null, $zone_id = null,$customer_id = null,$accountability_type = null)
    {
        $labels = [];
        $counts = [];
        if(!empty($filter)){
        switch ($filter) {
            case 'today':
                $startOfDay = $start_date->copy()->startOfDay();
                for ($i = 0; $i < 24; $i += 2) {
                    $from = $startOfDay->copy()->addHours($i);
                    $to   = $from->copy()->addHours(2);
                    $labels[] = $from->format('H:i') . '-' . $to->format('H:i');

                    $query = $model::whereBetween('created_at', [$from, $to]);
                    if ($status) $query->where('status', $status);
                    if ($city_id || $zone_id || $accountability_type) {
                        $query->whereHas('assignment.vehicleRequest', function($q) use ($city_id, $zone_id,$accountability_type) {
                            if ($city_id) $q->where('city_id', $city_id);
                            if ($zone_id) $q->where('zone_id', $zone_id);
                            if ($accountability_type) $q->where('account_ability_type', $accountability_type);
                        });
                    }
                    if ($customer_id ) {
                        $query->whereHas('assignment.vehicleRequest.customerLogin', function($q) use ($customer_id) {
                            if ($customer_id) $q->where('customer_id', $customer_id);
                        });
                    }
                    $counts[] = $query->count();
                }
                break;

            case 'week':
                $period = CarbonPeriod::create($start_date, $end_date);
                foreach ($period as $date) {
                    $labels[] = $date->format('D');
                    $query = $model::whereBetween('created_at', [$date->copy()->startOfDay(), $date->copy()->endOfDay()]);
                    if ($status) $query->where('status', $status);
                    if ($city_id || $zone_id || $accountability_type) {
                        $query->whereHas('assignment.vehicleRequest', function($q) use ($city_id, $zone_id,$accountability_type) {
                            if ($city_id) $q->where('city_id', $city_id);
                            if ($zone_id) $q->where('zone_id', $zone_id);
                            if ($accountability_type) $q->where('account_ability_type', $accountability_type);
                        });
                    }
                    if ($customer_id ) {
                        $query->whereHas('assignment.vehicleRequest.customerLogin', function($q) use ($customer_id) {
                            if ($customer_id) $q->where('customer_id', $customer_id);
                        });
                    }
                    $counts[] = $query->count();
                }
                break;

            case 'year':
                for ($m = 1; $m <= 12; $m++) {
                    $from = $start_date->copy()->month($m)->startOfMonth();
                    $to   = $from->copy()->endOfMonth();
                    $labels[] = $from->format('M');

                    $query = $model::whereBetween('created_at', [$from, $to]);
                    if ($status) $query->where('status', $status);
                    if ($city_id || $zone_id || $accountability_type) {
                        $query->whereHas('assignment.vehicleRequest', function($q) use ($city_id, $zone_id,$accountability_type) {
                            if ($city_id) $q->where('city_id', $city_id);
                            if ($zone_id) $q->where('zone_id', $zone_id);
                            if ($accountability_type) $q->where('account_ability_type', $accountability_type);
                        });
                    }
                    if ($customer_id ) {
                        $query->whereHas('assignment.vehicleRequest.customerLogin', function($q) use ($customer_id) {
                            if ($customer_id) $q->where('customer_id', $customer_id);
                        });
                    }
                    $counts[] = $query->count();
                }
                break;

            default: // month/daywise
                $period = CarbonPeriod::create($start_date, $end_date);
                foreach ($period as $date) {
                    $labels[] = $date->format('M d');
                    $query = $model::whereBetween('created_at', [$date->copy()->startOfDay(), $date->copy()->endOfDay()]);
                    if ($status) $query->where('status', $status);
                    if ($city_id || $zone_id || $accountability_type) {
                        $query->whereHas('assignment.vehicleRequest', function($q) use ($city_id, $zone_id,$accountability_type) {
                            if ($city_id) $q->where('city_id', $city_id);
                            if ($zone_id) $q->where('zone_id', $zone_id);
                            if ($accountability_type) $q->where('account_ability_type', $accountability_type);
                        });
                    }
                    if ($customer_id ) {
                        $query->whereHas('assignment.vehicleRequest.customerLogin', function($q) use ($customer_id) {
                            if ($customer_id) $q->where('customer_id', $customer_id);
                        });
                    }
                    $counts[] = $query->count();
                }
                break;
        }
        }
        elseif ($request->from_date && $request->to_date) {
    $start_date = Carbon::parse($request->from_date)->startOfDay();
    $end_date   = Carbon::parse($request->to_date)->endOfDay();

    $days_in_range = $start_date->diffInDays($end_date) + 1;

    // Previous period
    $prev_end_date   = $start_date->copy()->subDay();
    $prev_start_date = $prev_end_date->copy()->subDays($days_in_range - 1);

    $labels = [];
    $intervals = [];

    if ($days_in_range <= 7) {
        // Daily (Mon, Tue...)
        for ($i = 0; $i < $days_in_range; $i++) {
            $date = $start_date->copy()->addDays($i);
            $labels[] = $date->format('D');
            $intervals[] = [$date->copy()->startOfDay(), $date->copy()->endOfDay()];
        }
    } elseif ($days_in_range <= 31) {
        // Day-wise (Sep 01, Sep 02...)
        for ($i = 0; $i < $days_in_range; $i++) {
            $date = $start_date->copy()->addDays($i);
            $labels[] = $date->format('M d');
            $intervals[] = [$date->copy()->startOfDay(), $date->copy()->endOfDay()];
        }
    } else {
        // Month-wise (Sep 2025, Oct 2025...)
        $period = CarbonPeriod::create($start_date->copy()->startOfMonth(), '1 month', $end_date->copy()->endOfMonth());
        foreach ($period as $date) {
            $labels[] = $date->format('M Y');
            $intervals[] = [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()];
        }
    }

    // Generate counts for chart
    $counts = [];
    foreach ($intervals as [$from, $to]) {
        $query = $model::whereBetween('created_at', [$from, $to]);
        if ($status) {
            $query->where('status', $status);
        }
        if ($city_id || $zone_id || $accountability_type) {
                        $query->whereHas('assignment.vehicleRequest', function($q) use ($city_id, $zone_id,$accountability_type) {
                            if ($city_id) $q->where('city_id', $city_id);
                            if ($zone_id) $q->where('zone_id', $zone_id);
                            if ($accountability_type) $q->where('account_ability_type', $accountability_type);
                        });
                    }
        if ($customer_id ) {
                        $query->whereHas('assignment.vehicleRequest.customerLogin', function($q) use ($customer_id) {
                            if ($customer_id) $q->where('customer_id', $customer_id);
                        });
                    }
        $counts[] = $query->count();
    }
}
        
        else {

    $days_in_range = $start_date->diffInDays($end_date) + 1;

    // Previous period
    $prev_end_date   = $start_date->copy()->subDay();
    $prev_start_date = $prev_end_date->copy()->subDays($days_in_range - 1);

    $labels = [];
    $intervals = [];

    if ($days_in_range <= 7) {
        // Daily (Mon, Tue...)
        for ($i = 0; $i < $days_in_range; $i++) {
            $date = $start_date->copy()->addDays($i);
            $labels[] = $date->format('D');
            $intervals[] = [$date->copy()->startOfDay(), $date->copy()->endOfDay()];
        }
    } elseif ($days_in_range <= 31) {
        // Day-wise (Sep 01, Sep 02...)
        for ($i = 0; $i < $days_in_range; $i++) {
            $date = $start_date->copy()->addDays($i);
            $labels[] = $date->format('M d');
            $intervals[] = [$date->copy()->startOfDay(), $date->copy()->endOfDay()];
        }
    } 
    else {
    // Get current year
    $year = now()->year;

    // Generate period from Jan 1st to Dec 31st
    $period = CarbonPeriod::create(
        Carbon::create($year, 1, 1)->startOfMonth(),
        '1 month',
        Carbon::create($year, 12, 31)->endOfMonth()
    );

    foreach ($period as $date) {
        $labels[] = $date->format('M Y');
        $intervals[] = [
            $date->copy()->startOfMonth(),
            $date->copy()->endOfMonth(),
        ];
    }

    // Generate counts for chart
    $counts = [];
    foreach ($intervals as [$from, $to]) {
        $query = $model::whereBetween('created_at', [$from, $to]);
        if ($status) {
            $query->where('status', $status);
        }
        if ($city_id || $zone_id || $accountability_type) {
                        $query->whereHas('assignment.vehicleRequest', function($q) use ($city_id, $zone_id,$accountability_type) {
                            if ($city_id) $q->where('city_id', $city_id);
                            if ($zone_id) $q->where('zone_id', $zone_id);
                            if ($accountability_type) $q->where('account_ability_type', $accountability_type);
                        });
                    }
        if ($customer_id ) {
                        $query->whereHas('assignment.vehicleRequest.customerLogin', function($q) use ($customer_id) {
                            if ($customer_id) $q->where('customer_id', $customer_id);
                        });
                    }
        $counts[] = $query->count();
    }
}
}
        return compact('labels', 'counts');
    }

    /**
     * Recovery Filter
     */
    public function recoveryFilter(Request $request)
    {
        $dates = $this->resolveDates($request);
        $city_id = $request->city_id ?? null;
        $zone_id = $request->zone_id ?? null;
        $accountability_type = $request->accountability_type ?? null;
        $customer_id = $request->customer_id ?? null;
        $status  = !empty($request->status)  ? $request->status  : 'closed';
        
        $current_query = B2BRecoveryRequest::whereBetween('created_at', [$dates['start_date'], $dates['end_date']]);
        if ($city_id || $zone_id || $accountability_type) {
            $current_query->whereHas('assignment.vehicleRequest', function($q) use ($city_id, $zone_id,$accountability_type) {
                if ($city_id) $q->where('city_id', $city_id);
                if ($zone_id) $q->where('zone_id', $zone_id);
                if ($accountability_type) $q->where('account_ability_type', $accountability_type);
            });
        }
        if ($customer_id ) {
            $current_query->whereHas('assignment.vehicleRequest.customerLogin', function($q) use ($customer_id) {
                if ($customer_id) $q->where('customer_id', $customer_id);
            });
        }
        if ($status) {
            $current_query->where('status',$status);
            }
        $prev_query = B2BRecoveryRequest::whereBetween('created_at', [$dates['prev_start_date'], $dates['prev_end_date']]);
        if ($city_id || $zone_id) {
            $prev_query->whereHas('assignment.vehicleRequest', function($q) use ($city_id, $zone_id) {
                if ($city_id) $q->where('city_id', $city_id);
                if ($zone_id) $q->where('zone_id', $zone_id);
            });
        }
        if ($status) {
            $prev_query->where('status',$status);
            }
        $current = $current_query->count();
        $previous = $prev_query->count();
        $change = $this->calculateChange($previous, $current);

        $chartData = $this->generateChartData($request,$request->quick_date_filter, $dates['start_date'], $dates['end_date'], B2BRecoveryRequest::class,$status, $city_id, $zone_id,$customer_id,$accountability_type);

        return response()->json([
            'count' => ['current' => $current, 'previous' => $previous, 'change_percent' => $change],
            'labels' => $chartData['labels'],
            'data'   => $chartData['counts']
        ]);
    }

    /**
     * Accident Filter
     */
    public function accidentFilter(Request $request)
    {
        $dates = $this->resolveDates($request);
        $city_id = $request->city_id ?? null;
        $zone_id = $request->zone_id ?? null;
        $accountability_type = $request->accountability_type ?? null;
        $customer_id = $request->customer_id ?? null;
        $status  = !empty($request->status)  ? $request->status  : 'claim_closed';
        
        $current_query = B2BReportAccident::whereBetween('created_at', [$dates['start_date'], $dates['end_date']]);
        if ($city_id || $zone_id || $accountability_type) {
            $current_query->whereHas('assignment.vehicleRequest', function($q) use ($city_id, $zone_id,$accountability_type) {
                if ($city_id) $q->where('city_id', $city_id);
                if ($zone_id) $q->where('zone_id', $zone_id);
                if ($accountability_type) $q->where('account_ability_type', $accountability_type);
            });
        }
        if ($customer_id ) {
            $current_query->whereHas('assignment.vehicleRequest.customerLogin', function($q) use ($customer_id) {
                if ($customer_id) $q->where('customer_id', $customer_id);
            });
        }
        if ($status) {
            $current_query->where('status',$status);
            }
            
        $prev_query = B2BReportAccident::whereBetween('created_at', [$dates['prev_start_date'], $dates['prev_end_date']]);
        if ($city_id || $zone_id) {
            $prev_query->whereHas('assignment.vehicleRequest', function($q) use ($city_id, $zone_id) {
                if ($city_id) $q->where('city_id', $city_id);
                if ($zone_id) $q->where('zone_id', $zone_id);
            });
        }
        if ($status) {
            $prev_query->where('status',$status);
            }

        $current = $current_query->count();
        $previous = $prev_query->count();
        $change = $this->calculateChange($previous, $current);

        $chartData = $this->generateChartData($request,$request->quick_date_filter, $dates['start_date'], $dates['end_date'], B2BReportAccident::class, $status, $city_id, $zone_id,$customer_id,$accountability_type);

        return response()->json([
            'count' => ['current' => $current, 'previous' => $previous, 'change_percent' => $change],
            'labels' => $chartData['labels'],
            'data'   => $chartData['counts']
        ]);
    }

    /**
     * Return Filter
     */
    public function returnFilter(Request $request)
    {
        $dates = $this->resolveDates($request);
        $city_id = $request->city_id ?? null;
        $zone_id = $request->zone_id ?? null;
        $accountability_type = $request->accountability_type ?? null;
        $customer_id = $request->customer_id ?? null;
        $status  = !empty($request->status)  ? $request->status  : 'closed';
     
        $current_query = B2BReturnRequest::whereBetween('created_at', [$dates['start_date'], $dates['end_date']]);
        if ($city_id || $zone_id || $accountability_type) {
            $current_query->whereHas('assignment.vehicleRequest', function($q) use ($city_id, $zone_id,$accountability_type) {
                if ($city_id) $q->where('city_id', $city_id);
                if ($zone_id) $q->where('zone_id', $zone_id);
                 if ($accountability_type) $q->where('account_ability_type', $accountability_type);
            });
        }
        
        if (!empty($customer_id)) {
            $current_query->whereHas('assignment.vehicleRequest.customerLogin', function($q) use ($customer_id) {
                if ($customer_id) $q->where('customer_id', $customer_id);
            });
        }
        if ($status) {
            $current_query->where('status',$status);
            }
            
        $prev_query = B2BReturnRequest::whereBetween('created_at', [$dates['prev_start_date'], $dates['prev_end_date']]);
        if ($city_id || $zone_id) {
            $prev_query->whereHas('assignment.vehicleRequest', function($q) use ($city_id, $zone_id) {
                if ($city_id) $q->where('city_id', $city_id);
                if ($zone_id) $q->where('zone_id', $zone_id);
            });
        }
        if ($status) {
            $prev_query->where('status',$status);
            }
        $current = $current_query->count();
        $previous = $prev_query->count();
        $change = $this->calculateChange($previous, $current);

        $chartData = $this->generateChartData($request , $request->quick_date_filter , $dates['start_date'], $dates['end_date'], B2BReturnRequest::class, $status, $city_id, $zone_id,$customer_id,$accountability_type);

        return response()->json([
            'count' => ['current' => $current, 'previous' => $previous, 'change_percent' => $change],
            'labels' => $chartData['labels'],
            'data'   => $chartData['counts']
        ]);
    }

    /**
     * Service Filter
     */
    public function serviceFilter(Request $request)
    {
        $dates = $this->resolveDates($request);
        $city_id = $request->city_id ?? null;
        $zone_id = $request->zone_id ?? null;
        $accountability_type = $request->accountability_type ?? null;
        $customer_id = $request->customer_id ?? null;
        // $status  = $request->status ?? null;
        $status  = !empty($request->status)  ? $request->status  : 'closed';
        
        $current_query = B2BServiceRequest::whereBetween('created_at', [$dates['start_date'], $dates['end_date']]);
        if ($city_id || $zone_id || $accountability_type) {
            $current_query->whereHas('assignment.vehicleRequest', function($q) use ($city_id, $zone_id,$accountability_type) {
                if ($city_id) $q->where('city_id', $city_id);
                if ($zone_id) $q->where('zone_id', $zone_id);
                if ($accountability_type) $q->where('account_ability_type', $accountability_type);
            });
        }
        if ($customer_id ) {
            $current_query->whereHas('assignment.vehicleRequest.customerLogin', function($q) use ($customer_id) {
                if ($customer_id) $q->where('customer_id', $customer_id);
            });
        }
        if ($status) {
            $current_query->where('status',$status);
            }
            
        $prev_query = B2BServiceRequest::whereBetween('created_at', [$dates['prev_start_date'], $dates['prev_end_date']]);
        if ($city_id || $zone_id) {
            $prev_query->whereHas('assignment.vehicleRequest', function($q) use ($city_id, $zone_id) {
                if ($city_id) $q->where('city_id', $city_id);
                if ($zone_id) $q->where('zone_id', $zone_id);
            });
        }
        if ($status) {
            $prev_query->where('status',$status);
            }
            
        $current = $current_query->count();
        $previous = $prev_query->count();
        $change = $this->calculateChange($previous, $current);

        $chartData = $this->generateChartData($request , $request->quick_date_filter , $dates['start_date'], $dates['end_date'], B2BServiceRequest::class, $status, $city_id, $zone_id,$customer_id,$accountability_type);

        return response()->json([
            'count' => ['current' => $current, 'previous' => $previous, 'change_percent' => $change],
            'labels' => $chartData['labels'],
            'data'   => $chartData['counts']
        ]);
    }


    


}

<?php

namespace Modules\RecoveryManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\City\Entities\City;
use Modules\Zones\Entities\Zones;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\B2B\Entities\B2BRecoveryRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Modules\B2B\Entities\B2BVehicleAssignmentLog;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    
public function dashboard(Request $request)
{
    $start_date = Carbon::now()->startOfYear();
    $end_date = Carbon::now()->endOfYear();
    $user = Auth::user();

    // Basic metrics
    $agent_count = Deliveryman::where('work_type', 'in-house')
        ->when(!in_array($user->role, [1, 13]), function ($query) use ($user) {
            return $query->where('current_city_id', $user->city_id);
        })
        ->where('rider_status', 1)
        ->where('team_type', 22)
        ->count();

    $total_count = B2BRecoveryRequest::whereBetween('created_at', [$start_date, $end_date])
        ->when(!in_array($user->role, [1, 13]), function ($query) use ($user) {
            return $query->where('city_id', $user->city_id);
        })
        ->count();

    $opened_count = B2BRecoveryRequest::where('status','opened')
        ->when(!in_array($user->role, [1, 13]), function ($query) use ($user) {
            return $query->where('city_id', $user->city_id);
        })
        ->whereBetween('created_at', [$start_date, $end_date])
        ->count();
    
    $agent_assigned_count = B2BRecoveryRequest::where('status', 'agent_assigned')
        ->when(!in_array($user->role, [1, 13]), function ($query) use ($user) {
            return $query->where('city_id', $user->city_id);
        })
        ->whereBetween('created_at', [$start_date, $end_date])
        ->count();
        
    $not_recovered_count = B2BRecoveryRequest::where('status', 'not_recovered')
        ->when(!in_array($user->role, [1, 13]), function ($query) use ($user) {
            return $query->where('city_id', $user->city_id);
        })
        ->whereBetween('created_at', [$start_date, $end_date])
        ->count();
        
    $closed_count = B2BRecoveryRequest::where('status', 'closed')
        ->when(!in_array($user->role, [1, 13]), function ($query) use ($user) {
            return $query->where('city_id', $user->city_id);
        })
        ->whereBetween('created_at', [$start_date, $end_date])
        ->count();

    $generateMonthlyCounts = function ($status = null) use ($start_date, $end_date, $user) {
        $counts = [];
        $period = CarbonPeriod::create($start_date, '1 month', $end_date);

        foreach ($period as $date) {
            $query = B2BRecoveryRequest::whereBetween('created_at', [
                $date->copy()->startOfMonth(),
                $date->copy()->endOfMonth()
            ])
            ->when(!in_array($user->role, [1, 13]), function ($query) use ($user) {
                return $query->where('city_id', $user->city_id);
            });

            if ($status) {
                $query->where('status', $status);
            }

            $counts[] = $query->count();
        }

        return $counts;
    };

    // Chart data for closed requests
    $recoveryChartData = $generateMonthlyCounts();

    // Labels
    $labels = [];
    $period = CarbonPeriod::create($start_date, '1 month', $end_date);
    foreach ($period as $date) {
        $labels[] = $date->format('M Y');
    }

    // City & Zone access logic
    if (in_array($user->role, [1, 13])) {
        $cities = City::where('status', 1)->get();
        $zones = '';
    } else {
        $cities = City::where('id', $user->city_id)->where('status', 1)->get();
        $zones = Zones::where('city_id', $user->city_id)->where('status', 1)->get();
    }

    return view('recoverymanager::dashboard', compact(
        'agent_count',
        'total_count',
        'opened_count',
        'closed_count',
        'not_recovered_count',
        'agent_assigned_count',
        'recoveryChartData',
        'labels',
        'cities',
        'zones'
    ));
}



// public function filter(Request $request)
// {
//     $dates = $this->resolveDates($request);
//     $status  = !empty($request->status)  ? $request->status  : 'closed';
//     $query = B2BRecoveryRequest::query();
    
    
//     // Apply filters dynamically
//     if ($request->filled('city_id')) {
//         $query->where('city_id', $request->city_id);
//     }

//     if ($request->filled('zone_id')) {
//         $query->where('zone_id', $request->zone_id);
//     }

//     if ($request->filled('recovery_status')) {
//         $query->where('status', $request->recovery_status);
//     }

//     // Date filters
//     $query->whereBetween('created_at', [$dates['start_date'], $dates['end_date']]);

//     // Metrics
//     $total_count = $query->count();
//     $opened_count = (clone $query)->where('status', 'opened')->count();
//     $closed_count = (clone $query)->where('status', 'closed')->count();

//     $recoveryChartData = $this->generateChartData($request,$request->quick_date_filter, $dates['start_date'], $dates['end_date'], B2BRecoveryRequest::class,$status, $request->city_id, $request->zone_id);;


//     return response()->json([
//         'success' => true,
//         'total_count' => $total_count,
//         'opened_count' => $opened_count,
//         'closed_count' => $closed_count,
//         'charts' => [
//             'recovery' => $recoveryChartData
//         ],
//     ]);
// }

public function filter(Request $request)
{
    $user = Auth::user();
    $dates = $this->resolveDates($request);
    $status  = !empty($request->status) ? $request->status : '';

    $query = B2BRecoveryRequest::query();

    // Apply filters dynamically
    if ($request->filled('city_id')) {
        $query->where('city_id', $request->city_id);
    }

    if ($request->filled('zone_id')) {
        $query->where('zone_id', $request->zone_id);
    }

    // if ($request->filled('recovery_status')) {
    //     $query->where('status', $request->recovery_status);
    // }

    // ✅ Apply city filter for non-admin roles
    $query->when(!in_array($user->role, [1, 13]), function ($q) use ($user) {
        return $q->where('city_id', $user->city_id);
    });

    // Date filters
    $query->whereBetween('created_at', [$dates['start_date'], $dates['end_date']]);

    // Metrics
    $total_count = $query->count();
    $opened_count = (clone $query)->where('status', 'opened')->count();
    $closed_count = (clone $query)->where('status', 'closed')->count();
    $agent_assigned_count = (clone $query)->where('status', 'agent_assigned')->count();
    $not_recovered_count = (clone $query)->where('status', 'not_recovered')->count();
    
    $agent_count = Deliveryman::where('work_type', 'in-house')
        ->when(!in_array($user->role, [1, 13]), function ($query) use ($user) {
            return $query->where('current_city_id', $user->city_id);
        })
        ->where('rider_status', 1)
        ->where('team_type', 22);
        
    if ($request->filled('city_id')) {
        $agent_count->where('current_city_id', $request->city_id);
    }

    if ($request->filled('zone_id')) {
        $agent_count->where('zone_id', $request->zone_id);
    } 
    
    
    $recoveryChartData = $this->generateChartData(
        $request,
        $request->quick_date_filter,
        $dates['start_date'],
        $dates['end_date'],
        B2BRecoveryRequest::class,
        $status,
        $request->city_id,
        $request->zone_id
    );

    return response()->json([
        'success' => true,
        'agent_count' =>$agent_count->count(),
        'total_count' => $total_count,
        'opened_count' => $opened_count,
        'closed_count' => $closed_count,
        'not_recovered_count' =>$not_recovered_count,
        'agent_assigned_count' =>$agent_assigned_count,
        'charts' => [
            'recovery' => $recoveryChartData
        ],
    ]);
}


    private function resolveDates($request)
    {
        if ($request->quick_date_filter) {
            switch ($request->quick_date_filter) {
                case 'today':
                    $start_date = Carbon::today();
                    $end_date   = Carbon::today();
                    break;
                case 'week':
                    $start_date = Carbon::now()->startOfWeek();
                    $end_date   = Carbon::now()->endOfWeek();
                    break;
                case 'month':
                    $start_date = Carbon::now()->startOfMonth();
                    $end_date   = Carbon::now()->endOfMonth();
                    break;
                case 'year':
                    $start_date = Carbon::now()->startOfYear();
                    $end_date   = Carbon::now()->endOfYear();
                    break;
                default:
                    $start_date = Carbon::now()->startOfMonth();
                    $end_date   = Carbon::now()->endOfMonth();
            }
        } elseif ($request->from_date && $request->to_date) {
            $start_date = Carbon::parse($request->from_date);
            $end_date   = Carbon::parse($request->to_date);
            $days = $start_date->diffInDays($end_date) + 1;
        } else {
            $start_date = Carbon::now()->startOfYear();
            $end_date   = Carbon::now()->endOfYear();
        }
        // print_r($start_date);exit;
        return compact('start_date', 'end_date');
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
    private function generateChartData($request,$filter, $start_date, $end_date, $model, $status = null, $city_id = null, $zone_id = null)
    {
        $user = User::find(Auth::id());
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
                    if ($city_id || $zone_id) {
                        $query->whereHas('assignment.vehicleRequest', function($q) use ($city_id, $zone_id) {
                            if ($city_id) $q->where('city_id', $city_id);
                            if ($zone_id) $q->where('zone_id', $zone_id);
                        });
                    }
                    $query->when(!in_array($user->role, [1, 13]), function ($q) use ($user) {
                        $q->where('city_id', $user->city_id);
                    });
                    $counts[] = $query->count();
                }
                break;

            case 'week':
                $period = CarbonPeriod::create($start_date, $end_date);
                foreach ($period as $date) {
                    $labels[] = $date->format('D');
                    $query = $model::whereBetween('created_at', [$date->copy()->startOfDay(), $date->copy()->endOfDay()]);
                    if ($status) $query->where('status', $status);
                    
                    if ($city_id || $zone_id) {
                        $query->whereHas('assignment.vehicleRequest', function($q) use ($city_id, $zone_id) {
                            if ($city_id) $q->where('city_id', $city_id);
                            if ($zone_id) $q->where('zone_id', $zone_id);
                        });
                    }
                    $query->when(!in_array($user->role, [1, 13]), function ($q) use ($user) {
                        $q->where('city_id', $user->city_id);
                    });
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
                    if ($city_id || $zone_id) {
                        $query->whereHas('assignment.vehicleRequest', function($q) use ($city_id, $zone_id) {
                            if ($city_id) $q->where('city_id', $city_id);
                            if ($zone_id) $q->where('zone_id', $zone_id);
                        });
                    }
                    $query->when(!in_array($user->role, [1, 13]), function ($q) use ($user) {
                        $q->where('city_id', $user->city_id);
                    });
                    $counts[] = $query->count();
                }
                break;

            default: // month/daywise
                $period = CarbonPeriod::create($start_date, $end_date);
                foreach ($period as $date) {
                    $labels[] = $date->format('M d');
                    $query = $model::whereBetween('created_at', [$date->copy()->startOfDay(), $date->copy()->endOfDay()]);
                    if ($status) $query->where('status', $status);
                    if ($city_id || $zone_id) {
                        $query->whereHas('assignment.vehicleRequest', function($q) use ($city_id, $zone_id) {
                            if ($city_id) $q->where('city_id', $city_id);
                            if ($zone_id) $q->where('zone_id', $zone_id);
                        });
                    }
                    $query->when(!in_array($user->role, [1, 13]), function ($q) use ($user) {
                        $q->where('city_id', $user->city_id);
                    });
                    $counts[] = $query->count();
                }
                break;
        }
        }
        elseif ($request->from_date && $request->to_date) {
    $start_date = Carbon::parse($request->from_date)->startOfDay();
    $end_date   = Carbon::parse($request->to_date)->endOfDay();

    $days_in_range = $start_date->diffInDays($end_date) + 1;

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
        if ($city_id || $zone_id) {
            $query->whereHas('assignment.vehicleRequest', function ($q) use ($city_id, $zone_id) {
                if ($city_id) $q->where('city_id', $city_id);
                if ($zone_id) $q->where('zone_id', $zone_id);
            });
        }
        $query->when(!in_array($user->role, [1, 13]), function ($q) use ($user) {
                        $q->where('city_id', $user->city_id);
                    });
        $counts[] = $query->count();
    }
}
        
        else {

    $days_in_range = $start_date->diffInDays($end_date) + 1;


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
        if ($city_id || $zone_id) {
            $query->whereHas('assignment.vehicleRequest', function ($q) use ($city_id, $zone_id) {
                if ($city_id) $q->where('city_id', $city_id);
                if ($zone_id) $q->where('zone_id', $zone_id);
            });
        }
        $query->when(!in_array($user->role, [1, 13]), function ($q) use ($user) {
                        $q->where('city_id', $user->city_id);
                    });
        $counts[] = $query->count();
    }
}
}
        $recovery_count = array_sum($counts);
        return compact('labels', 'counts','recovery_count');
    }

    /**
     * Recovery Filter
     */
    // public function recoveryFilter(Request $request)
    // {
    //     $dates = $this->resolveDates($request);
    //     $city_id = $request->city_id ?? null;
    //     $zone_id = $request->zone_id ?? null;
    //     $status  = !empty($request->status)  ? $request->status  : 'closed';
        
    //     $current_query = B2BRecoveryRequest::whereBetween('created_at', [$dates['start_date'], $dates['end_date']]);
    //     if ($city_id || $zone_id) {
    //         $current_query->whereHas('assignment.vehicleRequest', function($q) use ($city_id, $zone_id) {
    //             if ($city_id) $q->where('city_id', $city_id);
    //             if ($zone_id) $q->where('zone_id', $zone_id);
    //         });
    //     }
    //     if ($status) {
    //         $current_query->where('status',$status);
    //         }
    //     $current = $current_query->count();

    //     $chartData = $this->generateChartData($request,$request->quick_date_filter, $dates['start_date'], $dates['end_date'], B2BRecoveryRequest::class,$status, $city_id, $zone_id);

    //     return response()->json([
    //         'count' => $current,
    //         'labels' => $chartData['labels'],
    //         'data'   => $chartData['counts']
    //     ]);
    // }
    
    public function recoveryFilter(Request $request)
{
    $user = Auth::user();
    $dates = $this->resolveDates($request);
    $city_id = $request->city_id ?? null;
    $zone_id = $request->zone_id ?? null;
    $status  = !empty($request->status) ? $request->status : '';

    $current_query = B2BRecoveryRequest::whereBetween('created_at', [$dates['start_date'], $dates['end_date']])
        ->when(!in_array($user->role, [1, 13]), function ($query) use ($user) {
            return $query->where('city_id', $user->city_id);
        });

    if ($city_id || $zone_id) {
        $current_query->whereHas('assignment.vehicleRequest', function ($q) use ($city_id, $zone_id) {
            if ($city_id) $q->where('city_id', $city_id);
            if ($zone_id) $q->where('zone_id', $zone_id);
        });
    }

    if ($status) {
        $current_query->where('status', $status);
    }

    $current = $current_query->count();

    $chartData = $this->generateChartData(
        $request,
        $request->quick_date_filter,
        $dates['start_date'],
        $dates['end_date'],
        B2BRecoveryRequest::class,
        $status,
        $city_id,
        $zone_id
    );

    return response()->json([
        'count' => $current,
        'labels' => $chartData['labels'],
        'data'   => $chartData['counts']
    ]);
}



   
}
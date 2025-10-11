<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Inventory\Entities\Expense;
use Modules\Inventory\Entities\InventoryParts;
use Modules\Purchase\Entities\PurchaseDetail;
use Modules\VehicleMaintenance\Entities\VehicleMaintenance;
use Modules\VehicleMaintenance\Entities\VehicleMaintenanceDetail;
use Modules\VehicleManagement\Entities\LegalDocumentation;
use Modules\VehicleManagement\Entities\PickupAndDrop;
use Modules\VehicleManagement\Entities\VehicleRequisition;
use Modules\VehicleRefueling\Entities\FuelRequisition;
use Illuminate\Http\Request;
use App\Models\LoginTimeRecord;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\City\Entities\City;
use Modules\Zones\Entities\Zones; //updated by Gowtham.s - Zone Map
class DashboardController extends Controller
{
    /**
     * Constructor for the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'status_check'])->except(['redirectToDashboard']);
        \cs_set('theme', [
            'title' => 'Dashboard',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => false,
                ],

            ],
            'rprefix' => 'admin.dashboard',
        ]);
    }

    public function index(Request $request)
    {
       
        
        $data = [];
        if (can('maintenance_report')) {
            $data['line_chart'] = $this->getLineChartData();
        }
        if (can('expense_report')) {
            $data['doughnut_chart'] = $this->getPieChartData();
        }
        if (can('vehicle_requisition_report')) {
            $data['venn_diagram'] = $this->getVennDiagramData();
            $data['multi_axis_line'] = $this->getMultiAxisLineData();
        }
        $total_requisitions = VehicleRequisition::where('status', 0)->count();
        $total_maintenances = VehicleMaintenance::where('status', 'pending')->count();

        $available_requisitions = $total_requisitions - $total_maintenances >= 0 ? $total_requisitions - $total_maintenances : 0;

        $todays_requisitions = VehicleRequisition::whereDate('requisition_date', date('Y-m-d'))->where('status', 0)->count();
        $todays_pick_drops = PickupAndDrop::whereDate('effective_date', date('Y-m-d'))->where('status', 0)->count();
        $todays_maintenances = VehicleMaintenance::whereDate('date', date('Y-m-d'))->where('status', 0)->count();
        $todays_fuel_requisitions = FuelRequisition::whereDate('date', date('Y-m-d'))->count();

        $doc_expire_soon = LegalDocumentation::where('expiry_date', '>', date('Y-m-d'))->where('expiry_date', '<', date('Y-m-d', strtotime('+30 days')))->count();
        $doc_expired = LegalDocumentation::where('expiry_date', '<', date('Y-m-d'))->count();

        $totalStockIn = InventoryParts::where('is_active', true)->sum('qty') + PurchaseDetail::whereHas('purchase', function ($query) {
            $query->where('status', 'approved');
        })->sum('qty');
        $totalStockOut = VehicleMaintenanceDetail::whereHas('maintenance', function ($query) {
            $query->where('status', 'approved');
        })->sum('qty');

        $reminders = LegalDocumentation::with(['vehicle', 'document_type'])->paginate(15);
        
        
        $cities = City::where('status',1)->get();
        $login_user_role = auth()->user()->role ?? '';
        
        $city_id = $request->city_id ?? '';
        $from_date = $request->from_date ?? '';
        $to_date = $request->to_date ?? '';
        
        $total_application = Deliveryman::where('delete_status', 0);
        if (!empty($from_date) && !empty($to_date)) {
            $total_application->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $total_application->where('current_city_id', $city_id);
        }
        $total_application_count = $total_application->get()->count();
        
        $pending_application = Deliveryman::where('delete_status', 0);
        if (!empty($from_date) && !empty($to_date)) {
            $pending_application->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $pending_application->where('current_city_id', $city_id);
        }
        $pending_application_count = $pending_application->where('kyc_verify', 0)->get()->count();
        
        $completed_application = Deliveryman::where('delete_status', 0);
        if (!empty($from_date) && !empty($to_date)) {
            $completed_application->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $completed_application->where('current_city_id', $city_id);
        }
        $completed_application_count = $completed_application->where('kyc_verify', 1)->get()->count();
        
        $rejected_application = Deliveryman::where('delete_status', 0);
        if (!empty($from_date) && !empty($to_date)) {
            $rejected_application->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $rejected_application->where('current_city_id', $city_id);
        }
        $rejected_application_count = $rejected_application->where('kyc_verify', 2)->get()->count();
        
        $hold_application = Deliveryman::where('delete_status', 0);
        if (!empty($from_date) && !empty($to_date)) {
            $hold_application->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $hold_application->where('current_city_id', $city_id);
        }
        $hold_application_count = $hold_application->where('kyc_verify', 3)->get()->count();
        
        $total_hr_approve = Deliveryman::where('delete_status', 0)->whereNotNull('approved_status')->where('rider_status', 3);
        if (!empty($from_date) && !empty($to_date)) {
            $total_hr_approve->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $total_hr_approve->where('current_city_id', $city_id);
        }
        $total_hr_approve_count = $total_hr_approve->get()->count();
        
        $total_hr_probation = Deliveryman::where('delete_status', 0)->whereNotNull('approved_status')->where('rider_status', 3);
        if (!empty($from_date) && !empty($to_date)) {
            $total_hr_probation->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $total_hr_probation->where('current_city_id', $city_id);
        }
        $total_hr_probation_count = $total_hr_probation->get()->count();
        
        $total_hr_reject = Deliveryman::where('delete_status', 0)->whereNotNull('approved_status')->where('rider_status', 2);
        if (!empty($from_date) && !empty($to_date)) {
            $total_hr_reject->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $total_hr_reject->where('current_city_id', $city_id);
        }
        $total_hr_reject_count = $total_hr_reject->get()->count();
        
        $total_hr_live = Deliveryman::where('delete_status', 0)->whereNotNull('approved_status')->where('rider_status', 1);
        if (!empty($from_date) && !empty($to_date)) {
            $total_hr_live->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        }
        if (!empty($city_id)) {
            $total_hr_live->where('current_city_id', $city_id);
        }
        $total_hr_live_count = $total_hr_live->get()->count();
        
        // Percentage calculations (rounded to 2 decimal places)
        $pending_percentage   = $total_application_count > 0 ? round(($pending_application_count / $total_application_count) * 100, 2) : 0;
        $completed_percentage = $total_application_count > 0 ? round(($completed_application_count / $total_application_count) * 100, 2) : 0;
        $rejected_percentage  = $total_application_count > 0 ? round(($rejected_application_count / $total_application_count) * 100, 2) : 0;
        $hold_percentage      = $total_application_count > 0 ? round(($hold_application_count / $total_application_count) * 100, 2) : 0;
        $total_application_percentage = $pending_percentage + $completed_percentage + $rejected_percentage + $hold_percentage;

        
        $hr_approve_percentage      = $total_application_count > 0 ? round(($total_hr_approve_count / $total_application_count) * 100, 2) : 0;
        $hr_probation_percentage      = $total_application_count > 0 ? round(($total_hr_probation_count / $total_application_count) * 100, 2) : 0;
        $hr_reject_percentage      = $total_application_count > 0 ? round(($total_hr_reject_count / $total_application_count) * 100, 2) : 0;  
        $hr_live_percentage      = $total_application_count > 0 ? round(($total_hr_live_count / $total_application_count) * 100, 2) : 0;  
        

        $todays_application = Deliveryman::whereDate('register_date_time', Carbon::today())
            ->where('delete_status', 0);
        
        if (!empty($city_id)) {
            $todays_application->where('current_city_id', $city_id);
        }
        
        $todays_applications = $todays_application->count();
        
        
    $bgv_pending_count = 0;
    $bgv_ageing_pending_count = 0;
    
    Deliveryman::where('delete_status', 0)
        ->where('kyc_verify', 0)
        // ->when(!empty($from_date) && !empty($to_date), function ($query) use ($from_date, $to_date) {
        //     $query->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
        // })
        // ->when(!empty($city_id), function ($query) use ($city_id) {
        //     $query->where('current_city_id', $city_id);
        // })
        ->chunk(1000, function ($bgv_pending_applications) use (&$bgv_pending_count, &$bgv_ageing_pending_count) {
            foreach ($bgv_pending_applications as $val) {
                $created_date = \Carbon\Carbon::parse($val->register_date_time);
                $current_date = \Carbon\Carbon::now();
                $ageing_days = $current_date->diffInDays($created_date);
    
                if ($ageing_days > 7) {
                    $bgv_ageing_pending_count += 1;
                } else {
                    $bgv_pending_count += 1;
                }
            }
        });
        
        $total_employee_count = Deliveryman::where('delete_status', 0)->where('work_type','in-house')->count();
        $total_rider_count = Deliveryman::where('delete_status', 0)->where('work_type','deliveryman')->count();
        $total_adhoc_count = Deliveryman::where('delete_status', 0)->where('work_type','adhoc')->count();
        $total_vehicle_count = DB::table('ev_modal_vehicles')->where('status',1)->get()->count();
       
       $onboarding_data = DB::table('rider_onboarding_lists as a')
        ->leftJoin('ev_tbl_customer_master as b', 'a.customer_master_id', '=', 'b.id')
        ->select('b.trade_name as name', DB::raw('COUNT(*) as cust_count'),DB::raw('COUNT(*) as city_count'),DB::raw('COUNT(DISTINCT a.hub_id) as hub_count') )
        ->where('a.role_type', 'deliveryman')
        ->groupBy('a.customer_master_id', 'b.trade_name') 
        ->orderByDesc('cust_count')
        ->get();
       
        if($login_user_role == 1){ //admin
 
            return view('dashboard', [
                'login_user_role'=>$login_user_role,
                'total_requisitions' => $total_requisitions,
                'total_maintenances' => $total_maintenances,
                'available' => $available_requisitions,
                'todays_requisitions' => $todays_requisitions,
                'todays_pick_drops' => $todays_pick_drops,
                'todays_maintenances' => $todays_maintenances,
                'todays_fuel_requisitions' => $todays_fuel_requisitions,
                'reminders' => $reminders,
                'doc_expire_soon' => $doc_expire_soon,
                'doc_expired' => $doc_expired,
                'totalStockIn' => $totalStockIn,
                'totalStockOut' => $totalStockOut,
                'data' => $data,
                'total_employee_count' => $total_employee_count,
                'total_rider_count' => $total_rider_count,
                'total_adhoc_count' => $total_adhoc_count,
                'total_vehicle_count' => $total_vehicle_count,
                'onboarding_data' => $onboarding_data
            ]);
        }
        else if($login_user_role == 11){ //BGV Vendor
            return view('bgv_dashboard',compact('login_user_role','cities','city_id','from_date','to_date','total_application_count','pending_application_count',
            'completed_application_count','rejected_application_count','hold_application_count','pending_percentage','completed_percentage','rejected_percentage','hold_percentage',
            'todays_applications','total_application_percentage','bgv_pending_count','bgv_ageing_pending_count'));
        }
         else if($login_user_role == 4){ //HR
             return view('hr_dashboard',compact('login_user_role','cities','city_id','from_date','to_date','total_application_count','pending_application_count',
            'completed_application_count','rejected_application_count','hold_application_count','pending_percentage','completed_percentage','rejected_percentage','hold_percentage',
            'total_hr_approve_count','total_hr_probation_count','total_hr_probation_count','total_hr_live_count','hr_approve_percentage','hr_probation_percentage','hr_reject_percentage','hr_live_percentage','todays_applications','total_application_percentage'));
        }else if($login_user_role == 12){ //HR Manager
            return redirect()->route('admin.Green-Drive-Ev.hr_level_one.dashboard');
        }
        else{
 
            return view('dashboard', [
                'login_user_role'=>$login_user_role,
                'total_requisitions' => $total_requisitions,
                'total_maintenances' => $total_maintenances,
                'available' => $available_requisitions,
                'todays_requisitions' => $todays_requisitions,
                'todays_pick_drops' => $todays_pick_drops,
                'todays_maintenances' => $todays_maintenances,
                'todays_fuel_requisitions' => $todays_fuel_requisitions,
                'reminders' => $reminders,
                'doc_expire_soon' => $doc_expire_soon,
                'doc_expired' => $doc_expired,
                'totalStockIn' => $totalStockIn,
                'totalStockOut' => $totalStockOut,
                'data' => $data,
                'total_employee_count' => $total_employee_count,
                'total_rider_count' => $total_rider_count,
                'total_adhoc_count' => $total_adhoc_count,
                'total_vehicle_count' => $total_vehicle_count,
            ]);
        }
    }

    public function redirectToDashboard()
    {
        return redirect()->route('admin.dashboard');
    }

    public function getLineChartData()
    {
        $endDate = Carbon::now();

        // Subtract 11 months from the current date to get the starting date for the last 12 months
        $startDate = $endDate->copy()->subMonths(11);

        // Initialize an empty array to store formatted data
        $data = [];

        // Query to retrieve monthly maintenance cost for the last 12 months
        $monthlyCosts = DB::table('vehicle_maintenances')
            ->select(DB::raw('YEAR(date) as year'), DB::raw('MONTH(date) as month'), DB::raw('SUM(total) as total_cost'))
            ->whereBetween('date', [$startDate->startOfMonth(), $endDate->endOfMonth()])
            ->groupBy(DB::raw('YEAR(date)'), DB::raw('MONTH(date)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Iterate over each month of the last 12 months
        for ($i = 0; $i < 12; $i++) {
            // Initialize cost to 0 for each month
            $cost = 0;

            // Check if there's data available for the current month
            $currentMonthData = $monthlyCosts->where('year', $endDate->year)->where('month', $endDate->month)->first();

            $cost = $currentMonthData->total_cost ?? 0;

            // Format the date and store the month and cost in the data array

            $data[] = [
                'label' => $endDate->format('M'),
                'value' => (int) $cost,
                // if even then #FF5733 else #000000
                'color' => $i % 2 == 0 ? '#FF5733' : '#000000',
            ];
            // Move to the previous month for the next iteration
            $endDate->subMonth();
        }
        // Reverse the data array to maintain chronological order
        $data = array_reverse($data);

        return $data;
    }

    public function getPieChartData()
    {
        // Get all expense types
        $types = Expense::getTypes();

        // Get the current date and date 12 months ago
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subMonths(12);

        // Fetch all expenses within the last 12 months
        $expenses = Expense::whereBetween('date', [$startDate, $endDate])->get();

        // Initialize an array to store data
        $data = [];

        // Loop through each expense type
        foreach ($types as $type => $typeName) {
            // Calculate the total sum for the current type
            $total = $expenses->where('type', $type)->sum('total');

            // Add data to the array
            $data[] = [
                'category' => $typeName,
                'value' => $total,
            ];
        }

        return $data;
    }

    public function getVennDiagramData()
    {
        $statues = [
            'pending' => [
                'name' => localize('Pending'),
                'value' => 0,
                'color' => '#dfd7d7',
            ],
            'approved' => [
                'name' => localize('Approved'),
                'value' => 0,
                'color' => '#17c653',
            ],
            'rejected' => [
                'name' => localize('Rejected'),
                'value' => 0,
                'color' => '#dc3545e0',
                // "sets" => [localize('Pending'),  localize('Approved')],
            ],
        ];

        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subMonths(11);
        // Get last 12 months data for vehicle requisitions status in percentage
        $data = VehicleRequisition::select(DB::raw('status'), DB::raw('COUNT(*) as total'))
            ->whereBetween('requisition_date', [$startDate->startOfMonth(), $endDate->endOfMonth()])
            ->groupBy('status')
            ->get();

        foreach ($data as $item) {
            $statues[$item->status]['value'] = $item->total;
        }

        return $statues;
    }

    public function getMultiAxisLineData()
    {
        $endDate = Carbon::now();

        // Subtract 11 months from the current date to get the starting date for the last 12 months
        $startDate = $endDate->copy()->subMonths(11);

        // Initialize an empty array to store formatted data
        $data = [];

        // Query to retrieve monthly maintenance cost for the last 12 months
        $monthlyRequisitions = VehicleRequisition::select(DB::raw('YEAR(requisition_date) as year'), DB::raw('MONTH(requisition_date) as month'), DB::raw('COUNT(*) as total'), DB::raw('status'))
            ->whereBetween('requisition_date', [$startDate->startOfMonth(), $endDate->endOfMonth()])
            ->groupBy(DB::raw('YEAR(requisition_date)'), DB::raw('MONTH(requisition_date)'), 'status')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        // Iterate over each month of the last 12 months
        for ($i = 0; $i < 12; $i++) {
            $pending = $monthlyRequisitions->where('year', $endDate->year)->where('month', $endDate->month)->where('status', 'pending')->first()->total ?? 0;
            $approved = $monthlyRequisitions->where('year', $endDate->year)->where('month', $endDate->month)->where('status', 'approved')->first()->total ?? 0;
            $data[] = [
                // date: new Date(2021, 0, 1).getTime(),

                'date' => $endDate->startOfMonth()->timestamp * 1000,
                'pending' => $pending,
                'approved' => $approved,
            ];
            // Move to the previous month for the next iteration
            $endDate->subMonth();
        }
        // Reverse the data array to maintain chronological order
        $data = array_reverse($data);

        return $data;
    }
    public function filterData(Request $request)
    {
        $filter = $request->input('filter');
        $telecaller = $request->input('telecaller');
        $today = Carbon::today();
    
        $query = DB::table('ev_tbl_leads as el')
            ->select(
                'u.name as name',
                'el.assigned',
                DB::raw("COUNT(CASE WHEN el.telecaller_status = 'New' THEN 1 END) AS New"),
                DB::raw("COUNT(CASE WHEN el.telecaller_status = 'Call_Back' THEN 1 END) AS Call_Back"),
                DB::raw("COUNT(CASE WHEN el.telecaller_status = 'Contacted' THEN 1 END) AS Contacted"),
                DB::raw("COUNT(CASE WHEN el.telecaller_status = 'DeadLead' THEN 1 END) AS DeadLead"),
                DB::raw('COUNT(*) AS count')
            )
            ->leftJoin('users as u', 'el.assigned', '=', 'u.id')
            ->whereIn('el.telecaller_status', ['New', 'Contacted', 'Call_Back', 'Onboarded', 'DeadLead'])
            ->where('u.name', '!=', '')
            ->where('el.assigned',$telecaller);
    
        // Apply date filter based on the request input
        if ($filter === 'today') {
            $query->whereDate('el.register_date', $today);
        } elseif ($filter === 'week') {
            $query->whereBetween('el.register_date', [
                Carbon::today()->startOfWeek()->toDateString(),
                Carbon::today()->endOfWeek()->toDateString()
            ]);
        } elseif ($filter === 'month') {
            $query->whereMonth('el.register_date', $today->month)
                  ->whereYear('el.register_date', $today->year);
        }
    
        // Group by and get the data
        $data = $query->groupBy('el.assigned', 'u.name')
                      ->get();
    
        return response()->json($data);
    }
    
    public function filterhrData(Request $request)
    {
        $filter = $request->input('filter');
        $today = Carbon::today();
    
        $query = DB::table('ev_tbl_leads as el')
            ->select(
                'u.name as name',
                'el.assigned',
                DB::raw("COUNT(CASE WHEN el.telecaller_status = 'New' THEN 1 END) AS New"),
                DB::raw("COUNT(CASE WHEN el.telecaller_status = 'Call_Back' THEN 1 END) AS Call_Back"),
                DB::raw("COUNT(CASE WHEN el.telecaller_status = 'Contacted' THEN 1 END) AS Contacted"),
                DB::raw("COUNT(CASE WHEN el.telecaller_status = 'DeadLead' THEN 1 END) AS DeadLead"),
                DB::raw('COUNT(*) AS count')
            )
            ->leftJoin('users as u', 'el.assigned', '=', 'u.id')
            ->whereIn('el.telecaller_status', ['New', 'Contacted', 'Call_Back', 'Onboarded', 'DeadLead'])
            ->where('u.name', '!=', '');
    
        // Apply date filter based on the request input
        if ($filter === 'today') {
            $query->whereDate('el.register_date', $today);
        } elseif ($filter === 'week') {

           $query->whereBetween('el.register_date', [
                Carbon::today()->startOfWeek()->toDateString(),
                Carbon::today()->endOfWeek()->toDateString()
            ]);

        }elseif ($filter === 'month') {
            $query->whereMonth('el.register_date', $today->month)
                  ->whereYear('el.register_date', $today->year);
        }
    
        // Group by and get the data
        $data = $query->groupBy('el.assigned', 'u.name')
                      ->get();
        return response()->json($data);
    }
    
   public function get_today_application_count(Request $request)
   {
        $date = $request->filter_date ?? now()->format('Y-m-d');
        $city_id = $request->city_id ?? '';
        $query = Deliveryman::whereDate('register_date_time', $date)
            ->where('delete_status', 0);
        if (!empty($city_id)) {
            $query->where('current_city_id', $city_id);
        }
        $count = $query->count();
        return response()->json(['count' => $count]);
    }
    
   
    public function get_today_pending_application_count(Request $request)
    {
        $city_id = $request->city_id;
    
        $dm = Deliveryman::orderBy('id','asc')->first();
        $from_date = $dm ? date('Y-m-d', strtotime($dm->register_date_time)) : '';
        $to_date = $request->filter_date ?? '';
    
    
        $bgv_pending_count = 0;
        $bgv_ageing_pending_count = 0;
    
        Deliveryman::where('delete_status', 0)
            ->where('kyc_verify', 0)
            ->when($from_date && $to_date, function ($query) use ($from_date, $to_date) {
                $query->whereBetween(DB::raw('DATE(register_date_time)'), [$from_date, $to_date]);
            })
            ->when(!is_null($city_id) && $city_id !== '', function ($query) use ($city_id) {
                $query->where('current_city_id', $city_id);
            })
            ->chunk(1000, function ($bgv_pending_applications) use (&$bgv_pending_count, &$bgv_ageing_pending_count) {
                foreach ($bgv_pending_applications as $val) {
                    $ageing_days = now()->diffInDays($val->register_date_time);
                    if ($ageing_days > 7) {
                        $bgv_ageing_pending_count++;
                    } else {
                        $bgv_pending_count++;
                    }
                }
            });

    
        return response()->json([
            'bgv_pending_count' => $bgv_pending_count,
            'bgv_ageing_pending_count' => $bgv_ageing_pending_count
        ]);
    }

    public function RiderOnboardfilterData(Request $request)
    {
        $category = $request->category === 'rider' ? 'deliveryman' : $request->category;
        
        $data = DB::table('rider_onboarding_lists as a')
            ->leftJoin('ev_tbl_customer_master as b', 'a.customer_master_id', '=', 'b.id')
            ->select(
                'b.trade_name as name',
                DB::raw('COUNT(*) as cust_count'),
                DB::raw('COUNT(*) as city_count'),
                DB::raw('COUNT(DISTINCT a.hub_id) as hub_count') 
            )
            ->where('a.role_type', $category)
            ->groupBy('a.customer_master_id', 'b.trade_name') 
            ->orderByDesc('cust_count')
            ->get();
    
        return response()->json([
            'labels' => $data->pluck('name'),
            'values' => $data->pluck('cust_count'),
            'cities' => $data->pluck('city_count'),
            'hubs' => $data->pluck('hub_count'),
        ]);
    }
    
    public function getCities(Request $request, $state_id)
    {
        $cities = City::where('state_id', $state_id)->where('status',1)->get();
    
        return response()->json([
            'success' => true,
            'data' => $cities
        ]);
    }
    
     
}

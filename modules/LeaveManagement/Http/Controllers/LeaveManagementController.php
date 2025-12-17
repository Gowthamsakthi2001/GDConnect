<?php

namespace Modules\LeaveManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Modules\City\Entities\City;
use Modules\City\Entities\Area;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\LeaveManagement\Entities\LeaveType; 
use Modules\LeaveManagement\Entities\LeaveRequest; 
use Modules\LeaveManagement\DataTables\LeaveDataTable;
use Modules\LeaveManagement\DataTables\LeaveRequestDataTable;
use Modules\LeaveManagement\DataTables\ApprovedLeaveRequestDataTable;
use Modules\LeaveManagement\DataTables\PermissionRequestDataTable;
use Modules\LeaveManagement\DataTables\LeaveLogDataTable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; //updated by Mugesh.B
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceReport;
class LeaveManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leaves = LeaveType::where('status',1)->get();
        return view('leavemanagement::index',compact('leaves'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('leavemanagement::create');
    }

    public function list(LeaveDataTable $dataTable)
    {
        return $dataTable->render('leavemanagement::index');
    }

    /**
     * Store a newly created resource in storage.
     */
   public function add_orupdate(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'leave_name' => 'required|max:191',
            'short_name' => 'required|max:3',
            'leave_type' => 'required|in:hour,day',
            'days' => 'required|integer',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $user = Auth::user();
        $roleName    = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        $performedBy = $user->name ?? 'Unknown User';
        $pageName    = 'leave_type.add_or_update';
    
    
        if ($request->leave_id != null) {
            $leave = LeaveType::find($request->leave_id);
            if ($leave) {
                $leave->update([
                    'leave_name' => $request->leave_name, 
                    'short_name' => $request->short_name,
                    'leave_type' => $request->leave_type,
                    'days' => $request->days,
                ]);
    
    
                $shortDescription = "Leave type updated ({$leave->leave_name})";
                $longDescription  = "The leave type '{$leave->leave_name}' (Short: {$leave->short_name}) "
                          . "was updated by {$performedBy} ({$roleName}). "
                          . "Type: {$leave->leave_type}, Days: {$leave->days}.";
                          
                          
                $message = 'Leave successfully updated!';
            }
        }else{
        $leave = new LeaveType();
        $leave->leave_name = $request->leave_name;
        $leave->short_name = $request->short_name;
        $leave->leave_type = $request->leave_type;
        $leave->days = $request->days ?? '0';
        $leave->status = 1;
        $leave->save();
        
        $shortDescription = "Leave type created ({$leave->leave_name})";
        $longDescription  = "A new leave type '{$leave->leave_name}' (Short: {$leave->short_name}) "
                          . "was created by {$performedBy} ({$roleName}). "
                          . "Type: {$leave->leave_type}, Days: {$leave->days}.";

        $message = 'Leave successfully added!';
        
        }
    
        audit_log_after_commit([
            'module_id'         => 2, 
            'short_description' => $shortDescription,
            'long_description'  => $longDescription,
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => $pageName,
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent(),
        ]);
    
        // 🔹 Return success response
        return response()->json([
            'success' => true,
            'message' => $message,
            'reset'   => true,
        ]);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('leavemanagement::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
     public function edit($id)
    {
        $leave = LeaveType::where('status', 1)->where('id', $id)->first();
    
        if (!$leave) {
            return response()->json([
                'success' => false,
                'message' => 'Leave record not found!',
            ], 404);
        }
    
        return response()->json([
            'success' => true,
            'data' => $leave,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request): RedirectResponse
    {
    
        $leaveIds = $request->input('leave_id'); 
        $days = $request->input('days'); 
    
        if (is_array($leaveIds) && is_array($days)) {
            foreach ($leaveIds as $index => $leaveId) {
                $leave = LeaveType::find($leaveId); 
                if ($leave) {
                    $leave->update(['days' => $days[$index] ?? 0]); 
                }
            }
        }
    
        return redirect()->route('admin.Green-Drive-Ev.leavemanagement.index')->with('success', 'Leave saved successfully!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
       
    }
    
    public function delete_leave($id)
    {
 
        
        try {
            $leave = LeaveType::findOrFail($id);
            $leave->status = 0;
            $leave->save();
            // $leave->delete();
    
            $user = Auth::user();
            $roleName    = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
            $performedBy = $user->name ?? 'Unknown User';
            $pageName    = 'leave_type.delete';
    
            // Prepare log details
            $shortDescription = "Leave type deleted ({$leave->leave_name})";
            $longDescription  = "The leave type '{$leave->leave_name}' (Short: {$leave->short_name}) "
                              . "was deleted by {$performedBy} ({$roleName}). "
                              . "Type: {$leave->leave_type}, Days: {$leave->days}.";
    
            // Save audit log
            audit_log_after_commit([
                'module_id'         => 2, // HR or Leave Management module ID
                'short_description' => $shortDescription,
                'long_description'  => $longDescription,
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => $pageName,
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent(),
            ]);
        
            return back()->with('success', 'Leave deleted successfully');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while deleting: ' . $e->getMessage());
        }
    }

    
    public function new_leave_request_list(LeaveRequestDataTable $dataTable){
        return $dataTable->render('leavemanagement::new_leave_request');
    }
    public function approved_leave_request_list(ApprovedLeaveRequestDataTable $dataTable){

        return $dataTable->render('leavemanagement::approved_leave_request');
    }
    
    public function new_permission_request_list(PermissionRequestDataTable $dataTable){
        return $dataTable->render('leavemanagement::permission_request');
    }
    
    public function leave_approve_or_reject(Request $request) {
   
        $leave_req = LeaveRequest::where('id', $request->id)->first();
        
        if (!$leave_req) {
            return response()->json(['success' => false, 'message' => 'Leave request not found'], 404);
        }
        
        $user = Auth::user();
        $roleName    = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        $performedBy = $user->name ?? 'Unknown User';
        $pageName    = 'leave_request.approval';
        
        $employeeName = $leave_req->deliveryman->first_name . ' ' . $leave_req->deliveryman->last_name;

    
        if($request->status == 1) {
            $leave_req->approve_status = 1;
            $leave_req->is_paid = $request->is_paid; 
            $leave_req->reject_status = 0;
            $leave_req->rejection_reason = null;
            

            $statusText = $leave_req->is_paid ? 'Paid' : 'Unpaid';
            $shortDescription = "Leave approved ({$employeeName} - {$statusText})";
            $longDescription = "{$employeeName}'s leave request was approved as {$statusText} "
                             . "by {$performedBy} ({$roleName}).";
                             
                             
                             
        } else {
            $leave_req->reject_status = 1;
            $leave_req->rejection_reason = urldecode($request->remarks);
            $leave_req->approve_status = 0;
            $leave_req->is_paid = 0;
            
            $shortDescription = "Leave rejected ({$employeeName})";
            $longDescription = "{$employeeName}'s leave request was rejected by {$performedBy} ({$roleName}). "
                             . "Reason: {$leave_req->rejection_reason}.";
                             
        }
        
        $leave_req->req_status = null;
        
        if ($leave_req->save()) {
            
            audit_log_after_commit([
                'module_id'         => 2, // HR / Leave Management module
                'short_description' => $shortDescription,
                'long_description'  => $longDescription,
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => $pageName,
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent(),
            ]);
        
        
            $message = $request->status == 1 
                ? 'The Leave has been approved as ' . ($leave_req->is_paid ? 'Paid' : 'Unpaid') 
                : 'The Leave has been rejected!';
                
            return response()->json([
                'success' => true,
                'message' => $message
            ], 200);
        }
        
        return response()->json([
            'success' => false, 
            'message' => 'Failed to update leave request'
        ], 500);
    }
    
    // public function leave_approve_or_reject(Request $request){

    //     $leave_req = LeaveRequest::where('id',$request->id)->first();
    //      if (!$leave_req->save()) {
    //         return response()->json(['success' => false, 'message' => 'The network connection has failed. Please try again later.'], 404);
    //     }
    //     if($request->status == 1){
    //         $leave_req->approve_status = 1;
    //     }else{
    //         $leave_req->reject_status = 1;
    //         $leave_req->rejection_reason = urldecode($request->remarks);
    //     }
    //     $leave_req->req_status = null;
    //     $leave_req->save();
    //     if($request->status == 1){
    //         return response()->json([
    //         'success' => true,
    //         'message' => 'The Leave has been approved!'
    //         ], 200);
    //     }else{
    //         return response()->json([
    //         'success' => true,
    //         'message' => 'The Leave has been rejected!'
    //     ], 200);
    //     }
    // }
    
    public function leave_log_report(LeaveLogDataTable $dataTable){
        return $dataTable->render('leavemanagement::leave_log_report');
    }
    
    public function get_leave_count(Request $request)
    {
       
        // Validate the request contains an ID
        if (!$request->has('id')) {
            return response()->json([
                'success' => false,
                'message' => 'ID parameter is required'
            ], 400);
        }
    
        $id = $request->id;
        
        try {
            // Get the leave request (if you need it)
            
            // Get counts for the specified DM
            $currentMonthStart = Carbon::now()->startOfMonth();
            $currentMonthEnd = Carbon::now()->endOfMonth();
            
            $casualLeaveCount = LeaveRequest::where('dm_id', $id)
                ->where('leave_id', 8) // Casual leave
                ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
                ->count();
             
            $sickLeaveCount = LeaveRequest::where('dm_id', $id)
                ->where('leave_id', 7) // Sick leave
                ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
                ->count();
                
            $permissionLeaves = LeaveRequest::where('dm_id', $id)
                ->where('permission_hr', 1)
                ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
                ->get();
    
                $totalHours = 0;
    
                foreach ($permissionLeaves as $leave) {
                    if ($leave->start_time && $leave->end_time) {
                        try {
                            $startTime = Carbon::parse($leave->start_time);
                            $endTime = Carbon::parse($leave->end_time);
                            $totalHours += $startTime->diffInHours($endTime);
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }
                
                $totalDays = intval($totalHours / 8);     // Whole days
                $remainingHours = $totalHours % 8; 
               
            $otherLeaveCount = LeaveRequest::where('dm_id', $id)
                ->whereNotIn('leave_id', [7, 8]) // Exclude sick and casual leaves
                ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
                ->count();
        
            $max_casual_leave = LeaveType::select('days')->where('id',8)->first(); 
            $max_sick_leave = LeaveType::select('days')->where('id',7)->first(); 
            return response()->json([
                'success' => true,
                'data' => [
                    'dm_id' => $id,
                    'casual_leave_count' => $casualLeaveCount,
                    'max_casual' => ceil($max_casual_leave->days / 12),
                    'max_sick' => ceil($max_sick_leave->days / 12),
                    'permission_leave' =>[
                        'days'=>$totalDays,
                        'hours'=>$remainingHours 
                        ],
                    'sick_leave_count' => $sickLeaveCount,
                    'other_leave_count' =>$otherLeaveCount,
                    'total_leaves' => $casualLeaveCount + $sickLeaveCount + $otherLeaveCount,
                    
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching leave counts: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function attendance_report(Request $request){
        if(!view()->exists('leavemanagement::attendance_report')){
            return back()->with('error','view not found');
        }
        $cities = DB::table('ev_tbl_city')->select('id','city_name')->get();
        return view('leavemanagement::attendance_report',compact('cities'));
        
    }
    
    public function get_attendance_report_data(Request $request){
        if ($request->ajax()) {
            try {
                   
                $start  = (int) $request->input('start', 0);
                $length = (int) $request->input('length', 25);
                $search = trim($request->input('search.value', ''));
                $draw   = (int) $request->input('draw', 1);

                $city_ids   = (array) $request->input('city', []);
                $zone_ids   = (array) $request->input('area', []);
                $user_types = (array) $request->input('user_type', []);
                $user_ids   = (array) $request->input('user_id', []);
    
                $dateRange = $request->input('date_filter', 'today');
                $from = $request->input('from_date');
                $to   = $request->input('to_date');

                switch ($dateRange) {
                    case 'today':
                        $from = $to = now()->toDateString();
                        break;
                    case 'yesterday':
                        $from = $to = now()->subDay()->toDateString();
                        break;
                    case 'this_week':
                        $from = now()->startOfWeek()->toDateString();
                        $to   = now()->endOfWeek()->toDateString();
                        break;
                    case 'last_week':
                        $from = now()->subWeek()->startOfWeek()->toDateString();
                        $to   = now()->subWeek()->endOfWeek()->toDateString();
                        break;
                    case 'this_month':
                        $from = now()->startOfMonth()->toDateString();
                        $to   = now()->endOfMonth()->toDateString();
                        break;
                    case 'last_month':
                        $from = now()->subMonthNoOverflow()->startOfMonth()->toDateString();
                        $to   = now()->subMonthNoOverflow()->endOfMonth()->toDateString();
                        break;
                    case 'custom':
                        // keep passed from/to
                        break;
                    default:
                        $from = $to = now()->toDateString();
                }
                $baseQuery = DB::table('ev_delivery_man_logs as dml')
                    ->join('ev_tbl_delivery_men as dm', 'dm.id', '=', 'dml.user_id')
                    ->leftJoin('ev_tbl_city as c', 'c.id', '=', 'dm.current_city_id')
                    ->leftJoin('ev_tbl_area as area', 'area.id', '=', 'dm.interested_city_id')
                    ->select(
                        'dml.id as log_id',
                        'dm.emp_id',
                        DB::raw("CONCAT(dm.first_name,' ',dm.last_name) as deliveryman_name"),
                        'dm.mobile_number',
                        'c.city_name',
                        'area.Area_name',
                        'dml.punched_in',
                        'dml.punched_out',
                        'dml.punchin_latitude',
                        'dml.punchin_longitude',
                        'dml.punchout_latitude',
                        'dml.punchedout_longitude',
                        'dml.punchin_address',
                        'dml.punchout_address'
                    );

                    if (!empty($city_ids) && !in_array('all', $city_ids)) {
                        $baseQuery->whereIn('dm.current_city_id', $city_ids);
                    }
                    if (!empty($zone_ids) && !in_array('all', $zone_ids)) {
                        $baseQuery->whereIn('dm.interested_city_id', $zone_ids);
                    }
                    if (!empty($user_types) && !in_array('all', $user_types)) {
                        $baseQuery->whereIn('dm.work_type', $user_types);
                    }
                    if (!empty($user_ids) && !in_array('all', $user_ids)) {
                        $baseQuery->whereIn('dm.id', $user_ids);
                    }

                    if ($from && $to) {
                        $baseQuery->whereDate('dml.punched_in', '>=', $from)
                                  ->whereDate('dml.punched_in', '<=', $to);
                    }
            
                    if (!empty($search)) {
                        $baseQuery->where(function($q) use ($search) {
                            $q->where('dm.first_name', 'like', "%{$search}%")
                              ->orWhere('dm.last_name', 'like', "%{$search}%")
                              ->orWhere('dm.emp_id', 'like', "%{$search}%")
                              ->orWhere('dm.mobile_number', 'like', "%{$search}%");
                        });
                    }
                    $totalRecords = (clone $baseQuery)->count();
                    // dd($baseQuery->toSql(),$baseQuery->getBindings(),$totalRecords);
                    if ($length == -1) $length = $totalRecords;

                    $datas = $baseQuery->orderBy('dml.id', 'asc')->skip($start)->take($length)->get();
                    $formattedData = $datas->map(function ($data, $key) use ($start) {

                        return [
                            $start + $key + 1,
                            $data->emp_id ?? '-',
                            $data->deliveryman_name ?? '-',
                            $data->city_name ?? '-',
                            $data->punched_in ? date('d-m-Y', strtotime($data->punched_in)) : '-',
                            $data->punched_in ? date('h:i:s A', strtotime($data->punched_in)) : '-',
                            $data->punched_out ? date('h:i:s A', strtotime($data->punched_out)) : 'Not Punched Out',
                            $this->calculateDuration($data->punched_in, $data->punched_out),
                            '<a href="javascript:void(0)" 
                                class="btn btn-sm btn-outline-primary view-attendance"
                                data-emp_id="'.$data->emp_id.'"
                                data-name="'.$data->deliveryman_name.'"
                                data-city="'.$data->city_name.'"
                                data-area="'.$data->Area_name.'"
                                data-date="'.($data->punched_in ? date('d-m-Y', strtotime($data->punched_in)) : '-').'"
                                data-punchin="'.($data->punched_in ? date('h:i:s A', strtotime($data->punched_in)) : '-').'"
                                data-punchout="'.($data->punched_out ? date('h:i:s A', strtotime($data->punched_out)) : 'Not Punched Out').'"
                                data-punchin_location="'.($data->punchin_address ?? '-').'"
                                data-punchout_location="'.($data->punchout_address ?? '-').'"
                                data-duration="'.$this->calculateDuration($data->punched_in, $data->punched_out).'"
                                title="View Details">
                                <i class="fa fa-eye"></i>
                            </a>'
                        ];
                    });


                    return response()->json([
                        'draw' => intval($request->input('draw')),
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $formattedData
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Deployment Report Error: ' . $e->getMessage());
                    return response()->json([
                        'draw' => intval($request->input('draw')),
                        'recordsTotal' => 0,
                        'recordsFiltered' => 0,
                        'data' => [],
                        'error' => $e->getMessage(),
                    ], 500);
                }
            }
    }
    
    // protected function calculateDuration($in, $out)
    // {
    //     if (!$in || !$out) return '00:00:00';
    
    //     $start = \Carbon\Carbon::parse($in);
    //     $end   = \Carbon\Carbon::parse($out);
    
    //     return $start->diff($end)->format('%H:%I:%S');
    // }
    
    protected function calculateDuration($in, $out)
    {
        if (!$in || !$out) {
            return '00:00:00';
        }
    
        $start = \Carbon\Carbon::parse($in);
        $end   = \Carbon\Carbon::parse($out);
    
        $seconds = $start->diffInSeconds($end);
    
        $hours   = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs    = $seconds % 60;
    
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
    
    public function export_attendance_report(Request $request)
    {
        $date_range = $request->input('date_range') ?? 'today';
        $from_date  = $request->input('from_date');
        $to_date    = $request->input('to_date');
    
        $city     = (array) $request->input('city_id', []);
        $area     = (array) $request->input('area', []);
        $user_type = $request->input('user_type',[]);
        $user_id  = (array) $request->input('user_id', []);
        $emp_id = (array) $request->input('emp_id',[]);
        $fields = (array) $request->input('fields',[]);

        $fileName = 'attendance-report-' . date('d-m-Y') . '.csv';
    
        /* -------- Filter Names -------- */
    
        $cityName = !empty($city)
            ? City::whereIn('id', $city)->pluck('city_name')->implode(', ')
            : null;
    
        $zoneName = !empty($area)
            ? Area::whereIn('id', $area)->pluck('Area_name')->implode(', ')
            : null;
    
        $userId = !empty($user_id)
            ? Deliveryman::whereIn('id', $user_id)->pluck('emp_id')->implode(', ')
            : null;
        $empId = !empty($emp_id)
            ? Deliveryman::whereIn('id', $emp_id)->pluck('emp_id')->implode(', ')
            : null;
    
        /* -------- Applied Filters -------- */
    
        $appliedFilters = [];
    
        if ($date_range) $appliedFilters[] = "Date Range: {$date_range}";
        if ($from_date)  $appliedFilters[] = "From: {$from_date}";
        if ($to_date)    $appliedFilters[] = "To: {$to_date}";
        if ($cityName)   $appliedFilters[] = "City: {$cityName}";
        if ($zoneName)   $appliedFilters[] = "Zone: {$zoneName}";
        if ($userId)     $appliedFilters[] = "Emp ID: {$userId}";
    
        $filtersText = empty($appliedFilters)
            ? 'No filters applied'
            : implode('; ', $appliedFilters);
    
        /* -------- Audit Log -------- */
    
        $user = Auth::user();
        $roleName = optional(
            \Modules\Role\Entities\Role::find(optional($user)->role)
        )->name ?? 'Unknown';
    
        // audit_log_after_commit([
        //     'module_id'         => 5,
        //     'short_description' => 'HR Module Attendance Export Initiated',
        //     'long_description'  => "Attendance export triggered. File: {$fileName}. Filters: {$filtersText}.",
        //     'role'              => $roleName,
        //     'user_id'           => Auth::id(),
        //     'user_type'         => 'gdc_admin_dashboard',
        //     'dashboard_type'    => 'web',
        //     'page_name'         => 'hr_management.attendance_export',
        //     'ip_address'        => $request->ip(),
        //     'user_device'       => $request->userAgent(),
        // ]);
    
        /* -------- Excel Export -------- */
   
        return Excel::download(
            new AttendanceReport(
                $from_date,
                $to_date,
                $date_range,
                $city,
                $area,
                $user_type,
                $user_id,
                $emp_id,
                $fields
                
            ),
            $fileName
        );
    }


}

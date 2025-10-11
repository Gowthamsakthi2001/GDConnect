<?php

namespace Modules\LeaveManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Modules\LeaveManagement\Entities\LeaveType; 
use Modules\LeaveManagement\Entities\LeaveRequest; 
use Modules\LeaveManagement\DataTables\LeaveDataTable;
use Modules\LeaveManagement\DataTables\LeaveRequestDataTable;
use Modules\LeaveManagement\DataTables\ApprovedLeaveRequestDataTable;
use Modules\LeaveManagement\DataTables\PermissionRequestDataTable;
use Modules\LeaveManagement\DataTables\LeaveLogDataTable;
use Illuminate\Support\Facades\Validator;

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
        if ($request->leave_id != null) {
            $leave = LeaveType::find($request->leave_id);
            if ($leave) {
                $leave->update([
                    'leave_name' => $request->leave_name, 
                    'short_name' => $request->short_name,
                    'leave_type' => $request->leave_type,
                    'days' => $request->days,
                ]);
    
                return response()->json([
                    'success' => true,
                    'message' => 'Leave successfully updated!',
                ]);
            }
        }
        $leave = new LeaveType();
        $leave->leave_name = $request->leave_name;
        $leave->short_name = $request->short_name;
        $leave->leave_type = $request->leave_type;
        $leave->days = $request->days ?? '0';
        $leave->status = 1;
        $leave->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Leave successfully added!',
            'reset'=>true
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
    
        if($request->status == 1) {
            $leave_req->approve_status = 1;
            $leave_req->is_paid = $request->is_paid; 
            $leave_req->reject_status = 0;
            $leave_req->rejection_reason = null;
        } else {
            $leave_req->reject_status = 1;
            $leave_req->rejection_reason = urldecode($request->remarks);
            $leave_req->approve_status = 0;
            $leave_req->is_paid = 0;
        }
        
        $leave_req->req_status = null;
        
        if ($leave_req->save()) {
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
}

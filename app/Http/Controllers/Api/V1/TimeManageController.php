<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Modules\Deliveryman\Entities\DeliveryManLogs;
use Modules\Deliveryman\Entities\Deliveryman;
use modules\Clients\Entities\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DateTime;
use Modules\Deliveryman\Entities\ClientDmReport; //updated by Gowtham.s

class TimeManageController extends Controller
{
    
    public function punchIn(Request $request)
    {
        $request->merge([
            'in_time' => str_replace("\u{00A0}", ' ', $request->in_time)
        ]);
        
        // Validate input data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'user_type' => 'required|string',
            'in_time' => 'required|date_format:Y-m-d H:i:s',
            'lat' => 'required|string',
            'long' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            // Return validation errors
            return response()->json(['errors' => $validator->errors()], 403);
        }
        
        Log::info("Punch In pass data " .json_encode($request->all()));
        
    
        $lastPunchIn = DeliveryManLogs::where('user_id', $request->user_id)->orderBy('punched_in', 'desc')->value('punched_in'); // Get the last punch-in time
        
       $dm = Deliveryman::where('id', $request->user_id)->first();
       
        if ($dm->work_type == "adhoc" && !empty($dm->active_date)) {
            $activeDateTime = new \DateTime($dm->active_date); // Example: 2025-03-15 06:00:00 AM
            $currentDateTime = new \DateTime(); // Get the current time
    
            $after_24_hrComplete = clone $activeDateTime; // Clone to avoid modifying original object
            $after_24_hrComplete->modify('+24 hours'); // Now: 2025-03-15 06:00:00 PM

            if ($currentDateTime > $after_24_hrComplete) {
                return response()->json([
                    'message' => 'Your account is inactive. Your active time has expired. Please contact our supervisor team for assistance.',
                    'status' => 'inactive',
                ], 400);
            }
        }

       
      
      if($dm->rider_status == 0 || $dm->approved_status != 1 && $dm->work_type != "adhoc"){
        if ($lastPunchIn) {
            $daysSinceLastPunch = now()->diffInDays($lastPunchIn);
    
            if ($daysSinceLastPunch >= 3) {
                Deliveryman::where('id', $request->user_id)->update(['rider_status' => 0]);
    
                return response()->json([
                    'message' => $dm->work_type.' is inactive due to no activity in the last 3 days.',
                    'status' => 'inactive',
                ], 400);
            }
        }
      }
      
        $dm = Deliveryman::where('id', $request->user_id)->first();
        if ($dm && $dm->rider_status == 0) {
            return response()->json([
                'message' => 'Your account is inactive. Please contact admin.',
            ], 403);
        }

        $latitude = $request->lat;
        $longitude = $request->long;
        
        // if($dm->work_type != "in-house"){ //zone checking for rider,adhoc - commend by Gowtham.S
            
        //   $matchingZones = DB::table('zones')->select('id')->whereRaw("ST_Contains(coordinates, ST_GeomFromText(CONCAT('POINT(', ?, ' ', ?, ')')))", [$longitude, $latitude])->get();
    
        //     if ($matchingZones->isEmpty()) {
        //         return response()->json(['message' => 'You are outside the allowed zones.'], 400);
        //     } 
        // }
        
    
        $record = DeliveryManLogs::where('user_id', $request->user_id)
            ->whereNull('punched_out')
            ->whereDate('punched_in', now())
            ->where('user_type', $request->user_type)
            ->first();
    
        if ($record) {
            // User is already punched in
            return response()->json(['message' => 'You are already punched in. Please punch out before punching in again.'], 400);
        }
    
        // Create a new punch-in record
        $timeRecord = new DeliveryManLogs();
        $timeRecord->user_id = $request->user_id;
        $timeRecord->user_type = $request->user_type ?? null;
        $timeRecord->punched_in = $request->in_time;
        $timeRecord->punchin_latitude = $request->lat;
        $timeRecord->punchin_longitude = $request->long; // Corrected 'longtitude' to 'longitude'
        $timeRecord->created_at = now();
        $timeRecord->status = 1;
        $timeRecord->client_id = $dm->client_id ?? null;
        $timeRecord->probation_period = $request->probation_period ?? null;
        
        $timeRecord->save();


        $records = DeliveryManLogs::where('id', $timeRecord->id)
            ->select('status', 'punched_in as in_time')
            ->where('user_type', $request->user_type)
            ->first();
            
        $delivery_man = Deliveryman::where('id', $request->user_id)->first();
        if(isset($delivery_man->client_id) && $delivery_man->client_id != ""){
            ClientDmReport::create([
                'client_id' => $delivery_man->client_id,
                'driver_id' => $request->user_id,
                'chass_serial_no'=>$delivery_man->Chassis_Serial_No,
                'latitude'=> $request->lat,
                'longitude'=>$request->long,
                'start_time' => $request->in_time,
                'created_at' => now()
            ]);  
        }
    
        // Return success response with the time record data
        return response()->json(['message' => 'Punched in successfully', 'data' => $records], 200);
    }


    public function punchOut(Request $request)
    {
        // if ($request->has('out_time')) {
        //     $request->merge([
        //         'out_time' => Carbon::parse($request->out_time)->format('Y-m-d H:i:s')
        //     ]);
        // }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'user_type' => 'required',
            // 'out_time' => 'required|date_format:Y-m-d H:i:s',
        ]);
    
        if ($validator->fails()) {
            // Handle validation errors directly
            return response()->json(['errors' => $validator->errors()], 403);
        }
        
        $record = DeliveryManLogs::where('user_id', $request->user_id)
            ->whereNull('punched_out')
            ->whereNotNull('punched_in') 
            ->orderBy('punched_in', 'desc')
            ->where('user_type', $request->user_type)
            ->first();
    
        if (!$record) {
            return response()->json(['message' => 'No punch-in record found'], 400);
        }
        $latitude = $request->lat;
        $longitude = $request->long;
        
        $record->punched_out = $request->out_time; 
        $record->punchout_latitude = $latitude;
        $record->punchedout_longitude = $longitude;
        $record->probation_period = $request->probation_period ?? null;
        $record->status = 0;
        $record->save(); 
        
        $records = DeliveryManLogs::where('id', $record->id)
            ->select('status', 'punched_out as out_time')
            ->where('user_type', $request->user_type)
            ->first();
            
        $delivery_man = Deliveryman::where('id', $request->user_id)->first(); 
        if(isset($delivery_man->client_id) && $delivery_man->client_id != ""){
            ClientDmReport::create([
                'client_id' => $delivery_man->client_id,
                'driver_id' => $request->user_id,
                'chass_serial_no'=>$delivery_man->Chassis_Serial_No,
                'latitude'=> $request->lat,
                'longitude'=>$request->long,
                'end_time' => $request->out_time,
                'created_at' => now()
            ]);  
        }
    
        return response()->json(['message' => 'Punched out successfully', 'data' => $records], 200);
    }
    
    public function deliveryman_reports(Request $request, $dm_id)
    {
        // Log::info("The Api Is called start api". json_encode($request->all()));
        //  Log::info("The Api Is called start api".$dm_id);
         
        //  $lastPunchIn = DB::table('ev_delivery_man_logs')
        //     ->where('user_id', $dm_id) // Ensure consistency
        //     ->orderBy('punched_in', 'desc')
        //     ->first();
        
        // if ($lastPunchIn) {
        //     $daysSinceLastPunch = now()->diffInDays(Carbon::parse($lastPunchIn->punched_in));
        
        //     if ($daysSinceLastPunch >= 3) { // Last punch-in was at least 3 days ago
        //         $dm = Deliveryman::where('id', $dm_id)->first();
        //         if ($dm) {
        //             $dm->rider_status = 0;
        //             $dm->save();
        //         }
        //     }
        // }

        $today = date('Y-m-d'); // Get today's date in YYYY-MM-DD format
    
        $query = "
            SELECT 
                DATE(ev_delivery_man_logs.punched_in) AS date,
                TIME(ev_delivery_man_logs.punched_in) AS in_time,
                TIME(ev_delivery_man_logs.punched_out) AS out_time,
                TIMESTAMPDIFF(SECOND, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out) AS active_seconds
            FROM ev_delivery_man_logs 
            WHERE ev_delivery_man_logs.user_id = ? 
            AND DATE(ev_delivery_man_logs.punched_in) = ?
            ORDER BY ev_delivery_man_logs.punched_in ASC
        ";
    
        // Execute the query with today's date filter
        $logs = DB::select($query, [$dm_id, $today]);
    
        if (!$logs) {
            return response()->json([
                'status' => false,
                'message' => 'No report found for today for the specified delivery man.',
            ]);
        }
    
        // Initialize variables
        $total_active_seconds = 0;
        $total_inactive_seconds = 0;
        $total_working_seconds = 0;
    
        $time_intervals = []; // Store punch-in/out intervals
        $previous_punch_out = null;
    
        foreach ($logs as $log) {
            // Store each punch-in/out interval
            $time_intervals[] = [
                'in_time' => $log->in_time,
                'out_time' => $log->out_time,
                'active_time' => $this->formatTime($log->active_seconds),
            ];

    
            // Calculate total active time
            $active_seconds = $log->active_seconds;
            $total_active_seconds += $active_seconds;
    
            // Calculate inactive time
            if ($previous_punch_out) {
                $inactive_seconds = strtotime($log->in_time) - strtotime($previous_punch_out);
                if ($inactive_seconds > 0) {
                    $time_intervals[count($time_intervals) - 1]['inactive_time'] = $this->formatTime($inactive_seconds);
                    $total_inactive_seconds += $inactive_seconds;
                } else {
                    $time_intervals[count($time_intervals) - 1]['inactive_time'] = '00:00:00';
                }
            }
    
            // Update previous punch-out time
            $previous_punch_out = $log->out_time;
        }
    
        // Calculate total working time
        $total_working_seconds = $total_active_seconds + $total_inactive_seconds;
        
        $currentDate = date('Y-m-d'); // Get today's date in YYYY-MM-DD format

        $sqlQuery = "
            SELECT 
                TIME(MIN(ev_delivery_man_logs.punched_in)) AS first_punch_in,
                TIME(MAX(ev_delivery_man_logs.punched_out)) AS last_punch_out
            FROM ev_delivery_man_logs
            WHERE ev_delivery_man_logs.user_id = ? 
            AND DATE(ev_delivery_man_logs.punched_in) = ?
        ";
        
        date_default_timezone_set('Asia/Kolkata');

        $currentTime = Carbon::now()->format('H:i:s');
        
        // Get the in_time from the log and ensure it's in a valid format (e.g., H:i:s)
        $inTime = Carbon::createFromFormat('H:i:s', $log->in_time);
        
        // Create a Carbon instance for the current time
        $currentTimeObj = Carbon::createFromFormat('H:i:s', $currentTime);
        
        // Calculate the difference using diffInSeconds, diffInMinutes, or diffInHours as needed
        $intervalInSeconds = $inTime->diffInSeconds($currentTimeObj);  // Difference in seconds
        
        // Optional: You can convert the interval into hours, minutes, etc.
        $intervalInMinutes = $inTime->diffInMinutes($currentTimeObj);  // Difference in minutes
        $intervalInHours = $inTime->diffInHours($currentTimeObj); // Difference in hours
        
        // Example of formatting the interval as hours, minutes, and seconds
        $formattedInterval = gmdate("H:i:s", $intervalInSeconds);
    
        
        $firstPunchInRecord = DB::select($sqlQuery, [$dm_id, $currentDate]);
    
        return response()->json([
            'status' => true,
            'message' => 'Delivery man report for today retrieved successfully.',
            'data' => [
                'date' => $today,
                'time_intervals' => $time_intervals,
                'current_punch_in' => $formattedInterval,
                'total_active_time' => $this->formatTime($total_active_seconds),
                'total_inactive_time' => $this->formatTime($total_inactive_seconds),
                'total_working_time' => $this->formatTime($total_working_seconds),
                'first_punchin_time' => $firstPunchInRecord,
            ],
        ]);
    }

    public function client_basedon_dm_reports(Request $request, $dm_id)
    {
        if (!isset($dm_id) || $dm_id == "") {
            return response()->json(['message' => 'deliveryman field is required'], 422);
        }
    
        // Fetch the report data with start_date and end_date
        $reports = DB::select("
            WITH paired_times AS (
                SELECT 
                    driver_id,
                    client_id,
                    start_time,
                    chass_serial_no,
                    LEAD(end_time) OVER (PARTITION BY driver_id, client_id ORDER BY id) AS next_end_time
                FROM 
                    ev_client_based_dm_working_reports
            )
            SELECT 
                driver_id,
                client_id,
                chass_serial_no,
                MIN(start_time) AS start_date,  -- Minimum start_time for the group
                MAX(next_end_time) AS end_date, -- Maximum next_end_time for the group
                SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, start_time, next_end_time))) AS total_working_time
            FROM 
                paired_times
            WHERE 
                start_time IS NOT NULL 
                AND next_end_time IS NOT NULL
                AND driver_id = ?
            GROUP BY 
                driver_id, client_id, chass_serial_no;
        ", [$dm_id]);
        $report_data = [];
        foreach ($reports as $report) {
            $item = [];
            $dm_data = Deliveryman::find($report->driver_id);
            $client_data = DB::table('ev_tbl_clients')->where('id', $report->client_id)->first();
    
            $item['driver_id'] = $report->driver_id;
            $item['driver_name'] = $dm_data ? $dm_data->first_name . ' ' . $dm_data->last_name : '';
            $item['client_id'] = $report->client_id;
            $item['client_name'] = $client_data ? $client_data->client_name : '';
            $item['chass_serial_no'] = $report->chass_serial_no;
            $item['start_date'] = date('d M Y H:i:s A',strtotime($report->start_date));
            $item['end_date'] = date('d M Y H:i:s A',strtotime($report->end_date));
            $item['total_working_time'] = $report->total_working_time;
            $report_data[] = $item;
        }
        return response()->json(['message' => 'deliveryman reports fetched successfully', 'data' => $report_data], 200);
    }


    
    /**
     * Helper method to convert seconds to "hours:minutes:seconds"
     */
    private function formatTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
    
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

}

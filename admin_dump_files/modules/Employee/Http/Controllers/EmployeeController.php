<?php

namespace Modules\Employee\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Employee\DataTables\EmployeeDataTable;
use Modules\Employee\DataTables\EmployeeListDataTable;
use Modules\Employee\Entities\Department;
use Modules\Employee\Entities\Employee;
use Modules\Employee\Entities\Position;
use Modules\Employee\Http\Requests\EmployeeRequest;
use Modules\City\Entities\City;
use Modules\City\Entities\Area;
use Modules\Zones\Entities\Zones;
use Modules\Clients\Entities\Client;
use Modules\RiderType\Entities\RiderType;
use Modules\LeadSource\Entities\LeadSource;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeeOnboardList;
use App\Exports\EmployeeLogExport;
use Illuminate\Support\Facades\DB;
use Modules\Deliveryman\Entities\Deliveryman;
class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:employee_management', 'status_check']);
        $this->middleware('strip_scripts_tag')->only(['store', 'update']);
        \cs_set('theme', [
            'title' => 'Employee List',
            'description' => 'Displaying all Employees.',
            'back' => \back_url(),
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('admin.dashboard'),
                ],
                [
                    'name' => 'Employee List',
                    'link' => false,
                ],
            ],
            'rprefix' => 'admin.employee',
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(EmployeeDataTable $dataTable)
    {

        $departments = Department::all();
        $positions = Position::all();

        return $dataTable->render('employee::employee.index', [
            'departments' => $departments,
            'positions' => $positions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::all();
        $positions = Position::all();

        return view('employee::employee.create_edit', [
            'departments' => $departments,
            'positions' => $positions,
        ])->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     */
    public function store(EmployeeRequest $request)
    {

        $data = $request->validated();

        if ($request->hasFile('picture')) {
            $data['avatar_path'] = upload_file($request, 'picture', 'employee');
        }

        $item = Employee::create($data);

        return response()->success($item, localize('Employee Added Successfully'), 201);
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        return view('employee::show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit(Employee $employee)
    {
        $departments = Department::all();
        $positions = Position::all();

        return view('employee::employee.create_edit', [
            'item' => $employee,
            'departments' => $departments,
            'positions' => $positions,
        ])->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     */
    public function update(EmployeeRequest $request, Employee $employee)
    {

        $data = $request->validated();

        if ($request->hasFile('picture')) {
            $data['avatar_path'] = upload_file($request, 'picture', 'employee');

            if ($employee->avatar_path) {
                delete_file($employee->avatar_path);
            }
        } else {
            $data['avatar_path'] = $employee->avatar_path;
        }

        $employee->update($data);

        return response()->success($employee, localize('Employee Updated Successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return response()->success(null, localize('Employee Deleted Successfully'), 200);
    }
    
    public function employee_lists(EmployeeListDataTable $dataTable){
        $clients = Client::All();
        $zones = Zones::where('status',1)->get();
        $cities = City::where('status',1)->get();
        return $dataTable->render('employee::employee_list',compact('zones','clients','cities'));
    }
    
     public function employee_create()
    {
         try {
            $city = City::where('status', 1)->get();
            $source = LeadSource::where('status', 1)->get();
            $rider_type = RiderType::where('status', 1)->get();
            $Zones = Zones::where('status', 1)->get();
            return view('employee::employee_create', compact('city', 'source', 'rider_type','Zones'));
        } catch (Exception $e) {
            return back()->with('error', 'View Not Found.');
        }
    }
    
   public function export_employee_verify_list(Request $request, $type)
    {
        $city_id = $request->get('city_id');
        
        if($type == 'all'){
          return Excel::download(new EmployeeOnboardList($type,$city_id), 'Employee-all-list-' . date('d-m-Y') . '.xlsx');
        }
        else if($type == 'approve'){
          return Excel::download(new EmployeeOnboardList($type, $city_id), 'Employee-approved-list-' . date('d-m-Y') . '.xlsx');
        }else if($type == 'deny'){
             return Excel::download(new EmployeeOnboardList($type, $city_id), 'Employee-rejected-list-' . date('d-m-Y') . '.xlsx');
        }else{
             return Excel::download(new EmployeeOnboardList($type, $city_id), 'Employee-pending-list-' . date('d-m-Y') . '.xlsx');
        }
    }
    
     public function employee_logs(Request $request)
    {
        $city_id = $request->city_id ?? '';
        $summary_type = $request->get('summary_type', 'all');
        $from_date = $summary_type == "period" ? $request->get('from_date') : '';
        $to_date = $summary_type == "period" ? $request->get('to_date') : '';

        
        $timeFilters = [
            'all'         => '', // No date condition
            'daily'       => "DATE(ev_delivery_man_logs.punched_in) = CURDATE()",
            'yesterday'   => "DATE(ev_delivery_man_logs.punched_in) = CURDATE() - INTERVAL 1 DAY",
            'this_week'   => "YEARWEEK(ev_delivery_man_logs.punched_in, 1) = YEARWEEK(CURDATE(), 1)",
            'last_week'   => "YEARWEEK(ev_delivery_man_logs.punched_in, 1) = YEARWEEK(CURDATE(), 1) - 1",
            'this_month'  => "MONTH(ev_delivery_man_logs.punched_in) = MONTH(CURDATE()) AND YEAR(ev_delivery_man_logs.punched_in) = YEAR(CURDATE())",
            'last_month'  => "MONTH(ev_delivery_man_logs.punched_in) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND YEAR(ev_delivery_man_logs.punched_in) = YEAR(CURDATE() - INTERVAL 1 MONTH)",
        ];
        
        // Build the dynamic WHERE clause
        $timeFilterWhere = '';
        
        if ($summary_type === 'period' && $from_date && $to_date) {
            $timeFilterWhere = "AND DATE(ev_delivery_man_logs.punched_in) BETWEEN '{$from_date}' AND '{$to_date}'";
        } elseif (!empty($timeFilters[$summary_type])) {
            $timeFilterWhere = "AND " . $timeFilters[$summary_type];
        }

        // Add city condition if needed
        $cityFilter = $city_id ? "AND ev_tbl_delivery_men.current_city_id = {$city_id}" : '';

        
        $dm = DB::select("
            SELECT 
                ev_tbl_delivery_men.id AS user_id,
                ev_tbl_delivery_men.first_name,
                ev_tbl_delivery_men.last_name,
                ev_tbl_delivery_men.rider_status,
                ev_tbl_delivery_men.current_city_id,
                ev_tbl_city.city_name,
                IFNULL(SUM(TIMESTAMPDIFF(MINUTE, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out)), 0) AS total_minutes,
                CONCAT(
                    FLOOR(IFNULL(SUM(TIMESTAMPDIFF(MINUTE, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out)), 0) / 60), ' hours ', 
                    MOD(IFNULL(SUM(TIMESTAMPDIFF(MINUTE, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out)), 0), 60), ' minutes'
                ) AS total_time
            FROM ev_tbl_delivery_men
            LEFT JOIN ev_delivery_man_logs 
                ON ev_tbl_delivery_men.id = ev_delivery_man_logs.user_id
                {$timeFilterWhere}
            LEFT JOIN ev_tbl_city 
                ON ev_tbl_city.id = ev_tbl_delivery_men.current_city_id
            WHERE ev_tbl_delivery_men.work_type = 'in-house'
            {$cityFilter}
            GROUP BY 
                ev_tbl_delivery_men.id, 
                ev_tbl_delivery_men.first_name, 
                ev_tbl_delivery_men.last_name, 
                ev_tbl_delivery_men.rider_status,
                ev_tbl_delivery_men.current_city_id,
                ev_tbl_city.city_name
            ORDER BY ev_tbl_delivery_men.first_name ASC
        ");
        
        
         
        $cities = City::where('status',1)->get();

        return view('employee::employee_log_reports', compact('dm','cities','city_id','summary_type','from_date','to_date'));
    }
    
     public function single_employee_log(Request $request, $dm_id)
    {
        $dm = Deliveryman::where('id', $dm_id)->first();

        return view('employee::single_employee_log', compact('dm', 'dm_id'));
    }
    
     public function job_status_update(Request $request)
    {
        $dm = Deliveryman::find($request->id);
    
        if (!$dm) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.',
            ]);
        }
    
        $dm->job_status = $request->job_status;
        
    
        if ($request->job_status === 'resigned') {
            $dm->job_status_resigned_remarks = $request->remarks ?? 'No remarks provided';
            $dm->job_status_resigned_at = now();
            $dm->rider_status = 2;
            $dm->job_status_resigned_by = auth()->id(); 
        } else {
            $dm->job_status_resigned_remarks = null;
            $dm->job_status_resigned_at = null;
            $dm->job_status_resigned_by = null;
            $dm->rider_status = 1;
        }
    
        $dm->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Job status updated successfully.',
        ]);
    }


    
    public function export_employee_log_list(Request $request)
    {
        $city_id = $request->input('city_id');
        $summary_type = $request->get('summary_type', 'all');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        return Excel::download(new EmployeeLogExport($city_id,$summary_type,$from_date,$to_date),'Employee_Log_list.xlsx');
    }
}

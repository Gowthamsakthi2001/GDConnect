<?php

namespace Modules\AssetMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; //updated by Mugesh.B
use Illuminate\Support\Facades\Auth;
use App\Mail\QualityCheckMail; //updated by Mugesh.B
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AssetMasterVehicleImport; //updated by Gowtham.s
use App\Imports\QualityCheckBulkImport;//updated by Mugesh.B
use Modules\MasterManagement\Entities\CustomerMaster;
use Modules\City\Entities\City;
use Modules\Zones\Entities\Zones; //updated by Gowtham.s
use Modules\MasterManagement\Entities\EvTblAccountabilityType;

use Modules\AssetMaster\Entities\QualityCheck; 
use Modules\AssetMaster\Entities\QualityCheckReinitiate;
use Modules\VehicleManagement\Entities\VehicleType;//updated by Mugesh.B
use Modules\AssetMaster\Entities\LocationMaster; //updated by Mugesh.B
use Modules\AssetMaster\Entities\AmsLocationMaster; 
use Modules\AssetMaster\Entities\AssetInsuranceDetails;
use Modules\AssetMaster\Entities\AssetMasterBattery;
use Modules\AssetMaster\Entities\AssetMasterCharger;
use Modules\AssetMaster\Entities\ManufacturerMaster;
use Modules\AssetMaster\Entities\ModalMasterVechile;
use Modules\AssetMaster\Entities\ModelMasterBattery;
use Modules\AssetMaster\Entities\ModelMasterCharger;
use Modules\AssetMaster\Entities\AssetStatus;//updated by Gowtham.s
use Modules\AssetMaster\Entities\PoTable;
use Modules\Deliveryman\Entities\Deliveryman;
use App\Mail\RiderRegisterationMail;
use App\Mail\RiderAdminNotificationMail;
use Illuminate\Support\Facades\Mail;
use Modules\AssetMaster\Entities\QualityCheckMaster; //updated by Mugesh.B
use Modules\AssetMaster\Entities\AssetMasterVehicle; //updated by Mugesh.B
use App\Exports\QualityCheckExport;//updated by Mugesh.B
use PhpOffice\PhpSpreadsheet\IOFactory;//updated by Mugesh.B
use App\Exports\QualityCheckBulkExport;//updated by Mugesh.B

//dataTable
use Modules\AssetMaster\DataTables\ModalMasterVechileDataTable;
use Modules\AssetMaster\DataTables\ModalMasterBatteryDataTable;
use Modules\AssetMaster\DataTables\ModalMasterChargerDataTable;
use Modules\AssetMaster\DataTables\ManufactureMasterDataTable;
use Modules\AssetMaster\DataTables\PotableDataTable;
use Modules\AssetMaster\DataTables\AmsLocationMasterDataTable;
use Modules\AssetMaster\DataTables\AssetInsuranceDataTable;
use Modules\AssetMaster\DataTables\AssetMasterBatteryDataTable;
use Modules\AssetMaster\DataTables\AssetMasterChargerDataTable;
use Modules\AssetMaster\DataTables\AssetMasterVechileDataTables;
use Modules\AssetMaster\DataTables\AssetStatusDataTable;
use App\Models\User;

class QualityCheckController extends Controller
{



//   public function quality_check_list(Request $request){
       
//         $query = QualityCheck::query();
        
//         $status = $request->status ?? 'all';
//         $from_date = $request->from_date ?? '';
//         $to_date = $request->to_date ?? '';
//         $timeline = $request->timeline ?? '';
//         $location_data = LocationMaster::where('status', 1)->get();
        
//         $location = $request->location ?? '';
    
//         $query->where('delete_status', 0);
    
        
//         if (in_array($status, ['pass', 'fail' , 'qc_pending'])) {
//             $query->where('status', $status);
//         }
    
//             if (!empty($location)) {
//             $query->where('location', $location);
//         }
        
//           if ($timeline) {
//             switch ($timeline) {
//                 case 'today':
//                     $query->whereDate('created_at', today());
//                     break;
    
//                 case 'this_week':
//                     $query->whereBetween('created_at', [
//                         now()->startOfWeek(), now()->endOfWeek()
//                     ]);
//                     break;
    
//                 case 'this_month':
//                     $query->whereBetween('created_at', [
//                         now()->startOfMonth(), now()->endOfMonth()
//                     ]);
//                     break;
    
//                 case 'this_year':
//                     $query->whereBetween('created_at', [
//                         now()->startOfYear(), now()->endOfYear()
//                     ]);
//                     break;
//             }
    
//             // Overwrite the from_date/to_date to empty for consistency
//             $from_date = null;
//             $to_date = null;
//         } else {
//             // Manual date filtering
//             if ($from_date) {
//                 $query->whereDate('created_at', '>=', $from_date);
//             }
    
//             if ($to_date) {
//                 $query->whereDate('created_at', '<=', $to_date);
//             }
//         }

        
//          $datas = $query->orderBy('id', 'desc')->get();
         
    
         
        
        
//         return view('assetmaster::quality_check.index' , compact('datas' , 'status' ,'from_date', 'to_date' , 'timeline' ,'location_data' ,'location'));
//     }


public function quality_check_list(Request $request)
{

    
    // $chassis_numbers = [
    //  'MD9HAPXF4FR710102',
    //  'MD9HAPXF4FR710108',
    //  'MD9HAPXF4FR710110',
    //  'MD9HAPXF4FR710111',
    //  'MD9HAPXF4FR710114',
    //  'MD9HAPXF4FR710116',
    //  etc..
    //  ];
        
    // $qc_array = QualityCheck::all();
    
    // $exts_array = [];
    // $away_arra = [];
    // foreach ($qc_array as $qc){
    //     if(in_array($qc->chassis_number,$chassis_numbers)){
    //         $exts_array [] = $qc->chassis_number;
    //     }else{
    //         $away_arra[]= $qc->chassis_number;
    //     }
    // }
    // Your array of chassis numbers to check


    $totalRecords=0;
    if ($request->ajax()) {
        try {
            $query = QualityCheck::with([
                'vehicle_type_relation:id,name',
                'vehicle_model_relation:id,vehicle_model',
                'location_relation:id,city_name' ,
                'zone:id,name',
                'accountability_type_relation:id,name',
            ])->where('delete_status', 0);

            // Apply filters
            $status = $request->input('status');
            $location = $request->input('location');
            $zone_id = $request->input('zone');
            $customer_id = $request->input('customer');
            $accountability_type_id = $request->input('accountability_type');
            $timeline = $request->input('timeline');
            $from_date = $request->input('from_date');
            $to_date = $request->input('to_date');
            $search = $request->input('search.value');
            $start = $request->input('start', 0);
            $length = $request->input('length', 15);

            // Status filter
            if (!empty($status) && in_array($status, ['pass', 'fail', 'qc_pending'])) {
                $query->where('status', $status);
            }

            // Location filter
            if (!empty($location)) {
                $query->where('location', (int)$location);
            }
            
            if (!empty($zone_id)) {
                $query->where('zone_id', (int)$zone_id);
            }
            
            // accountability type filter
            if (!empty($accountability_type_id)) {
                $query->where('accountability_type', (int)$accountability_type_id);
            }
            
            // customer  filter
            if (!empty($customer_id)) {
                $query->where('customer_id', $customer_id);
            }

            // Timeline filters
            if (!empty($timeline)) {
                $now = now();
                switch ($timeline) {
                    case 'today':
                        $query->whereDate('created_at', $now->toDateString());
                        break;
                    case 'this_week':
                        $query->whereBetween('created_at', [
                            $now->startOfWeek()->toDateTimeString(),
                            $now->endOfWeek()->toDateTimeString()
                        ]);
                        break;
                    case 'this_month':
                        $query->whereBetween('created_at', [
                            $now->startOfMonth()->toDateTimeString(),
                            $now->endOfMonth()->toDateTimeString()
                        ]);
                        break;
                    case 'this_year':
                        $query->whereBetween('created_at', [
                            $now->startOfYear()->toDateTimeString(),
                            $now->endOfYear()->toDateTimeString()
                        ]);
                        break;
                }
            } elseif (!empty($from_date) || !empty($to_date)) {
                // Date range filter
                if (!empty($from_date)) {
                    $query->where('created_at', '>=', Carbon::parse($from_date)->startOfDay());
                }
                if (!empty($to_date)) {
                    $query->where('created_at', '<=', Carbon::parse($to_date)->endOfDay());
                }
            }

            // Search functionality
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', "%$search%")
                      ->orWhere('chassis_number', 'like', "%$search%")
                      ->orWhere('battery_number', 'like', "%$search%")
                      ->orWhere('telematics_number', 'like', "%$search%")
                      ->orWhere('motor_number', 'like', "%$search%")
                      ->orWhere('status', 'like', "%$search%")
                      ->orWhereHas('vehicle_type_relation', function($q) use ($search) {
                          $q->where('name', 'like', "%$search%");
                      })
                      ->orWhereHas('vehicle_model_relation', function($q) use ($search) {
                          $q->where('vehicle_model', 'like', "%$search%");
                      })
                      ->orWhereHas('location_relation', function($q) use ($search) {
                          $q->where('city_name', 'like', "%$search%");
                      })
                      
                      ->orWhereHas('zone', function($q) use ($search) {
                          $q->where('name', 'like', "%$search%");
                      })
                    ->orWhereHas('accountability_type_relation', function($q) use ($search) {
                          $q->where('name', 'like', "%$search%");
                          $q->where('name', 'like', "%$search%");

                      
                    });
                });
            }

            // Get total records count (before pagination)
            $totalRecords = $query->count();

            // Handle "Show All" option
            if ($length == -1) {
                $length = $totalRecords; // Return all records
            }

            // Apply pagination and ordering
            $data = $query->orderBy('id', 'desc')
                         ->skip($start)
                         ->take($length)
                         ->get();

            // Format the response
            $formattedData = $data->map(function($item) {
                $rawStatus = $item->status ?? null;
                $normalizedStatus = strtolower($rawStatus);
                
                $colorClass = match ($normalizedStatus) {
                    'pass' => 'text-success',
                    'fail' => 'text-danger',
                    'qc_pending', 'nqc_pending', 'pending', null, '' => 'text-warning',
                    default => 'text-warning',
                };
                
                $displayStatus = match ($normalizedStatus) {
                    'qc_pending' => 'QC Pending',
                    'nqc_pending' => 'NQC Pending',
                    'pass' => 'Pass',
                    'fail' => 'Fail',
                    default => ucfirst($normalizedStatus ?: 'Pending'),
                };

                $id_encode = encrypt($item->id);
                
                return [
                    'checkbox' => '<div class="form-check"><input class="form-check-input sr_checkbox" style="width:25px; height:25px;" type="checkbox" value="'.$item->id.'"></div>',
                    'id' => $item->id,
                    'vehicle_type' => $item->vehicle_type_relation->name ?? '-',
                    'vehicle_model' => $item->vehicle_model_relation->vehicle_model ?? '-',
                    'location' => $item->location_relation->city_name ?? '-',
                    'zone' => $item->zone->name ?? '-',
                    'accountability_type' => $item->accountability_type_relation->name ?? '-',
                    'chassis_number' => $item->chassis_number,
                    'battery_number' => $item->battery_number,
                    'telematics_number' => $item->telematics_number,
                    'motor_number' => $item->motor_number,
                    'status' => '<div class="d-flex align-items-center gap-2"><i class="bi bi-circle-fill '.$colorClass.'"></i><span>'.$displayStatus.'</span></div>',
                    'action' => '<div class="dropdown">
                        <button type="button" class="btn btn-sm dropdown-toggle custom-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end text-center p-1">
                            <li>
                                <a href="'.route('admin.asset_management.quality_check.view_quality_check',['id'=>$id_encode]).'" class="dropdown-item d-flex align-items-center justify-content-center">
                                    <i class="bi bi-eye me-2 fs-5"></i> View
                                </a>
                            </li>'.
                            ($item->status != 'pass' ? 
                            '<li>
                                <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center justify-content-center" onclick="DeleteRecord(\''.$item->id.'\')">
                                    <i class="bi bi-trash me-2"></i> Delete
                                </a>
                            </li>' : '').'
                        </ul>
                    </div>'
                ];
            });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $formattedData
            ]);

        } catch (\Exception $e) {
            \Log::error('Quality Check List Error: '.$e->getMessage());
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'An error occurred while processing your request.'
            ], 500);
        }
    }

    // For initial page load (non-AJAX)
      $location_data = City::where('status', 1)->get();
    if ($totalRecords === 0) {
        $totalRecords = QualityCheck::where('delete_status', 0)->count();
    }
    
    $accountablity_types = EvTblAccountabilityType::where('status', 1)->get();
    $customers = CustomerMaster::where('status',1)->get();
    $zones = Zones::where('status', 1)->get();
    
    return view('assetmaster::quality_check.index', [
        'datas' => collect(),
        'status' => $request->status ?? 'all',
        'from_date' => $request->from_date ?? '',
        'to_date' => $request->to_date ?? '',
        'timeline' => $request->timeline ?? '',
       'accountablity_types' => $accountablity_types ,
        'zone_data' => $zones ,
        'customers' => $customers ,
        'location_data' => $location_data,
        'location' => $request->location ?? '',
        'zone_id'  => $request->zone,
        'customer_id' => $request->customer ,
        'accountability_type' => $request->accountability_type,
        'totalRecords'=>$totalRecords ,
    ]);
}
    
       public function add_quality_check(Request $request){
           
         $vehicles = DB::table('ev_tbl_vehicle_models')->where('status',1)->get();
         $vehicle_types = VehicleType::where('is_active', 1)->get();
         $location = LocationMaster::where('status', 1)->get();
         $check_lists = QualityCheckMaster::where('status', 1)->get();
         
         
        $cities = City::where('status',1)->get();
         $types = EvTblAccountabilityType::where('status',1)->get();
         $customers = CustomerMaster::where('status',1)->get();
         

        return view('assetmaster::quality_check.create' , compact('vehicles' , 'vehicle_types' ,'customers' , 'cities'  ,'types' , 'check_lists'));
    }
    
    
    
    
        public function get_qc_checklist(Request $request)
    {
        $vehicle_type_id = $request->vehicle_type_id;
    
        $checklists = QualityCheckMaster::where('status', 1)
            ->where('vehicle_type_id', $vehicle_type_id)
            ->get();
    
        return response()->json($checklists);
    }

     public function view_quality_check(Request $request , $id){
         
        $qc_id = decrypt($id);
        
        $datas = QualityCheck::where('id', $qc_id)->first();
       $initiate_values = QualityCheckReinitiate::where('qc_id', $qc_id)
       ->whereIn('status', ['pass', 'fail' , 'updated'])
        ->orderBy('id', 'desc')
        ->get();
        
        $vehicles = DB::table('ev_tbl_vehicle_models')->where('status',1)->get();
        $vehicle_types = VehicleType::where('is_active', 1)->get();
        $location = LocationMaster::where('status', 1)->get();
        
        $cities = City::where('status',1)->get();
         $types = EvTblAccountabilityType::where('status',1)->get();
         $customers = CustomerMaster::where('status',1)->get();
         
        
        
        return view('assetmaster::quality_check.view' , compact('datas' , 'vehicles' ,'cities' , 'types' ,'customers' , 'initiate_values' , 'vehicle_types' , 'location'));
    }
    
    public function quality_check_bulk_upload(Request $request){
       
        return view('assetmaster::quality_check.bulk_upload_table');
    }
    
       public function quality_check_bulk_upload_form(Request $request){
       
        return view('assetmaster::quality_check.bulk_upload_form');
    }
   
   public function total_qc_list(Request $request){
       
        return view('assetmaster::quality_check.total_qc_list');
    }
    
   
      
   public function qc_list_view(Request $request){
       
        return view('assetmaster::quality_check.qc_list_view');
    }
    
    
       public function export_quality_check(Request $request)
    {
          try{
         $selectedIds = json_decode($request->query('selected_ids', '[]'), true);
         $selectedFields = json_decode($request->query('fields'), true);
         
         
        
        $status = $request->status;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
         $timeline = $request->timeline;
         $location = $request->location;
                           $zone = $request->zone;
         $customer = $request->customer;
         $accountability_type = $request->accountability_type;
         
         $formattedFields = [];

if (is_array($selectedFields)) {
    foreach ($selectedFields as $item) {
        $name = null;

        // case: plain string
        if (is_string($item)) {
            $name = $item;
        }

        // case: associative array like ['name' => 'vehicle_type', 'value' => 'on']
        elseif (is_array($item)) {
            if (isset($item['name']) && is_string($item['name'])) {
                $name = $item['name'];
            } elseif (isset($item['field']) && is_string($item['field'])) {
                $name = $item['field'];
            } else {
                // fallback: take first scalar value from the array
                $first = reset($item);
                if (is_string($first)) {
                    $name = $first;
                } elseif (is_array($first)) {
                    $subFirst = reset($first);
                    if (is_string($subFirst)) $name = $subFirst;
                }
            }
        }

        // skip if we didn't find a usable name
        if (empty($name) || !is_string($name)) {
            continue;
        }

        // format: replace underscores with spaces and title-case
        $clean = str_replace('_', ' ', $name);
        $clean = ucwords(strtolower($clean));

        // optional: map specific keys to nicer labels
        $map = [
            'Qc Checklist' => 'QC Checklist', // 'Qc Checklist' -> 'QC Checklist'
            'Date Time' => 'Date & Time',     // if you prefer
        ];
        if (isset($map[$clean])) {
            $clean = $map[$clean];
        }

        $formattedFields[] = $clean;
    }
}

$fieldsText = empty($formattedFields) ? 'ALL' : implode(', ', $formattedFields);

         $fileName = 'quality_check-' . date('d-m-Y') . '.xlsx';
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    
        // ✅ Log before exporting (optional)
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Quality Check Export Initiated',
            'long_description'  => "User initiated QC export. File: {$fileName}, Selected Fields: {$fieldsText}",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'quality_check.export',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
      
            
        
         return Excel::download(new QualityCheckExport($status , $from_date  , $to_date ,$timeline , $selectedIds , $selectedFields ,$location , $zone , $customer , $accountability_type), $fileName);
          
        }catch(\Exception $e){
            dd($e->getMessage);
        }
    }
    
        public function Quality_Check_Excel_download(Request $request){
        return Excel::download(new QualityCheckBulkExport, 'Qualit_check_import.xlsx');
    }
    
    
        public function quality_check_import_verify(Request $request)
    {
        
        $vehicles = DB::table('ev_tbl_vehicle_models')->where('status',1)->get();
         $vehicle_types = VehicleType::where('is_active', 1)->get();
         $location = LocationMaster::where('status', 1)->get();

        return view('assetmaster::quality_check.quality_check_import_verify' , compact('vehicles' , 'vehicle_types' , 'location'));
    }
    
    
      public function store(Request $request){
       
          $checklists = QualityCheckMaster::where('status', 1)
        ->where('vehicle_type_id', $request->vehicle_type)
        ->get();

            $user     = Auth::user();
    $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

    // ✅ Log: create initiated (log this before validation so attempts are recorded)
    audit_log_after_commit([
        'module_id'         => 4,
        'short_description' => 'Quality Check Create Initiated',
        'long_description'  => 'User started creating a Quality Check record.',
        'role'              => $roleName,
        'user_id'           => Auth::id(),
        'user_type'         => 'gdc_admin_dashboard',
        'dashboard_type'    => 'web',
        'page_name'         => 'quality_check.store',
        'ip_address'        => $request->ip(),
        'user_device'       => $request->userAgent()
    ]);
    // Validation Rules
        $rules = [
            'vehicle_type' => 'required',
            'vehicle_model' => 'required',
            'location' => 'required',
            'accountability_type' => 'required',
            'customer_id' => 'required_if:accountability_type,2',
            'zone_id' => 'required',
            'chassis_number' => 'required|unique:vehicle_qc_check_lists,chassis_number',
            'battery_number' => 'required',
            'telematics_number' => 'required|unique:vehicle_qc_check_lists,telematics_number',
            'motor_number' => 'required',
            'datetime' => 'required',
            'result' => 'required',
            'remarks' => 'max:255',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        ];
        
    
    
        $messages = [
            'location.required' => 'Please select the city.',
            'accountability_type.required' => 'Please select the accountability type.',
            'customer_id.required_if' => 'Please select a customer when accountability type is Fixed.',
            'zone_id.required' => 'Please select the zone.',
        ];
        
        // If Checklist Exists for Vehicle Type -> Require `qc`
        if ($checklists->count() > 0) {
            $rules['qc'] = 'required|array';
        }
    
    
    
        // Custom Messages
         $messages['qc.required'] = 'QC Checklist is required.';
    
        // Validate
        $request->validate($rules, $messages);
        
        
        DB::beginTransaction(); // ✅ Start transaction

       try {
        
        
         $imageName = null; // Declare it here to use later
          if ($request->hasFile('file')) {
                $imageName  = $this->uploadFile($request->file('file'), 'EV/images/quality_check');
            }
    
            $user = Auth::user();
            $lastCode = QualityCheck::orderBy('id', 'desc')->value('id');
        
            if ($lastCode && preg_match('/QC(\d+)/', $lastCode, $matches)) {
                $lastNumber = (int)$matches[1];
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1001; // Start from QC1001
            }
        
            // ✅ Generate new QC code
            $qcCode = 'QC' . $newNumber;
            
            
            $customerId = ($request->accountability_type == 1) ? null : $request->customer_id;
            
        
            QualityCheck::create([
                'id' => $qcCode,
                'vehicle_type' => $request->vehicle_type ?? '',
                'vehicle_model' => $request->vehicle_model,
                'location' => $request->location ?? '',
                'zone_id' =>$request->zone_id ?? '',
                'accountability_type' => $request->accountability_type ?? '',
                'customer_id' => $customerId,
                'chassis_number' => $request->chassis_number ?? '',
                'battery_number' => $request->battery_number ?? '',
                'telematics_number' => $request->telematics_number ?? '',
                'motor_number' => $request->motor_number ?? '',
                'datetime' => $request->datetime ?? '',
                'status' => $request->result ?? '',
                'remarks' => $request->remarks ?? '',
                'is_recoverable' => $request->has('is_recoverable') ? 1 : 0, 
                'technician' => $user->id ?? '' ,
                 'check_lists' => !empty($request->qc) ? json_encode($request->qc) : null,
                 'image' => $imageName, 
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            
            if($request->result == 'pass'){
                
                 $lastCode = DB::table('ev_tbl_asset_master_vehicles')
                ->where('id', 'LIKE', 'VH%')
                ->orderByRaw("CAST(SUBSTRING(id, 3) AS UNSIGNED) DESC")
                ->value('id');
            
            if ($lastCode && preg_match('/VH(\d+)/', $lastCode, $matches)) {
                $lastNumber = (int)$matches[1];
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1001;
            }
            
            $ACode = 'VH' . $newNumber;
            
            
                AssetMasterVehicle::create([
                    'id'=>$ACode ,
                    'qc_id' => $qcCode,
                    'client' => $customerId ,
                    'chassis_number' => $request->chassis_number ,
                    'vehicle_type'=> $request->vehicle_type ?? '' ,
                    'motor_number' => $request->motor_number ?? '' ,
                    'location' => $request->location ?? '',
                    'city_code' => $request->location ?? '',
                    'battery_serial_no'=> $request->battery_number ?? '',
                    'telematics_serial_no' => $request->telematics_number ?? '' ,
                    'model' => $request->vehicle_model ?? '' ,
                    'qc_status' => 'pass',
                    'is_status' => 'pending',
               
            ]);
            
            }


           QualityCheckReinitiate::insert([
                'qc_id'=>$qcCode ?? '' ,
                'status' => $request->result ?? '',
                'remarks' => $request->remarks ?? '',
                'initiated_by' => $user->id ?? '' ,
                'created_at'=>now() ,
                'updated_at' => now()
            ]);


            $qcData = [
                'id' => $qcCode,
                'vehicle_model' => $request->vehicle_model,
                'status' => $request->result,
                'datetime' => $request->datetime,
                'technician_name' => $user->name ?? 'Technician'
            ];


         // Send email
        // Mail::to('')->send(new QualityCheckMail($qcData));
    


            
            // return redirect()->route('admin.asset_management.quality_check.list')->with('success', 'Quality check created successfully.');
            
          DB::commit(); // ✅ Commit transaction
             audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'Quality Check Create Completed',
                'long_description'  => "Quality Check {$qcCode} created successfully.",
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'quality_check.store',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
        
        return response()->json(['success' => true, 'redirect' => route('admin.asset_management.quality_check.list')]);

    } catch (\Exception $e) {
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Quality Check Create Failed',
            'long_description'  => "Failed to create QC " . ($qcCode ?? '(not generated)') . '. Error: ' . substr($e->getMessage(), 0, 1000),
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'quality_check.store',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
        DB::rollback(); // ❌ Rollback transaction
        return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
    }
            
        
        
        
    }
    
    
    
    public function reinitiate(Request $request){
              
              
           $checklists = QualityCheckMaster::where('status', 1)
        ->where('vehicle_type_id', $request->vehicle_type)
        ->get();

        $id = $request->qc_id;
    
        $rules = [
            'vehicle_type' => 'required',
            'vehicle_model' => 'required',
            'location' => 'required',
            'accountability_type' => 'required',
            'customer_id' => 'required_if:accountability_type,2',
            'zone_id' => 'required',
            'chassis_number' => 'required|unique:vehicle_qc_check_lists,chassis_number,' . $id . ',id',
            'battery_number' => 'required',
            'telematics_number' => 'required|unique:vehicle_qc_check_lists,telematics_number,' . $id . ',id',
            'motor_number' => 'required',
            'datetime' => 'required',
            'result' => 'required',
            'remarks' => 'max:255',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        ];
        
        
        
                $messages = [
            'location.required' => 'Please select the city.',
            'accountability_type.required' => 'Please select the accountability type.',
            'customer_id.required_if' => 'Please select a customer when accountability type is Fixed.',
            'zone_id.required' => 'Please select the zone.',
        ];    
        
        
        if ($checklists->count() > 0) {
            $rules['qc'] = 'required|array';
        }
    
       $messages['qc.required'] = 'QC Checklist is required.';
    
        $request->validate($rules, $messages);
    
  
    
        
        
     
  DB::beginTransaction(); // ✅ Start transaction

    try {
        $existing = QualityCheck::findOrFail($id);
        
        $valToString = function ($v) {
            if (is_null($v) || $v === '') return 'N/A';
            if (is_bool($v)) return $v ? '1' : '0';
            if (is_array($v)) return json_encode($v, JSON_UNESCAPED_UNICODE);
            return (string)$v;
        };
            
            
        $imageName = null;


        // Track changed fields
        $changes = [];
        $diffs = [];
        // Compare and track changes
        if ($existing->vehicle_type != $request->vehicle_type) {
            $changes[] = 'Vehicle Type';
            $diffs[] = "Vehicle Type: " . $valToString($existing->vehicle_type) . " → " . $valToString($request->vehicle_type);
        }
        if ($existing->vehicle_model != $request->vehicle_model) {
            $changes[] = 'Vehicle Model';
            $diffs[] = "Vehicle Model: " . $valToString($existing->vehicle_model) . " → " . $valToString($request->vehicle_model);
        }
        if ($existing->location != $request->location) {
            $changes[] = 'City';
            $oldLoc = optional(City::find($existing->location))->city_name ?? $existing->location;
            $newLoc = optional(City::find($request->location))->city_name ?? $request->location;
            $diffs[] = "City: {$oldLoc} → {$newLoc}";
        }
        if ($existing->chassis_number != $request->chassis_number) {
            $changes[] = 'Chassis Number';
            $diffs[] = "Chassis Number: " . $valToString($existing->chassis_number) . " → " . $valToString($request->chassis_number);
        }
        if ($existing->battery_number != $request->battery_number) {
            $changes[] = 'Battery Number';
            $diffs[] = "Battery Number: " . $valToString($existing->battery_number) . " → " . $valToString($request->battery_number);
        }
        if ($existing->telematics_number != $request->telematics_number) {
            $changes[] = 'Telematics Number';
            $diffs[] = "Telematics Number: " . $valToString($existing->telematics_number) . " → " . $valToString($request->telematics_number);
        }
        if ($existing->motor_number != $request->motor_number) {
            $changes[] = 'Motor Number';
            $diffs[] = "Motor Number: " . $valToString($existing->motor_number) . " → " . $valToString($request->motor_number);
        }
        
        if ($existing->accountability_type != $request->accountability_type) {
            $changes[] = 'Accountability Type';
            $oldAcc = optional(EvTblAccountabilityType::find($existing->accountability_type))->name ?? $existing->accountability_type;
            $newAcc= optional(EvTblAccountabilityType::find($request->accountability_type))->name ?? $request->accountability_type;
            $diffs[] = "Accountability Type: {$oldAcc} → {$newAcc}";
        }
        
        if ($existing->customer_id != $request->customer_id) {
            $changes[] = 'Customer';
            $oldCust = optional(CustomerMaster::find($existing->customer_id))->name ?? $existing->customer_id;
            $newCust = optional(CustomerMaster::find($request->customer_id))->name ?? $request->customer_id;
            $diffs[] = "Customer: {$oldCust} → {$newCust}";
        }
        
        if ($existing->zone_id != $request->zone_id) {
            $changes[] = 'Zone';
            $oldZone = optional(Zones::find($existing->zone_id))->name ?? $existing->zone_id;
            $newZone = optional(Zones::find($request->zone_id))->name ?? $request->zone_id;
            $diffs[] = "Zone: " . $valToString($oldZone) . " → " . $valToString($newZone);
        }
        
        // if ($existing->datetime != $request->datetime) {
        //     $changes[] = 'Datetime';
        // }
        if ($existing->status != $request->result) {
            
            $changes[] = 'Status';
            $diffs[] = "Status: " . $valToString($existing->status) . " → " . $valToString($request->result);
        }
        
        if ($existing->is_recoverable != $request->is_recoverable) {
            $changes[] = 'Is Recoverable';
            $oldRec = $existing->is_recoverable ? 'Yes':'No';
            $newRec =$request->is_recoverable ? 'Yes':'No';
             $diffs[] = "Is Recoverable: " . $oldRec . " → " . $newRec;
        }
            
            
        // $oldChecklist = json_decode($existing->check_lists ?? '[]', true);
        // $newChecklist = $request->qc ?? [];


        // $oldDecoded = is_string($oldChecklist) ? json_decode($oldChecklist, true) : $oldChecklist;
        // $newDecoded = is_string($newChecklist) ? json_decode($newChecklist, true) : $newChecklist;
        
        // if ($oldDecoded !== $newDecoded) {
        //     $changes[] = 'Checklist';
        // }
        
        
        // Decode old checklist safely
        // --------------------------
        // Checklist comparison (with label lookups)
        // --------------------------
        
        // Decode old checklist safely (may be JSON string or already array)
        $oldChecklist = json_decode($existing->check_lists ?? '[]', true);
        if (!is_array($oldChecklist)) $oldChecklist = [];
        
        // Ensure new checklist is an array (request->qc may be stringified JSON)
        $newChecklist = $request->qc ?? [];
        if (is_string($newChecklist)) {
            $decoded = json_decode($newChecklist, true);
            $newChecklist = is_array($decoded) ? $decoded : [$newChecklist];
        }
        if (!is_array($newChecklist)) $newChecklist = [];
        
        // Normalize keys to strings (some sources use numeric keys)
        $oldKeys = array_keys($oldChecklist);
        $newKeys = array_keys($newChecklist);
        $allKeys = array_unique(array_merge($oldKeys, $newKeys));
        
        // If there are checklist IDs, load labels from QualityCheckMaster
        $labelMap = [];
        if (!empty($allKeys)) {
            // cast keys to ints if your ids are ints in DB
            $idsToLoad = array_map(function($k){ return (int)$k; }, $allKeys);
            $labelMap = QualityCheckMaster::whereIn('id', $idsToLoad)
                        ->pluck('label_name', 'id')
                        ->toArray();
        }
        
        // Helper to build labeled checklist array: [ "Seatbelt" => "ok", "Headlight" => "ok" ]
        $labeledOld = [];
        foreach ($oldChecklist as $k => $v) {
            $id = (int)$k;
            $label = $labelMap[$id] ?? (string)$k; // fallback to id string
            $labeledOld[$label] = $v;
        }
        
        $labeledNew = [];
        foreach ($newChecklist as $k => $v) {
            $id = (int)$k;
            $label = $labelMap[$id] ?? (string)$k;
            $labeledNew[$label] = $v;
        }
        
        // If labeled arrays differ, add to changes/diffs
        if ($labeledOld !== $labeledNew) {
            $changes[] = 'Checklist';
        
            // Build readable inline representation: "Seatbelt: ok, Headlight: ok"
            $oldParts = [];
            foreach ($labeledOld as $label => $val) {
                $value = ucwords(str_replace('_', ' ', $val));
                $oldParts[] = "{$label}: {$value}";
            }
            $newParts = [];
            foreach ($labeledNew as $label => $val) {
                $value = ucwords(str_replace('_', ' ', $val));
                $newParts[] = "{$label}: {$value}";
            }
        
            $oldText = empty($oldParts) ? '[]' : '[' . implode(', ', $oldParts) . ']';
            $newText = empty($newParts) ? '[]' : '[' . implode(', ', $newParts) . ']';
        
            $diffs[] = "Checklist: {$oldText} → {$newText}";
        }
            
            if ($request->hasFile('file')) {
                
                $existing = QualityCheck::find($id);
                
                if ($existing && $existing->image) {
                    $oldPath = public_path('EV/images/quality_check/' . $existing->image);
                    if (file_exists($oldPath)) {
                        unlink($oldPath); // ❌ Delete previous file
                    }
                }
                
            
            
                $imageName = $this->uploadFile($request->file('file'), 'EV/images/quality_check');
            }
    
            $user = Auth::user();
            
            
            
            $statusMessage = ($existing->status == "qc_pending")
            ? "Quality check updated successfully"
            : "Quality check reinitiated successfully";
            
            
            
            $userRemark = trim($request->remarks ?? '');
            $changeRemark = empty($changes) ? 'No major field updates.' : 'Updated Fields: ' . implode(', ', $changes);
            $detailedDiffText = empty($diffs) ? '' : implode('; ', $diffs);
    
            $finalRemark = $userRemark
                ? "1) $userRemark\n2) $changeRemark"
                : $changeRemark;
                
            $customerId = ($request->accountability_type == 1) ? null : $request->customer_id;

            


           QualityCheckReinitiate::insert([
                'qc_id'=>$request->qc_id ?? '' ,
                'status' => $request->result ?? '',
                'remarks' => $finalRemark ?? '',
                'initiated_by' => $user->id ?? '' ,
                'created_at'=>now() ,
                'updated_at' => now()
            ]);
           
               
        
                $updateData = [
                'vehicle_type'       => $request->vehicle_type ?? '',
                'vehicle_model'      => $request->vehicle_model ?? '',
                'location'           => $request->location ?? '',
                'zone_id'             =>$request->zone_id ?? '',
                'accountability_type' => $request->accountability_type ?? '',
                'customer_id'         => $customerId,
                'chassis_number'     => $request->chassis_number ?? '',
                'battery_number'     => $request->battery_number ?? '',
                'telematics_number'  => $request->telematics_number ?? '',
                'motor_number'       => $request->motor_number ?? '',
                'is_recoverable' => $request->has('is_recoverable') ? 1 : 0, 
                'datetime'           => $request->datetime ?? '',
                'status'             => $request->result ?? '',
                'remarks'            => $request->remarks ?? '',
                'check_lists' => json_encode($request->qc),
                'updated_at'         => now()
            ];
                
                    $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
                    $shortDiff = \Str::limit($detailedDiffText ?: $changeRemark, 800); // truncated
                    $qcAuditData = [
                        'module_id'         => 4,
                        'short_description' => 'QC Reinitiated/Updated',
                        'long_description'  => "QC ({$id}) reinitiated/updated. New status: {$request->result}. Changes: {$shortDiff}",
                        'role'              => $roleName,
                        'user_id'           => Auth::id(),
                        'user_type'         => 'gdc_admin_dashboard',
                        'dashboard_type'    => 'web',
                        'page_name'         => 'quality_check.reinitiate',
                        'ip_address'        => $request->ip(),
                        'user_device'       => $request->userAgent()
                    ];
                    audit_log_after_commit($qcAuditData);
            // Only add image field if a file is uploaded
            if ($imageName) {
                $updateData['image'] = $imageName;
            }

            
            
          
             QualityCheck::where('id', $request->qc_id)->update($updateData);
             



            if($request->result == 'pass'){
                
                 $lastCode = DB::table('ev_tbl_asset_master_vehicles')
                ->where('id', 'LIKE', 'VH%')
                ->orderByRaw("CAST(SUBSTRING(id, 3) AS UNSIGNED) DESC")
                ->value('id');
            
            if ($lastCode && preg_match('/VH(\d+)/', $lastCode, $matches)) {
                $lastNumber = (int)$matches[1];
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1001;
            }
            
            $Acode = 'VH' . $newNumber;
         
              
            AssetMasterVehicle::create([
                'id'=>$Acode ,
                'qc_id' => $id,
                'client' => $customerId ,
                 'chassis_number' => $request->chassis_number ,
                'vehicle_type'=> $request->vehicle_type ?? '' ,
                'motor_number' => $request->motor_number ?? '' ,
                'location' => $request->location ?? '',
                'battery_serial_no'=> $request->battery_number ?? '',
                'telematics_serial_no' => $request->telematics_number ?? '' ,
                'model' => $request->vehicle_model ?? '' ,
                'qc_status' => 'pass',
                'is_status' => 'pending',
               
            ]);
            
            
            // ===== AUDIT: Asset created due to QC pass =====
            $assetAudit = [
                'module_id'         => 4,
                'short_description' => 'Vehicle Asset Created (QC Pass)',
                'long_description'  => "Asset ({$Acode}) created from QC ({$id}),Chassis Number ({$request->chassis_number}) after pass.",
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'quality_check.reinitiate.pass',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ];
            audit_log_after_commit($assetAudit);
            }
            
             

            
            // return redirect()->route('admin.asset_management.quality_check.list')->with('success', 'Quality check reinitiated successfully.');
            
                   DB::commit(); // ✅ Commit transaction

        return response()->json(['success' => true, 'message' => $statusMessage]);

    } catch (\Exception $e) {
        DB::rollback(); // ❌ Rollback transaction
        return response()->json(['error' => 'Something went wrong. Please try again.','error_message'=>$e->getMessage()], 500);
        // return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
    }
            
   
        
        
        
    }
    
    
    
//     public function bulk_upload_data(Request $request){
        
//      $request->validate([
//                 'excel_file' => 'required|file|mimes:xls,xlsx'
//             ]);

//     //  Excel::import(new QualityCheckBulkImport, $request->file('excel_file'));
     
//     //  return back()->with('success', 'Bulk upload successful!');
    
    
//     $file = $request->file('excel_file');
//     $excelPath = $file->getPathname();

//     $saveRoot = public_path('EV/images');
//     if (!file_exists($saveRoot)) mkdir($saveRoot, 0777, true);

//     $spreadsheet = IOFactory::load($excelPath);
//     $sheet = $spreadsheet->getActiveSheet();
//     $highestColumn = $sheet->getHighestColumn();
//     $highestRow = $sheet->getHighestRow();
    
    
        
//     if (($highestRow - 1) < 2) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Excel must contain at least 2 rows of data (excluding the header).'
//         ], 422);
//     }



//         $folder = 'quality_check';
//         $saveFolder = $saveRoot . '/' . $folder;
//         if (!file_exists($saveFolder)) mkdir($saveFolder, 0777, true);
        
        
//     // Map headers A => vehicle_type, B => vehicle_model, ...
//     $headerMap = [];
//     foreach (range('A', $highestColumn) as $col) {
//         $headerMap[$col] = strtolower(str_replace(' ', '_', trim($sheet->getCell($col . '1')->getValue())));
//     }

//     $imageMap = [];
//     $pdfMap = [];
//     $assignedFiles = [];

//     // STEP 1: Extract images
//     foreach ($sheet->getDrawingCollection() as $drawing) {
//         $coords = $drawing->getCoordinates(); // e.g., H2
//         preg_match('/([A-Z]+)(\d+)/', $coords, $matches);
//         $row = $matches[2];



//         if ($drawing instanceof \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing) {
//             ob_start();
//             call_user_func($drawing->getRenderingFunction(), $drawing->getImageResource());
//             $imageContents = ob_get_clean();
//             $ext = 'png';
//         } else {
//             $path = $drawing->getPath();
//             $imageContents = file_get_contents($path);
//             $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
//             if (!in_array($ext, ['jpg', 'jpeg', 'png'])) continue;
//         }

//         // do {
//         //     $imgName = mt_rand(1000000000, 999999999999999) . '.' . $ext;
//         // } while (file_exists($saveFolder . '/' . $imgName));

//         // file_put_contents($saveFolder . '/' . $imgName, $imageContents);
//         // $imageMap[$row] = $imgName;
//         // $assignedFiles[$row] = true;
//                 $imageMap[$row] = [
//             'ext' => $ext,
//             'content' => $imageContents
//         ];
//     }

//     // STEP 2: Extract PDFs from ZIP and map by unassigned rows
//     // $zip = new \ZipArchive();
//     // if ($zip->open($excelPath)) {
//     //     $pdfCandidates = [];
//     //     for ($i = 0; $i < $zip->numFiles; $i++) {
//     //         $entryName = $zip->getNameIndex($i);
//     //         if (str_starts_with($entryName, 'xl/embeddings/')) {
//     //             $stream = $zip->getFromIndex($i);
//     //             $pdfStart = strpos($stream, '%PDF');

//     //             if ($pdfStart !== false) {
//     //                 $pdfData = substr($stream, $pdfStart);
//     //                 $pdfEnd = strpos($pdfData, '%%EOF');
//     //                 if ($pdfEnd !== false) {
//     //                     $pdfData = substr($pdfData, 0, $pdfEnd + 6);
//     //                 }

//     //                 $folder = 'quality_check';
//     //                 $saveFolder = $saveRoot . '/' . $folder;
//     //                 if (!file_exists($saveFolder)) mkdir($saveFolder, 0777, true);

//     //                 do {
//     //                     $pdfName = mt_rand(1000000000, 999999999999999) . '.pdf';
//     //                 } while (file_exists($saveFolder . '/' . $pdfName));

//     //                 file_put_contents($saveFolder . '/' . $pdfName, $pdfData);
//     //                 $pdfCandidates[] = $pdfName;
//     //             }
//     //         }
//     //     }
//     //     $zip->close();

//     //     // Assign each unassigned row in order
//     //     $pdfIndex = 0;
//     //     for ($r = 2; $r <= $highestRow; $r++) {
//     //         if (!isset($assignedFiles[$r]) && isset($pdfCandidates[$pdfIndex])) {
//     //             $pdfMap[$r] = $pdfCandidates[$pdfIndex];
//     //             $assignedFiles[$r] = true;
//     //             $pdfIndex++;
//     //         }
//     //     }
//     // }

//     // STEP 3: Loop and insert row-wise
//     $inserted = [];


    
    

//     foreach ($sheet->getRowIterator(2) as $rowObj) {
//         $rowIndex = $rowObj->getRowIndex();

//         $cellValues = [];
//         foreach ($headerMap as $col => $heading) {
//             $cellValues[$heading] = $sheet->getCell($col . $rowIndex)->getValue() ?? null;
//         }
        
       
        

//         // Access fields individually
//         $vehicleType        = $cellValues['vehicle_type']        ?? null;
//         $vehicleModel       = $cellValues['vehicle_model']       ?? null;
//         $location           = $cellValues['location']            ?? null;
//         $chassisNumber      = $cellValues['chassis_number']      ?? null;
//         $telematicsNumber   = $cellValues['telematics_number']   ?? null;
//         $motor_number   = $cellValues['motor_number']   ?? null;
//         $battery_number   = $cellValues['battery_number']   ?? null;
//         $image = $cellValues['image']   ?? null;
        
//         // if(empty($chassisNumber) || empty($telematicsNumber)){
//         //     continue;
//         // }
        
        
//               $missingFields = [];
//         if (empty($chassisNumber)) $missingFields[] = 'Chassis Number';
//         if (empty($telematicsNumber)) $missingFields[] = 'Telematics Number';
//         if (empty($vehicleType)) $missingFields[] = 'Vehicle Type';
//         if (empty($vehicleModel)) $missingFields[] = 'Vehicle Model';
//         if (empty($location)) $missingFields[] = 'Location';
//         if (empty($battery_number)) $missingFields[] = 'Battery Number';
//         if (empty($motor_number)) $missingFields[] = 'Motor Number';

//         if (count($missingFields) > 0) {
//             $errorRows[] = [
//                 'row' => $rowIndex,
//                 'chassis_number' => $chassisNumber,
//                 'fields' => $missingFields
//             ];
//             continue;
//         }
        
        
//         $vehicle_type_id = null;
//         $vehicleModel_id = null;
//         $location_id = null;
        
//         // Vehicle Type
//         if (!empty($vehicleType)) {
//             $vehicle_type = VehicleType::whereRaw('LOWER(name) = ?', [trim(strtolower($vehicleType))])->first();
//             $vehicle_type_id = $vehicle_type?->id ?? null;
//         }
        
//         // Vehicle Model
//         if (!empty($vehicleModel)) {
//             $vehicleModelRecord = DB::table('ev_tbl_vehicle_models')
//                 ->whereRaw('LOWER(vehicle_model) = ?', [trim(strtolower($vehicleModel))])
//                 ->first();
//             $vehicleModel_id = $vehicleModelRecord?->id ?? null;
//         }
        
//         // Location
//         if (!empty($location)) {
//             $locationRecord = LocationMaster::whereRaw('LOWER(name) = ?', [trim(strtolower($location))])->first();
//             $location_id = $locationRecord?->id ?? null;
//         }





//         if(empty($vehicle_type_id) || empty($vehicleModel_id) || empty($location_id)){
//                         $invalids = [];
//             if (empty($vehicle_type_id)) $invalids[] = 'Vehicle Type (invalid)';
//             if (empty($vehicleModel_id)) $invalids[] = 'Vehicle Model (invalid)';
//             if (empty($location_id)) $invalids[] = 'Location (invalid)';

//             $errorRows[] = [
//                 'row' => $rowIndex,
//                 'chassis_number' => $chassisNumber,
//                 'fields' => $invalids
//             ];
            
//             continue;
//         }
        
        

//         // Skip if no file (image or pdf)
//         // $fileName = $imageMap[$rowIndex] ?? $pdfMap[$rowIndex] ?? null;
//         // if (!$fileName) continue;
        
//         // $fileName = $imageMap[$rowIndex] ?? null;

//         // Generate custom ID
//           $lastCode = QualityCheck::orderBy('id', 'desc')->value('id');
        
//             if ($lastCode && preg_match('/QC(\d+)/', $lastCode, $matches)) {
//                 $lastNumber = (int)$matches[1];
//                 $newNumber = $lastNumber + 1;
//             } else {
//                 $newNumber = 1001; // Start from QC1001
//             }
        
//             // ✅ Generate new QC code
//             $qcId = 'QC' . $newNumber;
            
            
        
                
        
//         $data = [
//             'id'                => $qcId,
//             'vehicle_type'      => $vehicle_type_id,
//             'vehicle_model'     => $vehicleModel_id,
//             'location'          => $location_id ,
//             'chassis_number'    => $chassisNumber,
//             'telematics_number' => $telematicsNumber,
//             'battery_number' => $battery_number,
//             'motor_number' => $motor_number,
//             'image'             => null, // PDF or image
//             'technician'        => Auth::id(),
//              'datetime' => now(),
//             'status'            => 'qc_pending',
//             'created_at'        => now(),
//             'updated_at'        => now(),
//         ];
        
//             $values = [
//             'id'                => $qcId,
//             'vehicle_type'      => $vehicle_type_id,
//             'vehicle_model'     => $vehicleModel_id,
//             'location'          => $location_id ,
//             'chassis_number'    => trim($chassisNumber),
//             'telematics_number' => trim($telematicsNumber),
//             'battery_number' => $battery_number,
//             'motor_number' => $motor_number,
//             'image'             => null, // PDF or image
//             'technician'        => Auth::id(),
//              'datetime' => now(),
//             'status'            => 'qc_pending',
//             'created_at'        => now(),
//             'updated_at'        => now(),
//         ];


//         $validator = Validator::make($values, [
//             'chassis_number'    => 'required|unique:vehicle_qc_check_lists,chassis_number',
//             'telematics_number' => 'required|unique:vehicle_qc_check_lists,telematics_number',
//             'vehicle_type' =>'required' ,
//             'vehicle_model' =>'required' ,
//             'location' =>'required' ,
//             'battery_number' =>'required' ,
//             'motor_number' =>'required' ,
            
//         ]);
       
        

//         // if ($validator->fails()) continue;
//                 if ($validator->fails()) {
//             $errorRows[] = [
//                 'row' => $rowIndex,
//                 'chassis_number' => $chassisNumber,
//                 'fields' => $validator->errors()->all()
//             ];
//             continue;
//         }
        
  
         
        
//          $fileName = null;
         
//         //  dd($imageMap);
         
//         if (!empty($imageMap[$rowIndex])) {
//             $imgData = $imageMap[$rowIndex];
            
//             // do {
//             //     $imgName = mt_rand(1000000000, 999999999999999) . '.' . $imgData['ext'];
//             // } while (file_exists($saveRoot . '/' . $imgName));

//             do {
//                 $imgName = mt_rand(1000000000, 999999999999999) . '.' . $imgData['ext'];
//             } while (file_exists($saveFolder . '/' . $imgName));
    
//             file_put_contents($saveFolder . '/' . $imgName, $imgData['content']);
        
//             // file_put_contents($saveRoot . '/' . $imgName, $imgData['content']);
            
//             $fileName = $imgName;
            
    
//         }
       
        
//         $data['image'] = $fileName;
        
        

//         // ✅ Insert into DB
//          QualityCheck::insert($data);
         
//         $user = Auth::user();


//           QualityCheckReinitiate::insert([
//                 'qc_id'=>$qcId ?? '' ,
//                 'status' =>  'qc_pending',
//                 'remarks' =>  '',
//                 'initiated_by' => $user->id ?? '' ,
//                 'created_at'=>now() ,
//                 'updated_at' => now()
//             ]);
            
    
//         $inserted[] = $data;
//     }


//     $chassisNumbers = array_column($inserted, 'chassis_number');
    
//     return response()->json([
//         'success' => true,
//         'message' => count($chassisNumbers) . ' records inserted successfully.',
//         'inserted_count' => count($chassisNumbers),
//         'chassis_numbers' => array_column($inserted, 'chassis_number'),
//         'error_rows' => $errorRows  // send skipped row details
//     ]);



// //   return redirect()->route('admin.asset_management.quality_check.list')
// //                  ->with('success', 'Bulk upload successful!');

        
//     }
    
    
    public function bulk_upload_data(Request $request){
        
        
    $user     = Auth::user();
    $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    
     $request->validate([
                'excel_file' => 'required|file|mimes:xls,xlsx'
            ]);

    
    
    $file = $request->file('excel_file');
    $excelPath = $file->getPathname();

    $saveRoot = public_path('EV/images');
    if (!file_exists($saveRoot)) mkdir($saveRoot, 0777, true);

    $spreadsheet = IOFactory::load($excelPath);
    $sheet = $spreadsheet->getActiveSheet();
    $highestColumn = $sheet->getHighestColumn();
    $highestRow = $sheet->getHighestRow();
    
    if (($highestRow - 1) < 1) {
         
         audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'QC Bulk Upload Completed',
            'long_description'  => 'Bulk upload ended with validation: no data rows found.',
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'quality_check.bulk_upload',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Excel must contain at least 1 row of data (excluding the header).'
        ], 422);
    }




        $folder = 'quality_check';
        $saveFolder = $saveRoot . '/' . $folder;
        if (!file_exists($saveFolder)) mkdir($saveFolder, 0777, true);
        
        
        $requiredHeaders = [
        'vehicle_type',
        'vehicle_model',
        'city',
        'zone',
        'accountability_type',
        'customer_trade_name',
        'is_recoverable',
        'chassis_number',
        'telematics_number',
        'battery_number',
        'motor_number',
        'image'
        ];

        
    // Map headers A => vehicle_type, B => vehicle_model, ...
    $headerMap = [];
    foreach (range('A', $highestColumn) as $col) {
        $headerMap[$col] = strtolower(str_replace(' ', '_', trim($sheet->getCell($col . '1')->getValue())));
    }
    
    
    $missingHeaders = array_diff($requiredHeaders, $headerMap);
    
    audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'QC Bulk Upload Completed',
            'long_description'  => 'Bulk upload ended with validation: missing headers: ' . implode(', ', $missingHeaders),
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'quality_check.bulk_upload',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
    
    if (!empty($missingHeaders)) {
    return response()->json([
        'success' => false,
        'message' => 'The Excel file is missing the following required columns: ' . implode(', ', $missingHeaders)
    ], 422);
    }


    $imageMap = [];
    $pdfMap = [];
    $assignedFiles = [];

    // STEP 1: Extract images
    foreach ($sheet->getDrawingCollection() as $drawing) {
        $coords = $drawing->getCoordinates(); // e.g., H2
        preg_match('/([A-Z]+)(\d+)/', $coords, $matches);
        $row = $matches[2];



        if ($drawing instanceof \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing) {
            ob_start();
            call_user_func($drawing->getRenderingFunction(), $drawing->getImageResource());
            $imageContents = ob_get_clean();
            $ext = 'png';
        } else {
            $path = $drawing->getPath();
            $imageContents = file_get_contents($path);
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png'])) continue;
        }

                $imageMap[$row] = [
            'ext' => $ext,
            'content' => $imageContents
        ];
    }

    $inserted = [];
    $errorRows = [];

    

    foreach ($sheet->getRowIterator(2) as $rowObj) {
        $rowIndex = $rowObj->getRowIndex();

        $cellValues = [];
        foreach ($headerMap as $col => $heading) {
            $cellValues[$heading] = $sheet->getCell($col . $rowIndex)->getValue() ?? null;
        }
        

        // Access fields individually
        $vehicleType        = $cellValues['vehicle_type']        ?? null;
        $vehicleModel       = $cellValues['vehicle_model']       ?? null;
        $city               = $cellValues['city']            ?? null;
        $zone               = $cellValues['zone']            ?? null;
        $accountability_type = $cellValues['accountability_type'] ?? null;
        $customer_trade_name = $cellValues['customer_trade_name'] ?? null;
        $is_recoverable      = $cellValues['is_recoverable'] ?? null;
        $chassisNumber      = $cellValues['chassis_number']      ?? null;
        $telematicsNumber   = $cellValues['telematics_number']   ?? null;
        $motor_number   = $cellValues['motor_number']   ?? null;
        $battery_number   = $cellValues['battery_number']   ?? null;
        $image = $cellValues['image']   ?? null;
        

        $missingFields = [];
        if (empty($chassisNumber)) $missingFields[] = 'Chassis Number';
        if (empty($telematicsNumber)) $missingFields[] = 'Telematics Number';
        if (empty($vehicleType)) $missingFields[] = 'Vehicle Type';
        if (empty($vehicleModel)) $missingFields[] = 'Vehicle Model';
        if (empty($city)) $missingFields[] = 'Location';
        if (empty($zone)) $missingFields[] = 'Zone';
        if (empty($accountability_type)) $missingFields[] = 'Accountability Type';
        if (empty($customer_trade_name)) $missingFields[] = 'Customer Trade Name';
        if (empty($battery_number)) $missingFields[] = 'Battery Number';
        if (empty($motor_number)) $missingFields[] = 'Motor Number';

        if (count($missingFields) > 0) {
            $errorRows[] = [
                'row' => $rowIndex,
                'chassis_number' => $chassisNumber,
                'fields' => $missingFields
            ];
            continue;
        }
        
        
        $vehicle_type_id = null;
        $vehicleModel_id = null;
        $location_id = null;
        
        // Vehicle Type
        if (!empty($vehicleType)) {
            $vehicle_type = VehicleType::whereRaw('LOWER(name) = ?', [trim(strtolower($vehicleType))])->first();
            $vehicle_type_id = $vehicle_type?->id ?? null;
        }
        
        // Vehicle Model
        if (!empty($vehicleModel)) {
            $vehicleModelRecord = DB::table('ev_tbl_vehicle_models')
                ->whereRaw('LOWER(vehicle_model) = ?', [trim(strtolower($vehicleModel))])
                ->first();
            $vehicleModel_id = $vehicleModelRecord?->id ?? null;
        }
        
        // City
        if (!empty($city)) {
            $cityRecord = City::whereRaw('LOWER(city_name) = ?', [trim(strtolower($city))])->first();
            $city_id = $cityRecord?->id ?? null;
        }
        
        //zone
        if (!empty($zone)) {
            $zoneRecord = Zones::whereRaw('LOWER(name) = ?', [trim(strtolower($zone))])->first();
            $zone_id = $zoneRecord?->id ?? null;
        }


        //recoverable
      if (isset($is_recoverable) && ($is_recoverable == 0 || $is_recoverable == 1)) {
            $recovery_id = $is_recoverable;
        } else {
            $recovery_id = null;
        }
        
        //accounttype

        if (!empty($accountability_type)) {
            $accountabilityRecord = EvTblAccountabilityType::whereRaw('LOWER(name) = ?', [trim(strtolower($accountability_type))])->first();
            $accounttype_id = $accountabilityRecord?->id ?? null;
        }

        //customer trade name
        if (!empty($customer_trade_name)) {
            $customerRecord = CustomerMaster::whereRaw('LOWER(trade_name) = ?', [trim(strtolower($customer_trade_name))])->first();
            $customer_id = $customerRecord?->id ?? null;
        }

        if(empty($vehicle_type_id) || empty($vehicleModel_id) || empty($city_id) || empty($zone_id) || empty($accounttype_id) || empty($recovery_id) || ($accounttype_id == 2 && empty($customer_id)) ){
            $invalids = [];
            if (empty($vehicle_type_id)) $invalids[] = 'Vehicle Type (invalid)';
            if (empty($vehicleModel_id)) $invalids[] = 'Vehicle Model (invalid)';
            if (empty($city)) $invalids[] = 'City (invalid)';
            if (empty($zone)) $invalids[] = 'Zone (invalid)';
            if (empty($accountability_type)) $invalids[] = 'Accountability Type (invalid)';
            if (empty($recovery_id)) $invalids[] = 'Is Recoverable (invalid)';
            
           if ($accounttype_id == 2 && empty($customer_id)) {
                $invalids[] = 'Customer (required for accountability type fixed)';
            }

            $errorRows[] = [
                'row' => $rowIndex,
                'chassis_number' => $chassisNumber,
                'fields' => $invalids
            ];
            
            continue;
        }
        
        

        // Generate custom ID
           $lastCode = QualityCheck::orderBy('id', 'desc')->value('id');
        
            if ($lastCode && preg_match('/QC(\d+)/', $lastCode, $matches)) {
                $lastNumber = (int)$matches[1];
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1001; // Start from QC1001
            }
        
            $qcId = 'QC' . $newNumber;
            
            
        if ($accounttype_id == 1) {
            $customer_id = null;
        }     
        
        $data = [
            'id'                => $qcId,
            'vehicle_type'      => $vehicle_type_id,
            'vehicle_model'     => $vehicleModel_id,
            'location'          => $city_id , //city
            'zone_id'          => $zone_id , //zone
            'accountability_type' => $accounttype_id ,
            'is_recoverable'   =>$recovery_id ?? '',
            'customer_id'      => $customer_id ?? '' ,
            'chassis_number'    => $chassisNumber,
            'telematics_number' => $telematicsNumber,
            'battery_number' => $battery_number,
            'motor_number' => $motor_number,
            'image'             => null, 
            'technician'        => Auth::id(),
            'datetime' => now(),
            'status'            => 'qc_pending',
            'created_at'        => now(),
            'updated_at'        => now(),
        ];
        
        
            $values = [
            'id'                => $qcId,
            'vehicle_type'      => $vehicle_type_id,
            'vehicle_model'     => $vehicleModel_id,
            'city'              => $city_id , // city
            'zone'              => $zone_id , // zone
            'accountability_type' => $accounttype_id ,
            'is_recoverable'    => $recovery_id ,
            'customer'            =>$customer_id ?? '' ,
            'chassis_number'    => trim($chassisNumber),
            'telematics_number' => trim($telematicsNumber),
            'battery_number' => $battery_number,
            'motor_number' => $motor_number,
            'image'             => null, // PDF or image
            'technician'        => Auth::id(),
             'datetime' => now(),
            'status'            => 'qc_pending',
            'created_at'        => now(),
            'updated_at'        => now(),
        ];


        $validator = Validator::make($values, [
            'chassis_number'    => 'required|unique:vehicle_qc_check_lists,chassis_number',
            'telematics_number' => 'required|unique:vehicle_qc_check_lists,telematics_number',
            'vehicle_type' =>'required' ,
            'vehicle_model' =>'required' ,
            'city' =>'required' ,
            'zone' =>'required' ,
            'is_recoverable' => 'required' ,
            'accountability_type' =>'required' ,
            'customer'          => 'required_if:accountability_type,2',
            'battery_number' =>'required' ,
            'motor_number' =>'required' ,
            
        ]);
       
        

        // if ($validator->fails()) continue;
        if ($validator->fails()) {
            $errorRows[] = [
                'row' => $rowIndex,
                'chassis_number' => $chassisNumber,
                'fields' => $validator->errors()->all()
            ];
            continue;
        }
        
  
         
        
         $fileName = null;
         
        //  dd($imageMap);
         
        if (!empty($imageMap[$rowIndex])) {
            $imgData = $imageMap[$rowIndex];
            
            // do {
            //     $imgName = mt_rand(1000000000, 999999999999999) . '.' . $imgData['ext'];
            // } while (file_exists($saveRoot . '/' . $imgName));

            do {
                $imgName = mt_rand(1000000000, 999999999999999) . '.' . $imgData['ext'];
            } while (file_exists($saveFolder . '/' . $imgName));
    
            file_put_contents($saveFolder . '/' . $imgName, $imgData['content']);
        
            // file_put_contents($saveRoot . '/' . $imgName, $imgData['content']);
            
            $fileName = $imgName;
            
    
        }
       
        
        $data['image'] = $fileName;
        
        
         QualityCheck::insert($data);
         
        $user = Auth::user();


           QualityCheckReinitiate::insert([
                'qc_id'=>$qcId ?? '' ,
                'status' =>  'qc_pending',
                'remarks' =>  '',
                'initiated_by' => $user->id ?? '' ,
                'created_at'=>now() ,
                'updated_at' => now()
            ]);
            
    
        $inserted[] = $data;
    }


    $chassisNumbers = array_column($inserted, 'chassis_number');
    
    audit_log_after_commit([
        'module_id'         => 4,
        'short_description' => 'QC Bulk Upload Completed',
        'long_description'  => sprintf(
            'Bulk upload finished. Inserted: %d, Errors: %d.',
            count($chassisNumbers),
            count($errorRows)
        ),
        'role'              => $roleName,
        'user_id'           => Auth::id(),
        'user_type'         => 'gdc_admin_dashboard',
        'dashboard_type'    => 'web',
        'page_name'         => 'quality_check.bulk_upload',
        'ip_address'        => $request->ip(),
        'user_device'       => $request->userAgent()
    ]);
    
    return response()->json([
        'success' => true,
        'message' => count($chassisNumbers) . ' records inserted successfully.',
        'inserted_count' => count($chassisNumbers),
        'chassis_numbers' => array_column($inserted, 'chassis_number'),
        'error_rows' => $errorRows  // send skipped row details
    ]);


    }
    
    public function uploadFile($file, $directory)
    {
        $imageName = Str::uuid(). '.' . $file->getClientOriginalExtension();
        $file->move(public_path($directory), $imageName);
        return $imageName; // Return the name of the uploaded file
    }
    
    public function destroy(Request $request)
    {

        
        $request->validate([
            'id' => 'required|exists:vehicle_qc_check_lists,id',
            'remarks' => 'required|string'
        ]);
    
        $id = $request->id;
        $remarks = $request->remarks;
    
        $qc = QualityCheck::find($id);
    
        if ($qc) {
            // Update the specific QC record
            $qc->delete_status = 1;
            $qc->delete_remarks = $remarks;
            $qc->save();
    
            // Log who deleted it
            $user = Auth::user();
    
            // Insert into reinitiate table
            QualityCheckReinitiate::create([
                'qc_id'        => $id,
                'status'       => 'deleted',
                'remarks'      => $remarks,
                'initiated_by' => $user?->id ?? null,
                'created_at'   => now(),
                'updated_at'   => now()
            ]);
    
            return response()->json(['success' => true, 'message' => 'Record deleted successfully.']);
        }
    
        return response()->json(['success' => false, 'message' => 'Record not found.']);
    }
    
    
    
    public function qc_bulk_pass(){ //only use gowtham.s not for all

            $user = auth()->user();

            DB::transaction(function () use ($user) {
    
            // 1. Get QC pending records
            $qc_pendings = DB::table('vehicle_qc_check_lists')
                ->where('status', 'qc_pending')
                ->where('delete_status', 0)
                ->whereIn('chassis_number',[
                    "P6DEC12CPDJ017138",
                    "P6DEC12CPDJ017135",
                    "P6DEC12CPDJ017136",
                    "P6DEC12CPDJ017132",
                    "P6DEC12CPDJ017134",
                    "P6DEC12CPDJ017129",
                    "P6DEC12CPDJ017127",
                    "P6DEC12CPDJ017124",
                    "P6DEC12CPDJ017122",
                    "P6DEC12CPDJ017121",
                    "P6DEC12CPDJ017130",
                    "P6DEC12CPDJ017131",
                    "P6DEC12CPDJ017125",
                    "P6DEC12CPDJ017119",
                    "P6DEC12CPDJ017123",
                    "P6DEC12CPDJ017128",
                    "P6DEC12CPDJ017120",
                    "P6DEC12CPDJ017118",
                    "P6DEC12CPDJ017115",
                    "P6DEC12CPDJ017126",
                    "P6DEC12CPDJ017113",
                    "P6DEC12CPDJ017117",
                    "P6DEC12CPDJ017112",
                    "P6DEC12CPDJ017114",
                    "P6DEC12CPDJ017110",
                    "P6DEC12CPDJ017116",
                    "P6DEC12CPDJ017111",
                    "P6DEC12CPDJ017108",
                    "P6DEC12CPDJ017109",
                    "P6DEC12CPDJ017107",
                    "P6DEC12CPDJ017100",
                    "P6DEC12CPDJ017105",
                    "P6DEC12CPDJ017099",
                    "P6DEC12CPDJ017103",
                    "P6DEC12CPDJ017097",
                    "P6DEC12CPDJ017095",
                    "P6DEC12CPDJ017102",
                    "P6DSC12SPAJ001812",
                    "P6DSC12SPAJ001811",
                    "P6DSC12SPAJ001808",
                    "P6DSC12SPAJ001810",
                    "P6DSC12SPAJ001806",
                    "P6DSC12SPAJ001807",
                    "P6DSC12SPAJ001809",
                    "P6DSC12SPAJ001805",
                    "P6DSC12SPAJ001804",
                    "P6DSC12SPAJ001803",
                    "P6DSC12SPAJ001802",
                    "P6DSC12SPAJ001800",
                    "P6DSC12SPAJ001799",
                    "P6DSC12SPAJ001801",
                    "P6DSC12SPAJ001795",
                    "P6DSC12SPAJ001798",
                    "P6DSC12SPAJ001791",
                    "P6DSC12SPAJ001793",
                    "P6DSC12SPAJ001797",
                    "P6DSC12SPAJ001790",
                    "P6DSC12SPAJ001782",
                    "P6DSC12SPAJ001792",
                    "P6DSC12SPAJ001796",
                    "P6DSC12SPAJ001788",
                    "P6DSC12SPAJ001780",
                    "P6DSC12SPAJ001789",
                    "P6DSC12SPAJ001794",
                    "P6DSC12SPAJ001785",
                    "P6DSC12SPAJ001779",
                    "P6DSC12SPAJ001787",
                    "P6DSC12SPAJ001781",
                    "P6DSC12SPAJ001783",
                    "P6DSC12SPAJ001776",
                    "P6DSC12SPAJ001786",
                    "P6DSC12SPAJ001778",
                    "P6DSC12SPAJ001777",
                    "P6DSC12SPAJ001775",
                    "P6DSC12SPAJ001784",
                    "P6DSC12SPAJ001772",
                    "P6DSC12SPAJ001771",
                    "P6DSC12SPAJ001773",
                    "P6DSC12SPAJ001774",
                    "P6DSC12SPAJ001768",
                    "P6DSC12SPAJ001769",
                    "P6DSC12SPAJ001767",
                    "P6DSC12SPAJ001770",
                    "P6DSC12SPAJ001765",
                    "P6DSC12SPAJ001764",
                    "P6DSC12SPAJ001766",
                    "P6DSC12SPAJ001763",
                    "P6DSC12SPAH001675",
                    "P6DSC12SPAH001674",
                    "P6DSC12SPAH001677",
                    "P6DSC12SPAH001670",
                    "P6DSC12SPAH001673",
                    "P6DSC12SPAH001668",
                    "P6DSC12SPAH001671",
                    "P6DSC12SPAH001662",
                    "P6DSC12SPAH001666",
                    "P6DSC12SPAH001661",
                    "P6DSC12SPAH001676",
                    "P6DSC12SPAH001664",
                    "P6DSC12SPAH001672",
                    "P6DSC12SPAH001659",
                    "P6DSC12SPAH001667",
                    "P6DSC12SPAH001663",
                    "P6DSC12SPAH001669",
                    "P6DSC12SPAH001658",
                    "P6DSC12SPAH001665",
                    "P6DSC12SPAH001660",
                    "P6DSC12SPAH001540",
                    "P6DSC12SPAH001536",
                    "P6DSC12SPAH001519",
                    "P6DSC12SPAH001532",
                    "P6DSC12SPAH001549",
                    "P6DSC12SPAH001529",
                    "P6DSC12SPAH001551",
                    "P6DSC12SPAH001541",
                    "P6DSC12SPAH001546",
                    "P6DSC12SPAH001559",
                    "P6DSC12SPAH001550",
                    "P6DSC12SPAH001539",
                    "P6DSC12SPAH001544",
                    "P6DSC12SPAH001557",
                    "P6DSC12SPAH001547",
                    "P6DSC12SPAH001561",
                    "P6DSC12SPAH001542",
                    "P6DSC12SPAH001552",
                    "P6DSC12SPAH001537",
                    "P6DSC12SPAH001528",
                    "P6DSC12SPAH001560",
                    "P6DSC12SPAH001548",
                    "P6DSC12SPAH001518",
                    "P6DSC12SPAH001526",
                    "P6DSC12SPAH001573",
                    "P6DSC12SPAH001568",
                    "P6DSC12SPAH001562",
                    "P6DSC12SPAH001590",
                    "P6DSC12SPAH001586",
                    "P6DSC12SPAH001581",
                    "P6DSC12SPAH001579",
                    "P6DSC12SPAH001605",
                    "P6DSC12SPAH001601",
                    "P6DSC12SPAH001604",
                    "P6DSC12SPAH001598",
                    "P6DSC12SPAH001603",
                    "P6DSC12SPAH001567",
                    "P6DSC12SPAH001597",
                    "P6DSC12SPAH001563",
                    "P6DSC12SPAH001600",
                    "P6DSC12SPAH001585",
                    "P6DSC12SPAH001595",
                    "P6DSC12SPAH001580",
                    "P6DSC12SPAH001599",
                    "P6DSC12SPAH001571",
                    "P6DSC12SPAH001572",
                    "P6DSC12SPAH001602",
                    "P6DSC12SPAH001596",
                    "P6DSC12SPAH001606",
                    "P6DSC12SPAH001607",
                    "P6DEC12CPDJ017183",
                    "P6DEC12CPDJ017181",
                    "P6DEC12CPDJ017182",
                    "P6DEC12CPDJ017180",
                    "P6DEC12CPDJ017177",
                    "P6DEC12CPDJ017175",
                    "P6DEC12CPDJ017179",
                    "P6DEC12CPDJ017178",
                    "P6DEC12CPDJ017174",
                    "P6DEC12CPDJ017172",
                    "P6DEC12CPDJ017176",
                    "P6DEC12CPDJ017169",
                    "P6DEC12CPDJ017173",
                    "P6DEC12CPDJ017168",
                    "P6DEC12CPDJ017171",
                    "P6DEC12CPDJ017167",
                    "P6DEC12CPDJ017170",
                    "P6DEC12CPDJ017164",
                    "P6DEC12CPDJ017163",
                    "P6DEC12CPDJ017166",
                    "P6DEC12CPDJ017161",
                    "P6DEC12CPDJ017160",
                    "P6DEC12CPDJ017162",
                    "P6DEC12CPDJ017165",
                    "P6DEC12CPDJ017157",
                    "P6DEC12CPDJ017156",
                    "P6DEC12CPDJ017159",
                    "P6DEC12CPDJ017158",
                    "P6DEC12CPDJ017154",
                    "P6DEC12CPDJ017152",
                    "P6DEC12CPDJ017151",
                    "P6DEC12CPDJ017155",
                    "P6DEC12CPDJ017144",
                    "P6DEC12CPDJ017150",
                    "P6DEC12CPDJ017147",
                    "P6DEC12CPDJ017149",
                    "P6DEC12CPDJ017143",
                    "P6DEC12CPDJ017153",
                    "P6DEC12CPDJ017146",
                    "P6DEC12CPDJ017148",
                    "P6DEC12CPDJ017142",
                    "P6DEC12CPDJ017140",
                    "P6DEC12CPDJ017137",
                    "P6DEC12CPDJ017145",
                    "P6DEC12CPDJ017141",
                    "P6DEC12CPDJ017139",
                    "P6DEC12CPDJ017133",
                    "P6DEC12CPDJ017094",
                    "P6DEC12CPDJ017093",
                    "P6DEC12CPDJ017091",
                    "P6DEC12CPDJ017098",
                    "P6DEC12CPDJ017090",
                    "P6DEC12CPDJ017092",
                    "P6DEC12CPDJ017106",
                    "P6DEC12CPDJ017096",
                    "P6DEC12CPDJ017087",
                    "P6DEC12CPDJ017085",
                    "P6DEC12CPDJ017104",
                    "P6DEC12CPDJ017088",
                    "P6DEC12CPDJ017086",
                    "P6DEC12CPDJ017084",
                    "P6DEC12CPDJ017089"
                ])
                ->get();

            if ($qc_pendings->isEmpty()) {
                return; // nothing to process
            }
        
            $qcArr = [];
            $qcArrLog = [];
        
            // 2. Get last vehicle code only once
            $lastCode = DB::table('ev_tbl_asset_master_vehicles')
                ->where('id', 'LIKE', 'VH%')
                ->orderByRaw("CAST(SUBSTRING(id, 3) AS UNSIGNED) DESC")
                ->value('id');
        
            if ($lastCode && preg_match('/VH(\d+)/', $lastCode, $matches)) {
                $lastNumber = (int) $matches[1];
            } else {
                $lastNumber = 1000; // first new will be VH1001
            }
        
            // Collect all IDs for bulk update later
            $idsToUpdate = [];
        
            // 3. Loop through records and build insert arrays
            foreach ($qc_pendings as $qc) {
                $lastNumber++;
                $ACode = 'VH' . $lastNumber;
        
                $qcArr[] = [
                    'id'                  => $ACode,
                    'qc_id'               => $qc->id,
                    'chassis_number'      => $qc->chassis_number,
                    'vehicle_type'        => $qc->vehicle_type ?? '',
                    'motor_number'        => $qc->motor_number ?? '',
                    'location'            => $qc->location ?? '',
                    'city_code'            => $qc->location ?? '',
                    'battery_serial_no'   => $qc->battery_number ?? '',
                    'telematics_serial_no'=> $qc->telematics_number ?? '',
                    'model'               => $qc->vehicle_model ?? '',
                    'qc_status'           => 'pass',
                    'is_status'           => 'pending',
                ];
        
                $qcArrLog[] = [
                    'qc_id'       => $qc->id,
                    'status'      => 'pass',
                    'remarks'     => "Vehicle QC passed successfully via backend bulk update (as per Maithra TM / GDM instruction).",
                    'initiated_by'=> $user->id ?? 1,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ];
        
                $idsToUpdate[] = $qc->id;
            }
        
        //   dd(count($qcArr),count($qcArrLog),count($qcArrLog));
            // 4. Bulk insert new vehicles
            if (!empty($qcArr)) {
                AssetMasterVehicle::insert($qcArr);
            }
        
            // 5. Bulk insert logs
            if (!empty($qcArrLog)) {
                QualityCheckReinitiate::insert($qcArrLog);
            }
        
            // 6. Bulk update QC status
            if (!empty($idsToUpdate)) {
                DB::table('vehicle_qc_check_lists')
                    ->whereIn('id', $idsToUpdate)
                    ->update([
                        'status'     => 'pass',
                        'updated_at' => now()
                    ]);
            }
        
        });
        
        return response()->json(['status'=>true,'message'=>'data has been updated successfully!']);

    }

    
}

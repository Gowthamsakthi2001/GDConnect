<?php

namespace Modules\B2BAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\City\Entities\City;
use Modules\B2B\Entities\B2BReportAccident;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\B2BAdminAccidentReportExport;
use Modules\MasterManagement\Entities\EvTblAccountabilityType; // updated by logesh
use Modules\MasterManagement\Entities\CustomerMaster; //updated by logesh
use Illuminate\Support\Facades\Auth;
use Modules\Zones\Entities\Zones;

class B2BAccidentController extends Controller
{
    
    
     public function list(Request $request)
{
    if ($request->ajax()) {
        try {
            $start  = $request->input('start', 0);
            $length = $request->input('length', 25);
            $search = $request->input('search.value');

            $query = B2BReportAccident::with([
                        'rider',
                        'assignment',
                        'assignment.vehicle',
                        'assignment.VehicleRequest',
                        'assignment.VehicleRequest.city',
                        'assignment.VehicleRequest.zone',
                        'rider.customerlogin.customer_relation' 
            ]);

            // Filter by status
                    if ($request->filled('status') && $request->status !== 'all') {
                        $query->where('status', $request->status);
                    }
        
                    // Filter by date range
                    if ($request->filled('from_date') && $request->filled('to_date')) {
                        $query->whereDate('created_at', '>=', $request->from_date)
                              ->whereDate('created_at', '<=', $request->to_date);
                    }
        
        
                    if ($request->filled('city_id')) {
                        $query->whereHas('assignment.VehicleRequest.city', function($ct) use ($request) {
                            $ct->where('id', $request->city_id);
                        });
                    }
                    
                    // Filter by zone_id
                    if ($request->filled('zone_id')) {
                        $query->whereHas('assignment.VehicleRequest.zone', function($zn) use ($request) {
                            $zn->where('id', $request->zone_id);
                        });
                    }
                    
                    if ($request->filled('accountability_type')) {
                        $query->whereHas('assignment.VehicleRequest', function($zn) use ($request) {
                            $zn->where('account_ability_type', $request->accountability_type);
                        });
                    }

                    // Search filters
                    if (!empty($search)) {
                        $query->where(function($q) use ($search) {
                            // Status, dates
                            $q->where('status', 'like', "%{$search}%")
                              ->orWhereDate('created_at', $search)
                              ->orWhereDate('updated_at', $search);
                    
                            // Vehicle Request (req_id)
                            $q->orWhereHas('assignment.VehicleRequest', function($vr) use ($search) {
                                $vr->where('req_id', 'like', "%{$search}%");
                            });
                    
                            // Vehicle details
                            $q->orWhereHas('assignment.vehicle', function($v) use ($search) {
                                $v->where('permanent_reg_number', 'like', "%{$search}%")
                                  ->orWhere('chassis_number', 'like', "%{$search}%");
                            });
                    
                            // Rider details
                            $q->orWhereHas('rider', function($r) use ($search) {
                                $r->where('name', 'like', "%{$search}%")
                                  ->orWhere('mobile_no', 'like', "%{$search}%");
                            });
                    
                            // Client details
                            $q->orWhereHas('rider.customerlogin.customer_relation', function($c) use ($search) {
                                $c->where('trade_name', 'like', "%{$search}%");
                            });
                            
                            $q->orWhereHas('assignment.VehicleRequest.city', function($ct) use ($search) {
                                $ct->where('city_name', 'like', "%{$search}%");
                            });
                    
                            // Zone
                            $q->orWhereHas('assignment.VehicleRequest.zone', function($zn) use ($search) {
                                $zn->where('name', 'like', "%{$search}%");
                            });
        
                        });
                    }

            $totalRecords = $query->count();
            if ($length == -1) {
                $length = $totalRecords;
            }

            $datas = $query->orderBy('id', 'desc')
                           ->skip($start)
                           ->take($length)
                           ->get();

            $formattedData = $datas->map(function ($item) {
                // Status Badge UI
                $statuses = [
                        'claim_initiated' => [
                            'label'  => 'Claim Initiated',
                            'colors' => ['#EDCACA', '#580F0F']
                        ],
                        'insurer_visit_confirmed' => [
                            'label'  => 'Insurer Visit Confirmed',
                            'colors' => ['#EDE0CA', '#58490F']
                        ],
                        'inspection_completed' => [
                            'label'  => 'Inspection Completed',
                            'colors' => ['#DEEDCA', '#56580F']
                        ],
                        'approval_pending' => [
                            'label'  => 'Approval Pending',
                            'colors' => ['#CAEDCE', '#1E580F']
                        ],
                        'repair_started' => [
                            'label'  => 'Repair Started',
                            'colors' => ['#CAEDE7', '#0F5847']
                        ],
                        'repair_completed' => [
                            'label'  => 'Repair Completed',
                            'colors' => ['#CAE7ED', '#0F4858']
                        ],
                        'invoice_submitted' => [
                            'label'  => 'Invoice Submitted',
                            'colors' => ['#CAD2ED', '#1A0F58']
                        ],
                        'payment_approved' => [
                            'label'  => 'Payment Approved',
                            'colors' => ['#EDCAE3', '#580F4B']
                        ],
                        'claim_closed' => [
                            'label'  => 'Claim Closed',
                            'colors' => ['#EDE9CA', '#584F0F']
                        ],
                    ];

                $status = $item->status ?? 'N/A';
                
                $label  = $statuses[$status]['label'] ?? ucfirst(str_replace('_', ' ', $status));
                $colors = $statuses[$status]['colors'] ?? ['#ddd', '#333'];
                
                $statusColumn = '<span style="background-color:'.$colors[0].'; color:'.$colors[1].'; border:'.$colors[1].' 1px solid" class="px-2 py-1 rounded-pill">'
                                .e($label).'</span>';

                // Values
                
                
                 if ($item->status === 'claim_closed') {
                    $aging = \Carbon\Carbon::parse($item->created_at)
                                ->diffForHumans(\Carbon\Carbon::parse($item->updated_at), true);
                    } else {
                        $aging = \Carbon\Carbon::parse($item->created_at)
                                    ->diffForHumans(now(), true);
                    }
                       
                        
                        $requestId  = data_get($item, 'assignment.VehicleRequest.req_id', 'N/A');
                        $regNumber  = data_get($item, 'assignment.vehicle.permanent_reg_number', '');
                        $chassis    = data_get($item, 'assignment.vehicle.chassis_number', '');
                        $riderName  = data_get($item, 'rider.name', '');
                        $riderPhone = data_get($item, 'rider.mobile_no', '');
                        $clientName = $item->client_business_name?? 'N/A';
                        $cityName = data_get($item, 'assignment.VehicleRequest.city.city_name', 'N/A');
                        $zoneName = data_get($item, 'assignment.VehicleRequest.zone.name', 'N/A');

                $createdAt = $item->created_at ? $item->created_at->format('d M Y, h:i A') : '';
                $updatedAt = $item->updated_at ? $item->updated_at->format('d M Y, h:i A') : '';
                $idEncode = encrypt($item->id);
                // Actions
                $actions = '
                    <div class="d-flex align-items-center gap-2">
                        <a title="View Ticket Details" href="'.route('b2b.admin.accident_report.view', $idEncode).'"
                           class="d-flex align-items-center justify-content-center border-0"
                           style="background-color:#CAEDCE; color:#155724; border-radius:8px; width:40px; height:40px;">
                           <i class="bi bi-eye fs-5"></i>
                        </a>
                        
                       <button type="button" class="d-flex align-items-center justify-content-center border-0 accident-status-btn"
                                style="background-color:#D2CAED; border-radius:8px; width:40px; height:40px;"
                                data-bs-toggle="modal" data-bs-target="#accidentStatusModal"
                                data-id="'.$item->id.'" data-assign_id="'.$item->assign_id.'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 27 27" fill="none">
                              <rect width="27" height="27" rx="8" fill="#D2CAED"/>
                              <path d="M21.2917 7.54297H11.2083C7.80449 7.54297 5.25 10.0042 5.25 13.5013" stroke="#6C51BF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M5.70834 19.4583H15.7917C19.1955 19.4583 21.75 16.9971 21.75 13.5" stroke="#6C51BF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M19.4583 5.25C19.4583 5.25 21.75 6.93779 21.75 7.54169C21.75 8.14559 19.4583 9.83333 19.4583 9.83333" stroke="#6C51BF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                              <path d="M7.54165 17.168C7.54165 17.168 5.25001 18.8557 5.25 19.4596C5.24999 20.0635 7.54167 21.7513 7.54167 21.7513" stroke="#6C51BF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                ';

                return [
                    '<input class="form-check-input sr_checkbox" style="width:25px; height:25px;" type="checkbox" value="'.$item->id.'">',
                    e($requestId),
                    e($item->assignment->VehicleRequest->accountAbilityRelation->name ?? 'N/A'), //updated by logesh
                    e($regNumber),
                    e($chassis),
                    e($riderName),
                    e($riderPhone),
                    e($clientName),
                    e($cityName),
                    e($zoneName),
                    $createdAt,
                    $updatedAt,
                    $aging,
                    $statusColumn,
                    $actions,
                ];
            });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            \Log::error('Accident List Error: '.$e->getMessage().' on line '.$e->getLine());

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Something went wrong: '.$e->getMessage()
            ], 500);
        }
    }

    $cities = City::where('status', 1)->get();
        $accountability_types = EvTblAccountabilityType::where('status', 1) //updated by logesh
        ->orderBy('id', 'desc')
        ->get();
        
    $customers = CustomerMaster::select('id','trade_name')->where('status', 1) //updated by logesh
        ->orderBy('id', 'desc')
        ->get();
    return view('b2badmin::accident.list', compact('cities','accountability_types','customers'));
}

        
    public function view(Request $request,$id)
    {
        $accident_id = decrypt($id);
       
       $data = B2BReportAccident::with('rider','logs')->where('id', $accident_id)
                ->first();
                
        return view('b2badmin::accident.view',compact('data'));
    }

public function updateStatus(Request $request)
{
    $request->validate([
        'assign_id' =>'nullable',
        'status' => 'required|string',
        'description' => 'nullable|string',
    ]);
    
    $accident = B2BReportAccident::find($request->id);
    $oldStatus = $accident->status;
    $newStatus = $request->status;
    // Save the log
    $accident->logs()->create([
        'assignment_id' =>$request->assign_id,
        'status' => $request->status,
        'remarks' => $request->description,
        'action_by' => auth()->id(),
        'type' => 'b2b-admin-web-dashboard',
        'request_type' => 'accident',
        'request_type_id' => $accident->accident_report_id,
    ]);
    
    $accident->status = $request->status;
    $accident->save();
    
    $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        $assignText = $request->assign_id ? "Assignment ID: {$request->assign_id}. " : '';

        if ((string)$oldStatus !== (string)$newStatus) {
            audit_log_after_commit([
                'module_id'         => 5,
                'short_description' => 'Accident Status Updated',
                'long_description'  => "Accident ({$accident->id}) status changed: {$oldStatus} → {$newStatus}. {$assignText}Remarks: " . ($request->description ?? 'N/A'),
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'b2b_admin_accident.update_status',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
        }
    return response()->json([
        'success' => true,
        'message' => 'Accident status updated successfully'
    ]);
}

public function export(Request $request)
        {

            $fields    = $request->input('fields', []);  
            $from_date = $request->input('from_date');
            $to_date   = $request->input('to_date');
            $zone = $request->input('zone_id')?? null;
            $status = $request->input('status')?? null;
            $city = $request->input('city')?? null;
            $accountability_type = $request->input('accountability_type')?? null;
            $customer_id = $request->input('customer_id')?? null;
            $selectedIds = $request->input('selected_ids', []);
    
        
            if (empty($fields)) {
                return back()->with('error', 'Please select at least one field to export.');
            }
            
            $formattedFields = [];
            if (is_array($fields)) {
                foreach ($fields as $item) {
                    $name = null;
        
                    if (is_string($item) && trim($item) !== '') {
                        $name = $item;
                    } elseif (is_array($item)) {
                        if (!empty($item['name']) && is_string($item['name'])) {
                            $name = $item['name'];
                        } elseif (!empty($item['field']) && is_string($item['field'])) {
                            $name = $item['field'];
                        } else {
                            $first = reset($item);
                            if (is_string($first) && trim($first) !== '') {
                                $name = $first;
                            }
                        }
                    }
        
                    if (empty($name) || !is_string($name)) {
                        continue;
                    }
        
                    $clean = str_replace('_', ' ', $name);
                    $clean = ucwords(strtolower($clean));
        
                    // manual friendly mappings
                    $manual = [
                        'Date Time' => 'Date & Time',
                        'Qc Checklist' => 'QC Checklist',
                        'Id' => 'ID',
                    ];
                    if (isset($manual[$clean])) {
                        $clean = $manual[$clean];
                    }
        
                    $formattedFields[] = $clean;
                }
            }
            $fieldsText = empty($formattedFields) ? 'ALL' : implode(', ', $formattedFields);
        
            // -----------------------
            // Resolve friendly names for zone, city, accountability_type, customer
            // -----------------------
            $zoneName = $zone ? (optional(Zones::find($zone))->name ?? $zone) : null;
            $cityName = $city ? (optional(City::find($city))->city_name ?? $city) : null;
        
            $accountability_name = null;
            if (!is_null($accountability_type) && $accountability_type !== '') {
                $accountability_name = optional(EvTblAccountabilityType::find($accountability_type))->name ?? $accountability_type;
            }
        
            $customerName = null;
            if (!is_null($customer_id) && $customer_id !== '') {
                $customerName = optional(CustomerMaster::find($customer_id))->name ?? $customer_id;
            }
        
            // -----------------------
            // Prepare audit log
            // -----------------------
            $fileName = 'accident-report-list-' . date('d-m-Y') . '.xlsx';
            $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find(optional($user)->role))->name ?? 'Unknown';
        
            $appliedFilters = [];
            if (!is_null($status) && $status !== '') $appliedFilters[] = 'Status: ' . $status;
            if (!is_null($from_date) && $from_date !== '') $appliedFilters[] = 'From: ' . $from_date;
            if (!is_null($to_date) && $to_date !== '') $appliedFilters[] = 'To: ' . $to_date;
            if (!is_null($zoneName) && $zoneName !== '') $appliedFilters[] = 'Zone: ' . $zoneName;
            if (!is_null($cityName) && $cityName !== '') $appliedFilters[] = 'City: ' . $cityName;
            if (!is_null($accountability_name) && $accountability_name !== '') $appliedFilters[] = 'Accountability Type: ' . $accountability_name;
            if (!is_null($customerName) && $customerName !== '') $appliedFilters[] = 'Customer: ' . $customerName;
        
            $filtersText = empty($appliedFilters) ? 'No filters applied' : implode('; ', $appliedFilters);
            $selectedIdsText = empty($selectedIds) ? 'ALL' : implode(', ', array_map('strval', $selectedIds));
        
            $longDesc = "B2B Admin Accident Report export triggered. File: {$fileName}. Selected Fields: {$fieldsText}. Filters: {$filtersText}. Selected IDs: {$selectedIdsText}.";
        
            audit_log_after_commit([
                'module_id'         => 5,
                'short_description' => 'B2B Admin Accident Report Export Initiated',
                'long_description'  => $longDesc,
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'b2b_admin_accident.export',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
    
            return Excel::download(
                new B2BAdminAccidentReportExport($from_date, $to_date, $selectedIds, $fields,$city,$zone,$status,$accountability_type,$customer_id),
                'accident-report-list-' . date('d-m-Y') . '.xlsx'
            );
        }
        

}

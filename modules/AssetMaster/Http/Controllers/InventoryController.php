<?php

namespace Modules\AssetMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AssetMasterVehicleImport; //updated by Gowtham.s
use App\Exports\AssetMasterVehicleExport;
use App\Exports\AssetMasterInventoryExport;//updated by Mugesh.B
use App\Helpers\CustomHandler;
use Illuminate\Support\Facades\DB;

use Modules\MasterManagement\Entities\FinancingTypeMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\AssetOwnershipMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\InsurerNameMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\InsuranceTypeMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\HypothecationMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\RegistrationTypeMaster;//updated by Mugesh.B
use Modules\VehicleManagement\Entities\VehicleType;//updated by Mugesh.B
use Modules\MasterManagement\Entities\TelemetricOEMMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\InventoryLocationMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\ColorMaster;//updated by Mugesh.B
use Modules\AssetMaster\Entities\VehicleModelMaster;//updated by Mugesh.B
use Modules\AssetMaster\Entities\VehicleTransferChassisLog;//updated by Mugesh.B
use Modules\AssetMaster\Entities\LocationMaster;

use Modules\AssetMaster\Entities\AmsLocationMaster; 
use Modules\AssetMaster\Entities\AssetInsuranceDetails;
use Modules\AssetMaster\Entities\AssetMasterBattery;
use Modules\AssetMaster\Entities\AssetMasterCharger;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\AssetMaster\Entities\ManufacturerMaster;
use Modules\AssetMaster\Entities\ModalMasterVechile;
use Modules\AssetMaster\Entities\ModelMasterBattery;
use Modules\AssetMaster\Entities\ModelMasterCharger;
use Modules\AssetMaster\Entities\AssetVehicleInventory;


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
use App\Exports\ArrayExport;

class InventoryController extends Controller
{
    

    // public function inventory_list(Request $request)
    // {
    //     $status = $request->status ?? 'all';
    //     $from_date = $request->from_date ?? '';
    //     $to_date = $request->to_date ?? '';
    //     $timeline = $request->timeline ?? '';
    //     $city = $request->city ?? '';
            
    //     $query = AssetVehicleInventory::with('assetVehicle');
    //     $inventory_locations = InventoryLocationMaster::where('status',1)->get();
    //     $valid_location_ids = $inventory_locations->pluck('id')->toArray();
    //     $locations = LocationMaster::where('status',1)->get();
    
    //     // Filter by status/location
    //     if (in_array($status, $valid_location_ids)) {
    //         $query->where('transfer_status', $status);
    //     }
        
    // // 🔹 Filter by city
    // if (!empty($city)) {
    //     $query->whereHas('assetVehicle', function ($q) use ($city) {
    //         $q->where('city_code', $city);
    //     });
    // }
    
    //     // Timeline filter
    //     if ($timeline) {
    //         switch ($timeline) {
    //             case 'today':
    //                 $query->whereDate('created_at', today());
    //                 break;
    
    //             case 'this_week':
    //                 $query->whereBetween('created_at', [
    //                     now()->startOfWeek(), now()->endOfWeek()
    //                 ]);
    //                 break;
    
    //             case 'this_month':
    //                 $query->whereBetween('created_at', [
    //                     now()->startOfMonth(), now()->endOfMonth()
    //                 ]);
    //                 break;
    
    //             case 'this_year':
    //                 $query->whereBetween('created_at', [
    //                     now()->startOfYear(), now()->endOfYear()
    //                 ]);
    //                 break;
    //         }
    
    //         // Clear manual dates when timeline is used
    //         $from_date = null;
    //         $to_date = null;
    
    //     } else {
    //         // Manual date filtering
    //         if (!empty($from_date)) {
    //             $query->whereDate('created_at', '>=', $from_date);
    //         }
    
    //         if (!empty($to_date)) {
    //             $query->whereDate('created_at', '<=', $to_date);
    //         }
    //     }
    
    //     // Final data query with sorting
    //     $data = $query->orderBy('id', 'desc')->get();
        
    //     return view('assetmaster::inventory.inventory_list' , compact('data' ,'status' , 'from_date' , 'to_date' , 'timeline' ,'inventory_locations' ,'locations' ,'city'));
    // }
    
public function inventory_list(Request $request)
{
    // Handle AJAX requests from DataTables
    if ($request->ajax()) {
        try {
            // Base query with eager loading for displaying data efficiently
            $query = AssetVehicleInventory::with([
                'assetVehicle.quality_check.vehicle_model_relation',
                'assetVehicle.vehicle_type_relation',
                'inventory_location',
                'assetVehicle.location'
            ]);

            // 1. Get total records count (before any filtering)
            $totalRecords = $query->count();

            // 2. Apply custom filters
            $status = $request->input('status');
            $city = $request->input('city');
            $timeline = $request->input('timeline');
            $from_date = $request->input('from_date');
            $to_date = $request->input('to_date');

            if ($status && $status !== 'all') {
                $query->where('transfer_status', $status);
            }

            if (!empty($city)) {
                $query->whereHas('assetVehicle', function ($q) use ($city) {
                    $q->where('city_code', $city);
                });
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
                if (!empty($from_date)) {
                    $query->where('created_at', '>=', Carbon::parse($from_date)->startOfDay());
                }
                if (!empty($to_date)) {
                    $query->where('created_at', '<=', Carbon::parse($to_date)->endOfDay());
                }
            }
            
            // 3. Apply search filter (using whereHas is safe and doesn't require joins)
                       // 3. Apply search filter
            $search = $request->input('search.value');
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    
                    // Search on main table columns: Lot No (id) and Verified at (created_at)
                    $q->where('id', 'like', "%$search%")
                      ->orWhere('created_at', 'like', "%$search%")

                      // Search on related AssetMasterVehicle: Chassis No and Vehicle ID
                      ->orWhereHas('assetVehicle', function($subQ) use ($search) {
                          $subQ->where('chassis_number', 'like', "%$search%")
                               ->orWhere('vehicle_id', 'like', "%$search%");
                      })
                      
                      // Search on nested relationships through QualityCheck: Battery No, Telematics No, and Vehicle Model
                      ->orWhereHas('assetVehicle.quality_check', function($subQ) use ($search) {
                          $subQ->where('battery_number', 'like', "%$search%")
                               ->orWhere('telematics_number', 'like', "%$search%")
                               // Search for the Vehicle Model name in its own related table
                               ->orWhereHas('vehicle_model_relation', function($modelSubQ) use ($search) {
                                   $modelSubQ->where('vehicle_model', 'like', "%$search%");
                               });
                      })

                      // Search on related VehicleType: Vehicle Type name
                      ->orWhereHas('assetVehicle.vehicle_type_relation', function($subQ) use ($search) {
                          $subQ->where('name', 'like', "%$search%");
                      })

                      // Search on related InventoryLocationMaster: Current Status name
                      ->orWhereHas('inventory_location', function($subQ) use ($search) {
                          $subQ->where('name', 'like', "%$search%");
                      });
                });
            }

            // 4. Get filtered records count BEFORE adding specific joins for sorting
            $recordsFiltered = $query->count();

            // 5. Apply ordering and pagination
            $orderColumn = $request->input('columns')[$request->input('order.0.column')]['name'] ?? 'id';
            $orderDir = $request->input('order.0.dir') ?? 'desc';
            $start = $request->input('start', 0);
            $length = $request->input('length', 15);
            
            if ($length == -1) { // Handle "Show All"
                $length = $recordsFiltered;
            }

            // --- FIX: Handle sorting on related columns ---
            $mainTableSortableColumns = ['id', 'created_at']; // Columns on `asset_vehicle_inventories` table
            
            if ($orderColumn === 'assetVehicle.chassis_number' || $orderColumn === 'assetVehicle.vehicle_id') {
                $query->join('asset_master_vehicles', 'asset_vehicle_inventories.vehicle_id', '=', 'asset_master_vehicles.id')
                      ->select('asset_vehicle_inventories.*'); // Important: select only from the main table to avoid ambiguous `id`
                
                $sortColumn = ($orderColumn === 'assetVehicle.chassis_number') ? 'asset_master_vehicles.chassis_number' : 'asset_master_vehicles.vehicle_id';
                $query->orderBy($sortColumn, $orderDir);

            } elseif (in_array($orderColumn, $mainTableSortableColumns)) {
                // Sort on a column from the main table
                $query->orderBy($orderColumn, $orderDir);
            }
            // If the column isn't sortable (e.g., checkbox, action), no specific order is applied, will default to DB order.

            // $data = $query->skip($start)->take($length)->get();
            
            $data = $query->orderBy('id', 'desc')
                         ->skip($start)
                         ->take($length)
                         ->get();

            // 6. Format data for the response
            $formattedData = $data->map(function($item) {
                $id_encode = encrypt($item->id);
                return [
                    'checkbox' => '<div class="form-check"><input class="form-check-input sr_checkbox" style="width:25px; height:25px;" name="is_select[]" type="checkbox" value="'.$item->id.'"></div>',
                    'id' => $item->id,
                    'chassis_no' => $item->assetVehicle->chassis_number ?? '-',
                    'vehicle_type' => $item->assetVehicle->vehicle_type_relation->name ?? '-',
                    'vehicle_model' => $item->assetVehicle->quality_check->vehicle_model_relation->vehicle_model ?? '-',
                    'vehicle_id' => $item->assetVehicle->vehicle_id ?? '-',
                    'battery_no' => $item->assetVehicle->quality_check->battery_number ?? '-',
                    'telematics_no' => $item->assetVehicle->quality_check->telematics_number ?? '-',
                    'verified_at' => $item->created_at ? Carbon::parse($item->created_at)->format('d M Y, h:i A') : '-',
                    'current_status' => $item->inventory_location->name ?? 'N/A',
                    'action' => '<div class="dropdown"><button type="button" class="btn btn-sm dropdown-toggle custom-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots"></i></button><ul class="dropdown-menu dropdown-menu-end text-center p-1"><li><a href="'.route('admin.asset_management.asset_master.inventory.view', ['id'=>$id_encode]).'" class="dropdown-item d-flex align-items-center justify-content-center"><i class="bi bi-eye me-2 fs-5"></i> View</a></li>
                        <li>
                            <a href="'.route('admin.asset_management.asset_master.inventory.edit', ['id'=>$id_encode]).'"  class="dropdown-item d-flex align-items-center justify-content-center">
                                <i class="bi bi-pencil me-2 fs-5"></i> Edit
                            </a>
                        </li>
                    </ul></div>'
                ];
            });
            
            // 7. Send JSON response
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $recordsFiltered,
                'data' => $formattedData
            ]);

        } catch (\Exception $e) {
            \Log::error('Inventory List Error: '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'An error occurred while processing your request. Please check logs for more details.'
            ], 500);
        }
    }

    // For initial page load (non-AJAX), pass filter values for the UI
    $inventory_locations = InventoryLocationMaster::where('status', 1)->get();
    $locations = LocationMaster::where('status', 1)->get();
    $total_count = AssetVehicleInventory::count();
    
    return view('assetmaster::inventory.inventory_list', [
        'status' => $request->status ?? 'all', 
        'from_date' => $request->from_date, 
        'to_date' => $request->to_date, 
        'timeline' => $request->timeline, 
        'inventory_locations' => $inventory_locations, 
        'locations' => $locations, 
        'city' => $request->city,
        'total_count' => $total_count
    ]);
}  
    
    
    public function inventory_view(Request $request , $id)
    {
        $decrypt_id = decrypt($id);
        $data = AssetVehicleInventory::with('assetVehicle')
                ->where('id', $decrypt_id)
                ->first(); // Use first() instead of get()
                
                
      $log_history = VehicleTransferChassisLog::where('vehicle_id', $data->assetVehicle->id)
    ->orderBy('id', 'desc') // or use 'created_at' if preferred
    ->get();

         
         

        $financing_types = FinancingTypeMaster::where('status',1)->get();
        $asset_ownerships = AssetOwnershipMaster::where('status',1)->get();
        $insurer_names = InsurerNameMaster::where('status',1)->get();
        $insurance_types = InsuranceTypeMaster::where('status',1)->get();
        $hypothecations = HypothecationMaster::where('status',1)->get();
        $registration_types = RegistrationTypeMaster::where('status',1)->get();
        $vehicle_types = VehicleType::where('is_active', 1)->get();
        $inventory_locations = InventoryLocationMaster::where('status',1)->get();
        $locations = LocationMaster::where('status',1)->get();
        $passed_chassis_numbers = AssetMasterVehicle::where('qc_status','pass')->get();
        $vehicle_models = VehicleModelMaster::where('status', 1)->get();
        $telematics = TelemetricOEMMaster::where('status',1)->get();
        $colors = ColorMaster::where('status',1)->get();
        
      
        return view('assetmaster::inventory.inventory_view' , compact('data' ,'financing_types' ,'asset_ownerships' ,'insurer_names' ,'insurance_types' , 'hypothecations' ,'registration_types' ,'vehicle_types' ,'locations' ,'vehicle_models' ,'passed_chassis_numbers' ,'inventory_locations' ,'telematics' ,'colors' ,'log_history'));
    }
 
 
     public function export_inventory_detail(Request $request)
    {
        
        // dd($request->all());
        
        $status = $request->status ?? 'all';
        $timeline = $request->timeline ?? '';
        $from_date = $request->from_date ?? '';
        $to_date = $request->to_date ?? '';
        $city = $request->city ?? '';
        
        $get_ids = $request->get('get_ids', []);
        // dd($request->get_export_labels);
        $get_labels = array_filter($request->get('get_export_labels', []), function ($label) {
            return !is_null($label) && trim($label) !== '';
        });
        
        $export = new AssetMasterInventoryExport(
            $request->status,
            $request->from_date,
            $request->to_date,
            $request->timeline,
            $request->get_export_labels ,
            $request->get_ids ,
            $city
        );
        return Excel::download($export, 'Inventory_' . date('d-m-Y') . '.xlsx');

        
        
        
    }



     public function edit(Request $request , $id)
    {
       
        $decrypt_id = decrypt($id);
        $data = AssetVehicleInventory::with('assetVehicle')
                ->where('id', $decrypt_id)
                ->first(); // Use first() instead of get()
                
                
      $log_history = VehicleTransferChassisLog::where('vehicle_id', $data->assetVehicle->id)
    ->orderBy('id', 'desc') // or use 'created_at' if preferred
    ->get();

         
         
         

        $financing_types = FinancingTypeMaster::where('status',1)->get();
        $asset_ownerships = AssetOwnershipMaster::where('status',1)->get();
        $insurer_names = InsurerNameMaster::where('status',1)->get();
        $insurance_types = InsuranceTypeMaster::where('status',1)->get();
        $hypothecations = HypothecationMaster::where('status',1)->get();
        $registration_types = RegistrationTypeMaster::where('status',1)->get();
        $vehicle_types = VehicleType::where('is_active', 1)->get();
        $inventory_locations = InventoryLocationMaster::where('status',1)->get();
        $locations = LocationMaster::where('status',1)->get();
        $passed_chassis_numbers = AssetMasterVehicle::where('qc_status','pass')->get();
        $vehicle_models = VehicleModelMaster::where('status', 1)->get();
        $telematics = TelemetricOEMMaster::where('status',1)->get();
        $colors = ColorMaster::where('status',1)->get();
        
              
        return view('assetmaster::inventory.inventory_edit' , compact('data' ,'financing_types' ,'asset_ownerships' ,'insurer_names' ,'insurance_types' , 'hypothecations' ,'registration_types' ,'vehicle_types' ,'locations' ,'vehicle_models' ,'passed_chassis_numbers' ,'inventory_locations' ,'telematics' ,'colors' ,'log_history'));
       
    }
    

     public function update(Request $request)
    {
              


        $validator = Validator::make($request->all(), [
       'chassis_number' => 'required|string|unique:ev_tbl_asset_master_vehicles,chassis_number,' . $request->id,
        'vehicle_category' => 'nullable|string',
        'vehicle_type' => 'required|numeric',
        'make' => 'required|string',
        'model' => 'required|string',
        'client' => 'nullable|string',
        'variant' => 'required|string',
        'color' => 'required|string',
        'motor_number' => 'required|string',
        'vehicle_id' => 'nullable|string',
        'tax_invoice_number' => 'nullable|string',
        'tax_invoice_date' => 'nullable|date',
        'tax_invoice_value' => 'nullable',
        'location' => 'nullable|numeric',
        'gd_hub_id' => 'nullable|string',
        'financing_type' => 'nullable|string',
        'asset_ownership' => 'nullable|string',
        'lease_start_date' => 'nullable|date',
        'lease_end_date' => 'nullable|date|after_or_equal:lease_start_date',
        'vehicle_delivery_date' => 'nullable|date',
        'emi_lease_amount' => 'nullable',
        'hypothecation' => 'nullable|string',
        'hypothecation_to' => 'nullable|string',
        'insurer_name' => 'nullable|string',
        'insurance_type' => 'nullable|string',
        'insurance_number' => 'nullable|string',
        'insurance_start_date' => 'nullable|date',
        'insurance_expiry_date' => 'nullable|date',
        'registration_type' => 'nullable|string',
        'registration_status' => 'nullable|string',
        'permanent_reg_number' => 'required|string',
        'permanent_reg_date' => 'nullable|date',
        'reg_certificate_expiry_date' => 'nullable|date',
        'fc_expiry_date' => 'nullable|date',
        'battery_type' => 'nullable|string',
        'battery_variant_name' => 'nullable|string',
        'battery_serial_no' => 'nullable|string',
        'charger_variant_name' => 'nullable|string',
        'charger_serial_no' => 'nullable|string',
        'telematics_variant_name' => 'nullable|string',
        'telematics_serial_no' => 'nullable|string',
        'vehicle_status' => 'nullable|string',
        'gd_hub_id_exiting' => 'nullable|string',
        'temporary_registration_number' => 'nullable|string',
        'temporary_registration_date' => 'nullable|date',
        'temporary_registration_expiry_date' => 'nullable|date',
        'servicing_dates' => 'nullable|string',
        'road_tax_applicable' => 'nullable|string',
        'road_tax_amount' => 'nullable|string',
        'road_tax_renewal_frequency' => 'nullable|string',
        'next_renewal_date' => 'nullable|string',
        'battery_serial_no_replacement1' => 'nullable|string',
        'battery_serial_no_replacement2' => 'nullable|string',
        'battery_serial_no_replacement3' => 'nullable|string',
        'battery_serial_no_replacement4' => 'nullable|string',
        'battery_serial_no_replacement5' => 'nullable|string',
        'charger_serial_no_replacement1' => 'nullable|string',
        'charger_serial_no_replacement2' => 'nullable|string',
        'charger_serial_no_replacement3' => 'nullable|string',
        'charger_serial_no_replacement4' => 'nullable|string',
        'charger_serial_no_replacement5' => 'nullable|string',
        'telematics_imei_no' => 'nullable|string',
        'telematics_serial_no_replacement1' => 'nullable|string',
        'telematics_serial_no_replacement2' => 'nullable|string',
        'telematics_serial_no_replacement3' => 'nullable|string',
        'telematics_serial_no_replacement4' => 'nullable|string',
        'telematics_serial_no_replacement5' => 'nullable|string',
        'city_code' => 'nullable|string',
         'telematics_oem' => 'nullable|string',

        // File validations
        'master_lease_agreement' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        'insurance_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        'reg_certificate_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        'fc_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        'hypothecation_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        'temporary_certificate_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        'hsrp_certificate_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors()
        ], 422);
    }
    
        DB::beginTransaction();
        try {
            
            $vehicle_update = AssetMasterVehicle::where('id', $request->id)
                ->where('is_status', 'accepted')
                ->first();
    
    
    
            // Handle file uploads
            // Tax Invoice Attachment
            if ($request->hasFile('tax_invoice_attachment')) {
                $oldFile = $vehicle_update->tax_invoice_attachment;
                $newFile = CustomHandler::uploadFileImage(
                    $request->file('tax_invoice_attachment'),
                    'EV/asset_master/tax_invoice_attachments'
                );
                $vehicle_update->tax_invoice_attachment = $newFile;
            
                if (!empty($oldFile)) {
                    CustomHandler::GlobalFileDelete($oldFile, 'EV/asset_master/tax_invoice_attachments');
                }
            }
            
            // Master Lease Agreement
            if ($request->hasFile('master_lease_agreement')) {
                $oldFile = $vehicle_update->master_lease_agreement;
                $newFile = CustomHandler::uploadFileImage(
                    $request->file('master_lease_agreement'),
                    'EV/asset_master/master_lease_agreements'
                );
                $vehicle_update->master_lease_agreement = $newFile;
            
                if (!empty($oldFile)) {
                    CustomHandler::GlobalFileDelete($oldFile, 'EV/asset_master/master_lease_agreements');
                }
            }
            
            // Insurance Attachment
            if ($request->hasFile('insurance_attachment')) {
                $oldFile = $vehicle_update->insurance_attachment;
                $newFile = CustomHandler::uploadFileImage(
                    $request->file('insurance_attachment'),
                    'EV/asset_master/insurance_attachments'
                );
                $vehicle_update->insurance_attachment = $newFile;
            
                if (!empty($oldFile)) {
                    CustomHandler::GlobalFileDelete($oldFile, 'EV/asset_master/insurance_attachments');
                }
            }
            
            // Registration Certificate Attachment
            if ($request->hasFile('reg_certificate_attachment')) {
                $oldFile = $vehicle_update->reg_certificate_attachment;
                $newFile = CustomHandler::uploadFileImage(
                    $request->file('reg_certificate_attachment'),
                    'EV/asset_master/reg_certificate_attachments'
                );
                $vehicle_update->reg_certificate_attachment = $newFile;
            
                if (!empty($oldFile)) {
                    CustomHandler::GlobalFileDelete($oldFile, 'EV/asset_master/reg_certificate_attachments');
                }
            }
            
            // FC Attachment
            if ($request->hasFile('fc_attachment')) {
                $oldFile = $vehicle_update->fc_attachment;
                $newFile = CustomHandler::uploadFileImage(
                    $request->file('fc_attachment'),
                    'EV/asset_master/fc_attachments'
                );
                $vehicle_update->fc_attachment = $newFile;
            
                if (!empty($oldFile)) {
                    CustomHandler::GlobalFileDelete($oldFile, 'EV/asset_master/fc_attachments');
                }
            }
            
            // Hypothecation Document
            if ($request->hasFile('hypothecation_document')) {
                $oldFile = $vehicle_update->hypothecation_document;
                $newFile = CustomHandler::uploadFileImage(
                    $request->file('hypothecation_document'),
                    'EV/asset_master/hypothecation_documents'
                );
                $vehicle_update->hypothecation_document = $newFile;
            
                if (!empty($oldFile)) {
                    CustomHandler::GlobalFileDelete($oldFile, 'EV/asset_master/hypothecation_documents');
                }
            }
            
            // Temporary Certificate Attachment
            if ($request->hasFile('temporary_certificate_attachment')) {
                $oldFile = $vehicle_update->temproary_reg_attachment;
                $newFile = CustomHandler::uploadFileImage(
                    $request->file('temporary_certificate_attachment'),
                    'EV/asset_master/temporary_certificate_attachments'
                );
                $vehicle_update->temproary_reg_attachment = $newFile;
            
                if (!empty($oldFile)) {
                    CustomHandler::GlobalFileDelete($oldFile, 'EV/asset_master/temporary_certificate_attachments');
                }
            }
            
            // HSRP Certificate Attachment
            if ($request->hasFile('hsrp_certificate_attachment')) {
                $oldFile = $vehicle_update->hsrp_copy_attachment;
                $newFile = CustomHandler::uploadFileImage(
                    $request->file('hsrp_certificate_attachment'),
                    'EV/asset_master/hsrp_certificate_attachments'
                );
                $vehicle_update->hsrp_copy_attachment = $newFile;
            
                if (!empty($oldFile)) {
                    CustomHandler::GlobalFileDelete($oldFile, 'EV/asset_master/hsrp_certificate_attachments');
                }
            }

            
    
            // Update fields
            $vehicle_update->fill([
                'chassis_number' => $vehicle_update->chassis_number,
                'vehicle_category' => $request->vehicle_category,
                'vehicle_type' => $request->vehicle_type,
                'make' => $request->make,
                'model' => $request->model,
                'variant' => $request->variant,
                'color' => $request->color,
                'client' => $request->client,
                'motor_number' => $request->motor_number,
                'vehicle_id' => $request->vehicle_id,
                'tax_invoice_number' => $request->tax_invoice_number,
                'tax_invoice_date' => $request->tax_invoice_date,
                'tax_invoice_value' => floatval(preg_replace('/[^0-9.]/', '', $request->tax_invoice_value)),
                'location' => $request->location,
                'gd_hub_name' => $request->gd_hub_id,
                'financing_type' => $request->financing_type,
                'asset_ownership' => $request->asset_ownership,
                'lease_start_date' => $request->lease_start_date,
                'lease_end_date' => $request->lease_end_date,
                'vehicle_delivery_date' => $request->vehicle_delivery_date,
                'emi_lease_amount' => floatval(preg_replace('/[^0-9.]/', '', $request->emi_lease_amount)),
                'hypothecation' => $request->hypothecation,
                'hypothecation_to' => $request->hypothecation_to,
                'insurer_name' => $request->insurer_name,
                'insurance_type' => $request->insurance_type,
                'insurance_number' => $request->insurance_number,
                'insurance_start_date' => $request->insurance_start_date,
                'insurance_expiry_date' => $request->insurance_expiry_date,
                'registration_type' => $request->registration_type,
                'registration_status' => $request->registration_status,
                'permanent_reg_number' => $request->permanent_reg_number,
                'permanent_reg_date' => $request->permanent_reg_date,
                'reg_certificate_expiry_date' => $request->reg_certificate_expiry_date,
                'fc_expiry_date' => $request->fc_expiry_date,
                'battery_type' => $request->battery_type,
                'battery_variant_name' => $request->battery_variant_name,
                'battery_serial_no' => $request->battery_serial_no,
                'charger_variant_name' => $request->charger_variant_name,
                'charger_serial_no' => $request->charger_serial_no,
                'telematics_variant_name' => $request->telematics_variant_name,
                'telematics_serial_no' => $request->telematics_serial_no,
                'vehicle_status' => $request->vehicle_status,
                
                'gd_hub_id' => $request->gd_hub_id_exiting,
                'temproary_reg_number' => $request->temporary_registration_number,
                'temproary_reg_date' => $request->temporary_registration_date,
                'temproary_reg_expiry_date' => $request->temporary_registration_expiry_date,
                'servicing_dates' => $request->servicing_dates,
                'road_tax_applicable' => $request->road_tax_applicable,
                'road_tax_amount' => $request->road_tax_amount,
                'road_tax_renewal_frequency' => $request->road_tax_renewal_frequency,
                'road_tax_next_renewal_date' => $request->next_renewal_date,
                'battery_serial_number1' => $request->battery_serial_no_replacement1,
                'battery_serial_number2' => $request->battery_serial_no_replacement2,
                'battery_serial_number3' => $request->battery_serial_no_replacement3,
                'battery_serial_number4' => $request->battery_serial_no_replacement4,
                'battery_serial_number5' => $request->battery_serial_no_replacement5,
                'charger_serial_number1' => $request->charger_serial_no_replacement1,
                'charger_serial_number2' => $request->charger_serial_no_replacement2,
                'charger_serial_number3' => $request->charger_serial_no_replacement3,
                'charger_serial_number4' => $request->charger_serial_no_replacement4,
                'charger_serial_number5' => $request->charger_serial_no_replacement5,
                'telematics_oem' => $request->telematics_oem,
                'telematics_imei_number' => $request->telematics_imei_no,
                'telematics_serial_number1' => $request->telematics_serial_no_replacement1,
                'telematics_serial_number2' => $request->telematics_serial_no_replacement2,
                'telematics_serial_number3' => $request->telematics_serial_no_replacement3,
                'telematics_serial_number4' => $request->telematics_serial_no_replacement4,
                'telematics_serial_number5' => $request->telematics_serial_no_replacement5,
                'city_code' => $request->city_code
            ]);
            
    
            $vehicle_update->save();
    
    
            // Generate remarks
            $remarks = "Inventory has been updated successfully.";
    
            // Update Inventory
            AssetVehicleInventory::where('asset_vehicle_id', $request->id)
                ->update(['transfer_status' => $request->vehicle_status]);
    
    
            // Log chassis transfer/update
            VehicleTransferChassisLog::create([
                'chassis_number' => $vehicle_update->chassis_number,
                'vehicle_id' => $request->id,
                'remarks' => $remarks,
                'created_by' => auth()->id(),
                'is_status' => 'updated' ,
                'status' => 'updated',
            ]);
            

    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Inventory Updated Successfully!',
                'data' => $vehicle_update,
            ]);
            
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong! ' . $e->getMessage(),
            ]);
        }

    }
    
}

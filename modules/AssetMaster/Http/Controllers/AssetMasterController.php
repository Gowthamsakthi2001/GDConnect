<?php

namespace Modules\AssetMaster\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; //updated by logesh
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AssetMasterVehicleImport; //updated by Gowtham.s
use App\Exports\AssetMasterVehicleExport;
use App\Exports\AssetVehicleLogHistory;
use Modules\VehicleManagement\Entities\VehicleType;
use Modules\AssetMaster\Entities\VehicleModelMaster; //updated by Mugesh.B
use Modules\City\Entities\City;//updated by Mugesh.B
use Modules\Zones\Entities\Zones; //updated by Mugesh.B
use Modules\MasterManagement\Entities\EvTblAccountabilityType;//updated by Mugesh.B
use App\Helpers\CustomHandler;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

use Modules\AssetMaster\Entities\AmsLocationMaster; 
use Modules\AssetMaster\Entities\AssetInsuranceDetails;
use Modules\AssetMaster\Entities\AssetMasterBattery;
use Modules\AssetMaster\Entities\AssetMasterCharger;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\AssetMaster\Entities\ManufacturerMaster;
use Modules\AssetMaster\Entities\ModalMasterVechile;
use Modules\AssetMaster\Entities\ModelMasterBattery;
use Modules\AssetMaster\Entities\ModelMasterCharger;
use Modules\AssetMaster\Entities\AssetStatus;//updated by Gowtham.s
use Modules\MasterManagement\Entities\FinancingTypeMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\AssetOwnershipMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\InsurerNameMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\InsuranceTypeMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\HypothecationMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\RegistrationTypeMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\TelemetricOEMMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\InventoryLocationMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\ColorMaster;//updated by Mugesh.B
use Modules\AssetMaster\Entities\LocationMaster;
use Modules\AssetMaster\Entities\AssetMasterVehicleLogHistory;
use Modules\AssetMaster\Entities\QualityCheck;
use Modules\AssetMaster\Entities\QualityCheckReinitiate;
use Modules\AssetMaster\Entities\AssetVehicleInventory;
use Modules\AssetMaster\Entities\PoTable;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\MasterManagement\Entities\CustomerMaster;

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
use Illuminate\Support\Facades\Cache;


class AssetMasterController extends Controller
{
    
    public function asset_manage_dashboard(Request $request)
    {
        // dd("Testing By Gowtham");
        // Log::info("Asset Management Dashboard Loading Start".now());
        
        $timeline   = $request->timeline ?? '';
        $from_date  = $request->from_date ?? '';
        $to_date    = $request->to_date ?? '';
        $customer_id = $request->input('customer_id') ?? 'all';
        $accountability_type_id = $request->input('accountability_type_id') ?? 'all';
        $location_id = $request->location_id ?? '';
        $vehicle_type = $request->vehicle_type ?? '';
        $vehicle_model = $request->vehicle_model ?? '';
                

        // dd($from_date,$to_date,$timeline);
        
    //   $assetWiseTable = DB::table('vehicle_qc_check_lists as qc')
    //     ->select(
    //         'bm.brand_name',
    //         'vm.vehicle_model',
    //         'vt.name as vehicle_type_name',   
    //         'qc.vehicle_type',
    //         DB::raw('COUNT(qc.vehicle_type) as vehicle_count'),
    //         DB::raw("SUM(CASE WHEN qc.status = 'pass' AND vh.is_status = 'accepted' THEN 1 ELSE 0 END) as registered_vehicles")
    //     )
    //     ->leftJoin('ev_tbl_asset_master_vehicles as vh', 'qc.id', '=', 'vh.qc_id')
    //     ->leftJoin('asset_vehicle_inventories as inv', 'inv.asset_vehicle_id', '=', 'vh.id')
    //     ->leftJoin('ev_tbl_location_master as lo', 'lo.id', '=', 'qc.location')
    //     ->leftJoin('ev_tbl_vehicle_models as vm', 'qc.vehicle_model', '=', 'vm.id')
    //     ->leftJoin('ev_tbl_brands as bm', 'vm.brand', '=', 'bm.id')
    //     ->leftJoin('vehicle_types as vt', 'qc.vehicle_type', '=', 'vt.id')  
    //     ->where('qc.delete_status', 0)
    //     ->when($location_id != "", function ($query) use ($location_id) {
    //         return $query->where('qc.location', $location_id);
    //     })
    //     ->when($vehicle_model != "", function ($query) use ($vehicle_model) {
    //         return $query->where('qc.vehicle_model', $vehicle_model);
    //     })
    //     ->when($vehicle_type != "", function ($query) use ($vehicle_type) {
    //         return $query->where('qc.vehicle_type', $vehicle_type);
    //     })
    //     ->when($timeline, function ($query) use ($timeline, &$from_date, &$to_date) {
    //         switch ($timeline) {
    //             case 'today':
    //                 $query->whereDate('qc.created_at', today());
    //                 break;
    
    //             case 'this_week':
    //                 $query->whereBetween('qc.created_at', [
    //                     now()->startOfWeek(), now()->endOfWeek()
    //                 ]);
    //                 break;
    
    //             case 'this_month':
    //                 $query->whereBetween('qc.created_at', [
    //                     now()->startOfMonth(), now()->endOfMonth()
    //                 ]);
    //                 break;
    
    //             case 'this_year':
    //                 $query->whereBetween('qc.created_at', [
    //                     now()->startOfYear(), now()->endOfYear()
    //                 ]);
    //                 break;
    //         }
    
    //         // reset manual dates
    //         $from_date = null;
    //         $to_date = null;
    //     })
    //     ->when(!$timeline && $from_date, function ($query) use ($from_date) {
    //         $query->whereDate('qc.created_at', '>=', $from_date);
    //     })
    //     ->when(!$timeline && $to_date, function ($query) use ($to_date) {
    //         $query->whereDate('qc.created_at', '<=', $to_date);
    //     })
    //     ->groupBy('bm.brand_name', 'vm.vehicle_model', 'qc.vehicle_type', 'vt.name')
    //     ->get();

        // Log::info("Asset Management Dashboard Loading middle".now());
        
        // $city_table_data = DB::table('vehicle_qc_check_lists as qc')
        // ->select(
        //     'qc.location',
        //     'lo.name as city',
        //     'qc.vehicle_type',
        //     DB::raw("SUM(CASE WHEN inv.transfer_status = 1 THEN 1 ELSE 0 END) as onroad_count"),
        //     DB::raw("SUM(CASE WHEN inv.transfer_status = 6 THEN 1 ELSE 0 END) as accident_case_count"),
        //     DB::raw("SUM(CASE WHEN inv.transfer_status = 2 THEN 1 ELSE 0 END) as undermaintanance_count"),
        //     DB::raw("SUM(CASE WHEN inv.transfer_status <> 1 OR inv.transfer_status IS NULL THEN 1 ELSE 0 END) as offroad_count")
        // )
        // ->leftJoin('ev_tbl_asset_master_vehicles as vh', 'qc.id', '=', 'vh.qc_id')
        // ->leftJoin('asset_vehicle_inventories as inv', 'inv.asset_vehicle_id', '=', 'vh.id')
        // ->leftJoin('ev_tbl_location_master as lo', 'lo.id', '=', 'qc.location')
        // ->where('vh.delete_status', 0)
        // ->when($location_id != "", function ($query) use ($location_id) {
        //     return $query->where('qc.location', $location_id);
        // })
        // ->when($vehicle_model != "", function ($query) use ($vehicle_model) {
        //     return $query->where('qc.vehicle_model', $vehicle_model);
        // })
        // ->when($vehicle_type != "", function ($query) use ($vehicle_type) {
        //     return $query->where('qc.vehicle_type', $vehicle_type);
        // })
        // ->when($timeline, function ($query) use ($timeline, &$from_date, &$to_date) {
        //     switch ($timeline) {
        //         case 'today':
        //             $query->whereDate('qc.created_at', today());
        //             break;
    
        //         case 'this_week':
        //             $query->whereBetween('qc.created_at', [
        //                 now()->startOfWeek(), now()->endOfWeek()
        //             ]);
        //             break;
    
        //         case 'this_month':
        //             $query->whereBetween('qc.created_at', [
        //                 now()->startOfMonth(), now()->endOfMonth()
        //             ]);
        //             break;
    
        //         case 'this_year':
        //             $query->whereBetween('qc.created_at', [
        //                 now()->startOfYear(), now()->endOfYear()
        //             ]);
        //             break;
        //     }
    
        //     // reset manual dates
        //     $from_date = null;
        //     $to_date = null;
        // })
        // ->when(!$timeline && $from_date, function ($query) use ($from_date) {
        //     $query->whereDate('qc.created_at', '>=', $from_date);
        // })
        // ->when(!$timeline && $to_date, function ($query) use ($to_date) {
        //     $query->whereDate('qc.created_at', '<=', $to_date);
        // })
        // ->groupBy('qc.location', 'lo.name', 'qc.vehicle_type')
        // ->get();

        
        // $onRoad_asset_count = $city_table_data->sum('onroad_count');
        // $offRoad_asset_count = $city_table_data->sum('offroad_count');
        // $undermaintance_asset_count = $city_table_data->sum('undermaintanance_count');
        // $accident_asset_count = $city_table_data->sum('accident_case_count');
        // $total_asset_count = $onRoad_asset_count + $offRoad_asset_count;
        // $onRoad_percentage = $total_asset_count > 0  ? round(($onRoad_asset_count / $total_asset_count) * 100, 2)  : 0;
        // $offRoad_percentage = $total_asset_count > 0 ? round(($offRoad_asset_count / $total_asset_count) * 100, 2) : 0;
        // $undermaintanance_percentage = $total_asset_count > 0 ? round(($undermaintance_asset_count / $total_asset_count) * 100, 2) : 0;
        // $accidentcase_percentage = $total_asset_count > 0 ? round(($accident_asset_count / $total_asset_count) * 100, 2) : 0;
        //  Log::info("Asset Management Dashboard Loading End".now());
        
        return view('assetmaster::asset_management_dashboard', compact( //updated by Mugesh B
            'timeline', 'from_date', 'to_date','vehicle_type','vehicle_model', 'location_id','accountability_type_id','customer_id'
        ));
        
    }
    
    public function get_dashboard_overall_data(Request $request){
        $timeline      = $request->timeline ?? '';
        $from_date     = $request->from_date ?? '';
        $to_date       = $request->to_date ?? '';
        $location_id   = $request->location_id ?? '';
        $vehicle_type  = $request->vehicle_type ?? '';
        $vehicle_model = $request->vehicle_model ?? '';
        $chart_type = $request->chart_type ?? '';
        $vehicle_types = DB::table('vehicle_types')->where('is_active',1)->get();
        $customer_id = $request->customer_id ?? 'all';
        $accountability_type_id = $request->accountability_type_id ?? 'all';
        
        if(!empty($chart_type) && $chart_type == 'SummaryCardcountShow'){
            
             $query = DB::table('vehicle_qc_check_lists as qc')
            ->join('ev_tbl_asset_master_vehicles as vh', 'qc.id', '=', 'vh.qc_id')
            ->leftJoin('asset_vehicle_inventories as inv', 'inv.asset_vehicle_id', '=', 'vh.id')
            ->when($accountability_type_id !== 'all', fn($q) => $q->where('qc.accountability_type', $accountability_type_id))
            ->when($customer_id !== 'all' && $accountability_type_id == 2, fn($q) => $q->where('qc.customer_id', $customer_id))
            ->when($customer_id !== 'all' && $accountability_type_id == 1, fn($q) => $q->where('vh.client', $customer_id))
            ->when($location_id, fn($q) => $q->where('qc.location', $location_id))
            ->when($vehicle_model, fn($q) => $q->where('qc.vehicle_model', $vehicle_model))
            ->when($vehicle_type, fn($q) => $q->where('qc.vehicle_type', $vehicle_type))
             ->when($timeline, function ($query) use ($timeline, &$from_date, &$to_date) {
                switch ($timeline) {
                    case 'today':
                        $query->whereDate('qc.created_at', today());
                        break;
        
                    case 'this_week':
                        $query->whereBetween('qc.created_at', [
                            now()->startOfWeek(), now()->endOfWeek()
                        ]);
                        break;
        
                    case 'this_month':
                        $query->whereBetween('qc.created_at', [
                            now()->startOfMonth(), now()->endOfMonth()
                        ]);
                        break;
        
                    case 'this_year':
                        $query->whereBetween('qc.created_at', [
                            now()->startOfYear(), now()->endOfYear()
                        ]);
                        break;
                }
        
                // reset manual dates
                $from_date = null;
                $to_date = null;
            })
            ->when(!$timeline && $from_date, function ($query) use ($from_date) {
                $query->whereDate('qc.created_at', '>=', $from_date);
            })
            ->when(!$timeline && $to_date, function ($query) use ($to_date) {
                $query->whereDate('qc.created_at', '<=', $to_date);
            })
            ->where('vh.delete_status', 0)
            ->selectRaw("
                SUM(CASE WHEN qc.delete_status = 0 AND qc.status = 'pass' THEN 1 ELSE 0 END) as total_qc_count,
                SUM(CASE WHEN inv.transfer_status = 1 THEN 1 ELSE 0 END) as onRoad,
                SUM(CASE WHEN inv.transfer_status != 1 THEN 1 ELSE 0 END) as offRoad,
                SUM(CASE WHEN inv.transfer_status = 2 THEN 1 ELSE 0 END) as underMaintenance,
                SUM(CASE WHEN inv.transfer_status = 6 THEN 1 ELSE 0 END) as accidentCase
            ");


        $countData =  $query->first();
        $total_qc_count = intval($countData->total_qc_count) ?? 0;
        $onRoad_asset_count = $countData->onRoad ?? 0;
        $offRoad_asset_count = $countData->offRoad ?? 0;
        $undermaintance_asset_count = $countData->underMaintenance ?? 0;
        $accident_asset_count = $countData->accidentCase ?? 0;
        
        $total_asset_count = $onRoad_asset_count + $offRoad_asset_count;
        
        $onRoad_percentage = $total_asset_count > 0 
            ? round(($onRoad_asset_count / $total_asset_count) * 100, 2) 
            : 0;
        
        $offRoad_percentage = $total_asset_count > 0 
            ? round(($offRoad_asset_count / $total_asset_count) * 100, 2) 
            : 0;
        
        $undermaintanance_percentage = $total_asset_count > 0 
            ? round(($undermaintance_asset_count / $total_asset_count) * 100, 2) 
            : 0;
        
        $accidentcase_percentage = $total_asset_count > 0 
            ? round(($accident_asset_count / $total_asset_count) * 100, 2) 
            : 0;
            
            $countData = [
                'total_qc_count'    => $total_qc_count,
                'onRoad_asset_count' => $onRoad_asset_count,
                'offRoad_asset_count' => $offRoad_asset_count,
                'undermaintanance_asset_count' => $undermaintance_asset_count,
                'accident_asset_count' => $accident_asset_count,
                // 'total_asset_count' => $total_asset_count,
                'total_asset_count' => $total_qc_count,
                'onRoad_percentage' => $onRoad_percentage,
                'offRoad_percentage' => $offRoad_percentage,
                'undermaintanance_percentage' => $undermaintanance_percentage,
                'accidentcase_percentage' => $accidentcase_percentage,
                
            ];


            return response()->json([
                'status' => true,
                'count_data' => $countData,
            ]);

            
        }
        
        if(!empty($chart_type) && $chart_type == 'MapChart'){
                $sql_query = DB::table('vehicle_qc_check_lists as qc')
                ->when($accountability_type_id === 'all', function ($query) {
                    $query->leftJoin('ev_tbl_asset_master_vehicles as vh', 'qc.id', '=', 'vh.qc_id')
                          ->leftJoin('asset_vehicle_inventories as inv', 'inv.asset_vehicle_id', '=', 'vh.id');
                }, function ($query) {
                    $query->join('ev_tbl_asset_master_vehicles as vh', 'qc.id', '=', 'vh.qc_id')
                          ->join('asset_vehicle_inventories as inv', 'inv.asset_vehicle_id', '=', 'vh.id');
                })
                ->Join('ev_tbl_city as lo', 'lo.id', '=', 'qc.location')
                ->where('qc.delete_status', 0)
                ->distinct('vh.chassis_number')
                ->when($location_id != "", fn($q) => $q->where('qc.location', $location_id))
                ->when($vehicle_model != "", fn($q) => $q->where('qc.vehicle_model', $vehicle_model))
                ->when($vehicle_type != "", fn($q) => $q->where('qc.vehicle_type', $vehicle_type))
                ->when($timeline, function ($query) use ($timeline, &$from_date, &$to_date) {
                switch ($timeline) {
                    case 'today':
                        $query->whereDate('qc.created_at', today());
                        break;
        
                    case 'this_week':
                        $query->whereBetween('qc.created_at', [
                            now()->startOfWeek(), now()->endOfWeek()
                        ]);
                        break;
        
                    case 'this_month':
                        $query->whereBetween('qc.created_at', [
                            now()->startOfMonth(), now()->endOfMonth()
                        ]);
                        break;
        
                    case 'this_year':
                        $query->whereBetween('qc.created_at', [
                            now()->startOfYear(), now()->endOfYear()
                        ]);
                        break;
                }
        
                // reset manual dates
                $from_date = null;
                $to_date = null;
            })
                ->when(!$timeline && $from_date, function ($query) use ($from_date) {
                    $query->whereDate('qc.created_at', '>=', $from_date);
                })
                ->when(!$timeline && $to_date, function ($query) use ($to_date) {
                    $query->whereDate('qc.created_at', '<=', $to_date);
                })
                ->when($accountability_type_id !== 'all', fn($q) => $q->where('qc.accountability_type', $accountability_type_id))
                ->when($customer_id !== 'all' && $accountability_type_id == 2, fn($q) => $q->where('qc.customer_id', $customer_id))
                ->when($customer_id !== 'all' && $accountability_type_id == 1, fn($q) => $q->where('vh.client', $customer_id))
                ->groupBy('qc.location', 'lo.city_name')
                ->selectRaw("
                    qc.location as location_id,
                    lo.city_name as location_name,
                    COUNT(qc.id) AS total_assets,
                    SUM(CASE WHEN inv.transfer_status = 1 THEN 1 ELSE 0 END) AS active_assets,
                    SUM(CASE WHEN inv.transfer_status != 1 THEN 1 ELSE 0 END) AS idle_assets
                ");
                
              $clientWiseTable = $sql_query->get();
        
            $MapchartData = [];
        
            $Total_values = 0;
            foreach ($clientWiseTable as $row) {
                $apiKey = BusinessSetting::where('key_name', 'google_map_api_key')->value('value');
                $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($row->location_name) . "&components=country:IN&key=" . $apiKey;
        
                $response = file_get_contents($url);
                $data = json_decode($response, true);
        
                $lat = $lng = null;
                if (!empty($data['results'][0]['geometry']['location'])) {
                    $lat = $data['results'][0]['geometry']['location']['lat'];
                    $lng = $data['results'][0]['geometry']['location']['lng'];
                }
                $Total_values += (int)$row->total_assets;
                $MapchartData[] = [
                    'location_id'=>$row->location_id,
                    'name'     => $row->location_name,
                    'value'    => (int)$row->total_assets,
                    'coords'   => [$lng, $lat],
                ];
            }
        
            return response()->json(['status'=>true,'map_data'=>$MapchartData,'total_vehicle_count'=>$Total_values,'sql_query'=>$sql_query->toSql(),'bindings'=>$sql_query->getBindings()]);
        }
        
       if(!empty($chart_type) && $chart_type == 'VehicleStatusSummaryChart' || $chart_type == 'OEMChart'){
    
            if($chart_type == 'VehicleStatusSummaryChart'){
  
              $vehicleData = DB::table('vehicle_qc_check_lists as qc')
                ->select(
                    'qc.vehicle_type',
                    'vt.name as vehicle_type_name',
                    DB::raw('COUNT(DISTINCT qc.id) as total_count'),
                    DB::raw("SUM(CASE WHEN inv.transfer_status = 1 THEN 1 ELSE 0 END) as onroad_count"),
                    DB::raw("SUM(CASE WHEN inv.transfer_status != 1 THEN 1 ELSE 0 END) as offroad_count")
                )
                 ->when($accountability_type_id === 'all', function ($query) {
                    $query->leftJoin('ev_tbl_asset_master_vehicles as vh', 'qc.id', '=', 'vh.qc_id')
                          ->leftJoin('asset_vehicle_inventories as inv', 'inv.asset_vehicle_id', '=', 'vh.id');
                }, function ($query) {
                    $query->join('ev_tbl_asset_master_vehicles as vh', 'qc.id', '=', 'vh.qc_id')
                          ->join('asset_vehicle_inventories as inv', 'inv.asset_vehicle_id', '=', 'vh.id');
                })
                ->leftJoin('vehicle_types as vt', 'qc.vehicle_type', '=', 'vt.id')
                ->where('qc.delete_status', 0)
                ->when($location_id != "", function ($query) use ($location_id) {
                    return $query->where('qc.location', $location_id);
                })
                ->when($vehicle_model != "", function ($query) use ($vehicle_model) {
                    return $query->where('qc.vehicle_model', $vehicle_model);
                })
                ->when($vehicle_type != "", function ($query) use ($vehicle_type) {
                    return $query->where('qc.vehicle_type', $vehicle_type);
                })
                ->when($timeline, function ($query) use ($timeline, &$from_date, &$to_date) {
                    switch ($timeline) {
                        case 'today':
                            $query->whereDate('qc.created_at', today());
                            break;
            
                        case 'this_week':
                            $query->whereBetween('qc.created_at', [
                                now()->startOfWeek(), now()->endOfWeek()
                            ]);
                            break;
            
                        case 'this_month':
                            $query->whereBetween('qc.created_at', [
                                now()->startOfMonth(), now()->endOfMonth()
                            ]);
                            break;
            
                        case 'this_year':
                            $query->whereBetween('qc.created_at', [
                                now()->startOfYear(), now()->endOfYear()
                            ]);
                            break;
                    }
            
                    $from_date = null;
                    $to_date = null;
                })
                ->when(!$timeline && $from_date, function ($query) use ($from_date) {
                    $query->whereDate('qc.created_at', '>=', $from_date);
                })
                ->when(!$timeline && $to_date, function ($query) use ($to_date) {
                    $query->whereDate('qc.created_at', '<=', $to_date);
                })
                ->when($accountability_type_id !== 'all', fn($q) => $q->where('qc.accountability_type', $accountability_type_id))
                ->when($customer_id !== 'all' && $accountability_type_id == 2, fn($q) => $q->where('qc.customer_id', $customer_id))
                ->when($customer_id !== 'all' && $accountability_type_id == 1, fn($q) => $q->where('vh.client', $customer_id))
                ->groupBy('qc.vehicle_type','vt.name')
                ->get();
                
                $summary = [
                    'total_assets' => 0,
                    'total_onroad' => 0,
                    'total_offroad' => 0,
                    'types' => []
                ];
                $total_vh_count = 0;
                foreach ($vehicleData as $row) {
                    $type = $row->vehicle_type ?? 'Unknown';
                
                    $summary['types'][$type] = [
                        'vehicle_type_name'=>$row->vehicle_type_name,
                        'total' => $row->total_count,
                        'onroad' => $row->onroad_count,
                        'offroad' => $row->offroad_count,
                        'utilization' => $row->total_count > 0
                            ? round(($row->onroad_count / $row->total_count) * 100, 2)
                            : 0
                    ];
                
                    $summary['total_assets'] += $row->total_count;
                    $summary['total_onroad'] += $row->onroad_count;
                    $summary['total_offroad'] += $row->offroad_count;
                    $total_vh_count += $row->total_count;
                }
                

                $summary['utilization'] = $summary['total_assets'] > 0
                    ? round(($summary['total_onroad'] / $summary['total_assets']) * 100, 2)
                    : 0;

                return response()->json(['status'=>true,'vehicle_summary'=>$summary,'total_vh_count'=>$total_vh_count]);
            }else{
                
                $assetWiseTable = DB::table('vehicle_qc_check_lists as qc')
                ->select(
                    'bm.brand_name',
                    'vm.vehicle_model',
                    'vt.name as vehicle_type_name',   
                    'qc.vehicle_type',
                    DB::raw('COUNT(qc.vehicle_type) as vehicle_count'),
                    DB::raw("SUM(CASE WHEN qc.status = 'pass' AND vh.is_status = 'accepted' THEN 1 ELSE 0 END) as registered_vehicles")
                )
                ->when($accountability_type_id === 'all', function ($query) {
                    $query->leftJoin('ev_tbl_asset_master_vehicles as vh', 'qc.id', '=', 'vh.qc_id')
                          ->leftJoin('asset_vehicle_inventories as inv', 'inv.asset_vehicle_id', '=', 'vh.id');
                }, function ($query) {
                    $query->join('ev_tbl_asset_master_vehicles as vh', 'qc.id', '=', 'vh.qc_id')
                          ->join('asset_vehicle_inventories as inv', 'inv.asset_vehicle_id', '=', 'vh.id');
                })
                ->leftJoin('ev_tbl_city as lo', 'lo.id', '=', 'qc.location')
                ->leftJoin('ev_tbl_vehicle_models as vm', 'qc.vehicle_model', '=', 'vm.id')
                ->leftJoin('ev_tbl_brands as bm', 'vm.brand', '=', 'bm.id')
                ->leftJoin('vehicle_types as vt', 'qc.vehicle_type', '=', 'vt.id')  
                ->where('qc.delete_status', 0)
                ->when($location_id != "", function ($query) use ($location_id) {
                    return $query->where('qc.location', $location_id);
                })
                ->when($vehicle_model != "", function ($query) use ($vehicle_model) {
                    return $query->where('qc.vehicle_model', $vehicle_model);
                })
                ->when($vehicle_type != "", function ($query) use ($vehicle_type) {
                    return $query->where('qc.vehicle_type', $vehicle_type);
                })
                ->when($timeline, function ($query) use ($timeline, &$from_date, &$to_date) {
                    switch ($timeline) {
                        case 'today':
                            $query->whereDate('qc.created_at', today());
                            break;
            
                        case 'this_week':
                            $query->whereBetween('qc.created_at', [
                                now()->startOfWeek(), now()->endOfWeek()
                            ]);
                            break;
            
                        case 'this_month':
                            $query->whereBetween('qc.created_at', [
                                now()->startOfMonth(), now()->endOfMonth()
                            ]);
                            break;
            
                        case 'this_year':
                            $query->whereBetween('qc.created_at', [
                                now()->startOfYear(), now()->endOfYear()
                            ]);
                            break;
                    }
            
                    $from_date = null;
                    $to_date = null;
                })
                ->when(!$timeline && $from_date, function ($query) use ($from_date) {
                    $query->whereDate('qc.created_at', '>=', $from_date);
                })
                ->when(!$timeline && $to_date, function ($query) use ($to_date) {
                    $query->whereDate('qc.created_at', '<=', $to_date);
                })
                ->when($accountability_type_id !== 'all', fn($q) => $q->where('qc.accountability_type', $accountability_type_id))
                ->when($customer_id !== 'all' && $accountability_type_id == 2, fn($q) => $q->where('qc.customer_id', $customer_id))
                ->when($customer_id !== 'all' && $accountability_type_id == 1, fn($q) => $q->where('vh.client', $customer_id))
                ->groupBy('bm.brand_name', 'vm.vehicle_model', 'qc.vehicle_type', 'vt.name')
                ->get();
                $total_vh_count = 0;
                $brandWiseData = [];
                foreach ($assetWiseTable as $row) {
                    $brand = $row->brand_name;
                
                    if (!isset($brandWiseData[$brand])) {
                        $brandWiseData[$brand] = [
                            'brand'   => $brand,
                            'total'   => 0,
                            'details' => []
                        ];
                    }
                    $brandWiseData[$brand]['total'] += $row->vehicle_count;
                    $brandWiseData[$brand]['details'][] = [
                        'model'   => $row->vehicle_model,
                        'type_id' => $row->vehicle_type,
                        'type'    => $row->vehicle_type_name,
                        'count'   => $row->vehicle_count,
                    ];
                    $total_vh_count += $row->vehicle_count;
                }
                
                $brandWiseData = array_values($brandWiseData);
            }
    
          return response()->json(['status'=>true,'brandWiseData'=>$brandWiseData,'total_vh_count'=>$total_vh_count]);
        }
        
        
         if (!empty($chart_type) && $chart_type == 'DocumentValidityTable') {
            
                $startDate = null;
                $endDate   = null;
                
               if (empty($timeline) && empty($from_date) && empty($to_date)) {
                
                    $startDate = now()->startOfMonth()->toDateString();
                    $endDate   = now()->endOfMonth()->toDateString();
                
                } elseif ($timeline === 'today') {
                
                    $startDate = today()->toDateString();
                    $endDate   = today()->toDateString();
                
                } elseif ($timeline === 'this_week') {
                
                    $startDate = now()->startOfWeek()->toDateString();
                    $endDate   = now()->endOfWeek()->toDateString();
                
                } elseif ($timeline === 'this_month') {
                
                    $startDate = now()->startOfMonth()->toDateString();
                    $endDate   = now()->endOfMonth()->toDateString();
                
                } elseif ($timeline === 'this_year') {
                
                    $startDate = now()->startOfYear()->toDateString();
                    $endDate   = now()->endOfYear()->toDateString();
                
                } elseif (!empty($from_date) && !empty($to_date)) {
                
                    $startDate = $from_date;
                    $endDate   = $to_date;
                }
                
                
                $insurance_sql = DB::table('ev_tbl_asset_master_vehicles as vh')
                ->leftJoin('vehicle_qc_check_lists as vqc', 'vh.qc_id', '=', 'vqc.id')
                ->selectRaw("
                    'Insurance' AS document_type,
                    SUM(CASE WHEN vh.insurance_expiry_date BETWEEN ? AND DATE_ADD(?, INTERVAL 1 MONTH) THEN 1 ELSE 0 END) AS within_1_month,
                    SUM(CASE WHEN vh.insurance_expiry_date BETWEEN ? AND DATE_ADD(?, INTERVAL 15 DAY) THEN 1 ELSE 0 END) AS within_15_days,
                    SUM(CASE WHEN vh.insurance_expiry_date BETWEEN ? AND DATE_ADD(?, INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS within_7_days,
                    SUM(CASE WHEN vh.insurance_expiry_date = ? THEN 1 ELSE 0 END) AS today
                ", [
                    $startDate, $startDate,
                    $startDate, $startDate,
                    $startDate, $startDate,
                    $startDate,
                ])
                ->whereBetween('vh.insurance_expiry_date', [$startDate, $endDate])
                ->where('vh.is_status', 'accepted')
            
                ->when($location_id, function ($query, $location_id) {
                    $query->where('vqc.location', $location_id);
                })
                ->when($accountability_type_id !== 'all', fn($q) => 
                    $q->where('vqc.accountability_type', $accountability_type_id)
                )
                ->when($customer_id !== 'all' && $accountability_type_id == 2, fn($q) => 
                    $q->where('vqc.customer_id', $customer_id)
                )
                ->when($customer_id !== 'all' && $accountability_type_id == 1, fn($q) => 
                    $q->where('vh.client', $customer_id)
                );

            
                $fitness_sql = DB::table('ev_tbl_asset_master_vehicles as vh')
                ->leftJoin('vehicle_qc_check_lists as vqc', 'vh.qc_id', '=', 'vqc.id')
                ->selectRaw("
                    'Fitness Certificate' AS document_type,
                    SUM(CASE WHEN vh.fc_expiry_date BETWEEN ? AND DATE_ADD(?, INTERVAL 1 MONTH) THEN 1 ELSE 0 END) AS within_1_month,
                    SUM(CASE WHEN vh.fc_expiry_date BETWEEN ? AND DATE_ADD(?, INTERVAL 15 DAY) THEN 1 ELSE 0 END) AS within_15_days,
                    SUM(CASE WHEN vh.fc_expiry_date BETWEEN ? AND DATE_ADD(?, INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS within_7_days,
                    SUM(CASE WHEN vh.fc_expiry_date = ? THEN 1 ELSE 0 END) AS today
                ", [
                    $startDate, $startDate,
                    $startDate, $startDate,
                    $startDate, $startDate,
                    $startDate,
                ])
                ->whereBetween('vh.fc_expiry_date', [$startDate, $endDate])
                ->where('vh.is_status', 'accepted')
            
                ->when($location_id, function ($query, $location_id) {
                    $query->where('vqc.location', $location_id);
                })
                ->when($accountability_type_id !== 'all', fn($q) => 
                    $q->where('vqc.accountability_type', $accountability_type_id)
                )
                ->when($customer_id !== 'all' && $accountability_type_id == 2, fn($q) => 
                    $q->where('vqc.customer_id', $customer_id)
                )
                ->when($customer_id !== 'all' && $accountability_type_id == 1, fn($q) => 
                    $q->where('vh.client', $customer_id)
                );
    
            $roadTax_sql = DB::table('ev_tbl_asset_master_vehicles as vh')
                ->leftJoin('vehicle_qc_check_lists as vqc', 'vh.qc_id', '=', 'vqc.id')
                ->selectRaw("
                    'Road Tax' AS document_type,
                    SUM(CASE WHEN vh.road_tax_next_renewal_date BETWEEN ? AND DATE_ADD(?, INTERVAL 1 MONTH) THEN 1 ELSE 0 END) AS within_1_month,
                    SUM(CASE WHEN vh.road_tax_next_renewal_date BETWEEN ? AND DATE_ADD(?, INTERVAL 15 DAY) THEN 1 ELSE 0 END) AS within_15_days,
                    SUM(CASE WHEN vh.road_tax_next_renewal_date BETWEEN ? AND DATE_ADD(?, INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS within_7_days,
                    SUM(CASE WHEN vh.road_tax_next_renewal_date = ? THEN 1 ELSE 0 END) AS today
                ", [
                    $startDate, $startDate,
                    $startDate, $startDate,
                    $startDate, $startDate,
                    $startDate,
                ])
                ->whereBetween('vh.road_tax_next_renewal_date', [$startDate, $endDate])
                ->where('vh.is_status', 'accepted')
            
                ->when($location_id, function ($query, $location_id) {
                    $query->where('vqc.location', $location_id);
                })
                ->when($accountability_type_id !== 'all', fn($q) => 
                    $q->where('vqc.accountability_type', $accountability_type_id)
                )
                ->when($customer_id !== 'all' && $accountability_type_id == 2, fn($q) => 
                    $q->where('vqc.customer_id', $customer_id)
                )
                ->when($customer_id !== 'all' && $accountability_type_id == 1, fn($q) => 
                    $q->where('vh.client', $customer_id)
                );
        
                
                $leaseAgreement_sql = DB::table('ev_tbl_asset_master_vehicles as vh')
                ->leftJoin('vehicle_qc_check_lists as vqc', 'vh.qc_id', '=', 'vqc.id')
                ->selectRaw("
                    'Lease Agreement' AS document_type,
                    SUM(CASE WHEN vh.lease_end_date BETWEEN ? AND DATE_ADD(?, INTERVAL 1 MONTH) THEN 1 ELSE 0 END) AS within_1_month,
                    SUM(CASE WHEN vh.lease_end_date BETWEEN ? AND DATE_ADD(?, INTERVAL 15 DAY) THEN 1 ELSE 0 END) AS within_15_days,
                    SUM(CASE WHEN vh.lease_end_date BETWEEN ? AND DATE_ADD(?, INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS within_7_days,
                    SUM(CASE WHEN vh.lease_end_date = ? THEN 1 ELSE 0 END) AS today
                ", [
                    $startDate, $startDate,
                    $startDate, $startDate,
                    $startDate, $startDate,
                    $startDate,
                ])
                ->whereBetween('vh.lease_end_date', [$startDate, $endDate])
                ->where('vh.is_status', 'accepted')
            
                ->when($location_id, function ($query, $location_id) {
                    $query->where('vqc.location', $location_id);
                })
                ->when($accountability_type_id !== 'all', fn($q) => 
                    $q->where('vqc.accountability_type', $accountability_type_id)
                )
                ->when($customer_id !== 'all' && $accountability_type_id == 2, fn($q) => 
                    $q->where('vqc.customer_id', $customer_id)
                )
                ->when($customer_id !== 'all' && $accountability_type_id == 1, fn($q) => 
                    $q->where('vh.client', $customer_id)
                );
        
            $document_alerts = $insurance_sql
                ->unionAll($fitness_sql)
                ->unionAll($roadTax_sql)
                ->unionAll($leaseAgreement_sql)
                ->get();
        
            $document_validity_count = DB::table('asset_vehicle_inventories as inv')
                ->join('ev_tbl_asset_master_vehicles as vh', 'vh.id', '=', 'inv.asset_vehicle_id')
                ->where('vh.is_status', 'accepted')
                ->count();

            return response()->json([
                'document_alerts' => $document_alerts,
                'document_validity_count' => $document_validity_count
            ]);
        }

        
        if (!empty($chart_type) && $chart_type == 'ClientwisebarChart'){
            $data = DB::table('asset_vehicle_inventories as inv')
                ->select(
                    'qc.vehicle_type',
                    'vt.name as vehicle_type_name', 
                    'inv.transfer_status',
                    'vs.name',
                    DB::raw('COUNT(inv.transfer_status) as vehicle_count')
                )
                ->leftJoin('ev_tbl_inventory_location_master as vs', 'inv.transfer_status', '=', 'vs.id')
                ->leftJoin('ev_tbl_asset_master_vehicles as vh', 'inv.asset_vehicle_id', '=', 'vh.id')
                ->leftJoin('vehicle_qc_check_lists as qc', 'vh.qc_id', '=', 'qc.id')
                ->leftJoin('vehicle_types as vt', 'qc.vehicle_type', '=', 'vt.id') 
                ->when($location_id != "", fn($q) => $q->where('qc.location', $location_id))
                ->when($vehicle_model != "", fn($q) => $q->where('qc.vehicle_model', $vehicle_model))
                ->when($vehicle_type != "", fn($q) => $q->where('qc.vehicle_type', $vehicle_type))
                ->when($timeline, function ($query) use ($timeline, &$from_date, &$to_date) {
                switch ($timeline) {
                    case 'today':
                        $query->whereDate('qc.created_at', today());
                        break;
        
                    case 'this_week':
                        $query->whereBetween('qc.created_at', [
                            now()->startOfWeek(), now()->endOfWeek()
                        ]);
                        break;
        
                    case 'this_month':
                        $query->whereBetween('qc.created_at', [
                            now()->startOfMonth(), now()->endOfMonth()
                        ]);
                        break;
        
                    case 'this_year':
                        $query->whereBetween('qc.created_at', [
                            now()->startOfYear(), now()->endOfYear()
                        ]);
                        break;
                }
        
                // reset manual dates
                $from_date = null;
                $to_date = null;
            })
                ->when(!$timeline && $from_date, function ($query) use ($from_date) {
                    $query->whereDate('qc.created_at', '>=', $from_date);
                })
                ->when(!$timeline && $to_date, function ($query) use ($to_date) {
                    $query->whereDate('qc.created_at', '<=', $to_date);
                })
                ->when($accountability_type_id !== 'all', fn($q) => $q->where('qc.accountability_type', $accountability_type_id))
                ->when($customer_id !== 'all' && $accountability_type_id == 2, fn($q) => $q->where('qc.customer_id', $customer_id))
                ->when($customer_id !== 'all' && $accountability_type_id == 1, fn($q) => $q->where('vh.client', $customer_id))
                ->groupBy('qc.vehicle_type', 'vt.name', 'inv.transfer_status', 'vs.name') 
                ->get();

            
            return response()->json(['data' => $data]);

        }
        
        if (!empty($chart_type) && $chart_type == 'ClientwiseDeployment') {

             $query =  DB::table('asset_vehicle_inventories as inv')
            ->join('ev_tbl_asset_master_vehicles as vh', 'inv.asset_vehicle_id', '=', 'vh.id')
            ->join('vehicle_qc_check_lists as qc', 'qc.id', '=', 'vh.qc_id')
            ->join('ev_tbl_accountability_types as ac', 'ac.id', '=', 'qc.accountability_type')
            ->leftjoin('ev_tbl_customer_master as cm', 'vh.client', '=', 'cm.id')
            ->whereNotNull('vh.client')
                     ->when($location_id != "", fn($q) => $q->where('qc.location', $location_id))
                    ->when($vehicle_model != "", fn($q) => $q->where('qc.vehicle_model', $vehicle_model))
                    ->when($vehicle_type != "", fn($q) => $q->where('qc.vehicle_type', $vehicle_type))
                    ->when($timeline, function ($query) use ($timeline, &$from_date, &$to_date) {
                        switch ($timeline) {
                            case 'today':
                                $query->whereDate('qc.created_at', today());
                                break;
                
                            case 'this_week':
                                $query->whereBetween('qc.created_at', [
                                    now()->startOfWeek(), now()->endOfWeek()
                                ]);
                                break;
                
                            case 'this_month':
                                $query->whereBetween('qc.created_at', [
                                    now()->startOfMonth(), now()->endOfMonth()
                                ]);
                                break;
                
                            case 'this_year':
                                $query->whereBetween('qc.created_at', [
                                    now()->startOfYear(), now()->endOfYear()
                                ]);
                                break;
                        }
                
                        // reset manual dates
                        $from_date = null;
                        $to_date = null;
                    })
                        ->when(!$timeline && $from_date, function ($query) use ($from_date) {
                            $query->whereDate('qc.created_at', '>=', $from_date);
                        })
                        ->when(!$timeline && $to_date, function ($query) use ($to_date) {
                            $query->whereDate('qc.created_at', '<=', $to_date);
                        })
                        ->when($accountability_type_id !== 'all', fn($q) => $q->where('qc.accountability_type', $accountability_type_id))
                        ->when($customer_id !== 'all' && $accountability_type_id == 2, fn($q) => $q->where('qc.customer_id', $customer_id))
                        ->when($customer_id !== 'all' && $accountability_type_id == 1, fn($q) => $q->where('vh.client', $customer_id))
                ->groupBy('vh.client', 'qc.accountability_type', 'ac.name', 'cm.trade_name')
                ->select(
                    'qc.accountability_type',
                    'ac.name',
                    'vh.client',
                    'cm.trade_name',
                    DB::raw('COUNT(qc.accountability_type) as depployed_count')
                );
                
               $results = $query->get();
    
                // Format data (just like your example)
                $clientDeployedData = $results->map(function ($item) {
                    return [
                        'ac_type'    => $item->accountability_type,
                        'client_id' => $item->client,
                         'client_name' => $item->trade_name ?? 'Client',
                        'depployed_count' => (int)$item->depployed_count,
                    ];
                });
            
                return response()->json([
                    'data' => $clientDeployedData,
                    'total_count' => $clientDeployedData->sum('depployed_count')
                ]);
        
           
        }
        
        if (!empty($chart_type) && $chart_type == 'clientDeployedReturnedChart') {
            
                $cw_depReturedArr = AssetVehicleInventory::select(
                        DB::raw('DATE(assl.created_at) AS date'),
                        DB::raw("SUM(CASE 
                                    WHEN assl.status = 'closed' 
                                         AND assl.request_type = 'return_request' 
                                    THEN 1 ELSE 0 
                                 END) AS returned_count"),
                        DB::raw("SUM(CASE 
                                    WHEN assl.status = 'running' 
                                    THEN 1 ELSE 0 
                                 END) AS running_count")
                    )
                    ->join('b2b_tbl_vehicle_assignments as ass', 'asset_vehicle_inventories.asset_vehicle_id', '=', 'ass.asset_vehicle_id')
                    ->join('b2b_tbl_vehicle_assignment_logs as assl', 'assl.assignment_id', '=', 'ass.id')
            
                    // Default filter — current month
                    // ->when(!$timeline, function ($query) {
                    //     $query->whereMonth('assl.created_at', now()->month)
                    //           ->whereYear('assl.created_at', now()->year);
                    // })
            
                    // QC-based filters
                    ->when($location_id, fn($q) => 
                        $q->whereHas('assetVehicle.quality_check', fn($qc) => 
                            $qc->where('location', $location_id)
                        )
                    )
                    ->when($vehicle_type, fn($q) => 
                        $q->whereHas('assetVehicle.quality_check', fn($qc) => 
                            $qc->where('vehicle_type', $vehicle_type)
                        )
                    )
                    ->when($vehicle_model, fn($q) => 
                        $q->whereHas('assetVehicle.quality_check', fn($qc) => 
                            $qc->where('vehicle_modal', $vehicle_model)
                        )
                    )
                    ->when($accountability_type_id !== 'all', fn($q) => 
                        $q->whereHas('assetVehicle.quality_check', fn($qc) => 
                            $qc->where('accountability_type', $accountability_type_id)
                        )
                    )
                    ->when($customer_id !== 'all' && $accountability_type_id == 2, fn($q) => 
                        $q->whereHas('assetVehicle.quality_check', fn($qc) => 
                            $qc->where('customer_id', $customer_id)
                        )
                    )
                    ->when($customer_id !== 'all' && $accountability_type_id == 1, fn($q) => 
                        $q->whereHas('assetVehicle', fn($qc) => 
                            $qc->where('client', $customer_id)
                        )
                    )
                    
            
                    // Timeline filters
                    ->when($timeline, function ($query) use ($timeline) {
                        switch ($timeline) {
                            case 'today':
                                $query->whereDate('assl.created_at', today());
                                break;
                            case 'this_week':
                                $query->whereBetween('assl.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                                break;
                            case 'this_month':
                                $query->whereBetween('assl.created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                                break;
                            case 'this_year':
                                $query->whereBetween('assl.created_at', [now()->startOfYear(), now()->endOfYear()]);
                                break;
                        }
                        // reset manual dates
                        $from_date = null;
                        $to_date = null;
                    })
                    ->when(!$timeline && $from_date, fn($q) => $q->whereDate('assl.created_at', '>=', $from_date))
                    ->when(!$timeline && $to_date, fn($q) => $q->whereDate('assl.created_at', '<=', $to_date))
            
                    ->groupBy(DB::raw('DATE(assl.created_at)'))
                    ->orderBy(DB::raw('DATE(assl.created_at)'), 'asc')
                    ->get();
            
                // Prepare response data
                $clientwisebothData = [];
                foreach ($cw_depReturedArr as $val) {
                    $clientwisebothData[] = [
                        'date' => $val->date,
                        'deployed_count' => (int) $val->running_count,
                        'returned_count' => (int) $val->returned_count,
                    ];
                }
            
                $filterMonth = now()->month;
                $filterYear = now()->year;
                if(!empty($filterMonth) || !empty($filterYear)){
                    $filterMonth = $timeline === 'this_month' ? now()->month : ($from_date ? \Carbon\Carbon::parse($from_date)->month : now()->month);
                    $filterYear = $timeline === 'this_month' ? now()->year : ($from_date ? \Carbon\Carbon::parse($from_date)->year : now()->year);
                }
                // Calculate totals
                $total_deployed_count = $cw_depReturedArr->sum('running_count');
                $total_returned_count = $cw_depReturedArr->sum('returned_count');
            
                return response()->json([
                    'data' => $clientwisebothData,
                    'total_deployed_count' => $total_deployed_count,
                    'total_returned_count' => $total_returned_count,
                    'filterMonth' => $filterMonth,
                    'filterYear' => $filterYear
                ]);
            }


       if (!empty($chart_type) && $chart_type == 'InventoryDataTable') {
             $query = AssetVehicleInventory::with([
                'assetVehicle.vehicle_model_relation',
                'assetVehicle.vehicle_type_relation',
                'inventory_location',
                'assetVehicle.quality_check',
                'assetVehicle.quality_check.location_relation',
                'assetVehicle.customer_relation'
            ])
            ->whereNotNull('asset_vehicle_id')
            ->when($location_id, function ($query) use ($location_id) {
                $query->whereHas('assetVehicle.location_relation', function ($q) use ($location_id) {
                    $q->where('id', $location_id);
                });
            })
            ->when($vehicle_type, function ($query) use ($vehicle_type) {
                $query->whereHas('assetVehicle.vehicle_type_relation', function ($q) use ($vehicle_type) {
                    $q->where('id', $vehicle_type);
                });
            })
            ->when($vehicle_model, function ($query) use ($vehicle_model) {
                $query->whereHas('assetVehicle.vehicle_model_relation', function ($q) use ($vehicle_model) {
                    $q->where('id', $vehicle_model);
                });
            })
            ->when($timeline, function ($query) use ($timeline, &$from_date, &$to_date) {
                switch ($timeline) {
                    case 'today':
                        $query->whereDate('created_at', today());
                        break;
            
                    case 'this_week':
                        $query->whereBetween('created_at', [
                            now()->startOfWeek(), now()->endOfWeek()
                        ]);
                        break;
            
                    case 'this_month':
                        $query->whereBetween('created_at', [
                            now()->startOfMonth(), now()->endOfMonth()
                        ]);
                        break;
            
                    case 'this_year':
                        $query->whereBetween('created_at', [
                            now()->startOfYear(), now()->endOfYear()
                        ]);
                        break;
                }
            
                $from_date = null;
                $to_date = null;
            })
            ->when(!$timeline && $from_date, function ($query) use ($from_date) {
                $query->whereDate('created_at', '>=', $from_date);
            })
            ->when(!$timeline && $to_date, function ($query) use ($to_date) {
                $query->whereDate('created_at', '<=', $to_date);
            })
            ->when($accountability_type_id !== 'all', function ($query) use ($accountability_type_id) {
                $query->whereHas('assetVehicle.quality_check', function ($q) use ($accountability_type_id) {
                    $q->where('accountability_type', $accountability_type_id);
                });
            })

            ->when($customer_id !== 'all' && $accountability_type_id == 2, fn($q) => 
                $q->whereHas('assetVehicle.quality_check', fn($qc) => 
                    $qc->where('customer_id', $customer_id)
                )
            )
            ->when($customer_id !== 'all' && $accountability_type_id == 1, function ($query) use ($customer_id) {
                $query->whereHas('assetVehicle', function ($q) use ($customer_id) {
                    $q->where('client', $customer_id);
                });
            });
            $total_count = (clone $query)->count();
            $inventory_summary = $query->orderBy('id', 'desc')->limit(30)->get();
        
            $html = '';

            if ($inventory_summary->count() > 0) {
                foreach ($inventory_summary as $inventory) {
                    $id_encode = encrypt($inventory->id);
            
                    $html .= '<tr>';
                    $html .= '<td><small>'.($inventory->assetVehicle->chassis_number ?? 'N/A').'</small></td>';
                    $html .= '<td><small>'.($inventory->assetVehicle->vehicle_type_relation->name ?? 'N/A').'</small></td>';
                    $html .= '<td><small>'.($inventory->assetVehicle->permanent_reg_number ?? 'N/A').'</small></td>';
                    // $html .= '<td><small>'.($inventory->assetVehicle->vehicle_model_relation->vehicle_model ?? 'N/A').'</small></td>';
                    $html .= '<td><small>'.($inventory->assetVehicle->vehicle_model_relation->make ?? 'N/A').'</small></td>';
                    $html .= '<td><small>'.($inventory->assetVehicle->quality_check->location_relation->name ?? 'N/A').'</small></td>';
                    $html .= '<td>-</td>';
                    $html .= '<td><small>'.($inventory->assetVehicle->telematics_imei_number ?? 'N/A').'</small></td>';
                    $html .= '<td><small>'.($inventory->inventory_location->name ?? 'N/A').'</small></td>';
                    $html .= '<td><small>'.($inventory->assetVehicle->customer_relation->trade_name ?? '-').'</small></td>';
                    $html .= '<td>
                                <small>
                                    <a href="'.route('admin.asset_management.asset_master.inventory.view', ['id' => $id_encode]).'">
                                        <i class="bi bi-eye me-2 fs-5"></i>
                                    </a>
                                </small>
                              </td>';
                    $html .= '</tr>';
                }
            } else {
                $html .= '<tr><td colspan="10" class="text-center text-muted">No Data Found</td></tr>';
            }
            
            return response()->json([
                'html' => $html,
                'total_count' => $total_count
            ]);

        }


       return response()->json(['status' => false, 'message' => 'Invalid chart type']);
    }
    
    function getStateCodeFromCity($city)
    {
        $apiKey = BusinessSetting::where('key_name', 'google_map_api_key')->value('value'); 
        
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($city) . "&components=country:IN&key=" . $apiKey;
    
        $response = file_get_contents($url);
        $data = json_decode($response, true);
    
        if (!empty($data['results'][0]['address_components'])) {
            foreach ($data['results'][0]['address_components'] as $component) {
                if (in_array('administrative_area_level_1', $component['types'])) {
                    return [
                        'short' => "IN-" . $component['short_name'], 
                        'long'  => $component['long_name']           
                    ];
                }
            }
        }
    
        return null;
    }



   public function inventory_summary_filter(Request $request)
    {
// dd($request->all());
        $query = AssetVehicleInventory::with([
            'assetVehicle.vehicle_model_relation',
            'assetVehicle.vehicle_type_relation',
            'inventory_location'
        ])->whereNotNull('asset_vehicle_id');
        
        if($request->status != "" && $request->status != "all"){
            $query->where('transfer_status',$request->status);
        }
        
        if ($request->customer_name != "" && $request->customer_name != "all") {
            $customer = $request->customer_name;
            $query->whereHas('assetVehicle', function ($q) use ($customer) {
                $q->where('client', $customer);
            });
        }
        
         if ($request->vehicle_make != "" && $request->vehicle_make != "all") {
            $vehicle_make = $request->vehicle_make;
            $query->whereHas('assetVehicle.vehicle_model_relation', function ($q) use ($vehicle_make) {
                $q->where('id', $vehicle_make);
            });
        }
    
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('assetVehicle', function($q) use ($search) {
                      $q->where('chassis_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('assetVehicle', function($q) use ($search) {
                      $q->where('permanent_reg_number', 'like', "%{$search}%");
                  });
            });
        }
    
        $inventory_summary = $query->orderBy('id', 'desc')->limit(30)->get();
    
        $data = [];
        foreach ($inventory_summary as $val) {
            $data[] = [
                'id' => $val->id,
                'chassis_number' => $val->assetVehicle->chassis_number ?? 'N/A',
                'vehicle_type' => $val->assetVehicle->vehicle_type_relation->name ?? 'N/A',
                'reg_number' => $val->assetVehicle->permanent_reg_number ?? 'N/A',
                // 'model' => $val->assetVehicle->vehicle_model_relation->vehicle_model ?? 'N/A',
                'make' => $val->assetVehicle->vehicle_model_relation->make ?? 'N/A',
                'location' => $val->assetVehicle->quality_check->location_relation->name,
                'hub' =>'-',
                'telematic_no'=>$val->assetVehicle->telematics_imei_number ?? 'N/A',
                'location_status'=>$val->inventory_location->name ?? 'N/A',
                'client_name' =>$val->assetVehicle->customer_relation->trade_name ?? 'N/A',
                'url' => route('admin.asset_management.asset_master.inventory.view', ['id' => encrypt($val->id)]),
            ];
        }
    
        return response()->json($data);
    }
    
    public function get_customer_name(Request $request)
    {
        $search = $request->input('search');
    
        $get_customers = CustomerMaster::where('status', 1)
            ->where(function ($q) use ($search) {
                $q->where('trade_name', 'LIKE', "%{$search}%")
                  ->orWhere('id', 'LIKE', "%{$search}%");
            })
            ->select('id', 'trade_name')
            ->limit(50)
            ->get();
    
        return response()->json($get_customers);
    }





    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('assetmaster::modal_master_vechile.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('assetmaster::create');
    }

    public function bulk_upload_preview()
    {
        return view('assetmaster::asset_master.bulk_upload_preview');
    }
 

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
    $user     = Auth::user();
    $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

    // ✅ Log: create initiated (log this before validation so attempts are recorded)
        audit_log_after_commit([
        'module_id'         => 4,
        'short_description' => 'Asset Master Create Initiated',
        'long_description'  => 'User started creating a Vehicle Asset Master record.',
        'role'              => $roleName,
        'user_id'           => Auth::id(),
        'user_type'         => 'gdc_admin_dashboard',
        'dashboard_type'    => 'web',
        'page_name'         => 'asset_master.store',
        'ip_address'        => $request->ip(),
        'user_device'       => $request->userAgent()
    ]);
        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'manufacturer_name' => 'required|string|max:255',
            'load_capacity_kg' => 'required|integer',
            'rated_voltage' => 'required|integer',
            'rated_Ah' => 'required|integer',
            'max_speed_km_h' => 'required|integer',
            'tyre_type' => 'required|string|max:255',
            'front_tyre_dimensions' => 'required|string|max:255',
            'rear_tyre_dimensions' => 'required|string|max:255',
            'vehicle_type' => 'required|string|max:255',
            'range_km_noload' => 'required|integer',
            'range_km_fullload' => 'required|integer',
            'vehicle_mode' => 'required|string|max:255',
            'motor_type' => 'required|string|max:255',
            'motor_max_rpm' => 'required|integer',
            'peak_power_watt' => 'required|integer',
            'rated_power_watt' => 'required|integer',
            'motor_can_enabled' => 'required|boolean',
            'peak_torque_nm' => 'required|integer',
            'continuous_torque_nm' => 'required|integer',
            'front_suspension_type' => 'required|string|max:255',
            'rear_suspension_type' => 'required|string|max:255',
            'ground_clearance_mm' => 'required|integer',
            'motor_ip_rating' => 'required|string|max:255',
            'throttle_type' => 'required|string|max:255',
            'peak_curr_cntrlr' => 'required|integer',
            'cntrlr_can_enabled' => 'required|string|max:255',
            'acceleration_0to40_sec' => 'required|integer',
            'head_light_type' => 'required|string|max:255',
            'vehicle_reverse_mode' => 'required|string|max:255',
            'inbuilt_iot' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ];
    
        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
    
        // Check if validation fails
        if ($validator->fails()) {
            audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Asset Master Create Failed (Validation)',
            'long_description'  => 'Validation failed while creating Vehicle Asset Master.',
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'asset_master.store',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
        
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        // Insert the validated data into the database
        // Create a new instance of the model
        $vehicle = new ModalMasterVechile();
        
        // Assign validated data to the model's attributes
        $vehicle->fill($validator->validated());
        
        // Save the model to the database
        $vehicle->save();
        
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Asset Master Created',
            'long_description'  => "New Vehicle Asset Master record created successfully: {$vehicle->name}",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'asset_master.store',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
        
         return redirect()->route('admin.Green-Drive-Ev.asset-master.list')->with('success', 'Vehicle Record Added Successfully!');
    }

     public function list(ModalMasterVechileDataTable $dataTable)
    {
        return $dataTable->render('assetmaster::modal_master_vechile.list');
    }
    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('assetmaster::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit_ModalMasterVechile($id)
    {
        $ModalMasterVechile = ModalMasterVechile::findOrFail($id);
        return view('assetmaster::modal_master_vechile.edit', compact('ModalMasterVechile'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        
        // dd($request->all());
        // exit;
        // Define the validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'manufacturer_name' => 'required|string|max:255',
            'load_capacity_kg' => 'required|integer',
            'rated_voltage' => 'required|integer',
            'rated_Ah' => 'required|integer',
            'max_speed_km_h' => 'required|integer',
            'tyre_type' => 'required|string|max:255',
            'front_tyre_dimensions' => 'required|string|max:255',
            'rear_tyre_dimensions' => 'required|string|max:255',
            'vehicle_type' => 'required|string|max:255',
            'range_km_noload' => 'required|integer',
            'range_km_fullload' => 'required|integer',
            'vehicle_mode' => 'required|string|max:255',
            'motor_type' => 'required|string|max:255',
            'motor_max_rpm' => 'required|integer',
            'peak_power_watt' => 'required|integer',
            'rated_power_watt' => 'required|integer',
            'motor_can_enabled' => 'required|boolean',
            'peak_torque_nm' => 'required|integer',
            'continuous_torque_nm' => 'required|integer',
            'front_suspension_type' => 'required|string|max:255',
            'rear_suspension_type' => 'required|string|max:255',
            'ground_clearance_mm' => 'required|integer',
            'motor_ip_rating' => 'required|string|max:255',
            'throttle_type' => 'required|string|max:255',
            'peak_curr_cntrlr' => 'required|integer',
            'cntrlr_can_enabled' => 'required|string|max:255',
            'acceleration_0to40_sec' => 'required|string',
            'head_light_type' => 'required|string|max:255',
            'vehicle_reverse_mode' => 'required|string|max:255',
            'inbuilt_iot' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ];
        
        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
        
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $vehicle = ModalMasterVechile::find($id);
        
        $oldValues = $vehicle->getAttributes();
        if (!$vehicle) {
            
            return redirect()->back()->with('error', 'Vehicle not found.');
        }
    
        // Assign the validated data to the vehicle instance
        $vehicle->fill($validator->validated());
    
        // Save the updated vehicle record to the database
        $vehicle->save();
        
        $vehicle->refresh();
        $newValues = $vehicle->getAttributes();
        $ignore = ['created_at', 'updated_at', 'deleted_at'];

    $changes = [];

    // Compare keys present in oldValues (and newValues) excluding ignored fields
    $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));

    foreach ($allKeys as $field) {
        if (in_array($field, $ignore)) {
            continue;
        }

        $old = array_key_exists($field, $oldValues) ? $oldValues[$field] : null;
        $new = array_key_exists($field, $newValues) ? $newValues[$field] : null;

        // Normalize boolean-like values to string '1'/'0' or 'true'/'false' consistently
        if (is_bool($old)) $old = $old ? '1' : '0';
        if (is_bool($new)) $new = $new ? '1' : '0';

        // Cast JSON/array to readable string if needed
        if (is_array($old)) $old = json_encode($old, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (is_array($new)) $new = json_encode($new, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // Compare as strings to avoid type-strict mismatch
        if ((string)$old !== (string)$new) {
            // Human friendly label: vehicle_type -> Vehicle Type
            $label = ucwords(str_replace('_', ' ', $field));

            $oldText = ($old === null || $old === '') ? 'N/A' : (string)$old;
            $newText = ($new === null || $new === '') ? 'N/A' : (string)$new;

            $changes[] = "{$label}: {$oldText} → {$newText}";
        }
    }

    $changesText = empty($changes) ? 'No visible changes detected.' : implode('; ', $changes);

    // --------- AUDIT LOG ----------
    audit_log_after_commit([
        'module_id'         => 4,
        'short_description' => 'Asset Master Updated',
        'long_description'  => "Vehicle Asset Master updated successfully (ID: {$id}). Changes: " . Str::limit($changesText, 1000),
        'role'              => $roleName,
        'user_id'           => Auth::id(),
        'user_type'         => 'gdc_admin_dashboard',
        'dashboard_type'    => 'web',
        'page_name'         => 'asset_master.update',
        'ip_address'        => $request->ip(),
        'user_device'       => $request->userAgent()
    ]);

        return redirect()->route('admin.Green-Drive-Ev.asset-master.list')->with('success', 'Vehicle record updated successfully!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete_ModalMasterVechile($id)
    {
        $ModalMasterVechile = ModalMasterVechile::findOrFail($id);
        $name = $ModalMasterVechile->name;
        $ModalMasterVechile->delete();
        
        $roleName = optional(\Modules\Role\Entities\Role::find(optional(Auth::user())->role))->name ?? 'Unknown';

        audit_log_after_commit([
            'module_id'         => 4,  // Use module id for vehicle master module
            'short_description' => 'Vehicle Model Master Deleted',
            'long_description'  => 'Vehicle Model Master "' . $name . '" (ID: ' . $id . ') has been removed from the Vehicle Model Master list.',
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'vehicle_model_master.delete',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
    
        return redirect()->route('admin.Green-Drive-Ev.asset-master.list')->with('success', 'ModalMasterVechile deleted successfully.');
    }
    
    // Change status of a city
    public function change_status($id, $status)
    {
            $user     = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

            $statusText = $status == 1 ? 'Active' : 'Inactive';
    
        $ModalMasterVechile = ModalMasterVechile::findOrFail($id);
        $ModalMasterVechile->status = $status;
        $ModalMasterVechile->save();
        
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Asset Master Status Updated',
            'long_description'  => "Status updated to '{$statusText}' for Vehicle Asset Master (ID: {$id}).",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'asset_master.change_status',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
        
        return redirect()->route('admin.Green-Drive-Ev.asset-master.list')->with('success', 'ModalMasterVechile status updated successfully.');
    }
    
    public function modal_master_battery_index()
    {
        return view('assetmaster::model_master_battery.index');
    }
    public function modal_master_battery_list(ModalMasterBatteryDataTable $dataTable)
    {
        return $dataTable->render('assetmaster::model_master_battery.list');
    }
    public function modal_master_battery_store(Request $request): RedirectResponse
    {
    
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

        // ✅ Log: create initiated
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Battery Model Create Initiated',
            'long_description'  => 'User started creating a new Battery Model record.',
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'modal_master_battery.store',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
        // Define the validation rules
    $rules = [
        'name' => 'required|string|max:255',
        'manufacturer_name' => 'required|string|max:255',
        'current_rating_Ah' => 'required|integer',
        'type' => 'required|string|max:255',
        'cell_chemistry' => 'required|string|max:255',
        'nominal_voltage' => 'required|integer',
        'max_discharge_rate_c' => 'required|integer',
        'max_voltage' => 'required|numeric',
        'min_voltage' => 'required|numeric',
        'weight_kg' => 'required|numeric',
        'connector_type' => 'required|string|max:255',
        'telematics_enabled' => 'required|boolean',
        'type_of_telematics' => 'nullable|string|max:255',
        'smart_bms_available' => 'required|boolean',
        'smart_bms_features' => 'nullable|string|max:255',
        'cell_structure' => 'required|string|max:255',
        'cell_model' => 'required|string|max:255',
        'ip_rating' => 'required|string|max:255',
        'dod_percentage' => 'required|integer|min:0|max:100',
        'connector_rating' => 'required|integer',
        'warranty_expiry_cycles' => 'required|integer',
        'warranty_expiry_duration' => 'required|string|max:255',
        'warranty_expiry_param_priority' => 'required|string|max:255',
        'status' => 'required|string|max:255',
    ];

    // Validate the request data
    $validator = Validator::make($request->all(), $rules);

    // Check if validation fails
    if ($validator->fails()) {
         audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Battery Model Create Validation Failed',
            'long_description'  => 'Validation errors: ' . json_encode($validator->errors()->all()),
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'modal_master_battery.store',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);

        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    // Insert the validated data into the database
    $battery = new ModelMasterBattery();
    $battery->fill($validator->validated());
    $battery->save();

    audit_log_after_commit([
        'module_id'         => 4,
        'short_description' => 'Battery Model Created',
        'long_description'  => "New Battery Model '{$battery->name}' has been successfully added.",
        'role'              => $roleName,
        'user_id'           => Auth::id(),
        'user_type'         => 'gdc_admin_dashboard',
        'dashboard_type'    => 'web',
        'page_name'         => 'modal_master_battery.store',
        'ip_address'        => $request->ip(),
        'user_device'       => $request->userAgent()
    ]);

    // Redirect with success message
    return redirect()->route('admin.Green-Drive-Ev.asset-master.modal_master_battery_list')->with('success', 'ModalMasterBattery record added successfully!');

    }
    
    public function modal_master_battery_change_status($id, $status)
    {
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

        $ModelMasterBattery = ModelMasterBattery::findOrFail($id);
        $ModelMasterBattery->status = $status;
        $ModelMasterBattery->save();

        $statusText = $status == 1 ? 'Active' : 'Inactive';

        // ✅ Log: Status Changed
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Battery Model Status Updated',
            'long_description'  => "Battery Model '{$ModelMasterBattery->name}' (ID: {$ModelMasterBattery->id}) status changed to '{$statusText}'.",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'modal_master_battery.change_status',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);

        return redirect()->route('admin.Green-Drive-Ev.asset-master.modal_master_battery_list')->with('success', 'ModalMasterBattery status updated successfully.');
    }
    public function modal_master_battery_delete($id)
    {
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        $ModelMasterBattery = ModelMasterBattery::findOrFail($id);
        $batteryName = $ModelMasterBattery->name;
        $ModelMasterBattery->delete();

        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Battery Model Deleted',
            'long_description'  => "Battery Model '{$batteryName}' (ID: {$id}) has been deleted from the system.",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'modal_master_battery.delete',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);

        return redirect()->route('admin.Green-Drive-Ev.asset-master.modal_master_battery_list')->with('success', 'ModalMasterBattery deleted successfully.');
    }
    public function modal_master_battery_edit($id)
    {
        $ModelMasterBattery = ModelMasterBattery::findOrFail($id);
        return view('assetmaster::model_master_battery.edit', compact('ModelMasterBattery'));
    }
    
    public function modal_master_battery_update(Request $request,$id): RedirectResponse
    {
        // Define the validation rules
        $user     = Auth::user();
    $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

    // ✅ Log: Update Start
    audit_log_after_commit([
        'module_id'         => 4,
        'short_description' => 'Battery Model Update Initiated',
        'long_description'  => "User started updating Battery Model ID: {$id}",
        'role'              => $roleName,
        'user_id'           => Auth::id(),
        'user_type'         => 'gdc_admin_dashboard',
        'dashboard_type'    => 'web',
        'page_name'         => 'modal_master_battery.update',
        'ip_address'        => $request->ip(),
        'user_device'       => $request->userAgent()
    ]);

    $rules = [
        'name' => 'required|string|max:255',
        'manufacturer_name' => 'required|string|max:255',
        'current_rating_Ah' => 'required|integer',
        'type' => 'required|string|max:255',
        'cell_chemistry' => 'required|string|max:255',
        'nominal_voltage' => 'required|integer',
        'max_discharge_rate_c' => 'required|integer',
        'max_voltage' => 'required|numeric',
        'min_voltage' => 'required|numeric',
        'weight_kg' => 'required|numeric',
        'connector_type' => 'required|string|max:255',
        'telematics_enabled' => 'required|boolean',
        'type_of_telematics' => 'nullable|string|max:255',
        'smart_bms_available' => 'required|boolean',
        'smart_bms_features' => 'nullable|string|max:255',
        'cell_structure' => 'required|string|max:255',
        'cell_model' => 'required|string|max:255',
        'ip_rating' => 'required|string|max:255',
        'dod_percentage' => 'required|integer|min:0|max:100',
        'connector_rating' => 'required|integer',
        'warranty_expiry_cycles' => 'required|integer',
        'warranty_expiry_duration' => 'required|string|max:255',
        'warranty_expiry_param_priority' => 'required|string|max:255',
        'status' => 'required|string|max:255',
    ];

    // Validate the request data
    $validator = Validator::make($request->all(), $rules);

    // Check if validation fails
    if ($validator->fails()) {
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Battery Model Update Validation Failed',
            'long_description'  => 'Validation Errors: ' . json_encode($validator->errors()->all()),
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'modal_master_battery.update',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    $battery = ModelMasterBattery::find($id);

        if (!$battery) {
            return redirect()->back()->with('error', 'ModelMasterBattery not found.');
        }
    
        // Assign the validated data to the vehicle instance
        $battery->fill($validator->validated());
    
        // Save the updated vehicle record to the database
        $battery->save();

        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Battery Model Updated',
            'long_description'  => "Battery Model '{$battery->name}' (ID: {$battery->id}) has been updated successfully.",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'modal_master_battery.update',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);


    // Redirect with success message
    return redirect()->route('admin.Green-Drive-Ev.asset-master.modal_master_battery_list')->with('success', 'ModalMasterBattery record added successfully!');

    }
    
    public function model_master_charger_index()
    {
        return view('assetmaster::model_master_charger.index');
    }
    
    public function model_master_charger_list(ModalMasterChargerDataTable $dataTable)
    {
        return $dataTable->render('assetmaster::model_master_charger.list');
    }
    
    public function model_master_charger_store(Request $request): RedirectResponse
    {
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

        // ✅ Log: create initiated
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Charger Model Create Initiated',
            'long_description'  => 'User started creating a new Charger Model record.',
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'model_master_charger.store',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
        // Define the validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'manufacturer_name' => 'required|string|max:255',
            'nominal_c_rating' => 'required|numeric',
            'charging_mode' => 'required|string|max:255',
            'output_voltage' => 'required|numeric',
            'output_current' => 'required|numeric',
            'input_voltage' => 'required|numeric',
            'input_current' => 'required|numeric',
            'connector_rating' => 'required|numeric',
            'status' => 'required|string|max:255',
        ];
    
        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
    
        // Check if validation fails
        if ($validator->fails()) {
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'Charger Model Create Validation Failed',
                'long_description'  => 'Validation errors: ' . json_encode($validator->errors()->all()),
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'model_master_charger.store',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        // Insert the validated data into the database
        $charger = new ModelMasterCharger();
        $charger->fill($validator->validated());
        $charger->save();
        
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Charger Model Created',
            'long_description'  => "New Charger Model '{$charger->name}' has been successfully added.",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'model_master_charger.store',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);


        // Redirect with success message
        return redirect()->route('admin.Green-Drive-Ev.asset-master.model_master_charger_list')
            ->with('success', 'ModalMasterCharger record added successfully!');
    }
    
    public function model_master_charger_update(Request $request, $id): RedirectResponse
    {   
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

        // ✅ Log: Update initiated
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Charger Model Update Initiated',
            'long_description'  => "User started updating Charger Model (ID: {$id}).",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'model_master_charger.update',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
        // Define the validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'manufacturer_name' => 'required|string|max:255',
            'nominal_c_rating' => 'required|numeric',
            'charging_mode' => 'required|string|max:255',
            'output_voltage' => 'required|numeric',
            'output_current' => 'required|numeric',
            'input_voltage' => 'required|numeric',
            'input_current' => 'required|numeric',
            'connector_rating' => 'required|numeric',
            'status' => 'required|string|max:255',
        ];
    
        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
    
        // Check if validation fails
        if ($validator->fails()) {
            audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Charger Model Update Failed (Validation)',
            'long_description'  => 'Validation Errors: ' . json_encode($validator->errors()->all()),
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'model_master_charger.update',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        // Find the charger record by ID and check if it exists
        $charger = ModelMasterCharger::find($id);
    
        // If the charger record doesn't exist, redirect with an error message
        if (!$charger) {
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'Charger Model Update Failed (Not Found)',
                'long_description'  => "Charger Model (ID: {$id}) not found.",
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'model_master_charger.update',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
            return redirect()->route('admin.Green-Drive-Ev.asset-master.model_master_charger_list')
                ->with('error', 'Charger record not found.');
        }
    
        // Update the charger record with validated data
        $charger->update($validator->validated());
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Charger Model Updated Successfully',
            'long_description'  => "Charger Model '{$charger->name}' (ID: {$charger->id}) has been successfully updated.",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'model_master_charger.update',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
        // Redirect with success message
        return redirect()->route('admin.Green-Drive-Ev.asset-master.model_master_charger_list')
            ->with('success', 'ModalMasterCharger record updated successfully!');
    }
    
    public function model_master_charger_change_status(Request $request, $id,$status): RedirectResponse{
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        $ModalMasterVechile = ModelMasterCharger::findOrFail($id);
        $ModalMasterVechile->status = $status;
        $ModalMasterVechile->save();
        $statusText = $status == 1 ? 'Active' : 'Inactive';
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Charger Model Status Updated Successfully',
            'long_description'  => "Charger Model '{$ModalMasterVechile->name}' (ID: {$id}) status changed to '{$statusText}'.",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'model_master_charger.change_status',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
        return redirect()->route('admin.Green-Drive-Ev.asset-master.model_master_charger_list')
            ->with('success', 'ModalMasterCharger status changed successfully!');
    }
    
    public function model_master_charger_delete($id)
    {   
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        $ModelMasterBattery = ModelMasterCharger::findOrFail($id);
        $chargerName =$ModelMasterBattery->name;
        $ModelMasterBattery->delete();
        
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Charger Model Deleted Successfully',
            'long_description'  => "Charger Model '{$chargerName}' (ID: {$id}) has been deleted.",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'model_master_charger.delete',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);

        return redirect()->route('admin.Green-Drive-Ev.asset-master.model_master_charger_list')->with('success', 'ModalMasterCharger deleted successfully.');
    }
    
    public function model_master_charger_edit($id)
    {
        $ModelMasterCharger = ModelMasterCharger::findOrFail($id);
        return view('assetmaster::model_master_charger.edit', compact('ModelMasterCharger'));
    }
    
    public function manufacturer_master_index()
    {
        return view('assetmaster::manufacturer_master.index');
    }
    
    public function manufacturer_master_list(ManufactureMasterDataTable $dataTable)
    {
        return $dataTable->render('assetmaster::manufacturer_master.list');
    }
    public function manufacturer_master_store(Request $request): RedirectResponse
    {
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        // Define the validation rules
        $rules = [
            'manufacturer_name' => 'required|string|max:255',
            'Address_line_1' => 'required|string|max:255',
            'Address_line_2' => 'nullable|string|max:255',
            'Address_line_3' => 'nullable|string|max:255',
            'Country' => 'required|string|max:255',
            'State' => 'required|string|max:255',
            'Phone' => ['required', 'string', 'regex:/^\+91\d{10}$/', 'max:13'], // Ensure it's a valid phone number format
            'Contact_Name' => 'required|string|max:255',
            'Status' => 'required',  // assuming status is boolean (active/inactive)
            'Web_site_URL' => 'nullable|url|max:255',  // Validate if provided, and ensure it's a valid URL
        ];
    
            // Validate the request data
            $validator = Validator::make($request->all(), $rules);
        
            // Check if validation fails
            if ($validator->fails()) {
                audit_log_after_commit([
                    'module_id'         => 4,
                    'short_description' => 'Manufacturer Create Validation Failed',
                    'long_description'  => 'Validation errors: ' . json_encode($validator->errors()->all()),
                    'role'              => $roleName,
                    'user_id'           => Auth::id(),
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'manufacturer_master.store',
                    'ip_address'        => $request->ip(),
                    'user_device'       => $request->userAgent()
                ]);
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        
            // Insert the validated data into the database
            $manufacturer = new ManufacturerMaster();
            $manufacturer->fill($validator->validated());
            $manufacturer->save();

            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'Manufacturer Created',
                'long_description'  => "Manufacturer '{$manufacturer->manufacturer_name}' (ID: {$manufacturer->id}) created successfully.",
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'manufacturer_master.store',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
        // Redirect with success message
        return redirect()->route('admin.Green-Drive-Ev.asset-master.manufacturer_master_list')
            ->with('success', 'Manufacturer record added successfully!');
    }

     public function manufacturer_master_delete($id)
    {
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        $ManufacturerMaster = ManufacturerMaster::findOrFail($id);
        $name = $ManufacturerMaster->manufacturer_name;
        $ManufacturerMaster->delete();
      
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Manufacturer Deleted',
            'long_description'  => "Manufacturer '{$name}' (ID: {$id}) deleted successfully.",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'manufacturer_master.delete',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
        return redirect()->route('admin.Green-Drive-Ev.asset-master.manufacturer_master_list')->with('success', 'ManufacturerMaster deleted successfully.');
    }
    
    public function manufacturer_master_change_status($id, $status)
    {
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

        $ManufacturerMaster = ManufacturerMaster::findOrFail($id);
        $ManufacturerMaster->status = $status;
        $ManufacturerMaster->save();
        
        $statusText = $status == 1 ? 'Active' : 'Inactive';
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Manufacturer Status Changed',
            'long_description'  => "Manufacturer '{$ManufacturerMaster->manufacturer_name}' status changed to {$statusText}.",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'manufacturer_master.change_status',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);

       return redirect()->route('admin.Green-Drive-Ev.asset-master.manufacturer_master_list')->with('success', 'ManufacturerMaster Status Changed successfully.');
    }
    
    public function manufacturer_master_edit($id)
    {
        $ManufacturerMaster = ManufacturerMaster::findOrFail($id);
        return view('assetmaster::manufacturer_master.edit', compact('ManufacturerMaster'));
    }
    
    public function manufacturer_master_update(Request $request, $id): RedirectResponse
    {
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        // Define the validation rules
        $rules = [
            'manufacturer_name' => 'required|string|max:255',
            'Address_line_1' => 'required|string|max:255',
            'Address_line_2' => 'nullable|string|max:255',
            'Address_line_3' => 'nullable|string|max:255',
            'Country' => 'required|string|max:255',
            'State' => 'required|string|max:255',
            'Phone' => ['required', 'string', 'regex:/^\+91\d{10}$/', 'max:13'], // Ensure it's a valid phone number format
            'Contact_Name' => 'required|string|max:255',
            'Status' => 'required',  // assuming status is boolean (active/inactive)
            'Web_site_URL' => 'nullable|url|max:255',  // Validate if provided, and ensure it's a valid URL
        ];
    
        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
    
        // Check if validation fails
        if ($validator->fails()) {
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'Manufacturer Update Validation Failed',
                'long_description'  => 'Validation errors: ' . json_encode($validator->errors()->all()),
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'manufacturer_master.update',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        // Find the ManufacturerMaster record by ID
        $ManufacturerMaster = ManufacturerMaster::find($id);
    
        // If the ManufacturerMaster record doesn't exist, redirect with an error message
        if (!$ManufacturerMaster) {
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'Manufacturer Update Failed',
                'long_description'  => "Manufacturer ID {$id} not found for update.",
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'manufacturer_master.update',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
            return redirect()->route('admin.Green-Drive-Ev.asset-master.manufacturer_master_list')
                ->with('error', 'Manufacturer record not found.');
        }
    
        // Update the ManufacturerMaster record with validated data
        $ManufacturerMaster->update($validator->validated());
        
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Manufacturer Updated',
            'long_description'  => "Manufacturer '{$ManufacturerMaster->manufacturer_name}' (ID: {$id}) updated successfully.",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'manufacturer_master.update',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);

        // Redirect with success message
        return redirect()->route('admin.Green-Drive-Ev.asset-master.manufacturer_master_list')
            ->with('success', 'Manufacturer record updated successfully!');
    }

    
    public function po_table_index()
    {
        return view('assetmaster::po_table.index');
    }
    
    public function po_table_store(Request $request){
         // Define validation rules
         $user     = Auth::user();
         $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        $rules = [
            'AMS_Location'   => 'required|string|max:255',
            'PO_Number'      => 'required|string|max:255',
            'Supplier_Name'  => 'required|string|max:255',
            'Description'    => 'nullable|string',
            'Manufacturer'   => 'nullable|string',
            'PO_Date'        => 'required|date',
            'Other_Amount'   => 'nullable|numeric|min:0',
            'Tax_Amount'     => 'nullable|numeric|min:0',
            'Delivery_Date'  => 'required|date',
            'Quantity'       => 'required|integer|min:1',
            'Status'         => 'required',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'PO Creation Failed - Validation Error',
                'long_description'  => 'Validation failed while adding PO: ' . json_encode($validator->errors()->toArray()),
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'po_table.store',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Insert data into the database
        $po = PoTable::create($request->all());

        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Purchase Order Created',
            'long_description'  => "PO Number: {$po->PO_Number}, Supplier: {$po->Supplier_Name}, Quantity: {$po->Quantity}",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'po_table.store',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
        return redirect()->route('admin.Green-Drive-Ev.asset-master.po_table_list')
            ->with('success', 'po_table record Added successfully!');
    }
    
    public function po_table_list(PotableDataTable $dataTable)
    {
        return $dataTable->render('assetmaster::po_table.list');
    }
    
    public function po_table_delete($id)
    {
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        $PoTable = PoTable::findOrFail($id);
        $meta = "PO_Number: {$PoTable->PO_Number}, Supplier: {$PoTable->Supplier_Name}, Quantity: {$PoTable->Quantity}";
        $PoTable->delete();

        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'PO Record Deleted',
            'long_description'  => "The following Purchase Order was deleted → {$meta}.",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'po_table.delete',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);

        return redirect()->route('admin.Green-Drive-Ev.asset-master.po_table_list')->with('success', 'PoTable deleted successfully.');
    }
    
    public function po_table_change_status($id, $status)
    {
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        $PoTable = PoTable::findOrFail($id);
        $PoTable->status = $status;
        $PoTable->save();

        $statusText = $status == 1 ? 'Active' : 'Inactive';

        $meta = "PO_Number: {$PoTable->PO_Number}, Supplier: {$PoTable->Supplier_Name}, Quantity: {$PoTable->Quantity}, New Status: {$statusText}";
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'PO Status Updated',
            'long_description'  => "Purchase Order status updated → {$meta}.",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'po_table.change_status',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
       return redirect()->route('admin.Green-Drive-Ev.asset-master.po_table_list')->with('success', 'PoTable Status Changed successfully.');
    }
    
    public function po_table_edit($id)
    {
        $PoTable = PoTable::findOrFail($id);
        return view('assetmaster::po_table.edit', compact('PoTable'));
    }
    
    public function po_table_update(Request $request, $id): RedirectResponse
    {
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        $rules = [
            'AMS_Location'   => 'required|string|max:255',
            'PO_Number'      => 'required|string|max:255',
            'Supplier_Name'  => 'required|string|max:255',
            'Description'    => 'nullable|string',
            'Manufacturer'   => 'nullable|string',
            'PO_Date'        => 'required|date',
            'Other_Amount'   => 'nullable|numeric|min:0',
            'Tax_Amount'     => 'nullable|numeric|min:0',
            'Delivery_Date'  => 'required|date',
            'Quantity'       => 'required|integer|min:1',
            'Status'         => 'required',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'PO Update Failed - Validation Error',
                'long_description'  => "PO update validation failed for ID {$id}. Errors: " . json_encode($validator->errors()->toArray()),
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'po_table.update',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        // Find the ManufacturerMaster record by ID
        $PoTable = PoTable::find($id);
    
        // If the ManufacturerMaster record doesn't exist, redirect with an error message
        if (!$PoTable) {
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'PO Update Failed - Not Found',
                'long_description'  => "PO record not found for update (ID: {$id}).",
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'po_table.update',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
            return redirect()->route('admin.Green-Drive-Ev.asset-master.po_table_list')
                ->with('error', 'po table record not found.');
        }
    
        // Update the ManufacturerMaster record with validated data
        $PoTable->update($validator->validated());
        
        $changes = [];
        foreach ($dirty as $field => $newVal) {
            $oldVal = $old[$field] ?? null;

            // stringify with small truncation
            $sv = fn($v) => is_scalar($v) ? (string)$v : json_encode($v);
            $t  = fn($s) => mb_strimwidth($s ?? '', 0, 120, '…');

            $changes[] = "{$field}: '" . $t($sv($oldVal)) . "' => '" . $t($sv($newVal)) . "'";
        }

        $changeText = empty($changes) ? 'No fields changed.' : ('Updated Fields -> ' . implode('; ', $changes));
        $meta = "PO_Number: {$po->PO_Number}, Supplier: {$po->Supplier_Name}, Quantity: {$po->Quantity}";

        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'PO Updated',
            'long_description'  => "{$meta}. {$changeText}",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'po_table.update',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);

        // Redirect with success message
        return redirect()->route('admin.Green-Drive-Ev.asset-master.po_table_list')
            ->with('success', 'po table record updated successfully!');
    }
    
    public function ams_location_master_index()
    {
        return view('assetmaster::ams_location_master.index');
    }
    
    public function ams_location_master_list(AmsLocationMasterDataTable $dataTable)
    {
        return $dataTable->render('assetmaster::ams_location_master.list');
    }
    
    public function ams_location_master_delete($id)
    {
        $AmsLocationMaster = AmsLocationMaster::findOrFail($id);
        $AmsLocationMaster->delete();

        return redirect()->route('admin.Green-Drive-Ev.asset-master.ams_location_master_list')->with('success', 'AmsLocationMaster deleted successfully.');
    }
    
    public function ams_location_master_edit($id)
    {
        $AmsLocationMaster= AmsLocationMaster::findOrFail($id);
        return view('assetmaster::ams_location_master.edit', compact('AmsLocationMaster'));
    }
    

    public function ams_location_master_store(Request $request)
    {
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        // Define the validation rules
        $rules = [
            'Name' => 'required|string|max:255',
            'Address_line_1' => 'required|string|max:255',
            'Address_line_2' => 'nullable|string|max:255',
            'Address_line_3' => 'nullable|string|max:255',
            'Country' => 'required|string|max:255',
            'State' => 'required|string|max:255',
        ];
    
        // Use the Validator facade to validate the input
        $validator = Validator::make($request->all(), $rules);
    
        // Check if validation fails
        if ($validator->fails()) {
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'AMS Location Create Failed',
                'long_description'  => 'Validation failed while creating AMS location. Errors: ' . json_encode($validator->errors()->all()),
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'ams_location_master.store',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        // Create a new AmsLocationMaster record with the validated data
        $location = AmsLocationMaster::create($validator->validated());
        
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'AMS Location Created',
            'long_description'  => "New AMS location added: {$location->Name}, City/State: {$location->State}, {$location->Country}.",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'ams_location_master.store',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
        // Redirect back or to a specific route with a success message
        return redirect()->route('admin.Green-Drive-Ev.asset-master.ams_location_master_list')->with('success', 'Location added successfully!');
    }
    
    // Method to handle the form submission for updating an existing AmsLocationMaster entry
    public function ams_location_master_update(Request $request, $id)
    {
        // Find the location by ID
        $amsLocationMaster = AmsLocationMaster::findOrFail($id);
    
        // Define the validation rules
        $rules = [
            'Name' => 'required|string|max:255',
            'Address_line_1' => 'required|string|max:255',
            'Address_line_2' => 'nullable|string|max:255',
            'Address_line_3' => 'nullable|string|max:255',
            'Country' => 'required|string|max:255',
            'State' => 'required|string|max:255',
        ];
    
        // Use the Validator facade to validate the input
        $validator = Validator::make($request->all(), $rules);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        // Update the existing record with the validated data
        $amsLocationMaster->update($validator->validated());
    
        // Redirect back or to a specific route with a success message
        return redirect()->route('admin.Green-Drive-Ev.asset-master.ams_location_master_list')->with('success', 'Location updated successfully!');
    }

    public function asset_insurance_details_index()
    {
        $AssetMasterVehicle = AssetMasterVehicle::all();
        return view('assetmaster::asset_insurance_details.index',compact('AssetMasterVehicle'));
    }
    
    public function asset_insurance_details_list(AssetInsuranceDataTable $dataTable)
    {
        return $dataTable->render('assetmaster::asset_insurance_details.list');
    }
    
    public function asset_insurance_details_delete($id)
    {
        $AssetInsuranceDetails = AssetInsuranceDetails::findOrFail($id);
        $AssetInsuranceDetails->delete();

        return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_insurance_details_list')->with('success', 'AssetInsuranceDetails deleted successfully.');
    }
    
    public function asset_insurance_details_edit($id)
    {
        $AssetInsuranceDetails= AssetInsuranceDetails::findOrFail($id);
        $AssetMasterVehicle = AssetMasterVehicle::all();
        return view('assetmaster::asset_insurance_details.edit', compact('AssetInsuranceDetails','AssetMasterVehicle'));
    }
    
    public function asset_insurance_details_store(Request $request){
        $rules = [
            'vehicle_reg_no' => 'required|string|max:255',
            'Insurance_Vendor_3rd_party' => 'required|string|max:255',
            'Policy_Number_3rd_party' => 'required|string|max:255',
            'Start_date_3rd_party' => 'required|date',
            'End_date_3rd_party' => 'required|date|after:Start_date_3rd_party',
            'Declared_Value_3rd_party' => 'required|numeric|min:0',
            'Policy_Number_OD' => 'required|string|max:255',
            'Start_date_OD' => 'required|date',
            'End_date_OD' => 'required|date|after:Start_date_OD',
            'Declared_Value_OD' => 'required|numeric|min:0',
            'Insurance_Status_OD' => 'required', 
            'Chassis_Serial_No' => 'required|string|max:255',
            'insurance_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10048',
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
    
        // Check for validation errors
        if ($validator->fails()) {
            return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
        }
        
        
    
        // Insert data into the database
        try {
            $insuranceDetails = new AssetInsuranceDetails();
    
            $insuranceDetails->vehicle_reg_no = $request->input('vehicle_reg_no');
            $insuranceDetails->insurance_vendor_3rd_party = $request->input('Insurance_Vendor_3rd_party');
            $insuranceDetails->policy_number_3rd_party = $request->input('Policy_Number_3rd_party');
            $insuranceDetails->start_date_3rd_party = $request->input('Start_date_3rd_party');
            $insuranceDetails->end_date_3rd_party = $request->input('End_date_3rd_party');
            $insuranceDetails->declared_value_3rd_party = $request->input('Declared_Value_3rd_party');
            $insuranceDetails->policy_number_od = $request->input('Policy_Number_OD');
            $insuranceDetails->start_date_od = $request->input('Start_date_OD');
            $insuranceDetails->end_date_od = $request->input('End_date_OD');
            $insuranceDetails->declared_value_od = $request->input('Declared_Value_OD');
            $insuranceDetails->insurance_status_od = $request->input('Insurance_Status_OD');
            $insuranceDetails->Chassis_Serial_No = $request->Chassis_Serial_No;
            if ($request->hasFile('insurance_file')) {
                $insuranceDetails->insurance_file = $this->uploadFile($request->file('insurance_file'), 'EV/images/Insurance');
            }
    
            $insuranceDetails->save();
    
            return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_insurance_details_list')->with('success', 'AssetInsuranceDetails save successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_insurance_details_list')->with('error', 'Failed to save insurance details.');
        }
    }
    
    public function asset_insurance_details_update(Request $request, $id)
    {
       
        // Define validation rules
        $rules = [
            'vehicle_reg_no' => 'required|string|max:255',
            'Insurance_Vendor_3rd_party' => 'required|string|max:255',
            'Policy_Number_3rd_party' => 'required|string|max:255',
            'Start_date_3rd_party' => 'required|date',
            'End_date_3rd_party' => 'required|date|after:Start_date_3rd_party',
            'Declared_Value_3rd_party' => 'required|numeric|min:0',
            'Policy_Number_OD' => 'required|string|max:255',
            'Start_date_OD' => 'required|date',
            'End_date_OD' => 'required|date|after:Start_date_OD',
            'Declared_Value_OD' => 'required|numeric|min:0',
            'Insurance_Status_OD' => 'required', 
            'Chassis_Serial_No' => 'required|string|max:255',
            'insurance_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10048',
        ];
    
        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
    
        // Check for validation errors
        if ($validator->fails()) {
            return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
                    
        }
    
        // Attempt to update the record
        try {
            // Find the existing record by ID
            $insuranceDetails = AssetInsuranceDetails::findOrFail($id);
    
            // Update the record with new data
            $insuranceDetails->vehicle_reg_no = $request->input('vehicle_reg_no');
            $insuranceDetails->insurance_vendor_3rd_party = $request->input('Insurance_Vendor_3rd_party');
            $insuranceDetails->policy_number_3rd_party = $request->input('Policy_Number_3rd_party');
            $insuranceDetails->start_date_3rd_party = $request->input('Start_date_3rd_party');
            $insuranceDetails->end_date_3rd_party = $request->input('End_date_3rd_party');
            $insuranceDetails->declared_value_3rd_party = $request->input('Declared_Value_3rd_party');
            $insuranceDetails->policy_number_od = $request->input('Policy_Number_OD');
            $insuranceDetails->start_date_od = $request->input('Start_date_OD');
            $insuranceDetails->end_date_od = $request->input('End_date_OD');
            $insuranceDetails->declared_value_od = $request->input('Declared_Value_OD');
            $insuranceDetails->insurance_status_od = $request->input('Insurance_Status_OD');
            $insuranceDetails->Chassis_Serial_No = $request->Chassis_Serial_No;
            if ($request->hasFile('insurance_file')) {
                $insuranceDetails->insurance_file = $this->uploadFiles($request->file('insurance_file'), 'EV/images/Insurance',$insuranceDetails->insurance_file);
            }
    
            // Save updated data to the database
            $insuranceDetails->save();
    
             return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_insurance_details_list')->with('success', 'AssetInsuranceDetails Updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_insurance_details_list')->with('error', 'Failed to Update insurance details.');
        }
    }

    
    public function asset_master_vehicle_index()
    {
        $delivery_man= Deliveryman::where('approved_status',1)->get();
        $asset_status = AssetStatus::where('status',1)->get();
        return view('assetmaster::asset_master_vehicle.index',compact('delivery_man','asset_status'));
    }
     public function asset_master_vehicle_list(AssetMasterVechileDataTables $dataTable)
    {
        return $dataTable->render('assetmaster::asset_master_vehicle.list');
    }
     public function asset_master_vehicle_import_verify()
    {
        $delivery_man= Deliveryman::where('approved_status',1)->get();
        $asset_status = AssetStatus::where('status',1)->get();
        return view('assetmaster::asset_master_vehicle.import_verify_data',compact('delivery_man','asset_status'));
    }
    
    public function asset_status_list_handle(AssetStatusDataTable $dataTable)
    {
        return $dataTable->render('assetmaster::asset_master_vehicle.asset_status_list');
    }
    
     public function asset_status_store(Request $request)
    {
        $rules = [
            'status_name' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Validation Error!');
        }
        try {
            
            if ($request->status_id == "" || $request->status_id == null) {
                $insert = new AssetStatus();
                $insert->status_name = $request->status_name;
                $insert->status = $request->status ?? 1;
                $insert->save();
                return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_status_list_handle')->with('success', 'Asset status created successfully.');
            } else {
                $asset_status = AssetStatus::where('id', $request->status_id)->first();
                $data = [];
                $data['status_name'] = $request->status_name;
                $data['status'] = $request->status;
                $asset_status->update($data);
                return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_status_list_handle')->with('success', 'Asset status updated successfully.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Asset Status Creation Failed');
        }
    }
    public function asset_get_status(Request $request,$id){
        $get_asset_status = AssetStatus::where('id', $id)->first();
        if(!$get_asset_status){
            return response()->json(['status'=>false,'message'=>'Asset status not found'],200);
        }
        return response()->json(['status'=>true,'message'=>'Asset status fetched successfully','data'=>$get_asset_status],200);
    }
    
     public function asset_status_delete(Request $request,$id){
        $get_asset_status = AssetStatus::where('id', $id)->first();
        if(!$get_asset_status){
            return response()->json(['success'=>false,'message'=>'Asset status not found'],200);
        }
        $get_asset_status->delete();
        return response()->json(['success'=>true,'message'=>'Asset status deleted successfully'],200);
    }

    public function asset_update_status(Request $request, $id, $status)
    {
        try {
            $assetStatus = AssetStatus::findOrFail($id);
            $assetStatus->status = $status; // Use the status parameter from the route
            $assetStatus->save();
    
            return back()->with('success', 'Asset status changed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while changing the Asset status: ' . $e->getMessage());
        }
    }


    public function asset_master_vehicle_store(Request $request)
    {
    $validator = Validator::make($request->all(), [
        'Reg_No' => 'required|string|max:255',
        'Model' => 'required|string|max:255',
        'Manufacturer' => 'required|string|max:255',
        'Original_Motor_ID' => 'required|string|max:255',
        'Chassis_Serial_No' => 'required|string|max:255',
        'Purchase_order_ID' => 'required|string|max:255',
        'Warranty_Kilometers' => 'nullable|numeric',
        'Hub' => 'nullable|string|max:255',
        'Client' => 'nullable|string|max:255',
        'Colour' => 'nullable|string|max:255',
        'Asset_In_Use_Date' => 'nullable|date|after_or_equal:today',
        'Deployed_To' => 'nullable|string|max:255',
        'Emp_ID' => 'nullable|string|max:255',
        'Procurement_Lease_Start_Date' => 'nullable|date',
        'Lease_Rental_End_Date' => 'nullable|date',
        'PO_Description' => 'nullable|string|max:255',
        'Registration_Type' => 'nullable|string|max:255',
        'Ownership_Type' => 'nullable|string|max:255',
        'Lease_Value' => 'nullable|numeric',
        'AMS_Location' => 'required|string|max:255',
        'Parking_Location' => 'nullable|string|max:255',
        'Asset_Status' => 'required|string|max:255',
        'Sub_Status' => 'nullable|string|max:255',
        'is_swappable' => 'required|boolean',
        // 'dm_id' => 'required|integer', 
        'Chassis_Serial_No' => 'required|string|max:255',
        'rc_book_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10048',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }
    $insuranceFilePath = null;

    if ($request->hasFile('rc_book_file')) {
        $insuranceFilePath = $this->uploadFile($request->file('rc_book_file'), 'EV/images/rc_book');
    }
    
    AssetMasterVehicle::create([
        'Reg_No' => $request->Reg_No,
        'Model' => $request->Model,
        'Manufacturer' => $request->Manufacturer,
        'Original_Motor_ID' => $request->Original_Motor_ID,
        'Chassis_Serial_No' => $request->Chassis_Serial_No,
        'Purchase_order_ID' => $request->Purchase_order_ID,
        'Warranty_Kilometers' => $request->Warranty_Kilometers,
        'Hub' => $request->Hub,
        'Client' => $request->Client,
        'Colour' => $request->Colour,
        'Asset_In_Use_Date' => $request->Asset_In_Use_Date,
        'Deployed_To' => $request->Deployed_To,
        'Emp_ID' => $request->Emp_ID,
        'Procurement_Lease_Start_Date' => $request->Procurement_Lease_Start_Date,
        'Lease_Rental_End_Date' => $request->Lease_Rental_End_Date,
        'PO_Description' => $request->PO_Description,
        'Registration_Type' => $request->Registration_Type,
        'Ownership_Type' => $request->Ownership_Type,
        'Lease_Value' => $request->Lease_Value,
        'AMS_Location' => $request->AMS_Location,
        'Parking_Location' => $request->Parking_Location,
        'Asset_Status' => $request->Asset_Status,
        'Sub_Status' => $request->Sub_Status,
        'is_swappable' => $request->is_swappable,
        'dm_id' => $request->dm_id ?? null,
        'rc_book' => $insuranceFilePath, // Add uploaded file path here
    ]);


    return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_master_vehicle_list')
        ->with('success', 'Asset Master Vehicle created successfully.');
    }

    public function asset_master_vehicle_update($id, Request $request)
    {
        // Find the existing record by ID
    $assetMasterVehicle = AssetMasterVehicle::find($id);

    // Check if the record exists
    if (!$assetMasterVehicle) {
        return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_master_vehicle_list')
            ->with('error', 'Asset Master Vehicle not found.');
    }

    // Validate the input data
    $validator = Validator::make($request->all(), [
        'Reg_No' => 'required|string|max:255',
        'Model' => 'required|string|max:255',
        'Manufacturer' => 'required|string|max:255',
        'Original_Motor_ID' => 'required|string|max:255',
        'Chassis_Serial_No' => 'required|string|max:255',
        'Purchase_order_ID' => 'required|string|max:255',
        'Warranty_Kilometers' => 'nullable|numeric',
        'Hub' => 'nullable|string|max:255',
        'Client' => 'nullable|string|max:255',
        'Colour' => 'nullable|string|max:255',
        'Asset_In_Use_Date' => 'nullable|date|after_or_equal:today',
        'Deployed_To' => 'nullable|string|max:255',
        'Emp_ID' => 'nullable|string|max:255',
        'Procurement_Lease_Start_Date' => 'nullable|date',
        'Lease_Rental_End_Date' => 'nullable|date',
        'PO_Description' => 'nullable|string|max:255',
        'Registration_Type' => 'nullable|string|max:255',
        'Ownership_Type' => 'nullable|string|max:255',
        'Lease_Value' => 'nullable|numeric',
        'AMS_Location' => 'required|string|max:255',
        'Parking_Location' => 'nullable|string|max:255',
        'Asset_Status' => 'required|string|max:255',
        'Sub_Status' => 'nullable|string|max:255',
        'is_swappable' => 'required|boolean',
        // 'dm_id' => 'required|integer', 
        'Chassis_Serial_No' => 'required|string|max:255',
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

   $insuranceFilePath = null;

    if ($request->hasFile('rc_book_file')) {
        $insuranceFilePath = $this->uploadFiles($request->file('rc_book_file'), 'EV/images/rc_book',$assetMasterVehicle->rc_book);
    }  
    // Update the existing record
    $assetMasterVehicle->update([
        'Reg_No' => $request->Reg_No,
        'Model' => $request->Model,
        'Manufacturer' => $request->Manufacturer,
        'Original_Motor_ID' => $request->Original_Motor_ID,
        'Chassis_Serial_No' => $request->Chassis_Serial_No,
        'Purchase_order_ID' => $request->Purchase_order_ID,
        'Warranty_Kilometers' => $request->Warranty_Kilometers,
        'Hub' => $request->Hub,
        'Client' => $request->Client,
        'Colour' => $request->Colour,
        'Asset_In_Use_Date' => $request->Asset_In_Use_Date,
        'Deployed_To' => $request->Deployed_To,
        'Emp_ID' => $request->Emp_ID,
        'Procurement_Lease_Start_Date' => $request->Procurement_Lease_Start_Date,
        'Lease_Rental_End_Date' => $request->Lease_Rental_End_Date,
        'PO_Description' => $request->PO_Description,
        'Registration_Type' => $request->Registration_Type,
        'Ownership_Type' => $request->Ownership_Type,
        'Lease_Value' => $request->Lease_Value,
        'AMS_Location' => $request->AMS_Location,
        'Parking_Location' => $request->Parking_Location,
        'Asset_Status' => $request->Asset_Status,
        'Sub_Status' => $request->Sub_Status,
        'is_swappable' => $request->is_swappable,
        'dm_id' => $request->dm_id ?? 'null',
        'rc_book' => $insuranceFilePath, // Add uploaded file path here
    ]);

    return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_master_vehicle_list')
        ->with('success', 'Asset Master Vehicle updated successfully.');
    }

    public function asset_master_vehicle_delete($id)
    {
        $AssetMasterVehicle= AssetMasterVehicle::findOrFail($id);
        $AssetMasterVehicle->delete();

        return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_master_vehicle_list')->with('success', 'Asset Master Vehicle deleted successfully.');
    }

    public function asset_master_vehicle_edit($id)
    {
        $delivery_man= Deliveryman::where('approved_status',1)->get();
        $AssetMasterVehicle= AssetMasterVehicle::findOrFail($id);
        $asset_status = AssetStatus::where('status',1)->get();
        return view('assetmaster::asset_master_vehicle.edit', compact('AssetMasterVehicle','delivery_man','asset_status'));
    }

    public function asset_master_vehicle_change_status($id, $status)
    {
        $AssetMasterCharger = AssetMasterCharger::findOrFail($id);
        $AssetMasterCharger->status = $status;
        $AssetMasterCharger->save();

       return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_master_charger_list')->with('success', 'Asset Master Vehicle Status Changed successfully.');
    }
    
    public function asset_master_charger_index()
    {
        $delivery_man= Deliveryman::where('approved_status',1)->get();
        $AssetMasterVehicle = AssetMasterVehicle::all();
        return view('assetmaster::asset_master_charger.index',compact('AssetMasterVehicle','delivery_man'));
    }
    public function asset_master_charger_list(AssetMasterChargerDataTable $dataTable)
    {
        return $dataTable->render('assetmaster::asset_master_charger.list');
    }

    public function asset_master_charger_store(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'AMS_Location' => 'required|string|max:255',
            'PO_ID' => 'required|string|max:255',
            'Invoice_Number' => 'required|string|max:255',
            'Charger_Model' => 'required|string|max:255',
            'Serial_Number' => 'required|string|max:255',
            'Engraved_Serial_Num' => 'required|string|max:255',
            'Sub_status' => 'nullable|string|max:255',
            'In_Use_Date' => 'nullable|date|after_or_equal:today', // Ensure date is today or later
            'Assigned_to' => 'required|string|max:255',
            'Status' => 'required', // Accepts 0 or 1
            'dm_id' => 'required|integer', // Ensures the ID exists in the deliverymen table
            'Chassis_Serial_No' => 'required|string|max:255',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        // Insert the new record
        AssetMasterCharger::create([
            'AMS_Location' => $request->AMS_Location,
            'PO_ID' => $request->PO_ID,
            'Invoice_Number' => $request->Invoice_Number,
            'Charger_Model' => $request->Charger_Model,
            'Serial_Number' => $request->Serial_Number,
            'Engraved_Serial_Num' => $request->Engraved_Serial_Num,
            'Sub_status' => $request->Sub_status,
            'In_Use_Date' => $request->In_Use_Date,
            'Assigned_to' => $request->Assigned_to,
            'Status' => $request->Status,
            'dm_id' => $request->dm_id,
            'Chassis_Serial_No' => $request->Chassis_Serial_No,
        ]);
    
        return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_master_charger_list')
            ->with('success', 'Asset Master Charger created successfully.');
    }

    public function asset_master_charger_update($id, Request $request)
    {
        // Find the existing record by ID
        $assetMasterCharger = AssetMasterCharger::find($id);
    
        // Check if the record exists
        if (!$assetMasterCharger) {
            return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_master_charger_list')
                ->with('error', 'Asset Master Charger not found.');
        }
    
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'AMS_Location' => 'required|string|max:255',
            'PO_ID' => 'required|string|max:255',
            'Invoice_Number' => 'required|string|max:255',
            'Charger_Model' => 'required|string|max:255',
            'Serial_Number' => 'required|string|max:255',
            'Engraved_Serial_Num' => 'required|string|max:255',
            'Sub_status' => 'nullable|string|max:255',
            'In_Use_Date' => 'nullable|date|after_or_equal:today', // Ensure date is today or later
            'Assigned_to' => 'required|string|max:255',
            'Status' => 'required', // Accepts 0 or 1
            'dm_id' => 'required|integer', // Ensures the ID exists in the deliverymen table
            'Chassis_Serial_No' => 'required|string|max:255',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        // Update the existing record
        $assetMasterCharger->update([
            'AMS_Location' => $request->AMS_Location,
            'PO_ID' => $request->PO_ID,
            'Invoice_Number' => $request->Invoice_Number,
            'Charger_Model' => $request->Charger_Model,
            'Serial_Number' => $request->Serial_Number,
            'Engraved_Serial_Num' => $request->Engraved_Serial_Num,
            'Sub_status' => $request->Sub_status,
            'In_Use_Date' => $request->In_Use_Date,
            'Assigned_to' => $request->Assigned_to,
            'Status' => $request->Status,
           'dm_id' => $request->dm_id,
           'Chassis_Serial_No' => $request->Chassis_Serial_No,
        ]);
    
        return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_master_charger_list')
            ->with('success', 'Asset Master Charger updated successfully.');
    }

    public function asset_master_charger_delete($id)
    {
        $AssetMasterCharger = AssetMasterCharger::findOrFail($id);
        $AssetMasterCharger->delete();

        return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_master_charger_list')->with('success', 'AssetMasterCharger deleted successfully.');
    }

    public function asset_master_charger_edit($id)
    {
        $delivery_man= Deliveryman::where('approved_status',1)->get();
        $AssetMasterCharger= AssetMasterCharger::findOrFail($id);
        $AssetMasterVehicle = AssetMasterVehicle::all();
        return view('assetmaster::asset_master_charger.edit', compact('AssetMasterVehicle','AssetMasterCharger','delivery_man'));
    }

    public function asset_master_charger_change_status($id, $status)
    {
        $AssetMasterCharger = AssetMasterCharger::findOrFail($id);
        $AssetMasterCharger->status = $status;
        $AssetMasterCharger->save();

       return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_master_charger_list')->with('success', 'AssetMasterCharger Status Changed successfully.');
    }
    
    public function asset_master_battery_index()
    {
        $delivery_man= Deliveryman::where('approved_status',1)->get();
        $AssetMasterVehicle = AssetMasterVehicle::all();
        return view('assetmaster::asset_master_battery.index',compact('delivery_man','AssetMasterVehicle'));
    }
    public function asset_master_battery_list(AssetMasterBatteryDataTable $dataTable)
    {
        return $dataTable->render('assetmaster::asset_master_battery.list');
    }

    public function asset_master_battery_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'AMS_Location' => 'required|string|max:255',
        'PO_ID' => 'required|string|max:255',
        'Invoice_Number' => 'required|string|max:255',
        'Battery_Model' => 'required|string|max:255',
        'Serial_Number' => 'required|string|max:255',
        'Engraved_Serial_Num' => 'required|string|max:255',
        'Sub_status' => 'required|string|max:255',
        'In_use_Date' => 'nullable|date|after_or_equal:today', // Ensure date is today or later
        'Assigned_To' => 'required|string|max:255',
        'Status' => 'required|boolean', // Accepts 0 or 1
        'dm_id' => 'required|integer', // Ensures the ID exists in the deliverymen table
        'Chassis_Serial_No' => 'required|string|max:255',
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return redirect()->back()
                ->withErrors($validator)
                ->withInput();
    }

    // Create a new record
    $assetMasterBattery = AssetMasterBattery::create([
        'AMS_Location' => $request->AMS_Location,
        'PO_ID' => $request->PO_ID,
        'Invoice_Number' => $request->Invoice_Number,
        'Battery_Model' => $request->Battery_Model,
        'Serial_Number' => $request->Serial_Number,
        'Engraved_Serial_Num' => $request->Engraved_Serial_Num,
        'Sub_status' => $request->Sub_status,
        'In_use_Date' => $request->In_use_Date,
        'Assigned_To' => $request->Assigned_To,
        'Status' => $request->status,
        'dm_id' => $request->dm_id,
        'Chassis_Serial_No' => $request->Chassis_Serial_No,
    ]);

     return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_master_battery_list')->with('success', 'AssetMasterBattery Add successfully.');
    }

    public function asset_master_battery_update($id, Request $request)
    {
        // Find the existing record by ID
        $assetMasterBattery = AssetMasterBattery::find($id);
    
        // Check if the record exists
        if (!$assetMasterBattery) {
            return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_master_battery_list')
                ->with('error', 'Asset Master Battery not found.');
        }
    
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'AMS_Location' => 'required|string|max:255',
            'PO_ID' => 'required|string|max:255',
            'Invoice_Number' => 'required|string|max:255',
            'Battery_Model' => 'required|string|max:255',
            'Serial_Number' => 'required|string|max:255',
            'Engraved_Serial_Num' => 'required|string|max:255',
            'Sub_status' => 'nullable|string|max:255',
            'In_use_Date' => 'nullable|date|after_or_equal:today', // Ensure date is today or later
            'Assigned_To' => 'required|string|max:255',
            'Status' => 'required|boolean', // Accepts 0 or 1
            'dm_id' => 'required', // Ensures the ID exists in the deliverymen table
            'Chassis_Serial_No' => 'required|string|max:255',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        // Update the existing record
        $assetMasterBattery->update([
            'AMS_Location' => $request->AMS_Location,
            'PO_ID' => $request->PO_ID,
            'Invoice_Number' => $request->Invoice_Number,
            'Battery_Model' => $request->Battery_Model,
            'Serial_Number' => $request->Serial_Number,
            'Engraved_Serial_Num' => $request->Engraved_Serial_Num,
            'Sub_status' => $request->Sub_status,
            'In_use_Date' => $request->In_use_Date,
            'Assigned_To' => $request->Assigned_To,
            'Status' => $request->Status,
            'dm_id' => $request->dm_id,
            'Chassis_Serial_No' => $request->Chassis_Serial_No,
        ]);
    
        return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_master_battery_list')
            ->with('success', 'Asset Master Battery updated successfully.');
    }


    public function asset_master_battery_delete($id)
    {
        $AssetMasterBattery = AssetMasterBattery::findOrFail($id);
        $AssetMasterBattery->delete();

        return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_master_battery_list')->with('success', 'AssetMasterBattery deleted successfully.');
    }

    public function asset_master_battery_edit($id)
    {  
        $delivery_man= Deliveryman::where('approved_status',1)->get();
        $AssetMasterBattery= AssetMasterBattery::findOrFail($id);
        $AssetMasterVehicle = AssetMasterVehicle::all();
        return view('assetmaster::asset_master_battery.edit', compact('AssetMasterVehicle','AssetMasterBattery','delivery_man'));
    }

    public function asset_master_battery_change_status($id, $status)
    {
        $AssetMasterBattery = AssetMasterBattery::findOrFail($id);
        $AssetMasterBattery->status = $status;
        $AssetMasterBattery->save();

       return redirect()->route('admin.Green-Drive-Ev.asset-master.asset_master_battery_list')->with('success', 'AssetMasterBattery Status Changed successfully.');
    }
    
    public function uploadFile($file, $directory)
    {
        $imageName = Str::uuid(). '.' . $file->getClientOriginalExtension();
        $file->move(public_path($directory), $imageName);
        return $imageName; // Return the name of the uploaded file
    }
    
    public function uploadFiles($file, $directory,$exist){
       // Check if the profile exists in the details
        if (!empty($exist)) {
            $profilePath = public_path($directory . '/' .$exist);
            
            // If the file exists, delete it
            if (file_exists($profilePath)) {
                unlink($profilePath);
            }
        }
    
        // Upload the new file
        $imageName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($directory), $imageName);
        
        return $imageName; // Return the name of the uploaded file
    }
//     public function importExcel(Request $request)
//     {
//         // Validate the uploaded file
//         $validator = Validator::make($request->all(), [
//             'excel_file' => 'required',
//         ]);

//         if ($validator->fails()) {
//             return back()->withErrors($validator)->withInput();
//         }

//         // Load the file
//         $file = $request->file('excel_file');

//         // Read the Excel file and insert rows into the leads table
//         $data = Excel::toArray([], $file);

//         // The first sheet's data
//         $rows = $data[0]; // Assuming the file has one sheet


//         // Skip the first row if it contains column headers
//         foreach ($rows as $index => $row) {
//             if ($index === 0) continue; // Skip header row
// // dd($row);
// //       exit;
//             // Insert into the database
//             AssetMasterVehicle::create([
//                 'Reg_No'          => $row[0] ?? null,
//                 'Model'            => $row[1] ?? null,
//                 'Manufacturer'            => $row[2] ?? null,
//                 'Original_Motor_ID'      => $row[3] ?? null,
//                 'Chassis_Serial_No'      => $row[4] ?? null,
//                 'Purchase_order_ID'    => $row[5] ?? null,
//                 'Warranty_Kilometers'      => $row[6] ?? null,
//                 'Hub'       => $row[7] ?? null,
//                 'Client'       => $row[8] ?? null,
//                 'Colour'       => $row[9] ?? null,
//                 // 'Asset_In_Use_Date'       => $row[10] ?? null,
//                 'Deployed_To'       => $row[11] ?? null,
//                 'Emp_ID'       => $row[12] ?? null,
//                 // 'Procurement_Lease_Start_Date'       => $row[13] ?? null,
//                 // 'Lease_Rental_End_Date'       => $row[14] ?? null,
//                 'PO_Description'       => $row[15] ?? null,
//                 'Registration_Type'       => $row[16] ?? null,
//                 'Ownership_Type'       => $row[17] ?? null,
//                 'Lease_Value'       => $row[18] ?? null,
//                 'AMS_Location'       => $row[19] ?? null,
//                 'Parking_Location'       => $row[20] ?? null,
//                 'Asset_Status'       => $row[21] ?? null,
//                 'Sub_Status'       => $row[22] ?? null,
//                 'is_swappable'       => $row[23] ?? null,
//             ]);
//         }

//         return redirect()->back()->with('success', ' Asset Imported successfully!');
//     }


    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $file = $request->file('excel_file');

        Excel::import(new AssetMasterVehicleImport, $file);

        return redirect()->back()->with('success', ' Asset Imported successfully!');
    }
    



public function asset_master_list(Request $request)
{
    //  $qc_lists = QualityCheck::all();

    //     foreach ($qc_lists as $qc) {
    //         $location = LocationMaster::find($qc->location);
    //         if ($location) {
    //             $qc->update([
    //                 'location' => $location->city, // replace with city_id
    //             ]);
    //         }
    //     }
        
    //   dd("success");
    
    
     $totalRecords = AssetMasterVehicle::where('delete_status', 0)->where('qc_status', 'pass')->count();
    if ($request->ajax()) {
        try {
            $query = AssetMasterVehicle::with([
                'quality_check' => function($q) {
                    $q->select('id', 'chassis_number', 'vehicle_model', 'vehicle_type', 'battery_number', 'telematics_number', 'updated_at');
                },
                'quality_check.vehicle_model_relation:id,vehicle_model',
                'quality_check.vehicle_type_relation:id,name'
            ])
            ->where('delete_status', 0)
            ->where('qc_status', 'pass');

            // Apply filters
            $status = $request->input('status', 'all');
            $timeline = $request->input('timeline');
            $from_date = $request->input('from_date');
            $to_date = $request->input('to_date');
            $city = $request->input('city');
            $zone_id = $request->input('zone');
            $customer_id = $request->input('customer');
            $accountability_type_id = $request->input('accountability_type');
            $search = $request->input('search.value');
            $start = $request->input('start', 0);
            $length = $request->input('length', 15);
            // dd($status);
            // Status filter
            if (!empty($status) && $status != "all") {
                $query->where('is_status', $status);
            }

            // City filter
            if (!empty($city)) {
                
                 $query->where(function($q) use ($city) {
                    $q->whereHas('quality_check', function($qcQuery) use ($city) {
                        $qcQuery->where('location',$city);
                    });
                });
            }
            
            if (!empty($zone_id)) {
                 $query->where(function($q) use ($zone_id) {
                    $q->whereHas('quality_check', function($qcQuery) use ($zone_id) {
                        $qcQuery->where('zone_id',$zone_id);
                    });
                });
            }
            
            if (!empty($accountability_type_id)) {
                 $query->where(function($q) use ($accountability_type_id) {
                    $q->whereHas('quality_check', function($qcQuery) use ($accountability_type_id) {
                        $qcQuery->where('accountability_type',$accountability_type_id);
                    });
                });
            }
            
            
            // if (!empty($customer_id)) {
            //      $query->where(function($q) use ($customer_id) {
            //         $q->whereHas('quality_check', function($qcQuery) use ($customer_id) {
            //             $qcQuery->where('customer_id',$customer_id);
            //         });
            //     });
            // }
            
            if (!empty($customer_id) && $accountability_type_id == 2) { //updated by Gowtham.s
                $query->whereHas('quality_check', function ($q) use ($customer_id) {
                    $q->where('customer_id', $customer_id);
                });
            }
             if (!empty($customer_id) && $accountability_type_id == 1) { //updated by Gowtham.s
                    $query->where('client', $customer_id);
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
                    $q->whereHas('quality_check', function($qcQuery) use ($search) {
                        $qcQuery->where('id', 'like', "%$search%")
                                ->orwhere('chassis_number', 'like', "%$search%")
                               ->orWhere('battery_number', 'like', "%$search%")
                               ->orWhere('telematics_number', 'like', "%$search%")
                               ->orWhereHas('vehicle_model_relation', function($modelQuery) use ($search) {
                                   $modelQuery->where('vehicle_model', 'like', "%$search%");
                               })
                               ->orWhereHas('vehicle_type_relation', function($typeQuery) use ($search) {
                                   $typeQuery->where('name', 'like', "%$search%");
                               });
                    });
                });
            }

            // Get total records count (before pagination)
            $totalRecords = $query->count();

            // Handle "Show All" option
            if ($length == -1) {
                $length = $totalRecords; // Return all records
            }
            
            dd($query->toSql(),$query->getBindings());

            // Apply pagination and ordering
            $data = $query->orderBy('id', 'desc')
                         ->skip($start)
                         ->take($length)
                         ->get();

            // Format the response
            $formattedData = $data->map(function($item) {
                $id_encode = encrypt($item->id);
                $previewUrl = $item->is_status === "rejected" 
                    ? route('admin.asset_management.asset_master.reupload_vehicle_data', ['id' => $id_encode])
                    : route('admin.asset_management.asset_master.view_asset_master', ['id' => $id_encode]);
                
                $statusBadge = $this->getStatusBadge($item->is_status);
                
                $lastQCDate = $item->quality_check->updated_at 
                    ? $item->quality_check->updated_at->format('d M Y') 
                    : 'N/A';
                $lastQCTime = $item->quality_check->updated_at 
                    ? $item->quality_check->updated_at->format('h:i:s A') 
                    : 'N/A';

                return [
                    'checkbox' => '<div class="form-check"><input class="form-check-input sr_checkbox" style="width:25px; height:25px;" name="is_select[]" type="checkbox" value="'.$item->id.'"></div>',
                    'qc_id' => $item->qc_id ?? 'N/A',
                    'chassis_number' => $item->quality_check->chassis_number ?? 'N/A',
                    'vehicle_model' => $item->quality_check->vehicle_model_relation->vehicle_model ?? 'N/A',
                    'vehicle_type' => $item->quality_check->vehicle_type_relation->name ?? 'N/A',
                    'battery_number' => $item->quality_check->battery_number ?? 'N/A',
                    'telematics_number' => $item->quality_check->telematics_number ?? 'N/A',
                    'last_qc' => '<div>'.$lastQCDate.'</div><div><small>'.$lastQCTime.'</small></div>',
                    'status' => $statusBadge,
                    'action' => '<div class="dropdown">
                        <button type="button" class="btn btn-sm dropdown-toggle custom-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a href="'.$previewUrl.'" class="dropdown-item d-flex align-items-center justify-content-center">
                                    <i class="bi bi-eye me-2 fs-5"></i> Preview
                                </a>
                            </li>'.
                            ($item->is_status != 'accepted' ? 
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
            \Log::error('Asset Master List Error: '.$e->getMessage());
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
    $locations = City::where('status', 1)
        ->select('id', 'city_name')
        ->get();
        
    $accountablity_types = EvTblAccountabilityType::where('status', 1)->get();
    $customers = CustomerMaster::where('status',1)->get();

    return view('assetmaster::asset_master.asset_master_list', [
        'lists' => collect(),
        'status' => $request->status ?? 'all',
        'from_date' => $request->from_date ?? '',
        'to_date' => $request->to_date ?? '',
        'timeline' => $request->timeline ?? '',
        'customers' => $customers ,
        'accountablity_types' => $accountablity_types ,
        'locations' => $locations,
        'city' => $request->city ?? '',
        'zone_id'  => $request->zone,
        'customer_id' => $request->customer ,
        'accountability_type' => $request->accountability_type,
        'totalRecords' => $totalRecords
    ]);
}

    private function getStatusBadge($status)
    {
        switch(strtolower($status)) {
            case 'pending': 
                return '<i class="bi bi-circle-fill" style="color:#ffd52c;"></i> Pending Asset';
            case 'uploaded': 
                return '<i class="bi bi-circle-fill" style="color:#1661c7;"></i> Asset Uploaded';
            case 'accepted': 
                return '<i class="bi bi-circle-fill" style="color:#72cf72;"></i> Asset Accepted';
            case 'rejected': 
                return '<i class="bi bi-circle-fill" style="color:#ff2c2c;"></i> Asset Rejected';
            default: 
                return 'N/A';
        }
    }


    
    
        
    public function add_vehicle(Request $request){
        
        
        $vehicle_types = VehicleType::where('is_active', 1)->get();
        $vehicle_models = VehicleModelMaster::where('status', 1)->get();
        
        $locations = City::where('status', 1)
        ->select('id', 'city_name')
        ->get();
        $passed_chassis_numbers = AssetMasterVehicle::where('qc_status','pass')->where('is_status','pending')->get();
        $financing_types = FinancingTypeMaster::where('status',1)->get();
        $asset_ownerships = AssetOwnershipMaster::where('status',1)->get();
        $insurer_names = InsurerNameMaster::where('status',1)->get();
        $insurance_types = InsuranceTypeMaster::where('status',1)->get();
        $hypothecations = HypothecationMaster::where('status',1)->get();
        $registration_types = RegistrationTypeMaster::where('status',1)->get();
        $telematics = TelemetricOEMMaster::where('status',1)->get();
        $inventory_locations = InventoryLocationMaster::where('status',1)->get();
         $colors = ColorMaster::where('status',1)->get();
        $customers = CustomerMaster::where('status',1)->get();


        return view('assetmaster::asset_master.create_vehicle',compact('vehicle_types','locations','passed_chassis_numbers' ,'financing_types' ,'asset_ownerships' ,'insurer_names' ,'insurance_types' ,'hypothecations' ,'registration_types' , 'vehicle_models' ,'telematics' ,'inventory_locations' ,'colors' , 'customers'));
    }
    
    
    
    public function store_vehicle(Request $request)
    {
        
            $user     = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    
        $validator = Validator::make($request->all(), [
        'chassis_number' => 'required|string|unique:ev_tbl_asset_master_vehicles,chassis_number',
        'vehicle_category' => 'string',
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
        'telematics_serial_no' => 'required|string',
        'vehicle_status' => 'string',
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
         'zone_id' => 'required',

        // File validations
        'master_lease_agreement' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        'insurance_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        'reg_certificate_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        'fc_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        'hypothecation_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        'temporary_certificate_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        'hsrp_certificate_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
    ],
    [
       
        'zone_id.required' => 'Please select a Zone. Zone field is mandatory.',
        'city_code.required' => 'Please select a City. City field is mandatory.',
    ]);

    if ($validator->fails()) {
        $errorsText = implode(', ', $validator->errors()->all());
        
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Asset Master Upload Failed (Validation)',
            'long_description'  => "Validation errors: {$errorsText}",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'asset_master.store_vehicle',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors()
        ], 422);
    }
    
        DB::beginTransaction();
        try {
            $exist_vehicle_update = AssetMasterVehicle::where('id', $request->chassis_number)
                ->where('is_status', 'uploaded')
                ->first();

            if ($exist_vehicle_update) {
                     audit_log_after_commit([
                    'module_id'         => 4,
                    'short_description' => 'Asset Master Upload Blocked (Already Uploaded)',
                    'long_description'  => "Vehicle Asset already uploaded for chassis: {$request->chassis_number}.",
                    'role'              => $roleName,
                    'user_id'           => Auth::id(),
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'asset_master.store_vehicle',
                    'ip_address'        => $request->ip(),
                    'user_device'       => $request->userAgent()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle Asset already Uploaded',
                ]);
            }
            $vehicle_update = AssetMasterVehicle::where('id', $request->chassis_number)
                ->where('is_status', 'pending')
                ->first();
            
            if (!$vehicle_update) {
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'Asset Master Upload Failed (Pending Not Found)',
                'long_description'  => "Pending Asset Master record not found for chassis: {$request->chassis_number}.",
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'asset_master.store_vehicle',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Pending vehicle record not found.',
            ], 404);
        }   
                
            $oldValues = $vehicle_update->getOriginal(); 
    
            $QC_PassData = QualityCheck::where('chassis_number', $vehicle_update->chassis_number)
                ->where('status', 'pass')
                ->first();

            if (!$QC_PassData) {
                audit_log_after_commit([
                    'module_id'         => 4,
                    'short_description' => 'Asset Master Upload Failed (QC Not Eligible)',
                    'long_description'  => "QC pass not found for chassis: {$vehicle_update->chassis_number}.",
                    'role'              => $roleName,
                    'user_id'           => Auth::id(),
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'asset_master.store_vehicle',
                    'ip_address'        => $request->ip(),
                    'user_device'       => $request->userAgent()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Quality check this Vehicle Failed Status. Vehicle not eligible.',
                ]);
            }
    
            // Handle file uploads
            if ($request->hasFile('tax_invoice_attachment')) {
                $vehicle_update->tax_invoice_attachment = CustomHandler::uploadFileImage(
                    $request->file('tax_invoice_attachment'),
                    'EV/asset_master/tax_invoice_attachments'
                );
            }
    
            if ($request->hasFile('master_lease_agreement')) {
                $vehicle_update->master_lease_agreement = CustomHandler::uploadFileImage(
                    $request->file('master_lease_agreement'),
                    'EV/asset_master/master_lease_agreements'
                );
            }
    
            if ($request->hasFile('insurance_attachment')) {
                $vehicle_update->insurance_attachment = CustomHandler::uploadFileImage(
                    $request->file('insurance_attachment'),
                    'EV/asset_master/insurance_attachments'
                );
            }
    
            if ($request->hasFile('reg_certificate_attachment')) {
                $vehicle_update->reg_certificate_attachment = CustomHandler::uploadFileImage(
                    $request->file('reg_certificate_attachment'),
                    'EV/asset_master/reg_certificate_attachments'
                );
            }
    
            if ($request->hasFile('fc_attachment')) {
                $vehicle_update->fc_attachment = CustomHandler::uploadFileImage(
                    $request->file('fc_attachment'),
                    'EV/asset_master/fc_attachments'
                );
            }
            
            if ($request->hasFile('hypothecation_document')) {
                $vehicle_update->hypothecation_document = CustomHandler::uploadFileImage(
                    $request->file('hypothecation_document'),
                    'EV/asset_master/hypothecation_documents'
                );
            }
            
            if ($request->hasFile('temporary_certificate_attachment')) {
                $vehicle_update->temproary_reg_attachment = CustomHandler::uploadFileImage(
                    $request->file('temporary_certificate_attachment'),
                    'EV/asset_master/temporary_certificate_attachments'
                );
            }
            
            
            if ($request->hasFile('hsrp_certificate_attachment')) {
                $vehicle_update->hsrp_copy_attachment = CustomHandler::uploadFileImage(
                    $request->file('hsrp_certificate_attachment'),
                    'EV/asset_master/hsrp_certificate_attachments'
                );
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
                'location' => $request->city_code,
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
                'city_code' => $request->city_code,
                
                'is_status' => 'uploaded',
                'created_by'=>auth()->id()
            ]);
            
    
            $changes = array_diff_assoc($vehicle_update->getDirty(), $oldValues);
            
            
            $vehicle_update->save();
             
            
            $quality_check = QualityCheck::where('chassis_number', $vehicle_update->chassis_number)
                ->where('status', 'pass')
                ->first();
            
            
            $this->handleLogsAndQcUpdate($vehicle_update, $changes , $request, $oldValues, $quality_check);
            
            
            // Log
            $remarks = 'The Asset Master Vehicle Chassis Number ' . $vehicle_update->chassis_number . ' has been Uploaded';
    
            AssetMasterVehicleLogHistory::create([
                'asset_vehicle_id' => $vehicle_update->id,
                'user_id' => auth()->id(),
                'remarks' => $remarks,
                'status_type' => 'uploaded',
            ]);
            

            DB::commit();
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'Asset Master Upload Completed',
                'long_description'  => "Asset Master uploaded successfully for chassis: {$vehicle_update->chassis_number}.",
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'asset_master.store_vehicle',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
            return response()->json([
                'success' => true,
                'message' => 'The Asset Master Vehicle Uploaded Successfully!',
                'data' => $vehicle_update,
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Asset Master Upload Failed (Exception)',
            'long_description'  => 'Error: ' . substr($e->getMessage(), 0, 1000),
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'asset_master.store_vehicle',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
        
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong! ' . $e->getMessage(),
            ]);
        }

    }
    



    
    public function update_data(Request $request)
    {
        
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    
        // ✅ Log: Update Initiated
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Asset Master Update Initiated',
            'long_description'  => "User started updating Asset Master Vehicle (ID: {$request->id}).",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'asset_master.update_data',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
    
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
        'city_code' => 'required',
        'zone_id' => 'required',
         'telematics_oem' => 'nullable|string',

        // File validations
        'master_lease_agreement' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        'insurance_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        'reg_certificate_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        'fc_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        'hypothecation_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        'temporary_certificate_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        'hsrp_certificate_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
    ],
    [
       
        'zone_id.required' => 'Please select a Zone. Zone field is mandatory.',
        'city_code.required' => 'Please select a City. City field is mandatory.',
    ]);

    if ($validator->fails()) {
        $errorsText = implode(', ', $validator->errors()->all());

        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Asset Master Update Failed (Validation)',
            'long_description'  => "Validation errors for ID {$request->id}: {$errorsText}",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'asset_master.update_data',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors()
        ], 422);
    }
    
        DB::beginTransaction();
        try {
            $exist_vehicle_update = AssetMasterVehicle::where('id', $request->id)
                ->where('is_status', 'uploaded')
                ->first();

            if ($exist_vehicle_update) {
                audit_log_after_commit([
                    'module_id'         => 4,
                    'short_description' => 'Asset Master Update Blocked (Already Uploaded)',
                    'long_description'  => "Record already uploaded for ID: {$request->id}.",
                    'role'              => $roleName,
                    'user_id'           => Auth::id(),
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'asset_master.update_data',
                    'ip_address'        => $request->ip(),
                    'user_device'       => $request->userAgent()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle Asset already Uploaded',
                ]);
            }
            $vehicle_update = AssetMasterVehicle::where('id', $request->id)
                ->where('is_status', 'pending')
                ->first();
            
            if (!$vehicle_update) {
                audit_log_after_commit([
                    'module_id'         => 4,
                    'short_description' => 'Asset Master Update Failed (Pending Not Found)',
                    'long_description'  => "Pending record not found for ID: {$request->id}.",
                    'role'              => $roleName,
                    'user_id'           => Auth::id(),
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'asset_master.update_data',
                    'ip_address'        => $request->ip(),
                    'user_device'       => $request->userAgent()
                ]);
    
                return response()->json([
                    'success' => false,
                    'message' => 'Pending vehicle record not found.',
                ], 404);
            }
        
             $oldValues = $vehicle_update->getOriginal(); 
    
            $QC_PassData = QualityCheck::where('chassis_number', $vehicle_update->chassis_number)
                ->where('status', 'pass')
                ->first();

            if (!$QC_PassData) {
                audit_log_after_commit([
                    'module_id'         => 4,
                    'short_description' => 'Asset Master Update Failed (QC Not Eligible)',
                    'long_description'  => "QC pass not found for chassis: {$vehicle_update->chassis_number}.",
                    'role'              => $roleName,
                    'user_id'           => Auth::id(),
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'asset_master.update_data',
                    'ip_address'        => $request->ip(),
                    'user_device'       => $request->userAgent()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Quality check this Vehicle Failed Status. Vehicle not eligible.',
                ]);
            }
    
            // Handle file uploads
            if ($request->hasFile('tax_invoice_attachment')) {
                $vehicle_update->tax_invoice_attachment = CustomHandler::uploadFileImage(
                    $request->file('tax_invoice_attachment'),
                    'EV/asset_master/tax_invoice_attachments'
                );
            }
    
            if ($request->hasFile('master_lease_agreement')) {
                $vehicle_update->master_lease_agreement = CustomHandler::uploadFileImage(
                    $request->file('master_lease_agreement'),
                    'EV/asset_master/master_lease_agreements'
                );
            }
    
            if ($request->hasFile('insurance_attachment')) {
                $vehicle_update->insurance_attachment = CustomHandler::uploadFileImage(
                    $request->file('insurance_attachment'),
                    'EV/asset_master/insurance_attachments'
                );
            }
    
            if ($request->hasFile('reg_certificate_attachment')) {
                $vehicle_update->reg_certificate_attachment = CustomHandler::uploadFileImage(
                    $request->file('reg_certificate_attachment'),
                    'EV/asset_master/reg_certificate_attachments'
                );
            }
    
            if ($request->hasFile('fc_attachment')) {
                $vehicle_update->fc_attachment = CustomHandler::uploadFileImage(
                    $request->file('fc_attachment'),
                    'EV/asset_master/fc_attachments'
                );
            }
            
            if ($request->hasFile('hypothecation_document')) {
                $vehicle_update->hypothecation_document = CustomHandler::uploadFileImage(
                    $request->file('hypothecation_document'),
                    'EV/asset_master/hypothecation_documents'
                );
            }
            
            if ($request->hasFile('temporary_certificate_attachment')) {
                $vehicle_update->temproary_reg_attachment = CustomHandler::uploadFileImage(
                    $request->file('temporary_certificate_attachment'),
                    'EV/asset_master/temporary_certificate_attachments'
                );
            }
            
            
            if ($request->hasFile('hsrp_certificate_attachment')) {
                $vehicle_update->hsrp_copy_attachment = CustomHandler::uploadFileImage(
                    $request->file('hsrp_certificate_attachment'),
                    'EV/asset_master/hsrp_certificate_attachments'
                );
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
                'location' => $request->city_code,
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
                'city_code' => $request->city_code,
                
                'is_status' => 'uploaded',
                'created_by'=>auth()->id()
            ]);
            
            $changes = array_diff_assoc($vehicle_update->getDirty(), $oldValues);
            $vehicle_update->save();
            
            
            $quality_check = QualityCheck::where('chassis_number', $vehicle_update->chassis_number)
                ->where('status', 'pass')
                ->first();
                
                
            $this->handleLogsAndQcUpdate($vehicle_update, $changes , $request, $oldValues, $quality_check);
    
            // Log
            $remarks = 'The Asset Master Vehicle Chassis Number ' . $vehicle_update->chassis_number . ' has been Uploaded';
    
            AssetMasterVehicleLogHistory::create([
                'asset_vehicle_id' => $vehicle_update->id,
                'user_id' => auth()->id(),
                'remarks' => $remarks,
                'status_type' => 'uploaded',
            ]);
    
    

            DB::commit();
            
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'Asset Master Update Completed',
                'long_description'  => "Asset Master updated successfully for chassis: {$vehicle_update->chassis_number} (ID: {$request->id}).",
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'asset_master.update_data',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
        
            return response()->json([
                'success' => true,
                'message' => 'The Asset Master Vehicle Updated Successfully!',
                'data' => $vehicle_update,
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'Asset Master Update Failed (Exception)',
                'long_description'  => 'Error: ' . substr($e->getMessage(), 0, 1000),
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'asset_master.update_data',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
        
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong! ' . $e->getMessage(),
            ]);
        }

    }
    


/**
 * Handle Asset Master and QC log updates after saving vehicle data.
 */
private function handleLogsAndQcUpdate($vehicle_update,$changes ,  $request, $oldValues, $quality_check)
{
                
    if (!empty($changes)) {

        $fieldLabels = [
            'chassis_number' => 'Chassis Number',
            'vehicle_category' => 'Vehicle Category',
            // 'vehicle_type' => 'Vehicle Type',
            // 'make' => 'Make',
            // 'model' => 'Model',
            // 'variant' => 'Variant',
            'color' => 'Color',
            // 'client' => 'Client',
            // 'motor_number' => 'Motor Number',
            'vehicle_id' => 'Vehicle ID',
            'tax_invoice_number' => 'Tax Invoice Number',
            'tax_invoice_date' => 'Tax Invoice Date',
            'tax_invoice_value' => 'Tax Invoice Value',
            // 'location' => 'Location',
            'gd_hub_name' => 'GD Hub Name',
            'gd_hub_id' => 'GD Hub ID',
            'financing_type' => 'Financing Type',
            'asset_ownership' => 'Asset Ownership',
            'lease_start_date' => 'Lease Start Date',
            'lease_end_date' => 'Lease End Date',
            'vehicle_delivery_date' => 'Vehicle Delivery Date',
            'emi_lease_amount' => 'EMI Lease Amount',
            'hypothecation' => 'Hypothecation',
            'hypothecation_to' => 'Hypothecation To',
            'insurer_name' => 'Insurer Name',
            'insurance_type' => 'Insurance Type',
            'insurance_number' => 'Insurance Number',
            'insurance_start_date' => 'Insurance Start Date',
            'insurance_expiry_date' => 'Insurance Expiry Date',
            'registration_type' => 'Registration Type',
            'registration_status' => 'Registration Status',
            'permanent_reg_number' => 'Permanent Registration Number',
            'permanent_reg_date' => 'Permanent Registration Date',
            'reg_certificate_expiry_date' => 'Registration Certificate Expiry Date',
            'fc_expiry_date' => 'FC Expiry Date',
            'battery_type' => 'Battery Type',
            'battery_variant_name' => 'Battery Variant Name',
            // 'battery_serial_no' => 'Battery Serial Number',
            'charger_variant_name' => 'Charger Variant Name',
            'charger_serial_no' => 'Charger Serial Number',
            'telematics_variant_name' => 'Telematics Variant Name',
            // 'telematics_serial_no' => 'Telematics Serial Number',
            'vehicle_status' => 'Vehicle Status',
            'temproary_reg_number' => 'Temporary Registration Number',
            'temproary_reg_date' => 'Temporary Registration Date',
            'temproary_reg_expiry_date' => 'Temporary Registration Expiry Date',
            'servicing_dates' => 'Servicing Dates',
            'road_tax_applicable' => 'Road Tax Applicable',
            'road_tax_amount' => 'Road Tax Amount',
            'road_tax_renewal_frequency' => 'Road Tax Renewal Frequency',
            'road_tax_next_renewal_date' => 'Next Road Tax Renewal Date',
            'battery_serial_number1' => 'Battery Serial Number 1',
            'battery_serial_number2' => 'Battery Serial Number 2',
            'battery_serial_number3' => 'Battery Serial Number 3',
            'battery_serial_number4' => 'Battery Serial Number 4',
            'battery_serial_number5' => 'Battery Serial Number 5',
            'charger_serial_number1' => 'Charger Serial Number 1',
            'charger_serial_number2' => 'Charger Serial Number 2',
            'charger_serial_number3' => 'Charger Serial Number 3',
            'charger_serial_number4' => 'Charger Serial Number 4',
            'charger_serial_number5' => 'Charger Serial Number 5',
            'telematics_oem' => 'Telematics OEM',
            'telematics_imei_number' => 'Telematics IMEI Number',
            'telematics_serial_number1' => 'Telematics Serial Number 1',
            'telematics_serial_number2' => 'Telematics Serial Number 2',
            'telematics_serial_number3' => 'Telematics Serial Number 3',
            'telematics_serial_number4' => 'Telematics Serial Number 4',
            'telematics_serial_number5' => 'Telematics Serial Number 5',
            // 'city_code' => 'City',
        ];
    
    
        $attachmentFields = [
            'tax_invoice_attachment'     => 'Tax Invoice Attachment',
            'master_lease_agreement'     => 'Master Lease Agreement',
            'insurance_attachment'       => 'Insurance Attachment',
            'reg_certificate_attachment' => 'Registration Certificate Attachment',
            'fc_attachment'              => 'FC Attachment',
            'hypothecation_document'     => 'Hypothecation Document',
            'temproary_reg_attachment'   => 'Temporary Registration Attachment',
            'hsrp_copy_attachment'       => 'HSRP Certificate Attachment',
        ];


        // Only include changed fields that exist in label map
        $updatedReadable = [];
        foreach ($changes as $key => $value) {
            if (isset($fieldLabels[$key])) {
                $updatedReadable[] = $fieldLabels[$key];
            }
        }
        
        foreach ($attachmentFields as $field => $label) {
            if ($request->hasFile($field)) {
                $updatedReadable[] = $label;
            }
        }
    
    
    if ($quality_check) {
            $qcFieldMap = [
                'vehicle_type' => 'Vehicle Type',
                'vehicle_model' => 'Model',
                'motor_number' => 'Motor Number',
                'battery_number' => 'Battery Number',
                'telematics_number' => 'Telematics Number',
                'location' => 'City',
                'customer_id' => 'Customer',
                'zone_id' => 'Zone',
            ];
            
    
            foreach ($qcFieldMap as $qcField => $label) {
                $reqField = match ($qcField) {
                    'vehicle_model' => 'model',
                    'battery_number' => 'battery_serial_no',
                    'telematics_number' => 'telematics_serial_no',
                    'location' => 'city_code',
                    'customer_id' => 'client',
                    'zone_id' => 'zone_id', 
                    default => $qcField,
                };
    
            $newValue = $request->$reqField ?? null;

            if ($qcField === 'zone_id') {
                if ($quality_check->zone_id != $request->zone_id) {
                    $updatedReadable[] = $label;
                }
            } elseif ($quality_check->$qcField != $newValue) {
                $updatedReadable[] = $label;
            }
            }
        }


        if (!empty($updatedReadable)) {
            $updatedText = implode(', ', $updatedReadable);
            $remarks = "The following Asset Master fields have been updated: {$updatedText}. These updates were applied successfully.";
    
            AssetMasterVehicleLogHistory::create([
                'asset_vehicle_id' => $vehicle_update->id,
                'user_id' => auth()->id(),
                'remarks' => $remarks,
                'status_type' => 'updated',
            ]);
        }
    }
    

    
    $updatedFields = [];
    $qcUpdates = [];
    
    if ($quality_check) {
        if ($quality_check->vehicle_type != $request->vehicle_type) {
            $updatedFields[] = 'Vehicle Type';
            $qcUpdates['vehicle_type'] = $request->vehicle_type;
        }
        if ($quality_check->vehicle_model != $request->model) {
            $updatedFields[] = 'Model';
            $qcUpdates['vehicle_model'] = $request->model;
        }
        if ($quality_check->motor_number != $request->motor_number) {
            $updatedFields[] = 'Motor Number';
            $qcUpdates['motor_number'] = $request->motor_number;
        }
        if ($quality_check->battery_number != $request->battery_serial_no) {
            $updatedFields[] = 'Battery Number';
            $qcUpdates['battery_number'] = $request->battery_serial_no;
        }
        if ($quality_check->telematics_number != $request->telematics_serial_no) {
            $updatedFields[] = 'Telematics Number';
            $qcUpdates['telematics_number'] = $request->telematics_serial_no;
        }
        if ($quality_check->location != $request->city_code) {
            $updatedFields[] = 'City';
            $qcUpdates['location'] = $request->city_code;
        }
        if ($quality_check->zone_id != $request->zone_id) {
            $updatedFields[] = 'Zone';
            $qcUpdates['zone_id'] = $request->zone_id;
        }
        
        if ($quality_check->accountability_type == 2 && $quality_check->customer_id != $request->client) {
            $updatedFields[] = 'Customer';
            $qcUpdates['customer_id'] = $request->client;
        }
    }
    
    
    if (!empty($updatedFields)) {

        $quality_check->update($qcUpdates);
    
        
        $defaultRemark = "The following QC details were updated in Asset Master";
        
        $remarks = "1) {$defaultRemark}\n2) Updated Fields: " . implode(', ', $updatedFields);
    
        QualityCheckReinitiate::create([
            'qc_id' => $quality_check->id ?? null,
            'status' => "updated",
            'remarks' => $remarks,
            'initiated_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    
}




    public function vehicle_bulk_upload_form_import(Request $request)
    {
     
         $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find(optional($user)->role))->name ?? 'Unknown';
    
        // ✅ Initiated
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Vehicle Bulk Import Initiated',
            'long_description'  => 'Asset Master Vehicles bulk upload form import has been initiated.',
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'asset_master.vehicle_bulk_upload_form_import',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
    
        if ($request->hasFile('asset_vehicle_excel_file')) {
            $ext = strtolower($request->file('asset_vehicle_excel_file')->getClientOriginalExtension());
        
            if (!in_array($ext, ['xlsx', 'xls'])) {
                
                audit_log_after_commit([
                    'module_id'         => 4,
                    'short_description' => 'Vehicle Bulk Import Failed (Validation)',
                    'long_description'  => 'Invalid file type for bulk import. Only xlsx/xls allowed.',
                    'role'              => $roleName,
                    'user_id'           => Auth::id(),
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'asset_master.vehicle_bulk_upload_form_import',
                    'ip_address'        => $request->ip(),
                    'user_device'       => $request->userAgent()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => [
                        'asset_vehicle_excel_file' => [
                            'The asset vehicle excel file field must be a file of type: xlsx, xls.'
                        ]
                    ]
                ], 422);
            }
        }
    
    
        $validator = Validator::make($request->all(), [
            'asset_vehicle_excel_file' => 'required|file',
        ]);
        
        try {
        $file = $request->file('asset_vehicle_excel_file');
        $excelPath = $file->getPathname();
    
        $saveRoot = public_path('EV/asset_master');
        if (!file_exists($saveRoot)) {
            mkdir($saveRoot, 0777, true);
        }
    
        $spreadsheet = IOFactory::load($excelPath);
        $sheet = $spreadsheet->getActiveSheet();
    
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
    
        $headerMap = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $cellValue = $sheet->getCell($colLetter . '1')->getValue();
            $headerName = Str::snake(trim($cellValue));
            if ($headerName) {
                $headerMap[$colLetter] = $headerName;
            }
        }
       
        
        
            $columnMap = [
                "A" => "chassis_number",
                "B" => "vehicle_category",
                "C" => "vehicle_type",
                "D" => "model",
                "E" => "make",
                "F" => "variant",
                "G" => "color",
                "H" => "motor_number",
                "I" => "vehicle_id",
                "J" => "tax_invoice_number",
                "K" => "tax_invoice_date",
                "L" => "tax_invoice_value",
                "M" => "tax_invoice_attachment",
                "N" => "city",
                "O" => "gd_hub_name",
                "P" => "gd_hub_id",
                "Q" => "financing_type",
                "R" => "asset_ownership",
                "S" => "master_lease_agreement",
                "T" => "lease_start_date",
                "U" => "lease_end_date",
                "V" => "emi_lease_amount",
                "W" => "hypothecation",
                "X" => "hypothecation_to",
                "Y" => "hypothecation_document",
                "Z" => "insurer_name",
                "AA" => "insurance_type",
                "AB" => "insurance_number",
                "AC" => "insurance_start_date",
                "AD" => "insurance_expiry_date",
                "AE" => "insurance_attachment",
                "AF" => "registration_type",
                
                "AG" => "temproary_reg_number",
                "AH" => "temproary_reg_date",
                "AI" => "temproary_reg_expiry_date",
                "AJ" => "temproary_reg_attachment",
                "AK" => "permanent_reg_number",
                "AL" => "permanent_reg_date",
                "AM" => "reg_certificate_expiry_date",
                "AN" => "hsrp_copy_attachment",
                "AO" => "reg_certificate_attachment",
                "AP" => "fc_expiry_date",
                "AQ" => "fc_attachment",
                "AR" => "servicing_dates",
                "AS" => "road_tax_applicable",
                "AT" => "road_tax_amount",
                "AU" => "road_tax_renewal_frequency",
                "AV" => "road_tax_next_renewal_date",
                "AW" => "battery_type",
                "AX" => "battery_serial_no",
                "AY" => "battery_serial_number1",
                "AZ" => "battery_serial_number2",
                "BA" => "battery_serial_number3",
                "BB" => "battery_serial_number4",
                "BC" => "battery_serial_number5",
                "BD" => "charger_variant_name",
                "BE" => "charger_serial_no",
                "BF" => "charger_serial_number1",
                "BG" => "charger_serial_number2",
                "BH" => "charger_serial_number3",
                "BI" => "charger_serial_number4",
                "BJ" => "charger_serial_number5",
                "BK" => "telematics_variant_name",
                "BL" => "telematics_oem",
                "BM" => "telematics_serial_no",
                "BN" => "telematics_imei_number",
                "BO" => "telematics_serial_number1",
                "BP" => "telematics_serial_number2",
                "BQ" => "telematics_serial_number3",
                "BR" => "telematics_serial_number4",
                "BS" => "telematics_serial_number5",
                "BT" => "client",
                "BU" => "vehicle_delivery_date",
                "BV" => "vehicle_status",
                "BW" => "accountability_type",
                "BX" => "zone",
            ];
        
    
    
        $sheetArray = $sheet->toArray(null, true, true, true);
        $headerRow = $sheetArray[1];
        
        $missingColumns = [];
        foreach (array_keys($columnMap) as $expectedColLetter) {
            if (!array_key_exists($expectedColLetter, $headerRow)) {
                $missingColumns[] = $expectedColLetter;
            }
        }
        
        if (!empty($missingColumns)) {
            return response()->json([
                'success' => false,
                'message' => 'Some required columns are missing from your Excel file. Please download the latest demo Excel template by clicking the Download Demo Excel button at the top. If you are importing or exporting data, please ensure that all required columns are selected to avoid any issues.'
            ]);
        }
    
    
    
        $successCount = 0;
        $skippedCount = 0;
    
        $attachmentFields = [
            'tax_invoice_attachment'       => 'tax_invoice_attachments',
            'master_lease_agreement'       => 'master_lease_agreements',
            'insurance_attachment'         => 'insurance_attachments',
            'reg_certificate_attachment'   => 'reg_certificate_attachments',
            'hypothecation_document'     => 'hypothecation_documents',
            'temproary_reg_attachment' => 'temporary_certificate_attachments',
            'hsrp_copy_attachment'                => 'hsrp_certificate_attachments',
            'fc_attachment'   => 'fc_attachments'
        ];
        
            $attachmentvalues = [
            'tax_invoice_attachment',
            'master_lease_agreement',
            'insurance_attachment',
            'reg_certificate_attachment',
            'hypothecation_document',
            'temproary_reg_attachment',
            'hsrp_copy_attachment',
            'fc_attachment'
        ];
    
    
        $updatedChassisNumbers = [];
        $errorRows =[];
    
        foreach ($sheet->toArray(null, true, true, true) as $index => $row) {
            if ($index == 1) continue;
    
            $data = [];
    
            foreach ($columnMap as $colLetter => $field) {
                $value = isset($row[$colLetter]) ? trim($row[$colLetter]) : null;
    
                if (in_array($field, $attachmentvalues)) {
                    continue;
                }
    
                if (array_key_exists($field, $attachmentFields)) {
                    $subFolder = $attachmentFields[$field];
                    $filePath = $saveRoot . '/' . $subFolder . '/' . $value;
    
                    if (!empty($value)) {
                        if (file_exists($filePath)) {
                            $data[$field] = 'EV/asset_master/' . $subFolder . '/' . $value;
                            Log::info("PDF found for $field: $filePath");
                        } else {
                            $data[$field] = null;
                            Log::warning("PDF NOT FOUND for $field in Row $index: $filePath");
                        }
                    } else {
                        $data[$field] = null;
                    }
                } else {
                    $data[$field] = $value;
                }
            }
            
            
    
            // if (empty($data['chassis_number'])) {
            //     $skippedCount++;
            //     continue;
            // }
            
            
              $vehicleType = VehicleType::whereRaw('LOWER(name) = ?', [strtolower(trim($data['vehicle_type']))])->first();
              $data['vehicle_type'] = $vehicleType ? $vehicleType->id : null;
              
              $vehicleModel = VehicleModelMaster::whereRaw('LOWER(vehicle_model) = ?', [strtolower(trim($data['model']))])->first();
              $data['model'] = $vehicleModel ? $vehicleModel->id : null;
              
              $LocationMaster = City::whereRaw('LOWER(city_name) = ?', [strtolower(trim($data['city']))])->first();
              $data['city_code'] = $LocationMaster ? $LocationMaster->id : null;
              
              $ZoneMaster = Zones::whereRaw('LOWER(name) = ?', [strtolower(trim($data['zone']))])->first();
              $qc_data['zone_id'] = $ZoneMaster ? $ZoneMaster->id : null;
              
              
            
            $inputType = strtolower(trim($data['accountability_type']));
            $AccountabilityTypeMaster = EvTblAccountabilityType::whereRaw('LOWER(name)=?', [$inputType])->first() 
                ?? collect(DB::select("SELECT id FROM ev_tbl_accountability_types WHERE TRIM(LOWER(name))=? LIMIT 1", [$inputType]))->first();
            $qc_data['accountability_type'] = $AccountabilityTypeMaster->id ?? null;
    
              
              $cleanFinancingType = preg_replace('/[^a-zA-Z0-9\s]/', '', $data['financing_type']);
              $cleanFinancingType = trim($cleanFinancingType);
              if(!empty($cleanFinancingType)){
              
              $FinancingTypeMaster = FinancingTypeMaster::whereRaw('LOWER(name) = ?', [strtolower(trim($data['financing_type']))])->first();
              $data['financing_type'] = $FinancingTypeMaster ? $FinancingTypeMaster->id : null;
              
              }else{
                  $data['financing_type'] = null;
              }
              
              
              
                $cleanAssetOwnership = preg_replace('/[^a-zA-Z0-9\s]/', '', $data['asset_ownership']);
                $cleanAssetOwnership = trim($cleanAssetOwnership);
               if(!empty($cleanAssetOwnership)){
              
              $AssetOwnershipMaster = AssetOwnershipMaster::whereRaw('LOWER(name) = ?', [strtolower(trim($data['asset_ownership']))])->first();
              $data['asset_ownership'] = $AssetOwnershipMaster ? $AssetOwnershipMaster->id : null;
              
               }
               else{
                  $data['asset_ownership'] = null;
              }
              
              $cleanHypothecation = preg_replace('/[^a-zA-Z0-9\s]/', '', $data['hypothecation_to']);
            $cleanHypothecation = trim($cleanHypothecation);
                
                 if(!empty($cleanHypothecation)){
                     
              $HypothecationMaster = HypothecationMaster::whereRaw('LOWER(name) = ?', [strtolower(trim($data['hypothecation_to']))])->first();
              $data['hypothecation_to'] = $HypothecationMaster ? $HypothecationMaster->id : null;
              
                 }
                 else{
                  $data['hypothecation_to'] = null;
              }
                 
            $cleanInsurer = preg_replace('/[^a-zA-Z0-9\s]/', '', $data['insurer_name']);
            $cleanInsurer = trim($cleanInsurer);
              
               if(!empty($cleanInsurer)){
                   
                $InsurerNameMaster = InsurerNameMaster::whereRaw('LOWER(name) = ?', [strtolower(trim($data['insurer_name']))])->first();
              $data['insurer_name'] = $InsurerNameMaster ? $InsurerNameMaster->id : null;
              
               }
              else{
                  $data['insurer_name'] = null;
              }
              
              $cleanColor = preg_replace('/[^a-zA-Z0-9\s]/', '', $data['color']);
                $cleanColor = trim($cleanColor);
                
                 if(!empty($cleanColor)){
                  
                $ColorMaster = ColorMaster::whereRaw('LOWER(name) = ?', [strtolower(trim($data['color']))])->first();
                $data['color'] = $ColorMaster ? $ColorMaster->id : null;
                
                 }else{
                  $data['color'] = null;
              }
              
            
            $cleanInsuranceType = preg_replace('/[^a-zA-Z0-9\s]/', '', $data['insurance_type']);
            $cleanInsuranceType = trim($cleanInsuranceType);
            
            if(!empty($cleanInsuranceType)){
                
            $InsuranceTypeMaster = InsuranceTypeMaster::whereRaw('LOWER(name) = ?', [strtolower(trim($data['insurance_type']))])->first();
              $data['insurance_type'] = $InsuranceTypeMaster ? $InsuranceTypeMaster->id : null;
            
            }else{
                  $data['insurance_type'] = null;
              }
              
              
            $cleanRegistrationType = preg_replace('/[^a-zA-Z0-9\s]/', '', $data['registration_type']);
            $cleanRegistrationType = trim($cleanRegistrationType);
            if(!empty($cleanRegistrationType)){
                
            $RegistrationTypeMaster = RegistrationTypeMaster::whereRaw('LOWER(name) = ?', [strtolower(trim($data['registration_type']))])->first();
              $data['registration_type'] = $RegistrationTypeMaster ? $RegistrationTypeMaster->id : null;
              
            }
              else{
                  $data['registration_type'] = null;
              }
              
              $cleanTelematicsOEM = preg_replace('/[^a-zA-Z0-9\s]/', '', $data['telematics_oem']);
            $cleanTelematicsOEM = trim($cleanTelematicsOEM);
              
               if(!empty($cleanTelematicsOEM)){
                   
            $TelemetricOEMMaster = TelemetricOEMMaster::whereRaw('LOWER(name) = ?', [strtolower(trim($data['telematics_oem']))])->first();
              $data['telematics_oem'] = $TelemetricOEMMaster ? $TelemetricOEMMaster->id : null;
              
               }
                else{
                  $data['telematics_oem'] = null;
              }
               
            $cleanVehicleStatus = preg_replace('/[^a-zA-Z0-9\s]/', '', $data['vehicle_status']);
            $cleanVehicleStatus = trim($cleanVehicleStatus);
            
             if(!empty($cleanVehicleStatus)){ //comment removed by Gowtham S
            
              $InventoryLocationMaster = InventoryLocationMaster::whereRaw('LOWER(name) = ?', [strtolower(trim($data['vehicle_status']))])->first();
              $data['vehicle_status'] = $InventoryLocationMaster ? $InventoryLocationMaster->id : null;
              
              
             }else{
                 $InventoryLocationMaster = null;
                  $data['vehicle_status'] = null;
             }
              
             
            $cleanVehicleCategory = preg_replace('/[^a-zA-Z0-9\s]/', '', $data['vehicle_category']);
            $cleanVehicleCategory = trim($cleanVehicleCategory);
            
            if(!empty($cleanVehicleCategory)){
            
            $category = strtolower(trim($data['vehicle_category']));
            $category = str_replace([' ', '-', '_'], '', $category);
            
            if (in_array($category, ['regularvehicle', 'reqularvehicle', 'requarvehicle'])) {
                $data['vehicle_category'] = 'regular_vehicle';
            } elseif ($category === 'lowspeedvehicle') {
                $data['vehicle_category'] = 'low_speed_vehicle';
            } else {
                $data['vehicle_category'] = '';
            }
            
            }else{
                  $data['vehicle_category'] = null;
             }
    
    
    
            $roadTaxValue =  strtolower(trim(preg_replace('/[^a-zA-Z0-9\s]/', '', $data['road_tax_applicable'])));
            
            if ($roadTaxValue === '') {
                $data['road_tax_applicable'] = 'no';
            } else {
                $data['road_tax_applicable'] = ($roadTaxValue === 'yes') ? 'yes' : 'no';
            }
    
    
            $hypothecationValue = strtolower(trim(preg_replace('/[^a-zA-Z0-9\s]/', '', $data['hypothecation'])));
            
            if ($hypothecationValue === '') {
                $data['hypothecation'] = 'no';
            } else {
                $data['hypothecation'] = ($hypothecationValue === 'yes') ? 'yes' : 'no';
            }
            
    
            $cleanBatteryType  = preg_replace('/[^a-zA-Z0-9\s]/', '', $data['battery_type']);
            $cleanBatteryType = trim($cleanBatteryType);
            
            if(!empty($cleanBatteryType)){
            
            $rawBatteryType = strtolower(trim($data['battery_type']));
    
            // Remove unwanted characters like commas, underscores, etc.
            $normalized = preg_replace('/[^a-z]/', '', $rawBatteryType); // keep only a-z
            
            if ($normalized === 'selfcharging') {
                $data['battery_type'] = '1'; // Self-Charging
            } elseif ($normalized === 'portable') {
                $data['battery_type'] = '2'; // Portable
            } else {
                $data['battery_type'] = ''; // Invalid or unmatched like 'ifan', 'cams'
            }
            }
            else{
                $data['battery_type'] = null;
            }
    
    
            
            $rowErrors = [];
    
            if (!$vehicleType) {
                $rowErrors[] = 'Vehicle Type';
            }
            
            if (!$vehicleModel) {
                $rowErrors[] = 'Vehicle Model';
            }
            
            if (!$LocationMaster) {
                $rowErrors[] = 'City';
            }
            
            if (!$ZoneMaster) {
                $rowErrors[] = 'Zone';
            }
            
            if (!$AccountabilityTypeMaster) {
                $rowErrors[] = 'Accountability Type';
            }
            
            if (!empty($qc_data['accountability_type']) && $qc_data['accountability_type'] == 2) {
                if (empty($data['client'])) {
                    $rowErrors[] = 'Client (Required when Accountability Type = Fixed)';
                }
            }
            
            if(!empty($cleanFinancingType)){
                
            if (!$FinancingTypeMaster) {
                $rowErrors[] = 'Financing Type';
            }
            
            }
            
            if(!empty($cleanCustomer)){
                
                if (!$CustomerMaster) {
                    $rowErrors[] = 'Customer';
                }
            
            }
            
            if (!empty($cleanAssetOwnership)) {
            if (!$AssetOwnershipMaster) {
                $rowErrors[] = 'Asset Ownership';
            }
            }
            if (!empty($cleanHypothecation)) {
            if (!$HypothecationMaster) {
                $rowErrors[] = 'Hypothecation To';
            }
            }
            
            if (!empty($cleanInsurer)) {
            if (!$InsurerNameMaster) {
                $rowErrors[] = 'Insurer Name';
            }
            }
            if (!empty($cleanInsuranceType)) {
            if (!$InsuranceTypeMaster) {
                $rowErrors[] = 'Insurance Type';
            }
            
            }
            if (!empty($cleanRegistrationType)) {
            
            if (!$RegistrationTypeMaster) {
                $rowErrors[] = 'Registration Type';
            }
            }
            
            if (!empty($cleanTelematicsOEM)) {
            if (!$TelemetricOEMMaster) {
                $rowErrors[] = 'Telematics OEM';
            }
            }
            
            // if (!empty($cleanVehicleStatus)) {
            // if (!$InventoryLocationMaster) {
            //     $rowErrors[] = 'Vehicle Status';
            // }
            // }
            
            
            if (!empty($cleanVehicleCategory)) {
            // Validate vehicle_category hardcoded values
            if ($data['vehicle_category'] === '') {
                $rowErrors[] = 'Vehicle Category';
            }
            
            }
            
            $permanentReg = preg_replace('/[^A-Za-z0-9]/', '', $data['permanent_reg_number']);
            
            if (empty($permanentReg)) {
                $rowErrors[] = 'Permanent Register Number';
            }
    
            
            
            // Validate hypothecation
            if ($data['hypothecation'] === '') {
                $rowErrors[] = 'Hypothecation';
            }
            
            // Validate road tax applicable
            if ($data['road_tax_applicable'] === '') {
                $rowErrors[] = 'Road Tax Applicable';
            }
            
             if(!empty($cleanBatteryType)){
            // Validate battery_type
            if ($data['battery_type'] === '') {
                $rowErrors[] = 'Battery Type';
            }
             }
    
            if (!empty($cleanColor)) {
            
            if (!$ColorMaster) {
                $rowErrors[] = 'Color';
            }
            
            }
    
            if (!empty($rowErrors)) {
                $errorRows[] = [
                    'row' => $index,
                    'chassis_number' => $row['A'] ?? 'N/A',
                    'fields' => $rowErrors
                ];
           
            }
            
            
              
            if (empty($data['chassis_number']) || empty($data['vehicle_type']) || empty($data['model']) || empty($data['motor_number']) || empty($data['telematics_serial_no']) || empty($data['city'])  || empty($qc_data['zone_id']) || empty($data['permanent_reg_number']) || empty($qc_data['accountability_type']) || empty($data['road_tax_applicable']) || empty($data['hypothecation']) || (!empty($cleanBatteryType) && empty($data['battery_type'])) ||  (!empty($cleanVehicleCategory) && empty($data['vehicle_category'])) ||
                (!empty($cleanFinancingType) && empty($data['financing_type'])) ||
                (!empty($cleanAssetOwnership) && empty($data['asset_ownership'])) ||
                (!empty($cleanHypothecation) && empty($data['hypothecation_to'])) ||
                (!empty($cleanInsurer) && empty($data['insurer_name'])) ||
                (!empty($cleanInsuranceType) && empty($data['insurance_type'])) ||
                (!empty($cleanRegistrationType) && empty($data['registration_type'])) ||
                (!empty($cleanTelematicsOEM) && empty($data['telematics_oem'])) ||
                (!empty($cleanColor) && empty($data['color'])) || 
                (!empty($qc_data['accountability_type']) && $qc_data['accountability_type'] == 2 && empty($data['client']))
            ) {
                    $skippedCount++;
                    continue;
            }
            
            
                
    
                
            $data['make'] = $vehicleModel ? $vehicleModel->make : null;
            $data['variant'] = $vehicleModel ? $vehicleModel->variant : null;
            
                
    
            $data['emi_lease_amount'] = !empty($data['emi_lease_amount']) ? floatval(preg_replace('/[^0-9.]/', '', $data['emi_lease_amount'])) : 0;
            $data['tax_invoice_value'] = !empty($data['tax_invoice_value']) ? floatval(preg_replace('/[^0-9.]/', '', $data['tax_invoice_value'])) : 0;
            $data['is_status'] = 'uploaded';
            $data['created_by'] = auth()->id();
    
            $vehicle = AssetMasterVehicle::where('chassis_number', $data['chassis_number'])->where('delete_status' , 0)->first();
    
            $oldValues = $vehicle->getOriginal(); 
            
            if ($vehicle && $vehicle->is_status === 'pending') {
                $targetRow = null;
                foreach ($sheet->getRowIterator() as $rowIter) {
                    $rowIndex = $rowIter->getRowIndex();
                    $cellValue = $sheet->getCell('A' . $rowIndex)->getValue();
                    if ($cellValue === $data['chassis_number']) {
                        $targetRow = $rowIndex;
                        break;
                    }
                }
    
                if ($targetRow) {
                    $imageColumnMap = [
                        'M'  => ['field' => 'tax_invoice_attachment',      'folder' => 'tax_invoice_attachments'],
                        'S'  => ['field' => 'master_lease_agreement',      'folder' => 'master_lease_agreements'],
                        'AE' => ['field' => 'insurance_attachment',        'folder' => 'insurance_attachments'],
                        'AO' => ['field' => 'reg_certificate_attachment',  'folder' => 'reg_certificate_attachments'],
                        'AQ' => ['field' => 'fc_attachment',               'folder' => 'fc_attachments'],
                        'Y' => ['field' => 'hypothecation_document',               'folder' => 'hypothecation_documents'],
                        'AJ' => ['field' => 'temproary_reg_attachment',               'folder' => 'temporary_certificate_attachments'],
                        'AN' => ['field' => 'hsrp_copy_attachment',               'folder' => 'hsrp_certificate_attachments'],
                    ];
    
                    $imageData = [];
    
                    foreach ($sheet->getDrawingCollection() as $drawing) {
                        $coords = $drawing->getCoordinates();
                        preg_match('/([A-Z]+)(\d+)/', $coords, $matches);
                        $col = $matches[1];
                        $row = $matches[2];
    
                        if ($row != $targetRow || !isset($imageColumnMap[$col])) continue;
    
                        $fieldInfo = $imageColumnMap[$col];
                        $field = $fieldInfo['field'];
                        $folder = $fieldInfo['folder'];
    
                        $storagePath = public_path("EV/asset_master/{$folder}");
                        if (!file_exists($storagePath)) mkdir($storagePath, 0777, true);
    
                        if ($drawing instanceof \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing) {
                            ob_start();
                            call_user_func($drawing->getRenderingFunction(), $drawing->getImageResource());
                            $imageContents = ob_get_clean();
                            $ext = 'png';
                        } else {
                            $path = $drawing->getPath();
                            $imageContents = file_get_contents($path);
                            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    
                            if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                                Log::warning("Unsupported image file for $field in row $row: .$ext");
                                continue;
                            }
                        }
    
                        // $fileName = bin2hex(random_bytes(16)) . '.' . $ext;
                        do {
                            $fileName = bin2hex(random_bytes(16)) . '.' . $ext;
                        } while (file_exists("{$storagePath}/{$fileName}"));
    
                        file_put_contents("{$storagePath}/{$fileName}", $imageContents);
    
                        // $imageData[$field] = "EV/asset_master/{$folder}/{$fileName}";
                        $imageData[$field] = $fileName;
                        Log::info("Image uploaded for $field in row $row: $fileName");
                    }
    
                    $data = array_merge($data, $imageData);
                }
    
    
    
    
                $dateFields = [
                    'tax_invoice_date', 'lease_start_date', 'lease_end_date', 'vehicle_delivery_date',
                    'insurance_start_date', 'insurance_expiry_date', 'permanent_reg_date',
                    'reg_certificate_expiry_date', 'fc_expiry_date', 'temproary_reg_date', 'temproary_reg_expiry_date',
                ];
                
    
                
                
            foreach ($dateFields as $field) { //updated by Gowtham.S
                if (isset($data[$field])) {
                    $Datavalue = trim($data[$field]);
            
                    
                    if ($Datavalue === '' || $Datavalue === '0' || $Datavalue === '0000-00-00' || strtoupper($Datavalue) === 'N/A') {
                        $data[$field] = null;
                        continue;
                    }
            
                    try {
                        
                        if (is_numeric($Datavalue)) {
                            $data[$field] = Carbon::instance(
                                \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($Datavalue)
                            )->format('Y-m-d');
                            continue;
                        }
            
                        
                        $dateString = str_replace('/', '-', $Datavalue);
            
                        
                        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
                            try {
                                $data[$field] = Carbon::parse($dateString)->format('Y-m-d');
                                continue;
                            } catch (\Exception $e) {
                                // If parse fails, move on to the format loop
                            }
                        } else {
                            $data[$field] = $dateString;
                            continue;
                        }
            
                        
                        $possibleFormats = [
                            'd-m-Y', 'm-d-Y', 'Y-m-d',
                            'd-m-y', 'm-d-y', 'Y/m/d',
                            'd/m/Y', 'm/d/Y', 'd.m.Y', 'm.d.Y'
                        ];
            
                        $parsed = null;
                        foreach ($possibleFormats as $format) {
                            try {
                                $parsed = Carbon::createFromFormat($format, $dateString);
                                if ($parsed) {
                                    $data[$field] = $parsed->format('Y-m-d');
                                    break;
                                }
                            } catch (\Exception $e) {
                                // Try next format
                            }
                        }
            
                        
                        if ($parsed === null) {
                            try {
                                $parsed = Carbon::parse($dateString);
                                $data[$field] = $parsed->format('Y-m-d');
                            } catch (\Exception $e) {
                                Log::warning("Invalid date for $field: " . $Datavalue);
                                $data[$field] = null;
                            }
                        }
            
                    } catch (\Exception $e) {
                        Log::warning("Invalid date for $field: " . $Datavalue);
                        $data[$field] = null;
                    }
                }
            }
    
    
    
                $uniqueFields = [
                    'battery_serial_no'   => 'Battery Serial Number',
                    'motor_number'        => 'Motor Number',
                    'telematics_serial_no'=> 'Telematics Serial Number',
                ];
                
                foreach ($uniqueFields as $field => $label) {
                    $value = trim($data[$field] ?? '');
                    if ($value === '') continue;
                
                    $exists = AssetMasterVehicle::where($field, $value)
                        ->where('chassis_number', '!=', $data['chassis_number'])
                        ->where('delete_status', 0)
                        ->first();
                
                    if ($exists) {
                        $errorRows[] = [
                            'row' => $index,
                            'chassis_number' => $data['chassis_number'],
                            'fields' => ["{$label} '{$value}' already exists for chassis: {$exists->chassis_number}"],
                        ];
                        $skippedCount++;
                        continue 2; 
                    }
                }
    
        
                 $qc = $vehicle->quality_check;
    
     
    
                $vehicle->fill($data);
                
                $changes = array_diff_assoc($vehicle->getDirty(), $oldValues);
                
                
                $vehicle->save();
                
                $updatedChassisNumbers[] = $vehicle->chassis_number;
    
                AssetMasterVehicleLogHistory::create([
                    'asset_vehicle_id' => $vehicle->id,
                    'user_id' => auth()->id(),
                    'remarks' => 'The Asset Master Vehicle Chassis Number ' . $vehicle->chassis_number . ' has been Uploaded',
                    'status_type' => 'uploaded',
                ]);
                
                
           // === Quality Check Update and Log ===
            $qc = $vehicle->quality_check;
            
            if ($qc) {
             
                $qcFields = [
                    'vehicle_type'      => 'Vehicle Type',
                    'vehicle_model'     => 'Vehicle Model',
                    'motor_number'      => 'Motor Number',
                    'battery_number'    => 'Battery Number',
                    'telematics_number' => 'Telematics Number',
                    'location'          => 'City',
                    'zone_id'           => 'Zone',
                    // 'customer_id'       => 'Customer',
                ];
            
                
                $fieldMapping = [
                    'vehicle_type'      => $data['vehicle_type'] ?? null,
                    'vehicle_model'     => $data['model'] ?? null,
                    'motor_number'      => $data['motor_number'] ?? null,
                    'battery_number'    => $data['battery_serial_no'] ?? null,
                    'telematics_number' => $data['telematics_serial_no'] ?? null,
                    'location'          => $data['city_code'] ?? null,
                    'zone_id'           => $qc_data['zone_id'] ?? null,
                    // 'customer_id'       => $data['client'] ?? null,
                ];
            
                $qcLogs = [];
                $updatedFields = [];
            
                foreach ($qcFields as $field => $label) {
                    
                    if ($field === 'customer_id') {
                        if (($qc_data['accountability_type'] ?? null) != 2) {
                            // Skip updating or comparing customer_id for other types
                            continue;
                        }
                    }
                                    
                    
            
                    $newValue = $fieldMapping[$field] ?? null;
            
                    if ($newValue !== null && $qc->$field != $newValue) {
                        
                        // Update QC field
                        $qc->$field = $newValue;
            
                        // Track updated field names
                        $updatedFields[] = $label;
                    }
                }
            
                // Save and log changes if any
                if (!empty($updatedFields)) {
                    $qc->save();
            
                    
                    $defaultRemark = "The following QC details were updated in Asset Master.";
                    $remarks = $defaultRemark . "\nUpdated Fields: " . implode(', ', $updatedFields);
            
                    // Create QC reinitiate log
                    QualityCheckReinitiate::create([
                        'qc_id'        => $qc->id,
                        'status'       => 'updated',
                        'remarks'      => $remarks,
                        'initiated_by' => auth()->id(),
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
            
                    // Log::info("QC updated for vehicle {$vehicle->chassis_number}", $qcLogs);
                }
            }
            
            
            
            $this->handleLogsAndUpdate($vehicle, $changes , $request , $qc);
            
        
    
                $successCount++;
            }
        }
        
    
        // Build summary for audit log
        $importedCount   = isset($updatedChassisNumbers) ? count($updatedChassisNumbers) : 0;
        $skippedCount    = isset($skippedCount) ? (int)$skippedCount : 0;
        $errorCount      = isset($errorRows) ? count($errorRows) : 0;
        
        // Show up to 10 chassis numbers in preview
        $chassisPreview  = $importedCount
            ? implode(', ', array_slice($updatedChassisNumbers, 0, 10)) . ($importedCount > 10 ? ' …' : '')
            : '-';
        
        // Show up to 5 error rows in preview
        if (!empty($errorRows)) {
            $errorPreviewList = array_slice($errorRows, 0, 5);
            $errorPreview = implode(' | ', array_map(function ($err) {
                $fields = is_array($err['fields'] ?? null) ? implode(', ', $err['fields']) : ($err['fields'] ?? '-');
                $ch = $err['chassis_number'] ?? 'N/A';
                $row = $err['row'] ?? '?';
                return "Row {$row} ({$ch}): {$fields}";
            }, $errorPreviewList));
        } else {
            $errorPreview = '-';
        }
        
        // Compose long description (trim to keep logs tidy)
        $longDescription = sprintf(
            "Asset Master Vehicles bulk upload completed. Imported: %d, Skipped: %d, Errors: %d. " .
            "Chassis (sample): %s. Error details (sample): %s",
            $importedCount,
            $skippedCount,
            $errorCount,
            $chassisPreview,
            $errorPreview
        );
        
        // (Optional) guard against extremely long logs
        $longDescription = mb_strimwidth($longDescription, 0, 1000, '…');
        
        // Final audit call
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Vehicle Bulk Import Completed',
            'long_description'  => $longDescription,
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'asset_master.vehicle_bulk_upload_form_import',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);

        
        return response()->json([
        'success' => true,
        'message' => "Asset Master Vehicles Bulk Uploaded Successfully! Total Updated: " . count($updatedChassisNumbers),
        'updated_count' => count($updatedChassisNumbers),
        'updated_chassis_numbers' => $updatedChassisNumbers,
        'skipped_count' => $skippedCount,
        'error_rows' => $errorRows,
    ]);
    
    
    } catch (\Exception $e) {
        Log::error('Bulk upload error: ' . $e->getMessage());
        
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Vehicle Bulk Import Failed (Exception)',
            'long_description'  => 'Error during bulk import: ' . substr($e->getMessage(), 0, 1000),
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'asset_master.vehicle_bulk_upload_form_import',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong: ' . $e->getMessage()
        ], 500);
    }

    
//     try {
//     $file = $request->file('asset_vehicle_excel_file');
//     $excelPath = $file->getPathname();

//     $saveRoot = public_path('EV/asset_master');
//     if (!file_exists($saveRoot)) {
//         mkdir($saveRoot, 0777, true);
//     }

//     $spreadsheet = IOFactory::load($excelPath);
//     $sheet = $spreadsheet->getActiveSheet();

//     $highestColumn = $sheet->getHighestColumn();
//     $highestRow = $sheet->getHighestRow();
//     $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

//     $headerMap = [];
//     for ($col = 1; $col <= $highestColumnIndex; $col++) {
//         $colLetter = Coordinate::stringFromColumnIndex($col);
//         $cellValue = $sheet->getCell($colLetter . '1')->getValue();
//         $headerName = Str::snake(trim($cellValue));
//         if ($headerName) {
//             $headerMap[$colLetter] = $headerName;
//         }
//     }

//     $successCount = 0;
//     $skippedCount = 0;

//     $attachmentFields = [
//         'tax_invoice_attachment'       => 'tax_invoice_attachments',
//         'master_lease_agreement'       => 'master_lease_agreements',
//         'insurance_attachment'         => 'insurance_attachments',
//         'reg_certificate_attachment'   => 'reg_certificate_attachments',
//         'fc_attachment'                => 'fc_attachments',
//     ];

//     foreach ($sheet->toArray(null, true, true, true) as $index => $row) {
//         if ($index == 1) continue;

//         $data = [];

//         foreach ($headerMap as $colLetter => $field) {
//             $value = isset($row[$colLetter]) ? trim($row[$colLetter]) : null;

//             if (array_key_exists($field, $attachmentFields)) {
//                 $subFolder = $attachmentFields[$field];
//                 $filePath = $saveRoot . '/' . $subFolder . '/' . $value;

//                 if (!empty($value) && file_exists($filePath)) {
//                     $data[$field] = 'EV/asset_master/' . $subFolder . '/' . $value;
//                 } else {
//                     $data[$field] = null;
//                     Log::warning("File not found for $field in Row $index: $filePath");
//                 }
//             } else {
//                 $data[$field] = $value;
//             }
//         }

//         if (empty($data['chassis_number'])) {
//             $skippedCount++;
//             continue;
//         }

//         $data['emi_lease_amount'] = !empty($data['emi_lease_amount']) ? floatval(preg_replace('/[^0-9.]/', '', $data['emi_lease_amount'])) : 0;
//         $data['tax_invoice_value'] = !empty($data['tax_invoice_value']) ? floatval(preg_replace('/[^0-9.]/', '', $data['tax_invoice_value'])) : 0;
//         $data['is_status'] = 'uploaded';
//         $data['created_by'] = auth()->id();

//         $vehicle = AssetMasterVehicle::where('chassis_number', $data['chassis_number'])->first();

//         if ($vehicle && $vehicle->is_status === 'pending') {
//             $targetRow = null;
//             foreach ($sheet->getRowIterator() as $rowIter) {
//                 $rowIndex = $rowIter->getRowIndex();
//                 $cellValue = $sheet->getCell('A' . $rowIndex)->getValue();
//                 if ($cellValue === $data['chassis_number']) {
//                     $targetRow = $rowIndex;
//                     break;
//                 }
//             }

//             if ($targetRow) {
//                 $imageColumnMap = [
//                     'N'  => ['field' => 'tax_invoice_attachment',      'folder' => 'tax_invoice_attachments'],
//                     'S'  => ['field' => 'master_lease_agreement',      'folder' => 'master_lease_agreements'],
//                     'AE' => ['field' => 'insurance_attachment',        'folder' => 'insurance_attachments'],
//                     'AK' => ['field' => 'reg_certificate_attachment',  'folder' => 'reg_certificate_attachments'],
//                     'AM' => ['field' => 'fc_attachment',               'folder' => 'fc_attachments'],
//                 ];

//                 $imageData = [];

//                 foreach ($sheet->getDrawingCollection() as $drawing) {
//                     $coords = $drawing->getCoordinates();
//                     preg_match('/([A-Z]+)(\d+)/', $coords, $matches);
//                     $col = $matches[1];
//                     $row = $matches[2];

//                     if ($row != $targetRow || !isset($imageColumnMap[$col])) continue;

//                     $fieldInfo = $imageColumnMap[$col];
//                     $field = $fieldInfo['field'];
//                     $folder = $fieldInfo['folder'];

//                     $storagePath = public_path("EV/asset_master/{$folder}");
//                     if (!file_exists($storagePath)) mkdir($storagePath, 0777, true);

//                     if ($drawing instanceof \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing) {
//                         ob_start();
//                         call_user_func($drawing->getRenderingFunction(), $drawing->getImageResource());
//                         $imageContents = ob_get_clean();
//                         $ext = 'png';
//                     } else {
//                         $path = $drawing->getPath();
//                         $imageContents = file_get_contents($path);
//                         $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

//                         if (!in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'])) {
//                             Log::warning("Unsupported file type for $field in row $row: .$ext");
//                             continue;
//                         }
//                     }

//                     $fileName = bin2hex(random_bytes(16)) . '.' . $ext;
//                     file_put_contents("{$storagePath}/{$fileName}", $imageContents);

//                     // $imageData[$field] = "EV/asset_master/{$folder}/{$fileName}";
//                     $imageData[$field] = $fileName;
//                 }

//                 $data = array_merge($data, $imageData);
//             }

//             // Format date fields
//             $dateFields = [
//                 'tax_invoice_date', 'lease_start_date', 'lease_end_date', 'vehicle_delivery_date',
//                 'insurance_start_date', 'insurance_expiry_date', 'permanent_reg_date',
//                 'reg_certificate_expiry_date', 'fc_expiry_date',
//             ];

//             foreach ($dateFields as $field) {
//                 if (!empty($data[$field])) {
//                     try {
//                         $data[$field] = Carbon::createFromFormat('m/d/Y', $data[$field])->format('Y-m-d');
//                     } catch (\Exception $e) {
//                         Log::warning("Invalid date format for $field: " . $data[$field]);
//                         $data[$field] = null;
//                     }
//                 }
//             }

//             $vehicle->fill($data)->save();

//             AssetMasterVehicleLogHistory::create([
//                 'asset_vehicle_id' => $vehicle->id,
//                 'user_id' => auth()->id(),
//                 'remarks' => 'The Asset Master Vehicle Chassis Number ' . $vehicle->chassis_number . ' has been Uploaded',
//                 'status_type' => 'uploaded',
//             ]);

//             $successCount++;
//         }
//     }

//     return response()->json([
//         'success' => true,
//         'message' => "Asset Master Vehicles Bulk Uploaded Successfully!"
//     ]);

// } catch (\Exception $e) {
//     Log::error('Bulk upload error: ' . $e->getMessage());

//     return response()->json([
//         'success' => false,
//         'message' => 'Something went wrong: ' . $e->getMessage()
//     ], 500);
// }


//     try {
//         $file = $request->file('asset_vehicle_excel_file');
//         $excelPath = $file->getPathname();

//         $saveRoot = public_path('EV/asset_master');
//         if (!file_exists($saveRoot)) {
//             mkdir($saveRoot, 0777, true);
//         }

//         $spreadsheet = IOFactory::load($excelPath);
//         $sheet = $spreadsheet->getActiveSheet();

//         $highestColumn = $sheet->getHighestColumn();
//         $highestRow = $sheet->getHighestRow();
//         $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

//         $headerMap = [];
//         for ($col = 1; $col <= $highestColumnIndex; $col++) {
//             $colLetter = Coordinate::stringFromColumnIndex($col);
//             $cellValue = $sheet->getCell($colLetter . '1')->getValue();
//             $headerName = Str::snake(trim($cellValue));

//             if ($headerName) {
//                 $headerMap[$colLetter] = $headerName;
//             }
//         }

// // dd($headerMap);
//         $basePath = public_path('EV/asset_master');
//         $importPath = $basePath . '/';

//         $successCount = 0;
//         $skippedCount = 0;

//         $attachmentFields = [
//             'tax_invoice_attachment'       => 'tax_invoice_attachments',
//             'master_lease_agreement'       => 'master_lease_agreements',
//             'insurance_attachment'         => 'insurance_attachments',
//             'reg_certificate_attachment'   => 'reg_certificate_attachments',
//             'fc_attachment'                => 'fc_attachments',
//         ];

//         // 🔁 Start reading rows
//         foreach ($sheet->toArray(null, true, true, true) as $index => $row) {
//             if ($index == 1) continue; // Skip header

//             $data = [];

//             foreach ($headerMap as $colLetter => $field) {
//                 $value = isset($row[$colLetter]) ? trim($row[$colLetter]) : null;

//                 if (array_key_exists($field, $attachmentFields)) {
//                     $subFolder = $attachmentFields[$field];
//                     $filePath = $basePath . '/' . $subFolder . '/' . $value;

//                     if (!empty($value) && file_exists($filePath)) {
                        
//                         $data[$field] = 'EV/asset_master/' . $subFolder . '/' . $value;
//                     } else {
//                         $data[$field] = null;
//                         Log::warning("File not found for $field in Row $index: $filePath");
//                     }
//                 } else {
//                     Log::info("File uploaded");
//                     $data[$field] = $value;
//                 }
//             }

//             Log::info("Processing Row $index", $data);

//             if (empty($data['chassis_number'])) {
//                 $skippedCount++;
//                 Log::info("Skipped Row $index: Missing chassis_number.");
//                 continue;
//             }

//             if (!empty($data['emi_lease_amount'])) {
//                 $data['emi_lease_amount'] = floatval(preg_replace('/[^0-9.]/', '', $data['emi_lease_amount']));
//             }

//             if (!empty($data['tax_invoice_value'])) {
//                 $data['tax_invoice_value'] = floatval(preg_replace('/[^0-9.]/', '', $data['tax_invoice_value']));
//             }

//             $data['is_status'] = 'uploaded';
//             $data['created_by'] = auth()->id();
            
//             $attachmentFields = [
//                 'tax_invoice_attachment'      => 'tax_invoice_attachments',
//                 'master_lease_agreement'      => 'master_lease_agreements',
//                 'insurance_attachment'        => 'insurance_attachments',
//                 'reg_certificate_attachment'  => 'reg_certificate_attachments',
//                 'fc_attachment'               => 'fc_attachments',
//             ];

//             $vehicle = AssetMasterVehicle::where('chassis_number', $data['chassis_number'])->first();
            
//             Log::info("Asset Master Vehicle Row ".json_encode($vehicle));

//           if ($vehicle && $vehicle->is_status === 'pending') {

//             $targetRow = null;
//             foreach ($sheet->getRowIterator() as $row) {
//                 $rowIndex = $row->getRowIndex();
//                 $cellValue = $sheet->getCell('A' . $rowIndex)->getValue(); 
//                 if ($cellValue === $data['chassis_number']) {
//                     $targetRow = $rowIndex;
//                     break;
//                 }
//             }

//             if ($targetRow) {
//                 // Step 2: Define image column map
//                 $imageColumnMap = [
//                     'N'  => ['field' => 'tax_invoice_attachment',      'folder' => 'tax_invoice_attachments'],
//                     'S'  => ['field' => 'master_lease_agreement',      'folder' => 'master_lease_agreements'],
//                     'AE' => ['field' => 'insurance_attachment',        'folder' => 'insurance_attachments'],
//                     'AK' => ['field' => 'reg_certificate_attachment',  'folder' => 'reg_certificate_attachments'],
//                     'AM' => ['field' => 'fc_attachment',               'folder' => 'fc_attachments'],
//                 ];
            
//                 $imageData = [];
            
//                 foreach ($sheet->getDrawingCollection() as $drawing) {
//                     $coords = $drawing->getCoordinates(); // e.g., "N2", "S3"
//                     preg_match('/([A-Z]+)(\d+)/', $coords, $matches);
//                     $col = $matches[1];
//                     $row = $matches[2];
            
//                     // Only process if this drawing is for the matching row
//                     if ($row != $targetRow || !isset($imageColumnMap[$col])) continue;
            
//                     $fieldInfo = $imageColumnMap[$col];
//                     $field = $fieldInfo['field'];
//                     $folder = $fieldInfo['folder'];
            
//                     $storagePath = public_path("EV/asset_master/{$folder}");
//                     if (!file_exists($storagePath)) mkdir($storagePath, 0777, true);
            
//                     if ($drawing instanceof MemoryDrawing) {
//                         ob_start();
//                         call_user_func($drawing->getRenderingFunction(), $drawing->getImageResource());
//                         $imageContents = ob_get_clean();
//                         $ext = 'png';
//                     } else {
//                         $path = $drawing->getPath();
//                         $imageContents = file_get_contents($path);
//                         $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
//                     }
            
//                     // $fileName = rand(1111111111,9999999999).''.time().''.uniqid() . '.' . $ext;
//                     $fileName = bin2hex(random_bytes(16)) . '.' . $ext;
//                     file_put_contents("{$storagePath}/{$fileName}", $imageContents);
            
//                     // Save image path to assign later
//                     // $imageData[$field] = "EV/asset_master/{$folder}/{$fileName}";
//                     $imageData[$field] = $fileName;
//                 }
            
//                 // Step 3: Now merge this $imageData into your $data
//                 $data = array_merge($data, $imageData);
//             }


               
//                 Log::info("Updating Vehicle ID {$vehicle->id} with Chassis: {$vehicle->chassis_number}");
            
//                 // Format date fields to 'Y-m-d' if needed
//                 $formattedData = $data;
//                 $dateFields = [
//                     'tax_invoice_date',
//                     'lease_start_date',
//                     'lease_end_date',
//                     'vehicle_delivery_date',
//                     'insurance_start_date',
//                     'insurance_expiry_date',
//                     'permanent_reg_date',
//                     'reg_certificate_expiry_date',
//                     'fc_expiry_date',
//                 ];
            
//                 foreach ($dateFields as $field) {
//                     if (!empty($data[$field])) {
//                         try {
//                             $formattedData[$field] = Carbon::createFromFormat('m/d/Y', $data[$field])->format('Y-m-d');
//                         } catch (\Exception $e) {
//                             Log::warning("Invalid date format for $field in vehicle ID {$vehicle->id}: " . $data[$field]);
//                             $formattedData[$field] = null;
//                         }
//                     }
//                 }
            
//                 // Sanitize numeric fields
//                 $formattedData['tax_invoice_value'] = isset($data['tax_invoice_value']) ? floatval(preg_replace('/[^0-9.]/', '', $data['tax_invoice_value'])) : 0;
//                 $formattedData['emi_lease_amount'] = isset($data['emi_lease_amount']) ? floatval(preg_replace('/[^0-9.]/', '', $data['emi_lease_amount'])) : 0;
            
//                 // Set status and created_by
//                 $formattedData['is_status'] = 'uploaded';
//                 $formattedData['created_by'] = auth()->id();
            
//                 // Fill and save the vehicle
//                 $vehicle->fill($formattedData)->save();
            
//                 // Log history
//                 AssetMasterVehicleLogHistory::create([
//                     'asset_vehicle_id' => $vehicle->id,
//                     'user_id' => auth()->id(),
//                     'remarks' => 'The Asset Master Vehicle Chassis Number ' . $vehicle->chassis_number . ' has been Uploaded',
//                     'status_type' => 'uploaded',
//                 ]);
            
//                 $successCount++;
//             } else {
//                 // Log::info("Skipped Row $index: Vehicle not found or not in 'pending' status.");
//             }
//         }

//         // return response()->json([
//         //     'success' => true,
//         //     'message' => "Bulk upload completed. $successCount records updated, $skippedCount skipped."
//         // ]);
        
//         return response()->json([
//             'success' => true,
//             'message' => "Asset Master Vehicles Bulk Uploaded Successfully!"
//         ]);

//     } catch (\Exception $e) {
//         Log::error('Bulk upload error: ' . $e->getMessage());

//         return response()->json([
//             'success' => false,
//             'message' => 'Something went wrong: ' . $e->getMessage()
//         ], 500);
//     }
}



// public function vehicle_bulk_upload_form_import(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         'asset_vehicle_excel_file' => 'required|file|mimes:xlsx,xls',
//     ]);

//     if ($validator->fails()) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Validation failed.',
//             'errors' => $validator->errors()
//         ], 422);
//     }

//     try {
//         $file = $request->file('asset_vehicle_excel_file');
//         $excelPath = $file->getPathname();

//         $saveRoot = public_path('EV/asset_master');
//         if (!file_exists($saveRoot)) {
//             mkdir($saveRoot, 0777, true);
//         }

//         $spreadsheet = IOFactory::load($excelPath);
//         $sheet = $spreadsheet->getActiveSheet();

//         $highestColumn = $sheet->getHighestColumn(); // e.g., 'AA'
//         $highestRow = $sheet->getHighestRow();       // e.g., 100
//         $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

//         $headerMap = [];

//         // Build header mapping
//         for ($col = 1; $col <= $highestColumnIndex; $col++) {
//             $cellValue = $sheet->getCellByColumnAndRow($col, 1)->getValue();
//             $headerName = Str::snake(trim($cellValue)); // Better formatting

//             if ($headerName) {
//                 $headerMap[$col] = $headerName;
//             }
//         }

//         $importPath = public_path('EV/asset_master/');
//         $successCount = 0;
//         $skippedCount = 0;
//         dd($sheet->toArray(null, true, true, true));
//         foreach ($sheet->toArray(null, true, true, true) as $index => $row) {
//             if ($index == 1) continue; // Skip header row

//             $data = [];

//             foreach ($headerMap as $col => $field) {
//                 $value = isset($row[$col]) ? trim($row[$col]) : null;

//                 // Handle attachments
//                 if (in_array($field, [
//                     'tax_invoice_attachment',
//                     'master_lease_agreement',
//                     'insurance_attachment',
//                     'reg_certificate_attachment',
//                     'fc_attachment'
//                 ])) {
//                     $filePath = $importPath . '/' . $value.'s';

//                     if (!empty($value) && file_exists($filePath)) {
//                         $data[$field] = 'EV/asset_master/' . $value.'s';
//                     } else {
//                         $data[$field] = null;
//                     }
//                 } else {
//                     $data[$field] = $value;
//                 }
//             }

//             // Validate required identifier
//             if (empty($data['chassis_number'])) {
//                 $skippedCount++;
//                 continue;
//             }

//             // Clean numeric values
//             if (!empty($data['emi_lease_amount'])) {
//                 $data['emi_lease_amount'] = floatval(preg_replace('/[^0-9.]/', '', $data['emi_lease_amount']));
//             }

//             if (!empty($data['tax_invoice_value'])) {
//                 $data['tax_invoice_value'] = floatval(preg_replace('/[^0-9.]/', '', $data['tax_invoice_value']));
//             }

//             $data['is_status'] = 'uploaded';
//             $data['created_by'] = auth()->id();
            
//             dd($data);
            
//             $vehicle = AssetMasterVehicle::where('chassis_number', $data['chassis_number'])->first();

//             if ($vehicle && $vehicle->is_status === 'pending') {
//                 $vehicle->fill($data)->save();

//                 AssetMasterVehicleLogHistory::create([
//                     'asset_vehicle_id' => $vehicle->id,
//                     'user_id' => auth()->id(),
//                     'remarks' => 'The Asset Master Vehicle Chassis Number ' . $vehicle->chassis_number . ' has been Uploaded',
//                     'status_type' => 'uploaded',
//                 ]);

//                 $successCount++;
//             } else {
//                 $skippedCount++;
//             }
//         }

//         return response()->json([
//             'success' => true,
//             'message' => "Bulk upload completed. $successCount records updated, $skippedCount skipped."
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Something went wrong: ' . $e->getMessage()
//         ], 500);
//     }
// }


    
     public function view_asset_master(Request $request)
    {
        $id_decode = decrypt($request->id);
        $vehicle_data = AssetMasterVehicle::where('id',$id_decode)->first();
        if(!$vehicle_data){
            return back()->with('error','Asset Vehicle Not Found');
        }

        $vehicle_types = VehicleType::where('is_active', 1)->get();
        $locations =  City::where('status', 1)
        ->select('id', 'city_name')
        ->get();
        $passed_chassis_numbers = AssetMasterVehicle::where('qc_status','pass')->get();
        $vehicle_models = VehicleModelMaster::where('status', 1)->get();
        
        $financing_types = FinancingTypeMaster::where('status',1)->get();
        $asset_ownerships = AssetOwnershipMaster::where('status',1)->get();
        $insurer_names = InsurerNameMaster::where('status',1)->get();
        $insurance_types = InsuranceTypeMaster::where('status',1)->get();
        $hypothecations = HypothecationMaster::where('status',1)->get();
        $registration_types = RegistrationTypeMaster::where('status',1)->get();
        $inventory_locations = InventoryLocationMaster::where('status',1)->get();
        $telematics = TelemetricOEMMaster::where('status',1)->get();
        $colors = ColorMaster::where('status',1)->get();
        $customers = CustomerMaster::where('status',1)->get();
        
        return view('assetmaster::asset_master.view_asset_master',compact('vehicle_data','vehicle_types','locations','passed_chassis_numbers' ,'financing_types' ,'asset_ownerships' ,'insurer_names' ,'insurance_types' ,'hypothecations' ,'registration_types' ,'inventory_locations', 'vehicle_models' ,'telematics' ,'colors' , 'customers'));
    }
    
    
    
    private function handleLogsAndUpdate($vehicle_update,$changes ,  $request, $quality_check)
    {
                
    if (!empty($changes)) {

        $fieldLabels = [
            'chassis_number' => 'Chassis Number',
            'vehicle_category' => 'Vehicle Category',
            // 'vehicle_type' => 'Vehicle Type',
            // 'make' => 'Make',
            // 'model' => 'Model',
            // 'variant' => 'Variant',
            'color' => 'Color',
            // 'client' => 'Client',
            // 'motor_number' => 'Motor Number',
            'vehicle_id' => 'Vehicle ID',
            'tax_invoice_number' => 'Tax Invoice Number',
            'tax_invoice_date' => 'Tax Invoice Date',
            'tax_invoice_value' => 'Tax Invoice Value',
            // 'location' => 'Location',
            'gd_hub_name' => 'GD Hub Name',
            'gd_hub_id' => 'GD Hub ID',
            'financing_type' => 'Financing Type',
            'asset_ownership' => 'Asset Ownership',
            'lease_start_date' => 'Lease Start Date',
            'lease_end_date' => 'Lease End Date',
            'vehicle_delivery_date' => 'Vehicle Delivery Date',
            'emi_lease_amount' => 'EMI Lease Amount',
            'hypothecation' => 'Hypothecation',
            'hypothecation_to' => 'Hypothecation To',
            'insurer_name' => 'Insurer Name',
            'insurance_type' => 'Insurance Type',
            'insurance_number' => 'Insurance Number',
            'insurance_start_date' => 'Insurance Start Date',
            'insurance_expiry_date' => 'Insurance Expiry Date',
            'registration_type' => 'Registration Type',
            'registration_status' => 'Registration Status',
            'permanent_reg_number' => 'Permanent Registration Number',
            'permanent_reg_date' => 'Permanent Registration Date',
            'reg_certificate_expiry_date' => 'Registration Certificate Expiry Date',
            'fc_expiry_date' => 'FC Expiry Date',
            'battery_type' => 'Battery Type',
            'battery_variant_name' => 'Battery Variant Name',
            // 'battery_serial_no' => 'Battery Serial Number',
            'charger_variant_name' => 'Charger Variant Name',
            'charger_serial_no' => 'Charger Serial Number',
            'telematics_variant_name' => 'Telematics Variant Name',
            // 'telematics_serial_no' => 'Telematics Serial Number',
            'vehicle_status' => 'Vehicle Status',
            'temproary_reg_number' => 'Temporary Registration Number',
            'temproary_reg_date' => 'Temporary Registration Date',
            'temproary_reg_expiry_date' => 'Temporary Registration Expiry Date',
            'servicing_dates' => 'Servicing Dates',
            'road_tax_applicable' => 'Road Tax Applicable',
            'road_tax_amount' => 'Road Tax Amount',
            'road_tax_renewal_frequency' => 'Road Tax Renewal Frequency',
            'road_tax_next_renewal_date' => 'Next Road Tax Renewal Date',
            'battery_serial_number1' => 'Battery Serial Number 1',
            'battery_serial_number2' => 'Battery Serial Number 2',
            'battery_serial_number3' => 'Battery Serial Number 3',
            'battery_serial_number4' => 'Battery Serial Number 4',
            'battery_serial_number5' => 'Battery Serial Number 5',
            'charger_serial_number1' => 'Charger Serial Number 1',
            'charger_serial_number2' => 'Charger Serial Number 2',
            'charger_serial_number3' => 'Charger Serial Number 3',
            'charger_serial_number4' => 'Charger Serial Number 4',
            'charger_serial_number5' => 'Charger Serial Number 5',
            'telematics_oem' => 'Telematics OEM',
            'telematics_imei_number' => 'Telematics IMEI Number',
            'telematics_serial_number1' => 'Telematics Serial Number 1',
            'telematics_serial_number2' => 'Telematics Serial Number 2',
            'telematics_serial_number3' => 'Telematics Serial Number 3',
            'telematics_serial_number4' => 'Telematics Serial Number 4',
            'telematics_serial_number5' => 'Telematics Serial Number 5',
            // 'city_code' => 'City',
        ];
    
    
        $attachmentFields = [
            'tax_invoice_attachment'     => 'Tax Invoice Attachment',
            'master_lease_agreement'     => 'Master Lease Agreement',
            'insurance_attachment'       => 'Insurance Attachment',
            'reg_certificate_attachment' => 'Registration Certificate Attachment',
            'fc_attachment'              => 'FC Attachment',
            'hypothecation_document'     => 'Hypothecation Document',
            'temproary_reg_attachment'   => 'Temporary Registration Attachment',
            'hsrp_copy_attachment'       => 'HSRP Certificate Attachment',
        ];


        // Only include changed fields that exist in label map
        $updatedReadable = [];
        foreach ($changes as $key => $value) {
            if (isset($fieldLabels[$key])) {
                $updatedReadable[] = $fieldLabels[$key];
            }
        }
        
        foreach ($attachmentFields as $field => $label) {
            if ($request->hasFile($field)) {
                $updatedReadable[] = $label;
            }
        }
    
    
    if ($quality_check) {
            $qcFieldMap = [
                'vehicle_type' => 'Vehicle Type',
                'vehicle_model' => 'Model',
                'motor_number' => 'Motor Number',
                'battery_number' => 'Battery Number',
                'telematics_number' => 'Telematics Number',
                'location' => 'City',
                // 'customer_id' => 'Customer',
                'zone_id' => 'Zone',
            ];
            
    
            foreach ($qcFieldMap as $qcField => $label) {
                $reqField = match ($qcField) {
                    'vehicle_model' => 'model',
                    'battery_number' => 'battery_serial_no',
                    'telematics_number' => 'telematics_serial_no',
                    'location' => 'city_code',
                    // 'customer_id' => 'client',
                    'zone_id' => 'zone_id', 
                    default => $qcField,
                };
    
            $newValue = $request->$reqField ?? null;
            
            // if ($qcField === 'customer_id' && ($quality_check->accountability_type ?? null) != 2) {
            //     continue;
            // }

            if ($qcField === 'zone_id') {
                if ($quality_check->zone_id != $request->zone_id) {
                    $updatedReadable[] = $label;
                }
            } elseif ($quality_check->$qcField != $newValue) {
                $updatedReadable[] = $label;
            }
            }
        }


        if (!empty($updatedReadable)) {
            $updatedText = implode(', ', $updatedReadable);
            $remarks = "The following Asset Master fields have been updated: {$updatedText}. These updates were applied successfully.";
    
            AssetMasterVehicleLogHistory::create([
                'asset_vehicle_id' => $vehicle_update->id,
                'user_id' => auth()->id(),
                'remarks' => $remarks,
                'status_type' => 'updated',
            ]);
        }
    }

    }
    
    public function reupload_vehicle_data(Request $request)
    {
        $id_decode = decrypt($request->id);
        $vehicle_data = AssetMasterVehicle::where('id',$id_decode)->first();
        if(!$vehicle_data){
            return back()->with('error','Asset Vehicle Not Found');
        }

        $vehicle_types = VehicleType::where('is_active', 1)->get();
        $locations = LocationMaster::where('status',1)->get();
        $passed_chassis_numbers = AssetMasterVehicle::where('qc_status','pass')->get();
        $vehicle_models = VehicleModelMaster::where('status', 1)->get();
        
                $financing_types = FinancingTypeMaster::where('status',1)->get();
        $asset_ownerships = AssetOwnershipMaster::where('status',1)->get();
        $insurer_names = InsurerNameMaster::where('status',1)->get();
        $insurance_types = InsuranceTypeMaster::where('status',1)->get();
        $hypothecations = HypothecationMaster::where('status',1)->get();
        $registration_types = RegistrationTypeMaster::where('status',1)->get();
        $telematics = TelemetricOEMMaster::where('status',1)->get();
       $inventory_locations = InventoryLocationMaster::where('status',1)->get();
       $colors = ColorMaster::where('status',1)->get();
       
        
        return view('assetmaster::asset_master.asset_master_reiniate',compact('vehicle_data','vehicle_types','locations','passed_chassis_numbers' ,'vehicle_models' , 'financing_types' ,'asset_ownerships' ,'insurer_names','insurance_types' ,'hypothecations' ,'registration_types' ,'telematics' ,'inventory_locations' ,'colors'));
        
    }
    
     public function reupdate_vehicle_data(Request $request)
    {
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    
        // ✅ Log: Re-update Initiated
    
        $vehicle_update = AssetMasterVehicle::where('id', $request->id)->first();

        if (!$vehicle_update) {
            return response()->json([
                'success' => false,
                'message' => 'Asset Master Vehicle Not Found',
            ]);
        }

        $oldValues = $vehicle_update->toArray();


        
        $validator = Validator::make($request->all(), [
        'chassis_number' => 'required|string|unique:ev_tbl_asset_master_vehicles,chassis_number,'.$vehicle_update->id,
        'status'=>'required|in:accepted,rejected',
        'reject_remark' => 'sometimes|required_if:status,rejected|string',
        'vehicle_category' => 'required|string',
        // 'vehicle_type' => 'required|numeric',
        // 'make' => 'required|string',
        // 'model' => 'required|string',
        'client' => 'nullable|string',
        // 'variant' => 'required|string',
        // 'color' => 'required|string',
        'motor_number' => 'required|string',
        'vehicle_id' => 'required|string',
        'tax_invoice_number' => 'nullable|string',
        'tax_invoice_date' => 'nullable|date',
        'tax_invoice_value' => 'nullable',
        'location' => 'required|numeric',
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
        'vehicle_status' => 'required|string',
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
        $errorsText = implode(', ', $validator->errors()->all());
        
        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors()
        ], 422);
    }
    
        DB::beginTransaction();
        try {
            
       
    
    
        // tax_invoice_attachment
        if ($request->hasFile('tax_invoice_attachment')) {
            $old_file = $vehicle_update->tax_invoice_attachment;
        
            $new_file = CustomHandler::uploadFileImage(
                $request->file('tax_invoice_attachment'),
                'EV/asset_master/tax_invoice_attachments'
            );
        
            $vehicle_update->tax_invoice_attachment = $new_file;
        
            if (!empty($old_file)) {
                CustomHandler::GlobalFileDelete($old_file, 'EV/asset_master/tax_invoice_attachments/');
            }
        }
        
        // master_lease_agreement
        if ($request->hasFile('master_lease_agreement')) {
            $old_file = $vehicle_update->master_lease_agreement;
        
            $new_file = CustomHandler::uploadFileImage(
                $request->file('master_lease_agreement'),
                'EV/asset_master/master_lease_agreements'
            );
        
            $vehicle_update->master_lease_agreement = $new_file;
        
            if (!empty($old_file)) {
                CustomHandler::GlobalFileDelete($old_file, 'EV/asset_master/master_lease_agreements/');
            }
        }
        
        // insurance_attachment
        if ($request->hasFile('insurance_attachment')) {
            $old_file = $vehicle_update->insurance_attachment;
        
            $new_file = CustomHandler::uploadFileImage(
                $request->file('insurance_attachment'),
                'EV/asset_master/insurance_attachments'
            );
        
            $vehicle_update->insurance_attachment = $new_file;
        
            if (!empty($old_file)) {
                CustomHandler::GlobalFileDelete($old_file, 'EV/asset_master/insurance_attachments/');
            }
        }
        
        // reg_certificate_attachment
        if ($request->hasFile('reg_certificate_attachment')) {
            $old_file = $vehicle_update->reg_certificate_attachment;
        
            $new_file = CustomHandler::uploadFileImage(
                $request->file('reg_certificate_attachment'),
                'EV/asset_master/reg_certificate_attachments'
            );
        
            $vehicle_update->reg_certificate_attachment = $new_file;
        
            if (!empty($old_file)) {
                CustomHandler::GlobalFileDelete($old_file, 'EV/asset_master/reg_certificate_attachments/');
            }
        }
        
        // fc_attachment
        if ($request->hasFile('fc_attachment')) {
            $old_file = $vehicle_update->fc_attachment;
        
            $new_file = CustomHandler::uploadFileImage(
                $request->file('fc_attachment'),
                'EV/asset_master/fc_attachments'
            );
        
            $vehicle_update->fc_attachment = $new_file;
        
            if (!empty($old_file)) {
                CustomHandler::GlobalFileDelete($old_file, 'EV/asset_master/fc_attachments/');
            }
        }


            if ($request->hasFile('hypothecation_document')) {
                
                $old_file = $vehicle_update->hypothecation_document;
                
                
                $new_file = CustomHandler::uploadFileImage(
                    $request->file('hypothecation_document'),
                    'EV/asset_master/hypothecation_documents'
                );
                
                 $vehicle_update->hypothecation_document = $new_file;
        
                if (!empty($old_file)) {
                    CustomHandler::GlobalFileDelete($old_file, 'EV/asset_master/hypothecation_documents/');
                }
            
            }
            
            if ($request->hasFile('temporary_certificate_attachment')) {
                
                $old_file = $vehicle_update->temproary_reg_attachment;
                
                $new_file = CustomHandler::uploadFileImage(
                    $request->file('temporary_certificate_attachment'),
                    'EV/asset_master/temporary_certificate_attachments'
                );
                
                $vehicle_update->temproary_reg_attachment = $new_file;
        
                if (!empty($old_file)) {
                    CustomHandler::GlobalFileDelete($old_file, 'EV/asset_master/temporary_certificate_attachments/');
                }
                
                
            }
            
            
            if ($request->hasFile('hsrp_certificate_attachment')) {
                $old_file = $vehicle_update->hsrp_copy_attachment;
                
                $new_file = CustomHandler::uploadFileImage(
                    $request->file('hsrp_certificate_attachment'),
                    'EV/asset_master/hsrp_certificate_attachments'
                );
                
                $vehicle_update->hsrp_copy_attachment = $new_file;
        
                if (!empty($old_file)) {
                    CustomHandler::GlobalFileDelete($old_file, 'EV/asset_master/hsrp_certificate_attachments/');
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
                'city_code' => $request->city_code,
                
                
                'is_status' => $request->status,
                'created_by'=>auth()->id()
            ]);
            
            
    
            $vehicle_update->save();

            if($request->status == 'rejected'){
                $remarks = $request->reject_remark;
            }else{
               $remarks = 'The Asset Master Vehicle Chassis Number ' . $vehicle_update->chassis_number . ' has been Updated'; 
            }
            
    
            AssetMasterVehicleLogHistory::create([
                'asset_vehicle_id' => $vehicle_update->id,
                'user_id' => auth()->id(),
                'remarks' => $remarks,
                'status_type' => $request->status,
            ]);
            
            if ($request->status == 'accepted') {
                $last = AssetVehicleInventory::selectRaw("CAST(SUBSTRING(id, 4) AS UNSIGNED) as lot_number")
                    ->orderByDesc('lot_number')
                    ->first();
            
                $lastNumber = $last ? (int)$last->lot_number : 1000;
                $newNumber = $lastNumber + 1;
            
                $gen_lot_id = $vehicle_update->chassis_number.'/'.$request->permanent_reg_number;
            
                AssetVehicleInventory::create([
                    'id' => $gen_lot_id,
                    'asset_vehicle_id' => $vehicle_update->id,
                    'asset_vehicle_status' => 'accepted',
                    'transfer_status' =>  $request->vehicle_status,
                    'is_status' => 1,
                    'user_id' => auth()->id(),
                    'created_by' => auth()->id(),
                ]);
            }
    
            DB::commit();
            $changes = [];
            $ignoreFields = ['created_at', 'updated_at'];
            foreach ($oldValues as $field => $oldValue) {
            
                // Skip ignored fields automatically
                if (in_array($field, $ignoreFields)) continue;
            
                $newValue = $request->$field ?? $vehicle_update->$field ?? null;
            
                if ((string)$oldValue !== (string)$newValue) {
            
                    // Format field names: chassis_number → Chassis Number
                    $label = ucwords(str_replace('_', ' ', $field));
            
                    $old = $oldValue !== null && $oldValue !== '' ? $oldValue : 'N/A';
                    $new = $newValue !== null && $newValue !== '' ? $newValue : 'N/A';
            
                    $changes[] = "{$label}: {$old} → {$new}";
                }
            }
            
            $changesText = empty($changes) 
                ? 'No major field updates.' 
                : implode('; ', $changes);
                
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'Asset Master Re-Update Completed',
                'long_description'  => "Vehicle re-updated successfully (ID: {$vehicle_update->id}, chassis: {$vehicle_update->chassis_number}), status: {$request->status}.Changes: {$changesText}",
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'asset_master.reupdate_vehicle_data',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
        
            return response()->json([
                'success' => true,
                'message' => 'The Asset Master Vehicle Updated Successfully!',
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
    
    public function vehicle_status_update(Request $request)
    {
            $user     = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        
        $status = $request->status == "accepted" ? 'accepted' : 'rejected';
        $status_update = AssetMasterVehicle::where('id',$request->id)->where('is_status','uploaded')->first();
        

        if(!$status_update){
        
            return response()->json([
                'success'=>false,
                'message' => 'Asset Master Vehicle Not Found!',
            ]);
        }
        
        DB::beginTransaction();
        try {
            $status_update->is_status = $status;
            $status_update->save();
        
            $remarks = $status === 'accepted'
                ? 'The Asset Master Vehicle Chassis Number ' . $status_update->chassis_number . ' has been Accepted'
                : $request->remarks;
                
            
        
            AssetMasterVehicleLogHistory::create([
                'asset_vehicle_id' => $status_update->id,
                'user_id' => auth()->id(),
                'remarks' => $remarks,
                'status_type' => $status,
            ]);
            
            if ($status == 'accepted') {
                $last = AssetVehicleInventory::selectRaw("CAST(SUBSTRING(id, 4) AS UNSIGNED) as lot_number")
                    ->orderByDesc('lot_number')
                    ->first();
            
                $lastNumber = $last ? (int)$last->lot_number : 1000;
                $newNumber = $lastNumber + 1;
            
                $gen_lot_id = $status_update->chassis_number.'/'.$status_update->permanent_reg_number;
            
                AssetVehicleInventory::create([
                    'id' => $gen_lot_id,
                    'asset_vehicle_id' => $status_update->id,
                    'asset_vehicle_status' => 'accepted',
                    'is_status' => 1,
                    'transfer_status'=>$status_update->vehicle_status,
                    'user_id' => auth()->id(),
                    'created_by' => auth()->id(),
                ]);
            }
            DB::commit();
            
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'Asset Master Status Updated',
                'long_description'  => "Status '{$status}' set for chassis: {$status_update->chassis_number} (ID: {$status_update->id}).",
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'asset_master.vehicle_status_update',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
        
            $statusText = $request->status == "accepted" ? 'The Asset Master Vehicle Chassis Number ' . $status_update->chassis_number . ' Accepted Successfully!' : 'The Asset Master Vehicle Chassis Number ' . $status_update->chassis_number . ' Rejected Successfully!';
        
            return response()->json([
                'success' => true,
                'message' => $statusText,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'Asset Master Status Update Failed (Exception)',
                'long_description'  => 'Error: ' . substr($e->getMessage(), 0, 1000),
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'asset_master.vehicle_status_update',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
        
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error'=>$e->getMessage()
            ]);
        }

    }
    
    public function bulk_vehicle_status_update(Request $request)
    {
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
        
        $request->validate([
            'get_ids' => 'required|array',
            'status' => 'required|in:accepted,rejected',
        ]);
    

    
        $status = $request->status;
        $remarks_template = $status === 'accepted'
            ? 'The Asset Master Vehicle Chassis Number :chassis has been Accepted'
            : ($request->remarks ?? 'The Asset Master Vehicle Chassis Number :chassis has been Rejected');
    
        // Fetch all in a single query
        $vehicles = AssetMasterVehicle::whereIn('id', $request->get_ids)
            ->where('is_status', 'uploaded')
            ->get();
        
        if ($vehicles->isEmpty()) {
        
            return response()->json([
                'success' => false,
                'message' => 'No Asset Master Vehicles found. Please ensure the selected assets have been uploaded before using bulk operations.',
            ]);
        }

        
    
        $inventory_data = [];
        $log_data = [];
        $updated_chassis_numbers = [];
        
        // Only if accepting, get last LOT number once
        if ($status == 'accepted') {
            $last = AssetVehicleInventory::selectRaw("CAST(SUBSTRING(id, 4) AS UNSIGNED) as lot_number")
                ->orderByDesc('lot_number')
                ->first();
    
            $lastNumber = $last ? (int)$last->lot_number : 1000;
        }
    
        foreach ($vehicles as $vehicle) {
            $vehicle->is_status = $status;
            $vehicle->save();
    
            $remarks = str_replace(':chassis', $vehicle->chassis_number, $remarks_template);
    
            $log_data[] = [
                'asset_vehicle_id' => $vehicle->id,
                'user_id' => auth()->id(),
                'remarks' => $remarks,
                'status_type' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ];
    
            $updated_chassis_numbers[] = $vehicle->chassis_number;
            
            if ($status == 'accepted') {
                $lastNumber++; 
                // $gen_lot_id = 'LOT' . $lastNumber;
                 $gen_lot_id = $vehicle->chassis_number.'/'.$vehicle->permanent_reg_number;
                
                // $inventory_data = [
                //     'id' => $gen_lot_id,
                //     'asset_vehicle_id' => $vehicle->id,
                //     'asset_vehicle_status' => 'accepted',
                //     'is_status' => 1,
                //     'transfer_status'=>$vehicle->vehicle_status,
                //     'user_id' => auth()->id(),
                //     'created_by' => auth()->id(), 
                // ];
                
             $inventory_data[] = [ // ✅ Append instead of overwrite
                'id' => $gen_lot_id,
                'asset_vehicle_id' => $vehicle->id,
                'asset_vehicle_status' => 'accepted',
                'is_status' => 1,
                'transfer_status' => $vehicle->vehicle_status,
                'user_id' => auth()->id(),
                'created_by' => auth()->id(), 
          ];
            }
        }

        // Bulk insert logs
        if (!empty($log_data)) {
            AssetMasterVehicleLogHistory::insert($log_data);
        }
        
        if (!empty($inventory_data)) {
            AssetVehicleInventory::insert($inventory_data);
        }
        
        $idsSample = implode(',', array_slice($request->get_ids, 0, 10));
        $more      = count($request->get_ids) > 10 ? '…' : '';
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Asset Master Bulk Status Update Completed',
            'long_description'  => "Status '{$status}' applied to ".count($updated_chassis_numbers)." vehicles. IDs: {$idsSample}{$more}",
            'role'              => $roleName,
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'asset_master.bulk_vehicle_status_update',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Asset Master Bulk Update Successfully',
            'chassis_numbers' => $updated_chassis_numbers,
        ]);
    }



    // public function logs_history(Request $request){
       
    //   $sub = DB::table('asset_master_vehicle_log_history')
    //     ->select(DB::raw('MAX(id) as id'))
    //     ->groupBy('asset_vehicle_id');
    
    //     $asset_logs = AssetMasterVehicleLogHistory::whereIn('id', $sub)
    //         ->orderBy('id', 'desc')
    //         ->get();
    
    //     return view('assetmaster::asset_master.log_history', compact('asset_logs'));
    // }
    
//now using 
public function logs_history(Request $request)
{
    // Handle AJAX requests from DataTables
    if ($request->ajax()) {
        try {
            // Base subquery to get latest log IDs for each vehicle
            $subquery = DB::table('asset_master_vehicle_log_history')
                ->select(DB::raw('MAX(id) as id'))
                ->groupBy('asset_vehicle_id');

            // Get filter parameters
            $timeline = $request->input('timeline', '');
            $from_date = $request->input('from_date', '');
            $to_date = $request->input('to_date', '');
            $city = $request->input('city', '');
            $zone_id = $request->input('zone');
            $customer_id = $request->input('customer');
            $accountability_type_id = $request->input('accountability_type');
            $searchValue = $request->input('search.value', '');

            // Apply timeline filters if specified
            if ($timeline) {
                $now = Carbon::now();
                switch ($timeline) {
                    case 'today':
                        $subquery->whereDate('created_at', $now->toDateString());
                        break;
                    case 'this_week':
                        $subquery->whereBetween('created_at', [
                            $now->startOfWeek()->toDateTimeString(),
                            $now->endOfWeek()->toDateTimeString()
                        ]);
                        break;
                    case 'this_month':
                        $subquery->whereBetween('created_at', [
                            $now->startOfMonth()->toDateTimeString(),
                            $now->endOfMonth()->toDateTimeString()
                        ]);
                        break;
                    case 'this_year':
                        $subquery->whereBetween('created_at', [
                            $now->startOfYear()->toDateTimeString(),
                            $now->endOfYear()->toDateTimeString()
                        ]);
                        break;
                }
            } else {
                // Apply date range filters if specified
                if ($from_date) {
                    $subquery->whereDate('created_at', '>=', $from_date);
                }
                if ($to_date) {
                    $subquery->whereDate('created_at', '<=', $to_date);
                }
            }

            // Apply city filter if specified
            if (!empty($city)) {
                 $vehicle_ids = DB::table('ev_tbl_asset_master_vehicles as v')
                ->join('vehicle_qc_check_lists as qc', 'v.qc_id', '=', 'qc.id')
                ->when(!empty($city), function ($query) use ($city) {
                    $query->where('qc.location', $city);
                })
                ->when(!empty($zone_id), function ($query) use ($zone_id) {
                    $query->where('qc.zone_id', $zone_id);
                })
                ->when(!empty($customer_id), function ($query) use ($customer_id) {
                    $query->where('qc.customer_id', $customer_id);
                })
                ->when(!empty($accountability_type_id), function ($query) use ($accountability_type_id) {
                    $query->where('qc.accountability_type', $accountability_type_id);
                })
                ->pluck('v.id');


                if ($vehicle_ids->isNotEmpty()) {
                    $subquery->whereIn('asset_vehicle_id', $vehicle_ids);
                } else {
                    // Force empty result if no vehicles match city
                    $subquery->whereRaw('1 = 0');
                }
            }

            // Get the log IDs from subquery
            $log_ids = $subquery->pluck('id');

            // Main query with joins
            $query = AssetMasterVehicleLogHistory::whereIn('asset_master_vehicle_log_history.id', $log_ids)
                  ->select([
                    'asset_master_vehicle_log_history.*',
                    'v.id as vehicle_id',
                    'v.chassis_number',
                    'v.telematics_serial_no',
                    'l.city_name as location_name',
                    'vt.name as vehicle_type_name', // from vehicle_types table
                    'vm.vehicle_model as vehicle_model_name' // from vehicle_models table
                ])
                ->leftJoin('ev_tbl_asset_master_vehicles as v', 'asset_master_vehicle_log_history.asset_vehicle_id', '=', 'v.id')
                ->leftJoin('ev_tbl_city as l', 'v.city_code', '=', 'l.id')
                ->leftJoin('ev_tbl_vehicle_models as vm', 'v.model', '=', 'vm.id')
                ->leftJoin('vehicle_types as vt', 'vm.vehicle_type', '=', 'vt.id'); // NEW join


            // Apply search filter if provided
            if (!empty($searchValue)) {
                $query->where(function($q) use ($searchValue) {
                    $q->where('v.id', 'like', "%$searchValue%")
                      ->orWhere('v.chassis_number', 'like', "%$searchValue%")
                      ->orWhere('v.telematics_serial_no', 'like', "%$searchValue%")
                     ->orWhere('vt.name', 'like', "%$searchValue%")
                        ->orWhere('vm.vehicle_model', 'like', "%$searchValue%")
                      ->orWhere('l.city_name', 'like', "%$searchValue%");
                });

            }

            // Get total records count (before pagination)
            $totalRecords = $query->count();

            // Apply ordering
            $orderColumnIndex = $request->input('order.0.column');
            $orderColumn = $request->input("columns.$orderColumnIndex.data");
            $orderDirection = $request->input('order.0.dir', 'asc');
            
          $sortableColumns = [
            'vehicle_id' => 'v.id',
            'location' => 'l.city_name',
            'chassis_number' => 'v.chassis_number',
            'telematics_serial_no' => 'v.telematics_serial_no',
            'vehicle_type' => 'vt.name',  // from vehicle_types
            'vehicle_model' => 'vm.vehicle_model', // from vehicle_models
            'updated_at' => 'asset_master_vehicle_log_history.updated_at'
        ];


            
            if (isset($sortableColumns[$orderColumn])) {
                $query->orderBy($sortableColumns[$orderColumn], $orderDirection);
            }

            // Apply pagination
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            if ($length != -1) {
                $query->skip($start)->take($length);
            }

            // Get the final data
            $data = $query->get();
        
            // Format the response
            $formattedData = $data->map(function($item) {
    try {
        // 1. First encrypt the values
        $log_id = encrypt($item->id);
        $asset_vehicle_id = encrypt($item->asset_vehicle_id);
        
        // 2. URL encode the encrypted values to prevent corruption in URLs
        $encoded_log_id = urlencode($log_id);
        $encoded_vehicle_id = urlencode($asset_vehicle_id);
        
        // 3. Generate the route URL with encoded values
        $preview_url = route('admin.asset_management.asset_master.log_history.preview', [
            'log_id' => $encoded_log_id,
            'asset_vehicle_id' => $encoded_vehicle_id
        ]);
        
        // Debug logging
        \Log::debug('Generated preview URL', [
            'raw_log_id' => $item->id,
            'raw_vehicle_id' => $item->asset_vehicle_id,
            'encrypted_log_id' => $log_id,
            'encrypted_vehicle_id' => $asset_vehicle_id,
            'encoded_log_id' => $encoded_log_id,
            'encoded_vehicle_id' => $encoded_vehicle_id,
            'preview_url' => $preview_url
        ]);
        
        return [
            'checkbox' => '<div class="form-check">
                <input class="form-check-input sr_checkbox" 
                       style="width:25px; height:25px;" 
                       name="is_select[]" 
                       type="checkbox" 
                       value="'.$item->asset_vehicle_id.'">
            </div>',
            'vehicle_id' => $item->vehicle_id ?? 'N/A',
            'location' => $item->location_name ?? 'N/A',
            'chassis_number' => $item->chassis_number ?? 'N/A',
            'telematics_serial_no' => $item->telematics_serial_no ? (string) $item->telematics_serial_no : 'N/A',
            'vehicle_type' => $item->vehicle_type_name ?? 'N/A',
            'vehicle_model' => $item->vehicle_model_name ?? 'N/A',
            'updated_at' => $item->updated_at ? Carbon::parse($item->updated_at)->format('d M Y, h:i A') : 'N/A',
            'action' => '<div class="dropdown">
                <button type="button" class="btn btn-sm dropdown-toggle custom-dropdown-toggle" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end text-center p-1">
                    <li>
                        <a href="'.$preview_url.'" 
                           class="dropdown-item d-flex align-items-center justify-content-center">
                            <i class="bi bi-eye me-2 fs-5"></i> View
                        </a>
                    </li>
                </ul>
            </div>',
            // For debugging purposes
            'encrypted_log_id' => $log_id,
            'encrypted_asset_vehicle_id' => $asset_vehicle_id,
            'raw_log_id' => $item->id,
            'raw_vehicle_id' => $item->asset_vehicle_id
        ];
        
    } catch (\Exception $e) {
        \Log::error('Error generating DataTables row', [
            'error' => $e->getMessage(),
            'item_id' => $item->id ?? null,
            'trace' => $e->getTraceAsString()
        ]);
        
        return [
            // ... other fields with empty values ...
            'action' => '<span class="text-danger">Error generating link</span>'
        ];
    }
});

            return response()->json([
                'draw' => (int)$request->input('draw'),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords, // Same as recordsTotal since we filtered in subquery
                'data' => $formattedData
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in logs_history AJAX: ' . $e->getMessage());
            return response()->json([
                'draw' => (int)$request->input('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'An error occurred while loading data. Please try again.'
            ], 500);
        }
    }

    // For non-AJAX requests (initial page load)
    $timeline = $request->timeline ?? '';
    $from_date = $request->from_date ?? '';
    $to_date = $request->to_date ?? '';
    $city = $request->city ?? '';
    $locations = City::where('status', 1)->get();
        $accountablity_types = EvTblAccountabilityType::where('status', 1)->get();
    $customers = CustomerMaster::where('status',1)->get();
    
    // Get total count for display
    $total_count = AssetMasterVehicleLogHistory::select(DB::raw('COUNT(DISTINCT asset_vehicle_id) as count'))->value('count');

    return view('assetmaster::asset_master.log_history', [
        'timeline' => $timeline,
        'from_date' => $from_date,
        'to_date' => $to_date,
        'city' => $city,
        'locations' => $locations,
        'customers' => $customers ,
        'accountablity_types' => $accountablity_types ,
        'total_count' => $total_count
    ]);
}

 //New code   
public function log_history_preview(Request $request) {
    
         $log_id = decrypt($request->log_id);
        $asset_vehicle_id = decrypt($request->asset_vehicle_id);




    $vehicle_types = VehicleType::where('is_active', 1)->get();
    $locations = LocationMaster::where('status',1)->get();
    $passed_chassis_numbers = AssetMasterVehicle::where('qc_status','pass')
        ->where('is_status','!=','pending')
        ->get();

    $vehicle_data = AssetMasterVehicle::where('id', $asset_vehicle_id)
        ->where('is_status', '!=', 'pending')
        ->first();

    if (!$vehicle_data) {
        return back()->with('error', 'Asset Vehicle Not Found');
    }

    $history_logs = AssetMasterVehicleLogHistory::where('asset_vehicle_id', $asset_vehicle_id)
        ->orderBy('id', 'desc')
        ->get();

    $financing_types = FinancingTypeMaster::where('status',1)->get();
    $asset_ownerships = AssetOwnershipMaster::where('status',1)->get();
    $insurer_names = InsurerNameMaster::where('status',1)->get();
    $insurance_types = InsuranceTypeMaster::where('status',1)->get();
    $hypothecations = HypothecationMaster::where('status',1)->get();
    $registration_types = RegistrationTypeMaster::where('status',1)->get();

    $inventory_locations = InventoryLocationMaster::where('status',1)->get();
    $vehicle_models = VehicleModelMaster::where('status', 1)->get();
    $telematics = TelemetricOEMMaster::where('status',1)->get();
    $colors = ColorMaster::where('status',1)->get();

    return view('assetmaster::asset_master.log_history_preview', compact(
        'log_id','asset_vehicle_id','vehicle_data', 'history_logs',
        'vehicle_types','locations','passed_chassis_numbers' ,
        'financing_types','asset_ownerships','insurer_names',
        'insurance_types','hypothecations','registration_types',
        'inventory_locations','vehicle_models','telematics','colors'
    ));
}


// Add this helper method to your controller
private function getLogsCount($timeline, $from_date, $to_date, $city)
{
    $subquery = DB::table('asset_master_vehicle_log_history')
        ->select(DB::raw('MAX(id) as id'))
        ->groupBy('asset_vehicle_id');

    // Apply the same filters as the main query
    if ($timeline) {
        $now = Carbon::now();
        switch ($timeline) {
            case 'today':
                $subquery->whereDate('created_at', $now->toDateString());
                break;
            case 'this_week':
                $subquery->whereBetween('created_at', [
                    $now->startOfWeek()->toDateTimeString(),
                    $now->endOfWeek()->toDateTimeString()
                ]);
                break;
            case 'this_month':
                $subquery->whereBetween('created_at', [
                    $now->startOfMonth()->toDateTimeString(),
                    $now->endOfMonth()->toDateTimeString()
                ]);
                break;
            case 'this_year':
                $subquery->whereBetween('created_at', [
                    $now->startOfYear()->toDateTimeString(),
                    $now->endOfYear()->toDateTimeString()
                ]);
                break;
        }
    } else {
        if ($from_date) {
            $subquery->whereDate('created_at', '>=', $from_date);
        }
        if ($to_date) {
            $subquery->whereDate('created_at', '<=', $to_date);
        }
    }

    if (!empty($city)) {
        $vehicle_ids = DB::table('ev_tbl_asset_master_vehicles')
            ->where('city_code', $city)
            ->pluck('id');

        if ($vehicle_ids->isNotEmpty()) {
            $subquery->whereIn('asset_vehicle_id', $vehicle_ids);
        } else {
            $subquery->whereRaw('1 = 0');
        }
    }

    $log_ids = $subquery->pluck('id');
    
    return AssetMasterVehicleLogHistory::whereIn('id', $log_ids)->count();
}

    
    public function vehicle_bulk_upload(Request $request){
        
        return view('assetmaster::asset_master.bulk_upload_table');
    }
    
    public function export_vehicle_detail(Request $request)
    {

        
        $status = $request->status ?? 'all';
        $timeline = $request->timeline ?? '';
        $from_date = $request->from_date ?? '';
        $to_date = $request->to_date ?? '';
        $city = $request->city ?? '';
        $get_ids = $request->get('get_ids', []);
        $get_labels = array_filter($request->get('get_export_labels', []), function ($label) {
            return !is_null($label) && trim($label) !== '';
        });


         $zone = $request->zone;
         $customer = $request->customer;
         $accountability_type = $request->accountability_type;
         
        // $query = AssetMasterVehicle::with('quality_check');

        // if (!empty($status) && $status != "all") {
        //     $query->where('is_status', $status);
        // }
        
        // if ($timeline) {
        //     switch ($timeline) {
        //         case 'today':
        //             $query->whereDate('created_at', today());
        //             break;
        
        //         case 'this_week':
        //             $query->whereBetween('created_at', [
        //                 now()->startOfWeek(), now()->endOfWeek()
        //             ]);
        //             break;
        
        //         case 'this_month':
        //             $query->whereBetween('created_at', [
        //                 now()->startOfMonth(), now()->endOfMonth()
        //             ]);
        //             break;
        
        //         case 'this_year':
        //             $query->whereBetween('created_at', [
        //                 now()->startOfYear(), now()->endOfYear()
        //             ]);
        //             break;
        //     }
        
        //     $from_date = null;
        //     $to_date = null;
        // } else {
        //     if ($from_date) {
        //         $query->whereDate('created_at', '>=', $from_date);
        //     }
        
        //     if ($to_date) {
        //         $query->whereDate('created_at', '<=', $to_date);
        //     }
        // }
        
        // // dd($query->toSql());

        // $lists =  $query->where('qc_status', 'pass')->orderBy('id', 'desc')->get();
        
        // // Get selected IDs
        // $get_ids = $request->get('get_ids', []);
    
        // // Get selected export labels and filter out null/empty values
        // $get_labels = array_filter($request->get('get_export_labels', []), function ($label) {
        //     return !is_null($label) && trim($label) !== '';
        // });
        

        $export = new AssetMasterVehicleExport(
            $request->status,
            $request->from_date,
            $request->to_date,
            $request->timeline,
            $request->get_export_labels ??[] ,
            $request->get_ids ?? [] ,
            $city ,
            $zone , 
            $customer , 
            $accountability_type
        );
        
        $fileName= 'Asset_Master_Vehicles-' . date('d-m-Y') . '.xlsx';
    
        // ✅ Minimal log: just record that an export was done
        audit_log_after_commit([
            'module_id'         => 4,
            'short_description' => 'Vehicle Detail Exported',
            'long_description'  => sprintf(
                    'Asset Master Vehicle detail export triggered. Filters -> Status: %s, From: %s, To: %s, Selected IDs: %d, Timeline: %s, City: %s, Zone: %s, Customer: %s, Accountability Type: %s',
                    $status ?: 'all',
                    $from_date ?: '-',
                    $to_date ?: '-',
                    is_array($get_ids) ? count($get_ids) : 0,
                    $timeline ?: '-',
                    $city ?: '-',
                    $zone ?: '-',
                    $customer ?: '-',
                    $accountability_type ?: '-'
                ),
            'role'              => optional(\Modules\Role\Entities\Role::find(optional(Auth::user())->role))->name ?? 'Unknown',
            'user_id'           => Auth::id(),
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'asset_master.export_vehicle_detail',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
    
        return Excel::download($export, $fileName);


    }
    
     public function export_vehicle_log_and_history(Request $request)
    {
        // dd($request->all());
        
        $timeline = !empty($request->timeline) ? $request->timeline : '';
        $from_date =  !empty($request->from_date) ? $request->from_date : '';
        $to_date = !empty($request->to_date) ? $request->to_date : '';
        $city = !empty($request->city) ? $request->city : '';
        $get_ids = $request->get('get_ids', []);
        $get_labels = array_filter($request->get('get_export_labels', []), function ($label) {
            return !is_null($label) && trim($label) !== '';
        });
        
        $zone = !empty($request->zone) ? $request->zone : '';
        $customer = !empty($request->customer) ? $request->customer : '';
        $accountability_type = !empty($request->accountability_type) ? $request->accountability_type : '';
        
        $export = new AssetVehicleLogHistory(
            $to_date,
            $from_date,
            $timeline,
            $request->get_export_labels ??[] ,
            $request->get_ids ?? [] ,
            $city , 
            $zone ,
            $customer , 
            $accountability_type
            
        );
        
         $fileName = 'Asset_Vehicles_Log_&_History-' . date('d-m-Y') . '.xlsx';

            // ✅ Simple log only
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'Vehicle Log Exported',
                'long_description'  => sprintf(
                    'Asset Vehicle Log & History export triggered. Filters -> From: %s, To: %s, Selected IDs: %d, Timeline: %s, City: %s, Zone: %s, Customer: %s, Accountability Type: %s',
                    $from_date ?: '-',
                    $to_date ?: '-',
                    is_array($get_ids) ? count($get_ids) : 0,
                    $timeline ?: '-',
                    $city ?: '-',
                    $zone ?: '-',
                    $customer ?: '-',
                    $accountability_type ?: '-'
                ),
                'role'              => optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown',
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'asset_master.export_vehicle_log_and_history',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
        
        return Excel::download($export, $fileName);


    }
    
   public function get_qc_data(Request $request)
    {
        
        $vehicle = AssetMasterVehicle::with('quality_check')->find($request->id);
        // dd($request->all(),$vehicle);
    
        if ($vehicle) {
            return response()->json([
                'success' => true,
                'data' => $vehicle,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle not found',
            ], 404);
        }
    }

 public function destroy(Request $request)
    {
        
        $request->validate([
            'id' => 'required|exists:ev_tbl_asset_master_vehicles,id',
            'remarks' => 'required|string'
        ]);
    
        $id = $request->id;
        $remarks = $request->remarks;
    
        $qc = AssetMasterVehicle::find($id);
        $chassisNumber = $qc ? $qc->chassis_number : 'N/A';
    
         $roleName = optional(\Modules\Role\Entities\Role::find(optional(Auth::user())->role))->name ?? 'Unknown';
        if ($qc) {
            // Update the specific QC record
            $qc->delete_status = 1;
            $qc->delete_remarks = $remarks;
            $qc->save();
    
    
            AssetMasterVehicleLogHistory::create([
                'asset_vehicle_id' => $id ,
                'user_id' => auth()->id(),
                'remarks' => $remarks,
                'status_type' => 'deleted',
            ]);
            
            $roleName = optional(\Modules\Role\Entities\Role::find(optional(Auth::user())->role))->name ?? 'Unknown';

            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'Vehicle Deleted',
                'long_description'  => 'Asset Master Vehicle Chassis Number '. $chassisNumber .' has been deleted. Reason: ' . $remarks,
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'asset_master.vehicle_delete',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
    
            return response()->json(['success' => true, 'message' => 'Record deleted successfully.']);
        }
    
        return response()->json(['success' => false, 'message' => 'Record not found.']);
    }

    
    public function vehicle_bulk_upload_form(Request $request){
        
        return view('assetmaster::asset_master.bulk_upload_form');
    }
    
}



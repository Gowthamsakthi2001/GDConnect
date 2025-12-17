<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\AssetMaster\Entities\VehicleTransfer;
use Illuminate\Support\Collection;


// class VehicleTransferLogExport implements FromQuery,FromCollection, WithHeadings
class VehicleTransferLogExport implements FromQuery, WithHeadings
{
    protected $status;
    protected $from_date;
    protected $to_date;
    protected $timeline;
    protected $selectedFields;
    protected $selectedIds;
    protected $chassis_number;
    protected $customer_id;

    public function __construct($status, $from_date, $to_date, $timeline, $selectedFields = [], $selectedIds = [] , $chassis_number,$customer_id)
    {
        $this->status = $status;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->timeline = $timeline;
        $this->selectedFields = array_filter($selectedFields);
        $this->selectedIds = array_filter($selectedIds) ?? [];
        $this->chassis_number = $chassis_number;
        $this->customer_id = $customer_id;
    }

    // public function collection(): Collection
    // {
    //     $query = VehicleTransfer::with([
    //         'transferType',
    //         'customerMaster',
    //     ])
    //      ->withCount([
    //             'transfer_details as return_vehicles_count' => function ($q) {
    //                 $q->where('initial_status', 1)->where('return_status', 1);
    //             },
    //             'transfer_details as total_vehicles_count' => function ($q) {
    //                 $q->where('initial_status', 1);
    //             }
    //         ]);

    //     if (!empty($this->selectedIds)) {
    //         // $query->whereIn('id', $this->selectedIds);
    //         $query->whereIn('id', $this->selectedIds);

    //         // Load all transfer_details for those selected transfers
    //         $query->with([
    //             'transfer_details.asset_vehicle.vehicle_type_relation',
    //             'transfer_details.asset_vehicle.vehicle_model_relation',
    //             'transfer_details.FromLocation',
    //             'transfer_details.ToLocation',
    //             'transfer_details.deliveryman',
    //         ]);
    //     } else {
    //         if (!empty($this->status) && $this->status !== "all") {
    //             $query->where('return_status', $this->status === "closed" ? 1 : 0);
    //         }
            
    //         if (!empty($this->customer_id)) {
    //             $query->where('id', $this->customer_id);
    //         }
                
    //         // Filter by chassis number (only matching transfer_details loaded)
    //         if (!empty($this->chassis_number)) {
    //             $query->whereHas('transfer_details', function ($q) {
    //                 $q->where('chassis_number', $this->chassis_number);
    //             });
    
    //             // Load only matching transfer_details
    //             $query->with(['transfer_details' => function ($q) {
    //                 $q->where('chassis_number', $this->chassis_number)
    //                   ->with([
    //                       'asset_vehicle.vehicle_type_relation',
    //                       'asset_vehicle.vehicle_model_relation',
    //                       'FromLocation',
    //                       'ToLocation',
    //                       'deliveryman',
    //                   ]);
    //             }]);
    //         } else {
    //             // Load all transfer_details if no chassis_number filter
    //             $query->with([
    //                 'transfer_details.asset_vehicle.vehicle_type_relation',
    //                 'transfer_details.asset_vehicle.vehicle_model_relation',
    //                 'transfer_details.FromLocation',
    //                 'transfer_details.ToLocation',
    //                 'transfer_details.deliveryman',
    //             ]);
    //         }

    //         if ($this->timeline) {
    //             switch ($this->timeline) {
    //                 case 'today':
    //                     $query->whereDate('created_at', today());
    //                     break;
    //                 case 'this_week':
    //                     $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    //                     break;
    //                 case 'this_month':
    //                     $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
    //                     break;
    //                 case 'this_year':
    //                     $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
    //                     break;
    //             }
    //         } else {
    //             if ($this->from_date) {
    //                 $query->whereDate('created_at', '>=', $this->from_date);
    //             }
    //             if ($this->to_date) {
    //                 $query->whereDate('created_at', '<=', $this->to_date);
    //             }
    //         }
    //     }

    //     $transfers = $query->latest()->get();
    //     $data = [];

    //     foreach ($transfers as $transfer) {
    //         foreach ($transfer->transferLogs as $detail) {
                
    //             $transferStatus = $detail->is_status == 'initial' ? 'Initial Transferred' : 'Return Transferred';
                
    //             $from_location = $detail->FromLocation;
    //             $to_location = $detail->ToLocation;
    //             $CreatedBy = $detail->CreatedBy;
                
    //             $data[] = [
    //                 $transfer->id,
    //                 $detail->transferType->name ?? '-',
    //                 date('d-m-Y', strtotime($detail->transfer_date)),
    //                 $from_location->name ?? '-',
    //                 $to_location->name ?? '-',
    //                 $transferStatus,
                    
    //                 $detail->remarks ?? '-',
    //                 trim(($CreatedBy->name ?? '') . ' (' . ($CreatedBy->get_role->name ?? '')).')',
    //                 date('d-m-Y h:i:s A', strtotime($detail->created_at))
                    
    //             ];

               
    //         }
    //     }

    //     return collect($data);
    // }
    
      public function query()
    {
        $q = DB::table('ev_tbl_vehicle_transfer_logs as log')
            ->join('ev_tbl_vehicle_transfers as vt','vt.id','=','log.transfer_id')
            ->leftJoin('ev_tbl_inventory_location_master as fl','fl.id','=','log.from_location_source')
            ->leftJoin('ev_tbl_inventory_location_master as tl','tl.id','=','log.to_location_destination')
            ->leftJoin('vehicle_transfer_types as vtt','vtt.id','=','log.transfer_type')
            ->leftJoin('users as u','u.id','=','log.created_by')
            ->leftJoin('roles as r','r.id','=','u.role')

        
                
            ->select(
                'vt.id as transfer_id',
                DB::raw("IFNULL(vtt.name,'-') as transfer_type"),
                DB::raw("DATE_FORMAT(log.transfer_date,'%d-%m-%Y') as transfer_date"),
                DB::raw("IFNULL(fl.name,'-') as from_location"),
                DB::raw("IFNULL(tl.name,'-') as to_location"),

               
                DB::raw("
                    CASE 
                        WHEN log.is_status='initial' THEN 'Initial Transferred'  
                        ELSE 'Return Transferred'
                    END AS transfer_status
                "),

                DB::raw("IFNULL(log.remarks,'-') as remarks"),

                // created_by → Name (Role)
                DB::raw("CONCAT(IFNULL(u.name,'-'),' (',IFNULL(r.name,'-'),')') AS created_by"),

                DB::raw("DATE_FORMAT(log.created_at,'%d-%m-%Y %h:%i:%s %p') as created_at")
            );



        if($this->selectedIds) 
            $q->whereIn('vt.id',$this->selectedIds);

        if(!empty($this->status) && $this->status!="all")
            $q->where("vt.return_status",$this->status=="closed" ? 1 : 0);

        if($this->customer_id)
            $q->where("vt.custom_master_id",$this->customer_id);

        if($this->chassis_number){
            $q->join('ev_tbl_vehicle_transfer_details as td','td.transfer_id','=','vt.id')
              ->where("td.chassis_number",$this->chassis_number);
        }

        if($this->timeline){
            $q->when($this->timeline=="today",fn($x)=>$x->whereDate('log.created_at',today()))
              ->when($this->timeline=="this_week",fn($x)=>$x->whereBetween('log.created_at',[now()->startOfWeek(),now()->endOfWeek()]))
              ->when($this->timeline=="this_month",fn($x)=>$x->whereBetween('log.created_at',[now()->startOfMonth(),now()->endOfMonth()]))
              ->when($this->timeline=="this_year",fn($x)=>$x->whereBetween('log.created_at',[now()->startOfYear(),now()->endOfYear()]));
        } else {
            $q->when($this->from_date,fn($x)=>$x->whereDate('log.created_at','>=',$this->from_date));
            $q->when($this->to_date,fn($x)=>$x->whereDate('log.created_at','<=',$this->to_date));
        }

        return $q->orderBy('vt.id','DESC')      // Top latest first
            ->orderBy('log.created_at','ASC'); // Oldest → newest for that vehicle
    }

    public function headings(): array
    {
        return [
            'Transfer ID',
            'Transfer Type',
            'Transfer Date',
            'From Location (Source)',
            'To Location (Destination)',
            'Transfer Status',
            'Remarks',
            'Created By',
            'Created At'
        ];
    }
}


// namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;
// use Maatwebsite\Excel\Concerns\WithHeadings;
// use Maatwebsite\Excel\Concerns\WithMapping;
// use Carbon\Carbon;
// use Modules\AssetMaster\Entities\VehicleTransfer;
// use Modules\AssetMaster\Entities\VehicleTransferDetail;
// use Maatwebsite\Excel\Concerns\WithMultipleSheets;


// class VehicleTransferDetailSheet implements FromCollection, WithHeadings
// {
//     protected $status;
//     protected $from_date;
//     protected $to_date;
//     protected $timeline;
//     protected $selectedFields;
//     protected $selectedIds;
//     protected $filters;

//     public function __construct($status, $from_date, $to_date, $timeline, $selectedFields = [] , $selectedIds = [])
//     {

//         $this->status = $status;
//         $this->from_date = $from_date;
//         $this->to_date = $to_date;
//         $this->timeline = $timeline;
//         $this->selectedFields = array_filter($selectedFields); 
//         $this->selectedIds = array_filter($selectedIds) ?? []; 
        
        
        
        
//     }

//   public function collection()
//     {
//         $query = VehicleTransfer::with(['transfer_details', 'transferLogs', 'transferType'])
//             ->withCount([
//                 'transfer_details as return_vehicles_count' => function ($q) {
//                     $q->where('initial_status', 1)->where('return_status', 1);
//                 },
//                 'transfer_details as running_vehicles_count' => function ($q) {
//                     $q->where('initial_status', 1)->where('return_status', 0);
//                 }
//             ]);
    
//         if (!empty($this->selectedIds)) {
//             $query->whereIn('id', $this->selectedIds);
//         } else {
//             if (!empty($this->status) && $this->status != "all") {
//                 $is_status =  $this->status == "closed" ? 1 : 0;
//                 $query->where('return_status', $is_status);
//             }
    
//             if ($this->timeline) {
//                 switch ($this->timeline) {
//                     case 'today':
//                         $query->whereDate('created_at', today());
//                         break;
//                     case 'this_week':
//                         $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
//                         break;
//                     case 'this_month':
//                         $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
//                         break;
//                     case 'this_year':
//                         $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
//                         break;
//                 }
    
//                 $this->from_date = null;
//                 $this->to_date = null;
//             } else {
//                 if ($this->from_date) {
//                     $query->whereDate('created_at', '>=', $this->from_date);
//                 }
    
//                 if ($this->to_date) {
//                     $query->whereDate('created_at', '<=', $this->to_date);
//                 }
//             }
//         }
    
//         return $query->latest()->get();
//     }
    
    

    

//     public function headings(): array
//     {
//         return [
//             'Transfer ID',
//             'Transfer Date',
//             'Customer ID',
//             'Customer Name',
//             'From Location (Source)',
//             'To Location (Destination)',
//             'Chassis_number',
//             'Vehicle Type',
//             'Vehicle Model',
//             'Rider ID',
//             'Rider Name',
//             'Transfer Status',
//             'Vehicle Status'
//         ];
//     }
// }

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\AssetMaster\Entities\VehicleTransfer;
use Illuminate\Support\Collection;


class VehicleTransferDetailSheet implements FromCollection, WithHeadings
{
    protected $status;
    protected $from_date;
    protected $to_date;
    protected $timeline;
    protected $selectedFields;
    protected $selectedIds;
    protected $chassis_number;


    public function __construct($status, $from_date, $to_date, $timeline, $selectedFields = [], $selectedIds = [] , $chassis_number)
    {
        $this->status = $status;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->timeline = $timeline;
        $this->selectedFields = array_filter($selectedFields);
        $this->selectedIds = array_filter($selectedIds) ?? [];
        $this->chassis_number = $chassis_number;
    }

    public function collection(): Collection
    {
        $query = VehicleTransfer::with([
            'transferType',
            'customerMaster',
        ])
         ->withCount([
                'transfer_details as return_vehicles_count' => function ($q) {
                    $q->where('initial_status', 1)->where('return_status', 1);
                },
                'transfer_details as total_vehicles_count' => function ($q) {
                    $q->where('initial_status', 1);
                }
            ]);

        if (!empty($this->selectedIds)) {
            // $query->whereIn('id', $this->selectedIds);
            $query->whereIn('id', $this->selectedIds);

        // Load all transfer details for selected IDs
            $query->with([
                'transfer_details.asset_vehicle.vehicle_type_relation',
                'transfer_details.asset_vehicle.vehicle_model_relation',
                'transfer_details.FromLocation',
                'transfer_details.ToLocation',
                'transfer_details.deliveryman',
            ]);
        } else {
            if (!empty($this->status) && $this->status !== "all") {
                $query->where('return_status', $this->status === "closed" ? 1 : 0);
            }
            
            if (!empty($this->chassis_number)) {
                // Filter the parent records by having at least one matching detail
                $query->whereHas('transfer_details', function ($q) {
                    $q->where('chassis_number', $this->chassis_number);
                });
                
            
                // Load only the matching transfer_details
                $query->with(['transfer_details' => function ($q) {
                    $q->where('chassis_number', $this->chassis_number)
                      ->with([
                          'asset_vehicle.vehicle_type_relation',
                          'asset_vehicle.vehicle_model_relation',
                          'FromLocation',
                          'ToLocation',
                          'deliveryman',
                      ]);
                }]);
            } else {
                // Load all transfer details when no chassis_number filter
                $query->with([
                    'transfer_details.asset_vehicle.vehicle_type_relation',
                    'transfer_details.asset_vehicle.vehicle_model_relation',
                    'transfer_details.FromLocation',
                    'transfer_details.ToLocation',
                    'transfer_details.deliveryman',
                ]);
            }
            
            


            if ($this->timeline) {
                switch ($this->timeline) {
                    case 'today':
                        $query->whereDate('created_at', today());
                        break;
                    case 'this_week':
                        $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'this_month':
                        $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                        break;
                    case 'this_year':
                        $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
                        break;
                }
            } else {
                if ($this->from_date) {
                    $query->whereDate('created_at', '>=', $this->from_date);
                }
                if ($this->to_date) {
                    $query->whereDate('created_at', '<=', $this->to_date);
                }
            }
        }

        $transfers = $query->latest()->get();
        $data = [];

        foreach ($transfers as $transfer) {
            foreach ($transfer->transfer_details as $detail) {
                $vehicle = $detail->asset_vehicle;
                $from_location = $detail->FromLocation;
                $to_location = $detail->ToLocation;
                $deliveryMan = $detail->deliveryman;
                // if($transfer->return_vehicles_count == $transfer->total_vehicles_count){
                //     $data[] = [
                //         $transfer->id,
                //         $transfer->transferType->name ?? '-',
                //         date('d-m-Y',strtotime($transfer->transfer_date)),
                //         $transfer->customerMaster->id ?? '-',
                //         $transfer->customerMaster->name ?? '-',
                //         $from_location->name ?? '-',
                //         $to_location->name ?? '-',
                //         $vehicle->chassis_number ?? '-',
                //         $vehicle->vehicle_type_relation->name ?? '-',
                //         $vehicle->vehicle_model_relation->vehicle_model ?? '-',
                //         $deliveryMan->emp_id ?? '',
                //         trim(($deliveryMan->first_name ?? '') . ' ' . ($deliveryMan->last_name ?? '')),
                //         'Closed',
                //         'Return',
                //         $detail->return_remarks ?? '-'
                //     ];
                // }else{
                //      $data[] = [
                //         $transfer->id,
                //         $transfer->transferType->name ?? '-',
                //         date('d-m-Y',strtotime($transfer->transfer_date)),
                //         $transfer->customerMaster->id ?? '-',
                //         $transfer->customerMaster->name ?? '-',
                //         $from_location->name ?? '-',
                //         $to_location->name ?? '-',
                //         $vehicle->chassis_number ?? '-',
                //         $vehicle->vehicle_type_relation->name ?? '-',
                //         $vehicle->vehicle_model_relation->vehicle_model ?? '-',
                //         $deliveryMan->emp_id ?? '',
                //         trim(($deliveryMan->first_name ?? '') . ' ' . ($deliveryMan->last_name ?? '')),
                //         'Active',
                //         $detail->return_status == 1 ? 'Return' : 'Running',
                //         $detail->return_remarks ?? '-'
                //     ];
                // }
                
                $transferStatus = ($transfer->return_vehicles_count == $transfer->total_vehicles_count) ? 'Closed' : 'Active';
                $vehicleStatus = ($transfer->return_vehicles_count == $transfer->total_vehicles_count) 
                    ? 'Return'
                    : ($detail->return_status == 1 ? 'Return' : 'Running');
                
                $data[] = [
                    $transfer->id,
                    $transfer->transferType->name ?? '-',
                    date('d-m-Y', strtotime($transfer->transfer_date)),
                    $transfer->customerMaster->id ?? '-',
                    $transfer->customerMaster->trade_name ?? '-',
                    $from_location->name ?? '-',
                    $to_location->name ?? '-',
                    $vehicle->chassis_number ?? '-',
                    $vehicle->vehicle_type_relation->name ?? '-',
                    $vehicle->vehicle_model_relation->vehicle_model ?? '-',
                    $deliveryMan->emp_id ?? '',
                    trim(($deliveryMan->first_name ?? '') . ' ' . ($deliveryMan->last_name ?? '')),
                    $transferStatus,
                    $vehicleStatus,
                    $detail->return_remarks ?? '-'
                ];

               
            }
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Transfer ID',
            'Transfer Type',
            'Transfer Date',
            'Customer ID',
            'Customer Name (Trade Name)',
            'From Location (Source)',
            'To Location (Destination)',
            'Chassis Number',
            'Vehicle Type',
            'Vehicle Model',
            'Rider ID',
            'Rider Name',
            'Transfer Status',
            'Vehicle Status',
            'Remarks'
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

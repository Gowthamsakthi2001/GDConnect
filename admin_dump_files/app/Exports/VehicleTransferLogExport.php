<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\AssetMaster\Entities\VehicleTransfer;
use Illuminate\Support\Collection;


class VehicleTransferLogExport implements FromCollection, WithHeadings
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

            // Load all transfer_details for those selected transfers
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
                
            // Filter by chassis number (only matching transfer_details loaded)
            if (!empty($this->chassis_number)) {
                $query->whereHas('transfer_details', function ($q) {
                    $q->where('chassis_number', $this->chassis_number);
                });
    
                // Load only matching transfer_details
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
                // Load all transfer_details if no chassis_number filter
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
            foreach ($transfer->transferLogs as $detail) {
                
                $transferStatus = $detail->is_status == 'initial' ? 'Initial Transferred' : 'Return Transferred';
                
                $from_location = $detail->FromLocation;
                $to_location = $detail->ToLocation;
                $CreatedBy = $detail->CreatedBy;
                
                $data[] = [
                    $transfer->id,
                    $detail->transferType->name ?? '-',
                    date('d-m-Y', strtotime($detail->transfer_date)),
                    $from_location->name ?? '-',
                    $to_location->name ?? '-',
                    $transferStatus,
                    $detail->remarks ?? '-',
                    trim(($CreatedBy->name ?? '') . ' (' . ($CreatedBy->get_role->name ?? '')).')',
                    date('d-m-Y h:i:s A', strtotime($detail->created_at))
                    
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

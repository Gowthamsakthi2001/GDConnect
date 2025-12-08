<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Modules\AssetMaster\Entities\VehicleTransfer;
use Modules\AssetMaster\Entities\VehicleTransferDetail;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class VehicleTransferExport implements WithMultipleSheets
{
    
    protected $status;
    protected $from_date;
    protected $to_date;
    protected $timeline;
    protected $selectedFields;
    protected $selectedIds;
    protected $chassis_number;
     protected $customer_id;

    public function __construct($status, $from_date, $to_date, $timeline, $selectedFields = [] , $selectedIds = [] ,$chassis_number,$customer_id)
    {
        //   dd($status,$from_date,$to_date,$timeline,$selectedFields,$selectedIds);
        $this->status = $status;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->timeline = $timeline;
        $this->selectedFields = array_filter($selectedFields); 
        $this->selectedIds = array_filter($selectedIds) ?? []; 
        $this->chassis_number = $chassis_number;
        $this->customer_id = $customer_id;
    }

   public function sheets(): array
    {
        $sheets = [];
    
        $sheets[] = new VehicleTransferSheet($this->status, $this->from_date,$this->to_date, $this->timeline,$this->selectedFields,$this->selectedIds ,$this->chassis_number,$this->customer_id);
    
        if (in_array('vehicle_transfer_detail_view', $this->selectedFields)) {
            $sheets[] = new VehicleTransferDetailSheet($this->status, $this->from_date, $this->to_date, $this->timeline, $this->selectedFields, $this->selectedIds , $this->chassis_number,$this->customer_id);
        }
    
        if (in_array('vehicle_transfer_logs', $this->selectedFields)) {
            $sheets[] = new VehicleTransferLogExport( $this->status, $this->from_date, $this->to_date, $this->timeline, $this->selectedFields,$this->selectedIds , $this->chassis_number ,$this->customer_id);
        }
    
        return $sheets;
    }

    
    // protected $status;
    // protected $from_date;
    // protected $to_date;
    // protected $timeline;
    // protected $selectedFields;
    // protected $selectedIds;

    // public function __construct($status, $from_date, $to_date, $timeline, $selectedFields = [] , $selectedIds = [])
    // {
    //     // dd($status,$from_date,$to_date,$timeline,$selectedFields,$selectedIds);
    //     $this->status = $status;
    //     $this->from_date = $from_date;
    //     $this->to_date = $to_date;
    //     $this->timeline = $timeline;
    //     $this->selectedFields = array_filter($selectedFields); 
    //     $this->selectedIds = array_filter($selectedIds) ?? []; 
        
        
        
        
    // }
    
    // public function collection()
    // {
    //     $query = VehicleTransfer::with(['transfer_details', 'transferLogs', 'transferType'])
    //         ->withCount([
    //             'transfer_details as return_vehicles_count' => function ($q) {
    //                 $q->where('initial_status', 1)->where('return_status', 1);
    //             },
    //             'transfer_details as running_vehicles_count' => function ($q) {
    //                 $q->where('initial_status', 1)->where('return_status', 0);
    //             }
    //         ]);
    
    //     if (!empty($this->selectedIds)) {
    //         $query->whereIn('id', $this->selectedIds);
    //     } else {
    //         if (!empty($this->status) && $this->status != "all") {
    //             $is_status =  $this->status == "closed" ? 1 : 0;
    //             $query->where('return_status', $is_status);
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
    
    //             $this->from_date = null;
    //             $this->to_date = null;
    //         } else {
    //             if ($this->from_date) {
    //                 $query->whereDate('created_at', '>=', $this->from_date);
    //             }
    
    //             if ($this->to_date) {
    //                 $query->whereDate('created_at', '<=', $this->to_date);
    //             }
    //         }
    //     }
    
    //     return $query->latest()->get();
    // }



    // public function map($row): array
    // {
    //     $mapped = [];
    
    //     foreach (array_filter($this->selectedFields) as $key) {
    //         switch ($key) {
    //             case 'transfer_id':
    //                 $mapped[] = $row->id ?? '-';
    //                 break;
    //             case 'transfer_type':
    //                 $mapped[] = $row->transferType->name ?? '-';
    //                 break;
    //             case 'total_vehicles':
    //                 $total_vehicles = count($row->transfer_details);
    //                 $mapped[] = $total_vehicles ?? '0';
    //                 break;
                
    //             case 'return_vehicles':
    //                 $mapped[] = $row->return_vehicles_count ?? '0';
    //                 break;
                
    //             case 'running_vehicles':
    //                 $mapped[] =  !empty($row->running_vehicles_count) ? $row->running_vehicles_count :  '0';
    //                 break;
  
    //             case 'transfer_date':
    //                 $mapped[] = $row->transfer_date 
    //                 ? \Carbon\Carbon::parse($row->transfer_date)->format('d-m-Y')
    //                 : '-';
    //                 break;
                
    //             case 'return_date':
    //                 $last_vehicle = VehicleTransferDetail::where('transfer_id',$row->id)
    //                                 ->where('initial_status',1)->where('return_status',1)->orderBy('id','desc')->first();
    //                 $mapped[] = $last_vehicle->return_transfer_date 
    //                     ? \Carbon\Carbon::parse($last_vehicle->return_transfer_date)->format('d-m-Y')
    //                     : '-';
    //                 break;
                
    //             case 'transfer_status':
    //                 $transferStatus = $row->return_status == 1 ? 'Closed' : 'Active';
    //                 $mapped[] = $transferStatus ?? 0;
    //                 break;
                    
    //             default:
    //         }
    //     }
    
    //     return $mapped;
    // }


    // public function headings(): array
    // {
    //     $headers = [];
    //     foreach ($this->selectedFields as $field) {
    //         $headers[] = ucfirst(str_replace('_', ' ', $field));
    //     }

    //     return $headers;
    // }

}

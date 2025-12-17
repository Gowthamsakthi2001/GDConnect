<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\AssetMaster\Entities\VehicleTransfer;
use Modules\AssetMaster\Entities\VehicleTransferDetail;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


// class VehicleTransferSheet implements FromCollection, WithHeadings, WithMapping
class VehicleTransferSheet implements FromQuery, WithHeadings, WithMapping

{
    

    
    protected $status;
    protected $from_date;
    protected $to_date;
    protected $timeline;
    protected $selectedFields;
    protected $selectedIds;
    protected $filters;
     protected $chassis_number;
      protected $customer_id;

    public function __construct($status, $from_date, $to_date, $timeline, $selectedFields = [] , $selectedIds = [] , $chassis_number,$customer_id)
    {
        // dd($status,$from_date,$to_date,$timeline,$selectedFields,$selectedIds);
        $this->status = $status;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->timeline = $timeline;
        $this->selectedFields = array_filter($selectedFields); 
        $this->selectedIds = array_filter($selectedIds) ?? []; 
        $this->chassis_number = $chassis_number;
        $this->customer_id = $customer_id;
        
        
        
        
    }
    
    // public function collection()
    // {
    //     $query = VehicleTransfer::with(['transfer_details', 'transferLogs', 'transferType','customerMaster'])
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
    
    //     if (!empty($this->chassis_number)) {
    //         $query->whereHas('transfer_details', function ($q) {
    //             $q->where('chassis_number', $this->chassis_number);
    //         });
    //     }
        
    //      if (!empty($this->customer_id)) {
    //         $query->whereHas('customerMaster', function ($q) {
    //             $q->where('id', $this->customer_id);
    //         });
    //     }

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
    
    
     public function query()
    {
        $query = DB::table('ev_tbl_vehicle_transfers as vt')
            ->leftJoin('ev_tbl_vehicle_transfer_details as vtd', 'vtd.transfer_id', '=', 'vt.id')
            ->leftJoin('vehicle_transfer_types as vtt', 'vtt.id', '=', 'vt.transfer_type')
            ->leftJoin('ev_tbl_customer_master as cm', 'cm.id', '=', 'vt.custom_master_id')
            ->select(
                'vt.id',
                'vt.transfer_date',
                'vt.return_status',
                'vtt.name as transfer_type_name',
                'cm.trade_name as customer_name',

                DB::raw('COUNT(vtd.id) as total_vehicles'),

                DB::raw("SUM(CASE WHEN vtd.initial_status = 1 AND vtd.return_status = 1 THEN 1 ELSE 0 END) as return_vehicles_count"),

                DB::raw("SUM(CASE WHEN vtd.initial_status = 1 AND vtd.return_status = 0 THEN 1 ELSE 0 END) as running_vehicles_count"),
                
                  DB::raw("(SELECT return_transfer_date 
                        FROM ev_tbl_vehicle_transfer_details d 
                        WHERE d.transfer_id = vt.id 
                        AND d.initial_status = 1 
                        AND d.return_status = 1 
                        ORDER BY d.id DESC LIMIT 1
                    ) as return_date")
            )
            ->groupBy('vt.id','vt.transfer_date','vt.return_status','vtt.name','cm.trade_name');

        /* 🔹 FILTERS */
        if (!empty($this->selectedIds)) {
            $query->whereIn('vt.id', $this->selectedIds);
        } else {

            if (!empty($this->status) && $this->status != "all") {
                $query->where('vt.return_status', $this->status == "closed" ? 1 : 0);
            }

            if (!empty($this->chassis_number)) {
                $query->where('vtd.chassis_number', $this->chassis_number);
            }

            if (!empty($this->customer_id)) {
                $query->where('vt.custom_master_id', $this->customer_id);
            }

            // 🔥 Timeline filters
            if ($this->timeline) {
                $query->when($this->timeline === 'today', fn($q) => $q->whereDate('vt.created_at', today()))
                      ->when($this->timeline === 'this_week', fn($q) => $q->whereBetween('vt.created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                      ->when($this->timeline === 'this_month', fn($q) => $q->whereBetween('vt.created_at', [now()->startOfMonth(), now()->endOfMonth()]))
                      ->when($this->timeline === 'this_year', fn($q) => $q->whereBetween('vt.created_at', [now()->startOfYear(), now()->endOfYear()]));

                $this->from_date = $this->to_date = null;
            } else {

                $query->when($this->from_date, fn($q) => $q->whereDate('vt.created_at','>=',$this->from_date))
                      ->when($this->to_date, fn($q) => $q->whereDate('vt.created_at','<=',$this->to_date));
            }
        }

        return $query->orderByDesc('vt.id'); // ✔ important
    }




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
    //                 $mapped[] = $last_vehicle?->return_transfer_date 
    //                     ? \Carbon\Carbon::parse($last_vehicle->return_transfer_date)->format('d-m-Y')
    //                     : '-';
    //                 break;
    //             case 'customer':
    //                 $mapped[] = $row->customerMaster->trade_name ?? '-';
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
    
    public function map($row): array
{
    $data = [];

    foreach ($this->selectedFields as $key) {
        $value = null;

        switch ($key) {
            case 'transfer_id':
                $value = $row->id;
                break;

            case 'transfer_type':
                $value = $row->transfer_type_name;
                break;

            case 'total_vehicles':
                $value = $row->total_vehicles;
                break;

            case 'return_vehicles':
                $value = ($row->return_vehicles_count > 0) ? $row->return_vehicles_count : '';
                break;

            case 'running_vehicles':
                $value = $row->running_vehicles_count;
                break;

            case 'customer':
                $value = $row->customer_name;
                break;

            case 'transfer_date':
                $value = $row->transfer_date
                    ? Carbon::parse($row->transfer_date)->format('d-m-Y')
                    : null;
                break;

            case 'return_date':
                $value = $row->return_date ? Carbon::parse($row->return_date)->format('d-m-Y') : '-'; break;


            case 'transfer_status':
                $value = $row->return_status == 1 ? 'Closed' : 'Active';
                break;

            default:
                $value = $row->$key ?? null;
        }

        /** 🔥 NULL = blank */
        $data[] = $value ?? '';
    }

    return $data;
}




    public function headings(): array
    {
        $headers = [];
    
        $filteredFields = array_diff($this->selectedFields, ['vehicle_transfer_detail_view']);
    
        foreach ($filteredFields as $field) {
            $headers[] = ucfirst(str_replace('_', ' ', $field));
        }
    
        return $headers;
    }
    
     public function chunkSize(): int
    {
        return 500;
    }


}

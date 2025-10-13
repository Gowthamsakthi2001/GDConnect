<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Modules\Deliveryman\Entities\Deliveryman;
use Illuminate\Support\Facades\DB;
class VehicleModelMasterExport implements FromCollection, WithHeadings, WithMapping
{
     protected $status;
     protected $from_date;
     protected $to_date;
      protected $selectedIds;

    public function __construct($status , $from_date , $to_date, $selectedIds = [])
    {
        $this->status = $status;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->selectedIds = $selectedIds;
    }

    public function collection()
    {
        
       
            
         $query = DB::table('ev_tbl_vehicle_models')
            ->join('ev_tbl_brands', 'ev_tbl_vehicle_models.brand', '=', 'ev_tbl_brands.id')
            ->select('ev_tbl_vehicle_models.*', 'ev_tbl_brands.brand_name');
            
     if (!empty($this->selectedIds)) {
        $query->whereIn('ev_tbl_vehicle_models.id', $this->selectedIds);
    } else {
        
        // Apply status filter if it's 1 or 0
        if (in_array($this->status, ['1', '0'])) {
            $query->where('ev_tbl_vehicle_models.status', $this->status);
        }
    
        // Apply date filters
        if (!empty($this->from_date)) {
            $query->whereDate('ev_tbl_vehicle_models.created_at', '>=', $this->from_date);
        }
    
        if (!empty($this->to_date)) {
            $query->whereDate('ev_tbl_vehicle_models.created_at', '<=', $this->to_date);
        }

    }
    
        $results = $query->orderBy('id', 'desc')->get();
    
    
        return $results;
    }

        public function map($row): array
    {
          $vehicleType = \Modules\VehicleManagement\Entities\VehicleType::find($row->vehicle_type);
        return [
            $row->brand_name ?? '-',
            $row->vehicle_model ?? '-',
            $vehicleType->name  ?? '-',
            $row->status == 1 ? 'Active' : 'Inactive',
            date('d-m-Y', strtotime($row->created_at)),
        ];
    }

  

    public function headings(): array
    {
        return [
            'Brand Model Name',
            'Vehicle Model',
            'Vehicle Type' ,
            'Status' ,
            'Created At'
        ];
    }
}

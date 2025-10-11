<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Modules\Deliveryman\Entities\Deliveryman;
use Illuminate\Support\Facades\DB;
use Modules\AssetMaster\Entities\LocationMaster;
use Modules\City\Entities\City; 

class LocationMasterExport implements FromCollection, WithHeadings, WithMapping
{
     protected $status;
     protected $from_date;
     protected $to_date;

    public function __construct($status , $from_date , $to_date)
    {
        $this->status = $status;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
    }

    public function collection()
    {
        
       
        
        $query = LocationMaster::with('state_relation');
    
        // Only apply filter if valid
        if (in_array($this->status, ['1', '0'])) {
            $query->where('status', $this->status);
        }
    
        if ($this->from_date) {
            $query->whereDate('created_at', '>=', $this->from_date);
        }
    
        if ($this->to_date) {
            $query->whereDate('created_at', '<=', $this->to_date);
        }
    
        
        $results = $query->orderBy('id', 'desc')->get();
   
    
        return $results;
    }

       public function map($row): array
    {
        $city = City::where('id' ,$row->city)->first();
        return [
            $row->name ?? '-',
            $row->state_relation->state_name ?? '-',
            $city->city_name ?? '-',
            date('d M Y h:i:s A', strtotime($row->created_at)),
            $row->status == 1 ? 'Active' : 'Inactive'
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'State',
            'City' ,
            'Created At' ,
            'Status'
        ];
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\MasterManagement\Entities\TelemetricOEMMaster; 
use Modules\MasterManagement\Entities\HypothecationMaster;
use Illuminate\Support\Facades\DB;
class HypothecationMasterExport implements FromCollection, WithHeadings, WithMapping
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

     $query = HypothecationMaster::query();

    if (!empty($this->selectedIds)) {
        $query->whereIn('id', $this->selectedIds);
    } else {
    
            if (in_array($this->status, ['0', '1'])) {
                $query->where('status', $this->status);
            }

            if ($this->from_date) {
                $query->whereDate('created_at', '>=', $this->from_date);
            }
        
            if ($this->to_date) {
                $query->whereDate('created_at', '<=', $this->to_date);
            }
            
    }
            
        $results = $query->orderBy('id', 'desc')->get();
    
        return $results;
    }

       public function map($row): array
    {
        return [
            $row->name ?? '-',
            date('d M Y h:i:s A', strtotime($row->created_at)),
            $row->status == 1 ? 'Active' : 'Inactive'
        ];
    }

    public function headings(): array
    {
        return [
            'Hypothecation Name',
            'Created At',
            'Status'
        ];
    }
}

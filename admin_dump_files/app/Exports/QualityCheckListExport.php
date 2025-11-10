<?php

namespace App\Exports;

use Modules\AssetMaster\Entities\QualityCheckMaster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class QualityCheckListExport implements FromCollection, WithHeadings, WithMapping
{
    
    protected $status;
    protected $from_date;
    protected $to_date;
    protected $vehicle_type;
    
    public function __construct($status, $from_date, $to_date, $vehicle_type)
    {
        $this->status = $status;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->vehicle_type = $vehicle_type;
    }
    
   public function collection()
    {
        $query = QualityCheckMaster::with('vehicle_type');

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if (!empty($this->vehicle_type)) {
            $query->where('vehicle_type_id', $this->vehicle_type);
        }

        if (!empty($this->from_date)) {
            $query->whereDate('created_at', '>=', $this->from_date);
        }

        if (!empty($this->to_date)) {
            $query->whereDate('created_at', '<=', $this->to_date);
        }

        return $query->get();
    }

    public function map($row): array
    {
        return [
            $row->label_name ?? 'N/A',
            $row->vehicle_type->name ?? 'N/A',
            $row->created_at ? Carbon::parse($row->created_at)->format('d M Y h:i:s A') : 'N/A',
            $row->status == 1 ? 'Active' : 'Inactive',
        ];
    }

    public function headings(): array
    {
        return [
            'Label Name',
            'Vehicle Type',
            'Created At',
            'Status'
        ];
    }
}

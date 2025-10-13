<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\MasterManagement\Entities\CustomerOperationalHub;

class OperationalHubExport implements FromCollection, WithHeadings, WithMapping
{
    protected $status;
    protected $from_date;
    protected $to_date;
    protected $selectedIds;
    protected $timeline;

    public function __construct($status, $from_date, $to_date,$timeline , $selectedIds = [])
    {
        $this->status = $status;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->selectedIds = $selectedIds;
        $this->timeline = $timeline;
    }

    public function collection()
    {
       $query = CustomerOperationalHub::with('customer');

          if (!empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        } else {
    
    
            if (in_array($this->status, ['0', '1'])) {
                $query->whereHas('customer', function ($q) {
                    $q->where('status', $this->status);
                });
            }
            
                      if ($this->timeline) {
            switch ($this->timeline) {
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
    
            // Overwrite the from_date/to_date to empty for consistency
            $this->from_date = null;
            $this->to_date = null;
        } else {
        
            // Filter by date range
            if ($this->from_date) {
                $query->whereDate('created_at', '>=', $this->from_date);
            }
        
            if ($this->to_date) {
                $query->whereDate('created_at', '<=', $this->to_date);
            }
            
        }
            
    }

        return $query->orderBy('id', 'desc')->get();
    }

    public function map($row): array
    {
        return [
            $row->customer_id ?? '-',
            $row->hub_name ?? '-',
            $row->created_at ? date('d M Y h:i:s A', strtotime($row->created_at)) : '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Customer ID',
            'Hub Name',
            'Created At',
        ];
    }
}


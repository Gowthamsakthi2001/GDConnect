<?php

namespace App\Exports;

use App\Models\EVState;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EVStatesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $status;
    protected $fromDate;
    protected $toDate;
    
    
    
    public function __construct($status = 'all', $fromDate = null, $toDate = null)
    {
        $this->status = $status;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }
    
    public function collection()
    {
        $query = EVState::orderBy('id', 'DESC');
        
        // Apply status filter
        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }
        
        // Apply date range filter
        if ($this->fromDate && $this->toDate) {
            $query->whereBetween('created_at', [
                \Carbon\Carbon::parse($this->fromDate)->startOfDay(),
                \Carbon\Carbon::parse($this->toDate)->endOfDay()
            ]);
        } elseif ($this->fromDate) {
            $query->where('created_at', '>=', \Carbon\Carbon::parse($this->fromDate)->startOfDay());
        } elseif ($this->toDate) {
            $query->where('created_at', '<=', \Carbon\Carbon::parse($this->toDate)->endOfDay());
        }
        
        return $query->get();
    }
    
    public function headings(): array
    {
        return [
            'ID',
            'State Name',
            'State Code',
            'Status',
            'Created At',
            'Updated At'
        ];
    }
    
    public function map($state): array
    {
        return [
            $state->id,
            $state->state_name,
            $state->state_code,
            $state->status ? 'Active' : 'Inactive',
            $state->created_at->format('d M Y h:i:s A'),
            $state->updated_at->format('d M Y h:i:s A')
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        // Style the header row
       return [];
    }
}


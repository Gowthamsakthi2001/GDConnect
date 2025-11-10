<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\MasterManagement\Entities\RecoveryUpdatesMaster;

class RecoveryUpdatesMasterExport implements FromCollection, WithHeadings, WithMapping
{
    protected $status;
    protected $from_date;
    protected $to_date;
    protected $selectedIds;

    public function __construct($status, $from_date, $to_date, $selectedIds = [])
    {
        $this->status      = $status;
        $this->from_date   = $from_date;
        $this->to_date     = $to_date;
        $this->selectedIds = $selectedIds;
    }

    public function collection()
    {
        $query = RecoveryUpdatesMaster::query();

        if (!empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        } else {
            // Filter by status if explicitly 0 or 1
            if (in_array($this->status, ['0', '1', 0, 1], true)) {
                $query->where('status', (int) $this->status);
            }

            // Date range filters on created_at
            if (!empty($this->from_date)) {
                $query->whereDate('created_at', '>=', $this->from_date);
            }

            if (!empty($this->to_date)) {
                $query->whereDate('created_at', '<=', $this->to_date);
            }
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function map($row): array
    {
        return [
            $row->label_name ?? '-',
            date('d M Y h:i:s A', strtotime($row->created_at)),
            ((int) $row->status === 1) ? 'Active' : 'Inactive',
        ];
    }

    public function headings(): array
    {
        return [
            'Label Name',
            'Created At',
            'Status',
        ];
    }
}

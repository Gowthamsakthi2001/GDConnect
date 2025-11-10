<?php

namespace App\Exports;

use Modules\VehicleManagement\Entities\VehicleType;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class VehicleTypeExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return VehicleType::all();
    }

    public function map($row): array
    {
        return [
            $row->name ?? 'N/A',
            $row->created_at ? Carbon::parse($row->created_at)->format('d M Y h:i:s A') : 'N/A',
            $row->is_active == 1 ? 'Active' : 'Inactive',
        ];
    }

    public function headings(): array
    {
        return [
            'Vehicle Type',
            'Created At',
            'Status'
        ];
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use DB;

class QualityCheckBulkExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'QualityCheckImportsheet' => new QualityCheckImportSheetHeaders(),
        ];
    }
}

class QualityCheckImportSheetHeaders implements FromArray
{
    public function array(): array
    {
        return [
            ['Vehicle_Type', 'Vehicle_Model', 'Location', 'Chassis_Number', 'Battery_Number', 'Telematics_Number', 'Motor_Number', 'Image'],
        ];
    }
}



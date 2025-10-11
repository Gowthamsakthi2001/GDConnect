<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class VehicleListSheet implements FromArray, WithTitle
{
    public function array(): array
    {
        return [
            ['Vehicle Type'], // 🟢 Header
            ['Car'],
            ['Bike'],
            ['Truck'],
            ['Bus'],
        ];
    }

    public function title(): string
    {
        return 'VehicleListSheet';
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromArray;

class TelecallerListSheet implements FromArray, WithTitle
{
    public function array(): array
    {
        return [
            ['Telecaller Name'],
            ['John Doe'],
            ['Jane Smith'],
            ['Alex Johnson'],
        ];
    }

    public function title(): string
    {
        return 'Telecaller List'; // ✅ Must match ValidationSheet reference
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class SourceListSheet implements FromArray, WithTitle
{
    public function array(): array
    {
        return [
            ["ID", "Source Name"],
            [1, "Website"],
            [2, "Facebook"],
            [3, "Google Ads"],
            [4, "Referral"],
        ];
    }

    public function title(): string
    {
        return "SourceListSheet";
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Schema;
use Modules\AssetMaster\Entities\AssetMasterVehicle;

class DemoAssetMasterUpload implements FromCollection, WithHeadings, WithMapping
{
    protected $columns;

    public function __construct()
    {

        $this->columns = Schema::getColumnListing((new AssetMasterVehicle)->getTable());
    }

    public function collection()
    {
        return AssetMasterVehicle::select($this->columns)->latest()->get();
    }

    public function map($row): array
    {
        return collect($this->columns)->map(function ($column) use ($row) {
            return $row->$column;
        })->toArray();
    }

    public function headings(): array
    {
        return collect($this->columns)->map(function ($col) {
            return ucfirst(str_replace('_', ' ', $col));
        })->toArray();
    }
}

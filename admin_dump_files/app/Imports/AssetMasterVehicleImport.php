<?php
namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\AssetMaster\Entities\AssetMasterVehicle;

class AssetMasterVehicleImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public function collection(Collection $rows)
    {
        
        dd($rows);
        $batch = [];

        foreach ($rows as $row) {
            $batch[] = [
                'chassis_number' => $row['chassis_number'] ?? null,
                'vehicle_type' => $row['vehicle_type'] ?? null,
                'model' => $row['model'] ?? null,
                // other fields...
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert in batches
        AssetMasterVehicle::insert($batch);
    }

    public function chunkSize(): int
    {
        return 1000; // Safe chunk size
    }
}

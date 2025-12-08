<?php
namespace App\Imports;

use Modules\AssetMaster\Entities\AssetMasterVehicle;
use App\Services\ImportErrorCollector;
use App\Services\AssetMasterUpdateService;

use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\Importable;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssetMasterInventoryBulkImport implements OnEachRow, WithHeadingRow, WithChunkReading
{
    use Importable;

    public function __construct()
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 0);
        set_time_limit(0);

        DB::connection()->disableQueryLog();
        ImportErrorCollector::clear();
    }

    public function onRow(Row $row)
    {
        $rowNumber = $row->getIndex();
        $data = array_change_key_case($row->toArray(), CASE_LOWER);
        
        \Log::info("Bulk Import Excel Sheet Columns ".json_encode($data));

        try {
            if (empty($data['chassis_number'])) {
                ImportErrorCollector::add($rowNumber, null, "Chassis number missing");
                return;
            }

            $vehicle = AssetMasterVehicle::where('chassis_number', $data['chassis_number'])
                ->where('delete_status', 0)
                ->first();

            if (!$vehicle) {
                ImportErrorCollector::add($rowNumber, $data['chassis_number'], "Vehicle not found");
                return;
            }

            // call updateVehicle — this method should return true/false
            $ok = app(AssetMasterUpdateService::class)->updateVehicle($vehicle, $data);

            // if service returned false it has already recorded an error; just continue
            if (!$ok) {
                return;
            }

        } catch (\Throwable $e) {
            Log::error("IMPORT ROW ERROR", [
                'row' => $rowNumber,
                'chassis' => $data['chassis_number'] ?? null,
                'message' => $e->getMessage()
            ]);

            ImportErrorCollector::add($rowNumber, $data['chassis_number'] ?? null, $e->getMessage());
        }
    }

    public function chunkSize(): int
    {
        return 500;
    }
}


// namespace App\Imports;

// use Modules\AssetMaster\Entities\AssetMasterVehicle;
// use App\Services\ImportErrorCollector;
// use App\Services\AssetMasterUpdateService;

// use Maatwebsite\Excel\Concerns\WithHeadingRow;
// use Maatwebsite\Excel\Concerns\WithChunkReading;
// use Maatwebsite\Excel\Concerns\OnEachRow;
// use Maatwebsite\Excel\Row;
// use Maatwebsite\Excel\Concerns\Importable;

// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;

// class AssetMasterInventoryBulkImport implements 
//     OnEachRow, 
//     WithHeadingRow, 
//     WithChunkReading
// {
//     use Importable;

//     public function __construct()
//     {
//         ini_set('memory_limit', '1024M');
//         ini_set('max_execution_time', 0);
//         set_time_limit(0);

//         DB::connection()->disableQueryLog();
//         ImportErrorCollector::clear();
//     }

//     public function onRow(Row $row)
//     {
//         $rowNumber = $row->getIndex();
//         $data = array_change_key_case($row->toArray(), CASE_LOWER);

//         try {

//             if (empty($data['chassis_number'])) {
//                 ImportErrorCollector::add($rowNumber, null, "Chassis number missing");
//                 return;
//             }

//             $vehicle = AssetMasterVehicle::where('chassis_number', $data['chassis_number'])
//                 ->where('delete_status', 0)
//                 ->first();

//             if (!$vehicle) {
//                 ImportErrorCollector::add($rowNumber, $data['chassis_number'], "Vehicle not found");
//                 return;
//             }

//             // Fast update using service
//             app(AssetMasterUpdateService::class)->updateVehicle($vehicle, $data);

//         } catch (\Throwable $e) {

//             Log::error("IMPORT ROW ERROR", [
//                 'row' => $rowNumber,
//                 'chassis' => $data['chassis_number'] ?? null,
//                 'message' => $e->getMessage()
//             ]);

//             ImportErrorCollector::add($rowNumber, $data['chassis_number'] ?? null, $e->getMessage());
//         }
//     }

//     public function chunkSize(): int
//     {
//         return 500;
//     }
// }

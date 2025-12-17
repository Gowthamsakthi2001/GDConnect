<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\From;
use Maatwebsite\Excel\Concerns\FromQuery; 
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Carbon\Carbon;
use Modules\Deliveryman\Entities\Deliveryman;
use Illuminate\Support\Facades\DB;
use Modules\AssetMaster\Entities\QualityCheck;
use Modules\AssetMaster\Entities\QualityCheckMaster;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\Exportable;

// class QualityCheckExport implements FromCollection, WithHeadings, WithMapping, WithDrawings, WithChunkReading
class QualityCheckExport implements FromQuery, WithHeadings, WithMapping, WithDrawings, WithChunkReading
{
    use Exportable;
     protected $status;
     protected $checklistMaster;
     protected $from_date;
     protected $to_date;
    protected $timeline;
    protected $selectedIds;
    protected $selectedFields;
    protected $images = [];
    protected $location;
    protected $zone;
    protected $customer;
    protected $accountability_type;
    protected $vehicle_type;
    protected $vehicle_model;
    protected $vehicle_make;
    
    public function __construct($status , $from_date , $to_date , $timeline, $selectedIds = [] , $selectedFields =[] , $location = '[]', $zone = '[]',
    $customer = '[]' , $accountability_type,$vehicle_type = '[]',$vehicle_model = '[]',$vehicle_make = '[]')
    {
        $this->location     = is_array($location) ? $location : json_decode($location, true);
        $this->zone         = is_array($zone) ? $zone : json_decode($zone, true);
        $this->customer     = is_array($customer) ? $customer : json_decode($customer, true);
        $this->vehicle_type = is_array($vehicle_type) ? $vehicle_type : json_decode($vehicle_type, true);
        $this->vehicle_model= is_array($vehicle_model) ? $vehicle_model : json_decode($vehicle_model, true);
        $this->vehicle_make = is_array($vehicle_make) ? $vehicle_make : json_decode($vehicle_make, true);
        $this->checklistMaster = QualityCheckMaster::pluck('label_name','id')->toArray();

    
        $this->status = $status;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        
        if (!empty($timeline) && $timeline !== 'custom') {
            $this->timeline = $timeline;
        } else {
            $this->timeline = '';
        }

        $this->selectedIds = (array)$selectedIds;
        $this->selectedFields = (array)$selectedFields;
        $this->accountability_type = $accountability_type;
        
        $this->location     = $this->location ?? [];
        $this->zone         = $this->zone ?? [];
        $this->customer     = $this->customer ?? [];
        $this->vehicle_type = $this->vehicle_type ?? [];
        $this->vehicle_model= $this->vehicle_model ?? [];
        $this->vehicle_make = $this->vehicle_make ?? [];
    }

//     public function collection()
//     {
        
       
//       $query = QualityCheck::with('vehicle_model_relation', 'vehicle_type_relation', 'location_relation');

        
        
//      if (!empty($this->selectedIds)) {
//         $query->whereIn('id', $this->selectedIds);
//     } else {
        
//         $query->where('delete_status', 0);
        
//         if (in_array($this->status, ['pass', 'fail' ,'qc_pending'])) {
//             $query->where('status', $this->status);
//         }
        
//         if (!empty($this->location)) {
//             $query->whereIn('location', $this->location);
//         }
        
//         if (!empty($this->zone)) {
//             $query->whereIn('zone_id', $this->zone);
//         }
        
//         if (!empty($this->customer)) {
//             $query->whereIn('customer_id', $this->customer);
//         }
        
//         if (!empty($this->accountability_type)) {
//             $query->where('accountability_type', $this->accountability_type);
//         }
        
//         if (!empty($this->vehicle_type)) {
//             $query->whereIn('vehicle_type', $this->vehicle_type);
//         }
        
//         if (!empty($this->vehicle_model)) {
//             $query->whereIn('vehicle_model', $this->vehicle_model);
//         }
//         if (!empty($this->vehicle_make)) {
//             $query->whereIn('vehicle_model', $this->vehicle_make);
//         }
        
//           if ($this->timeline) {
//             switch ($this->timeline) {
//                 case 'today':
//                     $query->whereDate('created_at', today());
//                     break;
    
//                 case 'this_week':
//                     $query->whereBetween('created_at', [
//                         now()->startOfWeek(), now()->endOfWeek()
//                     ]);
//                     break;
//                 case 'last_15_days':
//                         $query->whereBetween('created_at', [
//                             now()->subDays(14)->startOfDay(),
//                             now()->endOfDay()
//                         ]);
//                         break;
//                 case 'this_month':
//                     $query->whereBetween('created_at', [
//                         now()->startOfMonth(), now()->endOfMonth()
//                     ]);
//                     break;
    
//                 case 'this_year':
//                     $query->whereBetween('created_at', [
//                         now()->startOfYear(), now()->endOfYear()
//                     ]);
//                     break;
//             }
    
//             // Overwrite the from_date/to_date to empty for consistency
//             $this->from_date = null;
//             $this->to_date = null;
//         } else {
//             // Manual date filtering
//             if ($this->from_date) {
//                 $query->whereDate('created_at', '>=', $this->from_date);
//             }
    
//             if ($this->to_date) {
//                 $query->whereDate('created_at', '<=', $this->to_date);
//             }
//         }
// }

        
//         $results = $query->orderBy('id', 'desc')->get();
        
        
    
    
//         return $results;
//     }

public function query()
{
    $query = DB::table('vehicle_qc_check_lists as qc')
        ->leftJoin('ev_tbl_vehicle_models as vm', 'vm.id', '=', 'qc.vehicle_model')
        ->leftJoin('vehicle_types as vt', 'vt.id', '=', 'qc.vehicle_type')
        ->leftJoin('ev_tbl_city as city', 'city.id', '=', 'qc.location')
        ->leftJoin('zones', 'zones.id', '=', 'qc.zone_id')
        ->leftJoin('ev_tbl_customer_master as cust', 'cust.id', '=', 'qc.customer_id')
        ->leftJoin('ev_tbl_accountability_types as atype', 'atype.id', '=', 'qc.accountability_type')
        ->select([
            'qc.*',
            'vm.vehicle_model',
            'vm.make',
            'vt.name as vehicle_type_name',
            'city.city_name',
            'zones.name as zone_name',
            'cust.trade_name as customer_name',
            'atype.name as accountability_type_name',
        ]);

    // ------------------ filters stay SAME -------------------

    if (!empty($this->selectedIds)) {
        $query->whereIn('qc.id', $this->selectedIds);
    } else {

        $query->where('qc.delete_status', 0);

        if (in_array($this->status, ['pass','fail','qc_pending'])) {
            $query->where('qc.status', $this->status);
        }

        if (!empty($this->location)) $query->whereIn('qc.location', $this->location);
        if (!empty($this->zone)) $query->whereIn('qc.zone_id', $this->zone);
        if (!empty($this->customer)) $query->whereIn('qc.customer_id', $this->customer);
        if (!empty($this->accountability_type)) $query->where('qc.accountability_type', $this->accountability_type);
        if (!empty($this->vehicle_type)) $query->whereIn('qc.vehicle_type', $this->vehicle_type);
        if (!empty($this->vehicle_model)) $query->whereIn('qc.vehicle_model', $this->vehicle_model);
        if (!empty($this->vehicle_make)) $query->whereIn('qc.vehicle_model', $this->vehicle_make);

        if ($this->timeline) {
            switch ($this->timeline) {
                case 'today':        $query->whereDate('qc.created_at', today()); break;
                case 'this_week':    $query->whereBetween('qc.created_at',[now()->startOfWeek(), now()->endOfWeek()]); break;
                case 'last_15_days': $query->whereBetween('qc.created_at',[now()->subDays(14), now()]); break;
                case 'this_month':   $query->whereBetween('qc.created_at',[now()->startOfMonth(), now()->endOfMonth()]); break;
                case 'this_year':    $query->whereBetween('qc.created_at',[now()->startOfYear(), now()->endOfYear()]); break;
            }
        } else {
            if ($this->from_date) $query->whereDate('qc.created_at','>=',$this->from_date);
            if ($this->to_date)   $query->whereDate('qc.created_at','<=',$this->to_date);
        }
    }

    return $query->orderBy('qc.id','desc');
}




    
//   public function map($row): array
// {
//     $mapped = [];

//     foreach ($this->selectedFields as $field) {
//         $key = $field['name'];

//         switch ($key) {
            
//         //  case 'image':
//         //   $imageFile = $row->image ?? null;

//         //     if (!empty($imageFile)) {
//         //         $imagePath = public_path("EV/images/quality_check/" . $imageFile);
                
//         //         if (file_exists($imagePath)) {
//         //             // Find the column index for dynamic image placement
//         //             $columnIndex = array_search('image', array_column($this->selectedFields, 'name'));
//         //             $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex + 1);
        
//         //             $this->images[] = [
//         //                 'path' => $imagePath,
//         //                 'row' => count($this->images) + 2, // header offset
//         //                 'column' => $columnLetter,
//         //             ];
        
//         //             $mapped[] = ''; // Leave cell blank for image
//         //         } else {
//         //             $mapped[] = 'Image not found'; // Optional: or just '-'
//         //         }
//         //     } else {
//         //         $mapped[] = 'No image'; // Optional: or just '-'
//         //     }
//         //     break;
        

//             case 'vehicle_type':
//                 $mapped[] = $row->vehicle_type_relation->name ?? '-';
//                 break;
                

//             case 'vehicle_model':
//                 $mapped[] = $row->vehicle_model_relation->vehicle_model ?? '-';
//                 break;

//             case 'location':
//                 $mapped[] = $row->location_relation->city_name ?? '-';
//                 break;
                
//             case 'zone':
//                 $mapped[] = $row->zone->name ?? '-';
//                 break;
                
//             case 'customer':
//                 $mapped[] = $row->customer_relation->trade_name ?? '-';
//                 break;
                
//             case 'accountability_type':
//                 $mapped[] = $row->accountability_type_relation->name ?? '-';
//                 break;
        
//             case 'result':
//             $status = $row->status ?? '-';
        
//             if ($status && $status !== '-') {
//                 // Replace underscores with spaces, convert to lowercase, then ucfirst each word
//                 $mapped[] = ucwords(strtolower(str_replace('_', ' ', $status)));
//             } else {
//                 $mapped[] = '-';
//             }
//             break;


//             case 'chassis_number':
//             case 'battery_number':
//             case 'telematics_number':
//             case 'motor_number':
//                 $mapped[] = $row->$key ?? '-';
//                 break;
                
//                 case 'qc_checklist':
//                     $output = '-';
                
//                     if (!empty($row->check_lists)) {
//                         // Step 1: Decode the JSON
//                         $raw = $row->check_lists;
                    
//                         $firstDecode = json_decode($raw, true);
                    
//                         // Step 2: Handle double-encoded JSON
//                         if (is_string($firstDecode)) {
//                             $checklists = json_decode($firstDecode, true);
//                         } else {
//                             $checklists = $firstDecode;
//                         }
                    
//                         // Step 3: If decoded data is an array, process it
//                         if (is_array($checklists)) {
//                             // Get checklist master [id => label_name]
//                             $checklistMaster = QualityCheckMaster::pluck('label_name', 'id');
                    
//                             $outputParts = [];
//                             foreach ($checklists as $id => $status) {
                               
//                                 $name = $checklistMaster[$id] ?? 'Unknown';
//                                 $outputParts[] = "$name: $status";
                                 
//                             }
                 
                    
//                             $output = implode(', ', $outputParts);
                           
//                         }
//                     }
    
//                     $mapped[] = $output;
//                 break;


//             case 'date_time':
//             case 'datetime':
//                 $mapped[] = $row->datetime ? \Carbon\Carbon::parse($row->datetime)->format('d M Y, h:i A') : '-';
//                 break;

//             default:
//                 $mapped[] = $row->$key ?? '-';
//         }
//     }

//     return $mapped;
// }

public function map($row): array
{
    $mapped = [];

    foreach ($this->selectedFields as $field) {
        $key = $field;

        switch ($key) {

            // Vehicle / Model / Type / Zone / Location / Customer
            case 'vehicle_type':
                $mapped[] = $row->vehicle_type_name ?? '-';
                break;

            case 'vehicle_model':
                $mapped[] = $row->vehicle_model ?? '-';
                break;

            case 'location':
                $mapped[] = $row->city_name ?? '-';
                break;

            case 'zone':
                $mapped[] = $row->zone_name ?? '-';
                break;

            case 'customer':
                $mapped[] = $row->customer_name ?? '-';
                break;

            case 'accountability_type':
                $mapped[] = $row->accountability_type_name ?? '-';
                break;

            // QC Result
            case 'result':     
                $mapped[] = $row->status 
                    ? ucwords(strtolower(str_replace('_',' ',$row->status)))
                    : '-';
                break;

            // QC Checklist JSON
            case 'qc_checklist':  
                $output = '-';
                if (!empty($row->check_lists)) {
                    $decoded = json_decode($row->check_lists, true);

                    // If double encoded JSON
                    if (is_string($decoded))
                        $decoded = json_decode($decoded, true);

                    if (is_array($decoded)) {
                        $output = collect($decoded)->map(function($status,$id){
                            return ($this->checklistMaster[$id] ?? 'Unknown') . ": $status";
                        })->implode(', ');
                    }
                }
                $mapped[] = $output;
                break;

            // Plain Direct Fields
            case 'chassis_number':
            case 'battery_number':
            case 'telematics_number':
            case 'motor_number':
                $mapped[] = $row->$key ?? '-';
                break;

            // Date Formats
            case 'date_time':
            case 'datetime':
                $mapped[] = $row->datetime
                    ? Carbon::parse($row->datetime)->format('d M Y, h:i A')
                    : '-';
                break;

            // Default fallback
            default:
                $mapped[] = $row->$key ?? '-';
        }
    }

    return $mapped;
}



public function headings(): array
{
    $headers = [];
 
    foreach ($this->selectedFields as $field) {
        $headers[] = ucfirst(str_replace('_', ' ', $field));
    }

    return $headers;
}

public function drawings()
{
    $drawings = [];

    foreach ($this->images as $imageInfo) {
        $drawing = new Drawing();
        $drawing->setName('Image');
        $drawing->setDescription('QC Image');
        $drawing->setPath($imageInfo['path']);
        $drawing->setHeight(15); // adjust for best fit
        $drawing->setCoordinates($imageInfo['column'] . $imageInfo['row']);
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);
        $drawings[] = $drawing;
    }

    return $drawings;
}

public function chunkSize(): int
    {
        return 500;
    }

}



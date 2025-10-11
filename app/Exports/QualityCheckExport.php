<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Carbon\Carbon;
use Modules\Deliveryman\Entities\Deliveryman;
use Illuminate\Support\Facades\DB;
use Modules\AssetMaster\Entities\QualityCheck;
use Modules\AssetMaster\Entities\QualityCheckMaster;
class QualityCheckExport implements FromCollection, WithHeadings, WithMapping, WithDrawings
{
     protected $status;
     protected $from_date;
     protected $to_date;
    protected $timeline;
    protected $selectedIds;
    protected $selectedFields;
    protected $images = [];
    protected $location;
    
    public function __construct($status , $from_date , $to_date , $timeline, $selectedIds = [] , $selectedFields =[] ,$location)
    {
        $this->status = $status;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->timeline = $timeline;
        $this->selectedIds = $selectedIds;
        $this->selectedFields = $selectedFields;
        $this->location = $location;
    }

    public function collection()
    {
        
       
       $query = QualityCheck::with('vehicle_model_relation', 'vehicle_type_relation', 'location_relation');

        
        
     if (!empty($this->selectedIds)) {
        $query->whereIn('id', $this->selectedIds);
    } else {
        
        $query->where('delete_status', 0);
        
        if (in_array($this->status, ['pass', 'fail' ,'qc_pending'])) {
            $query->where('status', $this->status);
        }
        
        if (!empty($this->location)) {
            $query->where('location', $this->location);
        }
        
    
          if ($this->timeline) {
            switch ($this->timeline) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
    
                case 'this_week':
                    $query->whereBetween('created_at', [
                        now()->startOfWeek(), now()->endOfWeek()
                    ]);
                    break;
    
                case 'this_month':
                    $query->whereBetween('created_at', [
                        now()->startOfMonth(), now()->endOfMonth()
                    ]);
                    break;
    
                case 'this_year':
                    $query->whereBetween('created_at', [
                        now()->startOfYear(), now()->endOfYear()
                    ]);
                    break;
            }
    
            // Overwrite the from_date/to_date to empty for consistency
            $this->from_date = null;
            $this->to_date = null;
        } else {
            // Manual date filtering
            if ($this->from_date) {
                $query->whereDate('created_at', '>=', $this->from_date);
            }
    
            if ($this->to_date) {
                $query->whereDate('created_at', '<=', $this->to_date);
            }
        }
}

        
        $results = $query->orderBy('id', 'desc')->get();
        
        
    
    
        return $results;
    }


    
  public function map($row): array
{
    $mapped = [];

    foreach ($this->selectedFields as $field) {
        $key = $field['name'];

        switch ($key) {
            
        //  case 'image':
        //   $imageFile = $row->image ?? null;

        //     if (!empty($imageFile)) {
        //         $imagePath = public_path("EV/images/quality_check/" . $imageFile);
                
        //         if (file_exists($imagePath)) {
        //             // Find the column index for dynamic image placement
        //             $columnIndex = array_search('image', array_column($this->selectedFields, 'name'));
        //             $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex + 1);
        
        //             $this->images[] = [
        //                 'path' => $imagePath,
        //                 'row' => count($this->images) + 2, // header offset
        //                 'column' => $columnLetter,
        //             ];
        
        //             $mapped[] = ''; // Leave cell blank for image
        //         } else {
        //             $mapped[] = 'Image not found'; // Optional: or just '-'
        //         }
        //     } else {
        //         $mapped[] = 'No image'; // Optional: or just '-'
        //     }
        //     break;
        

            case 'vehicle_type':
                $mapped[] = $row->vehicle_type_relation->name ?? '-';
                break;

            case 'vehicle_model':
                $mapped[] = $row->vehicle_model_relation->vehicle_model ?? '-';
                break;

            case 'location':
                $mapped[] = $row->location_relation->name ?? '-';
                break;
        
            case 'result':
            $status = $row->status ?? '-';
        
            if ($status && $status !== '-') {
                // Replace underscores with spaces, convert to lowercase, then ucfirst each word
                $mapped[] = ucwords(strtolower(str_replace('_', ' ', $status)));
            } else {
                $mapped[] = '-';
            }
            break;


            case 'chassis_number':
            case 'battery_number':
            case 'telematics_number':
            case 'motor_number':
                $mapped[] = $row->$key ?? '-';
                break;
                
                case 'qc_checklist':
                    $output = '-';
                
                    if (!empty($row->check_lists)) {
                        // Step 1: Decode the JSON
                        $raw = $row->check_lists;
                    
                        $firstDecode = json_decode($raw, true);
                    
                        // Step 2: Handle double-encoded JSON
                        if (is_string($firstDecode)) {
                            $checklists = json_decode($firstDecode, true);
                        } else {
                            $checklists = $firstDecode;
                        }
                    
                        // Step 3: If decoded data is an array, process it
                        if (is_array($checklists)) {
                            // Get checklist master [id => label_name]
                            $checklistMaster = QualityCheckMaster::pluck('label_name', 'id');
                    
                            $outputParts = [];
                            foreach ($checklists as $id => $status) {
                               
                                $name = $checklistMaster[$id] ?? 'Unknown';
                                $outputParts[] = "$name: $status";
                                 
                            }
                 
                    
                            $output = implode(', ', $outputParts);
                           
                        }
                    }
    
                    $mapped[] = $output;
                break;


            case 'date_time':
            case 'datetime':
                $mapped[] = $row->datetime ? \Carbon\Carbon::parse($row->datetime)->format('d M Y, h:i A') : '-';
                break;

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
        $headers[] = ucfirst(str_replace('_', ' ', $field['name']));
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


}

<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Carbon\Carbon;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
class AssetVehicleLogHistory implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{

    protected $from_date;
    protected $to_date;
    protected $timeline;
    protected $selectedFields;
    protected $selectedIds;
    protected $city;
    protected $zone;
    protected $customer;
    protected $accountability_type;
    protected $vehicle_type;
    protected $vehicle_model;
    protected $vehicle_make;
    public function __construct($from_date, $to_date, $timeline, $selectedFields = [] , $selectedIds = [] , $city = '[]'  , $zone = '[]', $customer = '[]' ,
    $accountability_type,$vehicle_type = '[]',$vehicle_model = '[]',$vehicle_make = '[]')
    {
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->timeline = $timeline;
        $this->city     = is_array($city) ? $city : json_decode($city, true);
        $this->zone     = is_array($zone) ? $zone : json_decode($zone, true);
        $this->customer     = is_array($customer) ? $customer : json_decode($customer, true);
        $this->accountability_type = $accountability_type;
        $this->vehicle_type = is_array($vehicle_type) ? $vehicle_type : json_decode($vehicle_type, true);
        $this->vehicle_model= is_array($vehicle_model) ? $vehicle_model : json_decode($vehicle_model, true);
        $this->vehicle_make = is_array($vehicle_make) ? $vehicle_make : json_decode($vehicle_make, true);
        $this->selectedFields = array_filter($selectedFields); // removes null/empty
        $this->selectedIds = array_filter($selectedIds) ?? []; // removes null/empty
        if (!empty($timeline) && $timeline !== 'custom') {
            $this->timeline = $timeline;
        } else {
            $this->timeline = '';
        }
        $this->city     = $this->city ?? [];
        $this->zone         = $this->zone ?? [];
        $this->customer     = $this->customer ?? [];
        $this->vehicle_type = $this->vehicle_type ?? [];
        $this->vehicle_model= $this->vehicle_model ?? [];
        $this->vehicle_make = $this->vehicle_make ?? [];
        
    }

    // public function collection()
    // {
    //     $query = AssetMasterVehicle::with('vehicle_type_relation' , 'quality_check' ,'vehicle_model_relation' ,'location_relation' ,'hypothecation_relation' ,'financing_type_relation' ,'asset_ownership_relation' ,'insurer_name_relation' ,'insurer_type_relation' ,'registration_type_relation' ,'telematics_oem_relation' ,'inventory_location_relation' ,'color_relation' ,'leasing_partner_relation');


    //     if (!empty($this->selectedIds)) {
    //         $query->whereIn('id', $this->selectedIds);
    //     }
    //     else{
            
    //       if (!empty($this->city)) {
    //             $query->whereHas('quality_check', function ($q) {
    //                 $q->whereIn('location', $this->city);
    //             });
    //         }
            
    //          if (!empty($this->zone)) {
    //             $query->whereHas('quality_check', function ($q) {
    //                 $q->whereIn('zone_id', $this->zone);
    //             });
    //         }
            
    //         if (!empty($this->customer)) {
    //             $query->whereHas('quality_check', function ($q) {
    //                 $q->whereIn('customer_id', $this->customer);
    //             });
    //         }
    
    //          if (!empty($this->accountability_type)) {
    //             $query->whereHas('quality_check', function ($q) {
    //                 $q->where('accountability_type', $this->accountability_type);
    //             });
    //         }
            
    //          if (!empty($this->vehicle_type)) {
    //             $query->whereHas('quality_check', function ($q) {
    //                 $q->whereIn('vehicle_type', $this->vehicle_type);
    //             });
    //         }
            
    //         if (!empty($this->vehicle_model)) {
    //             $query->whereHas('quality_check', function ($q) {
    //                 $q->whereIn('vehicle_model', $this->vehicle_model);
    //             });
    //         }
    //         if (!empty($this->vehicle_make)) {
    //             $query->whereHas('quality_check', function ($q) {
    //                 $q->whereIn('vehicle_model', $this->vehicle_make);
    //             });
    //         }

    //     if ($this->timeline) {
    //         switch ($this->timeline) {
    //             case 'today':
    //                 $query->whereDate('created_at', today());
    //                 break;

    //             case 'this_week':
    //                 $query->whereBetween('created_at', [
    //                     now()->startOfWeek(), now()->endOfWeek()
    //                 ]);
    //                 break;
    //             case 'last_15_days':
    //                     $query->whereBetween('created_at', [
    //                         now()->subDays(14)->startOfDay(),
    //                         now()->endOfDay()
    //                     ]);
    //                     break;
    //             case 'this_month':
    //                 $query->whereBetween('created_at', [
    //                     now()->startOfMonth(), now()->endOfMonth()
    //                 ]);
    //                 break;

    //             case 'this_year':
    //                 $query->whereBetween('created_at', [
    //                     now()->startOfYear(), now()->endOfYear()
    //                 ]);
    //                 break;
    //         }

    //         $this->from_date = null;
    //         $this->to_date = null;
    //     } else {
    //         if ($this->from_date) {
    //             $query->whereDate('created_at', '>=', $this->from_date);
    //         }

    //         if ($this->to_date) {
    //             $query->whereDate('created_at', '<=', $this->to_date);
    //         }
    //     }
        
    //     }

    //     return $query->latest()->get();
    // }
    
     /**
     * ✅ REQUIRED for FromQuery
     */
    public function query()
    {
        $query = AssetMasterVehicle::with([
            'vehicle_type_relation',
            'quality_check.zone',
            'quality_check.location_relation',
            'quality_check.accountability_type_relation',
            'vehicle_model_relation',
            'location_relation',
            'hypothecation_relation',
            'financing_type_relation',
            'asset_ownership_relation',
            'insurer_name_relation',
            'insurer_type_relation',
            'registration_type_relation',
            'telematics_oem_relation',
            'inventory_location_relation',
            'color_relation',
            'leasing_partner_relation',
        ]);

        /** ✅ Selected IDs Priority */
        if (!empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
            return $query->latest();
        }

        /** ✅ Filters */
        if (!empty($this->city)) {
            $query->whereHas('quality_check', fn ($q) =>
                $q->whereIn('location', $this->city)
            );
        }

        if (!empty($this->zone)) {
            $query->whereHas('quality_check', fn ($q) =>
                $q->whereIn('zone_id', $this->zone)
            );
        }

        if (!empty($this->customer)) {
            $query->whereHas('quality_check', fn ($q) =>
                $q->whereIn('customer_id', $this->customer)
            );
        }

        if (!empty($this->accountability_type)) {
            $query->whereHas('quality_check', fn ($q) =>
                $q->where('accountability_type', $this->accountability_type)
            );
        }

        if (!empty($this->vehicle_type)) {
            $query->whereHas('quality_check', fn ($q) =>
                $q->whereIn('vehicle_type', $this->vehicle_type)
            );
        }

        if (!empty($this->vehicle_model)) {
            $query->whereHas('quality_check', fn ($q) =>
                $q->whereIn('vehicle_model', $this->vehicle_model)
            );
        }

        if (!empty($this->vehicle_make)) {
            $query->whereHas('quality_check', fn ($q) =>
                $q->whereIn('vehicle_make', $this->vehicle_make)
            );
        }

        /** ✅ Timeline / Date Filters */
        if ($this->timeline) {
            match ($this->timeline) {
                'today' =>
                    $query->whereDate('created_at', today()),

                'this_week' =>
                    $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]),

                'last_15_days' =>
                    $query->whereBetween('created_at', [
                        now()->subDays(14)->startOfDay(),
                        now()->endOfDay()
                    ]),

                'this_month' =>
                    $query->whereBetween('created_at', [
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    ]),

                'this_year' =>
                    $query->whereBetween('created_at', [
                        now()->startOfYear(),
                        now()->endOfYear()
                    ]),
                default => null
            };
        } else {
            if (!empty($this->from_date)) {
                $query->whereDate('created_at', '>=', $this->from_date);
            }

            if (!empty($this->to_date)) {
                $query->whereDate('created_at', '<=', $this->to_date);
            }
        }

        return $query->latest();
    }

    /**
     * ✅ Chunk size for large exports
     */
     public function chunkSize(): int
    {
        return 1000;
    }


    public function map($row): array
    {
        $mapped = [];
    
        foreach (array_filter($this->selectedFields) as $key) {
            switch ($key) {
                case 'vehicle_category':
                    $mapped[] = $row->vehicle_category ?? '-';
                    break;
                case 'vehicle_type':
                    $mapped[] = $row->vehicle_type_relation->name ?? '-';
                    // $mapped[] = $row->vehicle_type ?? '-';
                    break;
                case 'make':
                    $mapped[] = $row->vehicle_model_relation->make ?? '-';
                    break;
                case 'model':
                    $mapped[] = $row->vehicle_model_relation->vehicle_model ?? '-';
                    // $mapped[] = $row->vehicle_model ?? '-';
                    break;
                case 'variant':
                    $mapped[] = $row->vehicle_model_relation->variant ?? '-';
                    break;
                case 'client':
                    // $mapped[] = $row->client ?? '-';
                    $mapped[] = optional($row->customer_relation)->name ?? $row->client ?? '';
                    break;
                case 'color':
                    $mapped[] = $row->color_relation->name ?? '-';
                    break;
                case 'chassis_number':
                    $mapped[] = $row->chassis_number ?? '-';
                    break;
                case 'gd_hub_id_allowcated':
                    $mapped[] = $row->gd_hub_name ?? '-';
                    break;
                case 'gd_hub_id_existing':
                    $mapped[] = $row->gd_hub_id ?? '-';
                break;
                case 'city':
                    $mapped[] = $row->quality_check->location_relation->city_name ?? '-';
                    // $mapped[] = $row->location ?? '-';
                    break;
                 case 'zone':
                    $mapped[] = $row->quality_check->zone->name ?? '-';
                    // $mapped[] = $row->location ?? '-';
                    break;
                 case 'leasing_partner':
                    $mapped[] = $row->leasing_partner_relation->name ?? '-';
                    // $mapped[] = $row->location ?? '-';
                    break;
                case 'accountability_type':
                    $mapped[] = $row->quality_check->accountability_type_relation->name ?? '-';
                    // $mapped[] = $row->location ?? '-';
                    break;
                case 'road_tax_next_renewal_date':
                    $mapped[] = ($date = $row->road_tax_next_renewal_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) 
                    ? \Carbon\Carbon::parse($date)->format('d-m-Y') 
                    : '-';
                    break;
                 case 'financing_type':
                    $mapped[] = $row->financing_type_relation->name ?? '-';
                    break;
                  case 'asset_ownership':
                    $mapped[] = $row->asset_ownership_relation->name ?? '-';
                    break;
                  case 'hypothecation_to':
                    $mapped[] = $row->hypothecation_relation->name ?? '-';
                    break;
                    case 'insurer_name':
                    $mapped[] = $row->insurer_name_relation->name ?? '-';
                    break;
                    case 'insurance_type':
                    $mapped[] = $row->insurer_type_relation->name ?? '-';
                    break;
                   case 'registration_type':
                    $mapped[] = $row->registration_type_relation->name ?? '-';
                    break;
                 case 'telematics_oem':
                    $mapped[] = $row->telematics_oem_relation->name ?? '-';
                    break;
                 case 'vehicle_status':
                    $mapped[] = $row->inventory_location_relation->name ?? '-';
                    break;
                case 'battery_serial_number_replacement_1':
                    $mapped[] = $row->battery_serial_number1 ?? '-';
                    break;
                case 'battery_serial_number_replacement_2':
                    $mapped[] = $row->battery_serial_number2 ?? '-';
                    break;
                case 'battery_serial_number_replacement_3':
                    $mapped[] = $row->battery_serial_number3 ?? '-';
                    break;
                case 'battery_serial_number_replacement_4':
                    $mapped[] = $row->battery_serial_number4 ?? '-';
                    break;
                case 'battery_serial_number_replacement_5':
                    $mapped[] = $row->battery_serial_number5 ?? '-';
                    break;
                case 'charger_serial_number_replacement_1':
                    $mapped[] = $row->charger_serial_number1 ?? '-';
                    break;
                case 'charger_serial_number_replacement_2':
                    $mapped[] = $row->charger_serial_number2 ?? '-';
                    break;
                case 'charger_serial_number_replacement_3':
                    $mapped[] = $row->charger_serial_number3 ?? '-';
                    break;
                case 'charger_serial_number_replacement_4':
                    $mapped[] = $row->charger_serial_number4 ?? '-';
                    break;
                case 'charger_serial_number_replacement_5':
                    $mapped[] = $row->charger_serial_number5 ?? '-';
                    break;
                case 'telematics_serial_number_replacement_1':
                    $mapped[] = $row->telematics_serial_number1 ?? '-';
                    break;
                case 'telematics_serial_number_replacement_2':
                    $mapped[] = $row->telematics_serial_number2 ?? '-';
                    break;
                case 'telematics_serial_number_replacement_3':
                    $mapped[] = $row->telematics_serial_number3 ?? '-';
                    break;
                case 'telematics_serial_number_replacement_4':
                    $mapped[] = $row->telematics_serial_number4 ?? '-';
                    break;
                case 'telematics_serial_number_replacement_5':
                    $mapped[] = $row->telematics_serial_number5 ?? '-';
                    break;
                default:
                    $mapped[] = $row->$key ?? '-';
                    break;
            }
        }
    
        return $mapped;
    }


    public function headings(): array
    {
        $headers = [];
        // dd($this->selectedFields);
        foreach ($this->selectedFields as $field) {
            $headers[] = ucfirst(str_replace('_', ' ', $field));
        }

        return $headers;
    }
 
}

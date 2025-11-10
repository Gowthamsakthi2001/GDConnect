<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\AssetMaster\Entities\AssetVehicleInventory;
use Modules\MasterManagement\Entities\InventoryLocationMaster;//updated by Mugesh.B
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\Exportable;

class AssetMasterInventoryExport implements FromCollection, WithHeadings, WithMapping, WithChunkReading
{
    use Exportable;
    
    protected $status;
    protected $from_date;
    protected $to_date;
    protected $timeline;
    protected $selectedFields;
    protected $selectedIds;
    protected $city;
    protected $customer_id;

    public function __construct($status, $from_date, $to_date, $timeline, $selectedFields = [] , $selectedIds = [] ,$city,$customer_id)
    {
        $this->status = $status;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->timeline = $timeline;
        $this->selectedFields = array_filter($selectedFields); // removes null/empty
        $this->selectedIds = array_filter($selectedIds ?? []);
        $this->city = $city;
        $this->customer_id = $customer_id;
        
    }


    public function collection()
    {
        
        $inventory_locations = InventoryLocationMaster::where('status',1)->get();
         $valid_location_ids = $inventory_locations->pluck('id')->toArray();
          $query = AssetVehicleInventory::with('assetVehicle' ,'inventory_location');
           // Filter by Customer
            if (!empty($this->customer_id) && $this->customer_id !== "all") {
                $query->whereHas('assetVehicle', function ($q) {
                    $q->where('client', $this->customer_id);
                });
            }
           if (!empty($this->selectedIds)) {
                $query->whereIn('id', $this->selectedIds);
            }else{
        
        
                // Filter by status
                // if (in_array($this->status, ['0', '1'])) {
                //     $query->where('is_status', $this->status);
                // }
                
                if (in_array($this->status, $valid_location_ids)) {
                    $query->where('transfer_status', $this->status);
                }
                
                // Filter by city
                if (!empty($this->city)) {
                    $query->whereHas('assetVehicle', function ($q) {
                        $q->where('city_code', $this->city);
                    });
                }
                
                // Timeline filter
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
            
                    // Clear manual dates when timeline is used
                    $this->from_date = null;
                    $this->to_date = null;
            
                } else {
                    // Manual date filtering
                    if (!empty($from_date)) {
                        $query->whereDate('created_at', '>=', $this->from_date);
                    }
            
                    if (!empty($to_date)) {
                        $query->whereDate('created_at', '<=', $this->to_date);
                    }
                }
        }
        
    
        return $query->latest()->get();
    }

    public function map($row): array
    {
        $mapped = [];
    
        foreach (array_filter($this->selectedFields) as $key) {
            switch ($key) {
                case 'vehicle_category':
                    $mapped[] = $row->assetVehicle->vehicle_category ?? '-';
                    break;
                case 'vehicle_type':
                    $mapped[] = $row->assetVehicle->vehicle_type_relation->name ?? '-';
                    // $mapped[] = $row->assetVehicle->vehicle_type ?? '-';
                    break;
                case 'make':
                    $mapped[] = $row->assetVehicle->vehicle_model_relation->make ?? '-';
                    break;
                case 'model':
                    $mapped[] = $row->assetVehicle->vehicle_model_relation->vehicle_model ?? '-';
                    // $mapped[] = $row->assetVehicle->vehicle_model ?? '-';
                    break;
                case 'variant':
                    $mapped[] = $row->assetVehicle->vehicle_model_relation->variant ?? '-';
                    break;
                case 'client':
                    // $mapped[] = $row->assetVehicle->client ?? '-';
                    $mapped[] = optional($row->assetVehicle->customer_relation)->name ?? $row->assetVehicle->client ?? '' ;
                    break;
                case 'color':
                    $mapped[] = $row->assetVehicle->color_relation->name ?? '-';
                    break;
                case 'chassis_number':
                    $mapped[] = $row->assetVehicle->chassis_number ?? '-';
                    break;
                case 'zone':
                    $mapped[] = $row->assetVehicle->quality_check->zone->name ?? '-';
                    break;
                case 'gd_hub_id_allowcated':
                    $mapped[] = $row->assetVehicle->gd_hub_name ?? '-';
                    break;
                case 'gd_hub_id_existing':
                    $mapped[] = $row->assetVehicle->gd_hub_id ?? '-';
                break;
                case 'city_code':
                    $mapped[] = $row->assetVehicle->location_relation->city_code ?? '-';
                    // $mapped[] = $row->assetVehicle->location ?? '-';
                    break;
                case 'road_tax_next_renewal_date':
                    $mapped[] = ($date = $row->assetVehicle->road_tax_next_renewal_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) 
                    ? \Carbon\Carbon::parse($date)->format('d-m-Y') 
                    : '-';
                    break;
                 case 'financing_type':
                    $mapped[] = $row->assetVehicle->financing_type_relation->name ?? '-';
                    break;
                  case 'asset_ownership':
                    $mapped[] = $row->assetVehicle->asset_ownership_relation->name ?? '-';
                    break;
                  case 'hypothecation_to':
                    $mapped[] = $row->assetVehicle->hypothecation_relation->name ?? '-';
                    break;
                    case 'insurer_name':
                    $mapped[] = $row->assetVehicle->insurer_name_relation->name ?? '-';
                    break;
                    case 'insurance_type':
                    $mapped[] = $row->assetVehicle->insurer_type_relation->name ?? '-';
                    break;
                   case 'registration_type':
                    $mapped[] = $row->assetVehicle->registration_type_relation->name ?? '-';
                    break;
                 case 'telematics_oem':
                    $mapped[] = $row->assetVehicle->telematics_oem_relation->name ?? '-';
                    break;
                 case 'vehicle_status':
                    $mapped[] = $row->inventory_location->name ?? '-';
                    break;
                case 'battery_serial_number_replacement_1':
                    $mapped[] = $row->assetVehicle->battery_serial_number1 ?? '-';
                    break;
                case 'battery_serial_number_replacement_2':
                    $mapped[] = $row->assetVehicle->battery_serial_number2 ?? '-';
                    break;
                case 'battery_serial_number_replacement_3':
                    $mapped[] = $row->assetVehicle->battery_serial_number3 ?? '-';
                    break;
                case 'battery_serial_number_replacement_4':
                    $mapped[] = $row->assetVehicle->battery_serial_number4 ?? '-';
                    break;
                case 'battery_serial_number_replacement_5':
                    $mapped[] = $row->assetVehicle->battery_serial_number5 ?? '-';
                    break;
                case 'charger_serial_number_replacement_1':
                    $mapped[] = $row->assetVehicle->charger_serial_number1 ?? '-';
                    break;
                case 'charger_serial_number_replacement_2':
                    $mapped[] = $row->assetVehicle->charger_serial_number2 ?? '-';
                    break;
                case 'charger_serial_number_replacement_3':
                    $mapped[] = $row->assetVehicle->charger_serial_number3 ?? '-';
                    break;
                case 'charger_serial_number_replacement_4':
                    $mapped[] = $row->assetVehicle->charger_serial_number4 ?? '-';
                    break;
                case 'charger_serial_number_replacement_5':
                    $mapped[] = $row->assetVehicle->charger_serial_number5 ?? '-';
                    break;
                case 'telematics_serial_number_replacement_1':
                    $mapped[] = $row->assetVehicle->telematics_serial_number1 ?? '-';
                    break;
                case 'telematics_serial_number_replacement_2':
                    $mapped[] = $row->assetVehicle->telematics_serial_number2 ?? '-';
                    break;
                case 'telematics_serial_number_replacement_3':
                    $mapped[] = $row->assetVehicle->telematics_serial_number3 ?? '-';
                    break;
                case 'telematics_serial_number_replacement_4':
                    $mapped[] = $row->assetVehicle->telematics_serial_number4 ?? '-';
                    break;
                case 'telematics_serial_number_replacement_5':
                    $mapped[] = $row->assetVehicle->telematics_serial_number5 ?? '-';
                    break;
                    
                case 'tax_invoice_attachment':
                     $mapped[] = !empty($row->assetVehicle->tax_invoice_attachment)
                        ? asset("EV/asset_master/tax_invoice_attachments/" . $row->assetVehicle->tax_invoice_attachment)
                        : '-';
                    break;
                case 'master_lease_agreement':
                            $mapped[] = !empty($row->assetVehicle->master_lease_agreement)
                        ? asset("EV/asset_master/master_lease_agreements/" . $row->assetVehicle->master_lease_agreement)
                        : '-';
                    break;
                case 'hypothecation_document':
                            $mapped[] = !empty($row->assetVehicle->hypothecation_document)
                    ? asset("EV/asset_master/hypothecation_documents/" . $row->assetVehicle->hypothecation_document)
                    : '-';

                    break;
                case 'insurance_attachment':
                           $mapped[] = !empty($row->assetVehicle->insurance_attachment)
                        ? asset("EV/asset_master/insurance_attachments/" . $row->assetVehicle->insurance_attachment)
                        : '-';
                    break;
                case 'temporary_registration_certificate_attachment':
                           $mapped[] = !empty($row->assetVehicle->temproary_reg_attachment)
                        ? asset("EV/asset_master/temporary_certificate_attachments/" . $row->assetVehicle->temproary_reg_attachment)
                        : '-';
                    break;
                 case 'hsrp_copy_attachment':
                            $mapped[] = !empty($row->assetVehicle->hsrp_copy_attachment)
                            ? asset("EV/asset_master/hsrp_certificate_attachments/" . $row->assetVehicle->hsrp_copy_attachment)
                            : '-';
                    break;
                case 'reg_certificate_attachment':
                         $mapped[] = !empty($row->assetVehicle->reg_certificate_attachment)
                        ? asset("EV/asset_master/reg_certificate_attachments/" . $row->assetVehicle->reg_certificate_attachment)
                        : '-';
                    break;
                case 'fc_attachment':
                          $mapped[] = !empty($row->assetVehicle->fc_attachment)
                        ? asset("EV/asset_master/fc_attachments/" . $row->assetVehicle->fc_attachment)
                        : '-';
                    break;
                default:
                    $mapped[] = $row->assetVehicle->$key ?? '-';
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
    
    public function chunkSize(): int
    {
        return 1000;
    }

}

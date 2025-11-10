<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Modules\AssetMaster\Entities\AssetMasterVehicle;

class AssetMasterVehicleExport implements FromCollection, WithHeadings, WithMapping
{
    protected $status;
    protected $from_date;
    protected $to_date;
    protected $timeline;
    protected $selectedFields;
    protected $selectedIds;
    protected $city;
    protected $zone;
    protected $customer;
    protected $accountability_type;
    public function __construct($status, $from_date, $to_date, $timeline, $selectedFields = [] , $selectedIds = [] ,$city  , $zone , $customer , $accountability_type)
    {
        // dd($status,$from_date,$to_date,$timeline,$selectedFields,$selectedIds);
        $this->status = $status;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->timeline = $timeline;
        $this->selectedFields = array_filter($selectedFields); // removes null/empty
        $this->selectedIds = array_filter($selectedIds) ?? []; // removes null/empty
        
         $this->city = $city;
         $this->zone = $zone;
         $this->customer = $customer;
         $this->accountability_type = $accountability_type;
        
        
    }


    public function collection()
    {
       $query = AssetMasterVehicle::with('vehicle_type_relation' , 'quality_check' , 'quality_check.zone','quality_check.accountability_type_relation','vehicle_model_relation' ,'location_relation' ,'hypothecation_relation' ,'financing_type_relation' ,'asset_ownership_relation' ,'insurer_name_relation' ,'insurer_type_relation' ,'registration_type_relation' ,'telematics_oem_relation' ,'inventory_location_relation' , 'customer_relation' ,'color_relation');


        if (!empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        }
        else{

        $query->where('delete_status', 0);

        if ($this->status && $this->status != "all") {
            $query->where('is_status', $this->status);
        }
            
        if (!empty($this->city)) {
            $query->whereHas('quality_check', function ($q) {
                $q->where('location', $this->city);
            });
        }
        
         if (!empty($this->zone)) {
            $query->whereHas('quality_check', function ($q) {
                $q->where('zone_id', $this->zone);
            });
        }
        
        // if (!empty($this->customer)) {
        //     $query->whereHas('quality_check', function ($q) {
        //         $q->where('customer_id', $this->customer);
        //     });
        // }

         if (!empty($this->accountability_type)) {
            $query->whereHas('quality_check', function ($q) {
                $q->where('accountability_type', $this->accountability_type);
            });
        }
        
        
        if (!empty($this->customer) && $this->accountability_type == 2) { 
            // Customer accountability
            $customer = $this->customer;
            $query->whereHas('quality_check', function ($q) use ($customer) {
                $q->where('customer_id', $customer);
            });
        }

        if (!empty($this->customer) && $this->accountability_type == 1) { 
            // Client accountability
            $query->where('client', $this->customer);
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

            $this->from_date = null;
            $this->to_date = null;
        } else {
            if ($this->from_date) {
                $query->whereDate('created_at', '>=', $this->from_date);
            }

            if ($this->to_date) {
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
                    $mapped[] = optional($row->customer_relation)->name ?? $row->client ?? '' ;
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
                case 'accountability_type':
                    $mapped[] = $row->quality_check->accountability_type_relation->name ?? '-';
                    // $mapped[] = $row->location ?? '-';
                    break;
                case 'road_tax_next_renewal_date':
                    try {
                        $mapped[] = !empty($row->road_tax_next_renewal_date)
                            ? \Carbon\Carbon::parse($row->road_tax_next_renewal_date)->format('d-m-Y')
                            : '-';
                    } catch (\Exception $e) {
                        $mapped[] = '-'; // Fallback if parsing fails
                    }
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
                case 'tax_invoice_attachment':
                     $mapped[] = !empty($row->tax_invoice_attachment)
                        ? asset("EV/asset_master/tax_invoice_attachments/" . $row->tax_invoice_attachment)
                        : '-';
                    break;
                case 'master_lease_agreement':
                            $mapped[] = !empty($row->master_lease_agreement)
                        ? asset("EV/asset_master/master_lease_agreements/" . $row->master_lease_agreement)
                        : '-';
                    break;
                case 'hypothecation_document':
                            $mapped[] = !empty($row->hypothecation_document)
                    ? asset("EV/asset_master/hypothecation_documents/" . $row->hypothecation_document)
                    : '-';

                    break;
                case 'insurance_attachment':
                           $mapped[] = !empty($row->insurance_attachment)
                        ? asset("EV/asset_master/insurance_attachments/" . $row->insurance_attachment)
                        : '-';
                    break;
                case 'temporary_registration_certificate_attachment':
                           $mapped[] = !empty($row->temproary_reg_attachment)
                        ? asset("EV/asset_master/temporary_certificate_attachments/" . $row->temproary_reg_attachment)
                        : '-';
                    break;
                 case 'hsrp_copy_attachment':
                            $mapped[] = !empty($row->hsrp_copy_attachment)
                            ? asset("EV/asset_master/hsrp_certificate_attachments/" . $row->hsrp_copy_attachment)
                            : '-';
                    break;
                case 'reg_certificate_attachment':
                         $mapped[] = !empty($row->reg_certificate_attachment)
                        ? asset("EV/asset_master/reg_certificate_attachments/" . $row->reg_certificate_attachment)
                        : '-';
                    break;
                case 'fc_attachment':
                          $mapped[] = !empty($row->fc_attachment)
                        ? asset("EV/asset_master/fc_attachments/" . $row->fc_attachment)
                        : '-';
                    break;
                default:
                // Format if it's a date field
                if (
                    in_array($key, [
                        'tax_invoice_date',
                        'lease_start_date',
                        'lease_end_date',
                        'vehicle_delivery_date',
                        'insurance_start_date',
                        'insurance_expiry_date',
                        'permanent_reg_date',
                        'reg_certificate_expiry_date',
                        'fc_expiry_date',
                        'created_at',
                        'updated_at'
                    ])
                ) {
                    $mapped[] = $row->$key
                        ? Carbon::parse($row->$key)->format('d-m-Y')
                        : '-';
                } else {
                    $mapped[] = $row->$key ?? '-';
                }
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

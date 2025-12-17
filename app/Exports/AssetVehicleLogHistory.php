<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
        $query = DB::table('ev_tbl_asset_master_vehicles as amv')
    ->leftJoin('ev_tbl_inventory_location_master as loc','loc.id','=','amv.location')
            ->leftJoin('vehicle_qc_check_lists as qcl', 'qcl.id', '=', 'amv.qc_id')
            ->leftJoin('ev_tbl_accountability_types as atype', 'atype.id', '=', 'qcl.accountability_type')
            ->leftJoin('ev_tbl_vehicle_models as vms', 'vms.id', '=', 'amv.model')
            ->leftJoin('ev_tbl_color_master as cmr', 'cmr.id', '=', 'amv.color')
            ->leftJoin('ev_tbl_customer_master as cum', 'cum.id', '=', 'amv.client')
            ->leftJoin('vehicle_types as vts', 'vts.id', '=', 'amv.vehicle_type')
            ->leftJoin('ev_tbl_leasing_partner_master as lpm', 'lpm.id', '=', 'amv.leasing_partner')
            ->leftJoin('ev_tbl_city as cty', 'cty.id', '=', 'qcl.location')
            ->leftJoin('zones', 'zones.id', '=', 'qcl.zone_id')
            ->leftJoin('ev_tbl_financing_type_master as ftm', 'ftm.id', '=', 'amv.financing_type')
            ->leftJoin('ev_tbl_asset_ownership_master as aom', 'aom.id', '=', 'amv.asset_ownership')
            ->leftJoin('ev_tbl_hypothecations_master as hym', 'hym.id', '=', 'amv.hypothecation_to')
            ->leftJoin('ev_tbl_insurer_name_master as inm', 'inm.id', '=', 'amv.insurer_name')
            ->leftJoin('ev_tbl_insurance_types as int', 'int.id', '=', 'amv.insurance_type')
            ->leftJoin('ev_tbl_registration_types as ret', 'ret.id', '=', 'amv.registration_type')
            ->leftJoin('ev_tbl_telemetric_oem_master as tom', 'tom.id', '=', 'amv.telematics_oem')
            ->select([
                'amv.vehicle_category',
                'amv.chassis_number',
                'amv.gd_hub_name',
                'amv.gd_hub_id',
                'amv.road_tax_next_renewal_date',
                'amv.battery_serial_number1',
                'amv.battery_serial_number2',
                'amv.battery_serial_number3',
                'amv.battery_serial_number4',
                'amv.battery_serial_number5',
                'amv.charger_serial_number1',
                'amv.charger_serial_number2',
                'amv.charger_serial_number3',
                'amv.charger_serial_number4',
                'amv.charger_serial_number5',
                'amv.telematics_serial_number1',
                'amv.telematics_serial_number2',
                'amv.telematics_serial_number3',
                'amv.telematics_serial_number4',
                'amv.telematics_serial_number5',
                'amv.tax_invoice_attachment',
                'amv.master_lease_agreement',
                'amv.hypothecation_document',
                'amv.insurance_attachment',
                'amv.temproary_reg_attachment',
                'amv.hsrp_copy_attachment',
                'amv.reg_certificate_attachment',
                'amv.fc_attachment',
                'amv.motor_number',
                'amv.vehicle_id',
                'amv.tax_invoice_number',
                'amv.tax_invoice_date',
                'amv.tax_invoice_value',
                'amv.lease_start_date',
                'amv.lease_end_date',
                'amv.emi_lease_amount',
                'amv.hypothecation',
                'amv.insurance_number',
                'amv.insurance_start_date',
                'amv.insurance_expiry_date',
                'amv.registration_status',
                'amv.temproary_reg_number',
                'amv.temproary_reg_date',
                'amv.temproary_reg_expiry_date',
                'amv.permanent_reg_number',
                'amv.permanent_reg_date',
                'amv.reg_certificate_expiry_date',
                'amv.fc_expiry_date',
                'amv.servicing_dates',
                'amv.road_tax_applicable',
                'amv.road_tax_amount',
                'amv.road_tax_renewal_frequency',
                'amv.battery_type',
                'amv.battery_serial_no',
                'amv.charger_variant_name',
                'amv.charger_serial_no',
                'amv.telematics_variant_name',
                'amv.telematics_serial_no',
                'amv.telematics_imei_number',
                'amv.vehicle_delivery_date',
                'vts.name as vehicle_type_name',
                'vms.make',
                'vms.vehicle_model',
                'vms.variant',
                'cum.name as customer_name',
                'cmr.name as color_name',
                'zones.name as zone_name',
                'cty.city_name',
                'atype.name as accountability_type' ,
                'ftm.name as finance_type_name' ,
                'aom.name as asset_ownership_name',
                'hym.name as hypothecation_name',
                'inm.name as insurer_name',
                'int.name as insurance_type_name',
                'ret.name as registration_type_name',
                'tom.name as telmatics_oem_name',
                'loc.name as inventory_location_name',
                'lpm.name as leasing_partner_name'
                
            ]);
    
    
        if (!empty($this->selectedIds)) {
            $query->whereIn('amv.id', $this->selectedIds);
        }
    
        if (!empty($this->status)) {
            $query->whereIn('amv.transfer_status', $this->status);
        }
    
        if (!empty($this->city)) {
            $query->whereIn('qcl.location', $this->city);
        }
    
        if (!empty($this->zone)) {
            $query->whereIn('qcl.zone_id', $this->zone);
        }
    
        if (!empty($this->customer) && $this->accountability_type == 2) {
            $query->whereIn('qcl.customer_id', $this->customer);
        }
    
        if (!empty($this->customer) && $this->accountability_type == 1) {
            $query->whereIn('amv.client', $this->customer);
        }
        if (!empty($this->customer) && empty($this->accountability_type)) {
            $query->whereIn('amv.client', $this->customer);
        }
        
        if (!empty($this->vehicle_type) ) {
            $query->whereIn('amv.vehicle_type', $this->vehicle_type);
        }
        if (!empty($this->vehicle_model) ) {
            $query->whereIn('amv.model', $this->vehicle_model);
        }
         if (!empty($this->vehicle_make) ) {
            $query->whereIn('amv.model', $this->vehicle_make);
        }
    
        // TIMELINE FILTERS
        if ($this->timeline) {
    
            switch ($this->timeline) {
                case 'today':
                    $query->whereDate('amv.created_at', today());
                    break;
    
                case 'this_week':
                    $query->whereBetween('amv.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                 case 'last_15_days':
                        $query->whereBetween('created_at', [
                            now()->subDays(14)->startOfDay(),
                            now()->endOfDay()
                        ]);
                        break;
                case 'this_month':
                    $query->whereBetween('amv.created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
    
                case 'this_year':
                    $query->whereBetween('amv.created_at', [now()->startOfYear(), now()->endOfYear()]);
                    break;
            }
            
        $this->from_date = null;
        $this->to_date = null;
    
        } else {
    
            if (!empty($this->from_date)) {
                $query->whereDate('amv.created_at', '>=', $this->from_date);
            }
    
            if (!empty($this->to_date)) {
                $query->whereDate('amv.created_at', '<=', $this->to_date);
            }
        }
    
        return $query->orderBy('amv.id', 'desc');

    }

    /**
     * ✅ Chunk size for large exports
     */
     public function chunkSize(): int
    {
        return 1000;
    }


    // public function map($row): array
    // {
    //     $mapped = [];
    
    //     foreach (array_filter($this->selectedFields) as $key) {
    //         switch ($key) {
    //             case 'vehicle_category':
    //                 $mapped[] = $row->vehicle_category ?? '-';
    //                 break;
    //             case 'vehicle_type':
    //                 $mapped[] = $row->vehicle_type_relation->name ?? '-';
    //                 // $mapped[] = $row->vehicle_type ?? '-';
    //                 break;
    //             case 'make':
    //                 $mapped[] = $row->vehicle_model_relation->make ?? '-';
    //                 break;
    //             case 'model':
    //                 $mapped[] = $row->vehicle_model_relation->vehicle_model ?? '-';
    //                 // $mapped[] = $row->vehicle_model ?? '-';
    //                 break;
    //             case 'variant':
    //                 $mapped[] = $row->vehicle_model_relation->variant ?? '-';
    //                 break;
    //             case 'client':
    //                 // $mapped[] = $row->client ?? '-';
    //                 $mapped[] = optional($row->customer_relation)->name ?? $row->client ?? '';
    //                 break;
    //             case 'color':
    //                 $mapped[] = $row->color_relation->name ?? '-';
    //                 break;
    //             case 'chassis_number':
    //                 $mapped[] = $row->chassis_number ?? '-';
    //                 break;
    //             case 'gd_hub_id_allowcated':
    //                 $mapped[] = $row->gd_hub_name ?? '-';
    //                 break;
    //             case 'gd_hub_id_existing':
    //                 $mapped[] = $row->gd_hub_id ?? '-';
    //             break;
    //             case 'city':
    //                 $mapped[] = $row->quality_check->location_relation->city_name ?? '-';
    //                 // $mapped[] = $row->location ?? '-';
    //                 break;
    //              case 'zone':
    //                 $mapped[] = $row->quality_check->zone->name ?? '-';
    //                 // $mapped[] = $row->location ?? '-';
    //                 break;
    //              case 'leasing_partner':
    //                 $mapped[] = $row->leasing_partner_relation->name ?? '-';
    //                 // $mapped[] = $row->location ?? '-';
    //                 break;
    //             case 'accountability_type':
    //                 $mapped[] = $row->quality_check->accountability_type_relation->name ?? '-';
    //                 // $mapped[] = $row->location ?? '-';
    //                 break;
    //             case 'road_tax_next_renewal_date':
    //                 $mapped[] = ($date = $row->road_tax_next_renewal_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) 
    //                 ? \Carbon\Carbon::parse($date)->format('d-m-Y') 
    //                 : '-';
    //                 break;
    //              case 'financing_type':
    //                 $mapped[] = $row->financing_type_relation->name ?? '-';
    //                 break;
    //               case 'asset_ownership':
    //                 $mapped[] = $row->asset_ownership_relation->name ?? '-';
    //                 break;
    //               case 'hypothecation_to':
    //                 $mapped[] = $row->hypothecation_relation->name ?? '-';
    //                 break;
    //                 case 'insurer_name':
    //                 $mapped[] = $row->insurer_name_relation->name ?? '-';
    //                 break;
    //                 case 'insurance_type':
    //                 $mapped[] = $row->insurer_type_relation->name ?? '-';
    //                 break;
    //               case 'registration_type':
    //                 $mapped[] = $row->registration_type_relation->name ?? '-';
    //                 break;
    //              case 'telematics_oem':
    //                 $mapped[] = $row->telematics_oem_relation->name ?? '-';
    //                 break;
    //              case 'vehicle_status':
    //                 $mapped[] = $row->inventory_location_relation->name ?? '-';
    //                 break;
    //             case 'battery_serial_number_replacement_1':
    //                 $mapped[] = $row->battery_serial_number1 ?? '-';
    //                 break;
    //             case 'battery_serial_number_replacement_2':
    //                 $mapped[] = $row->battery_serial_number2 ?? '-';
    //                 break;
    //             case 'battery_serial_number_replacement_3':
    //                 $mapped[] = $row->battery_serial_number3 ?? '-';
    //                 break;
    //             case 'battery_serial_number_replacement_4':
    //                 $mapped[] = $row->battery_serial_number4 ?? '-';
    //                 break;
    //             case 'battery_serial_number_replacement_5':
    //                 $mapped[] = $row->battery_serial_number5 ?? '-';
    //                 break;
    //             case 'charger_serial_number_replacement_1':
    //                 $mapped[] = $row->charger_serial_number1 ?? '-';
    //                 break;
    //             case 'charger_serial_number_replacement_2':
    //                 $mapped[] = $row->charger_serial_number2 ?? '-';
    //                 break;
    //             case 'charger_serial_number_replacement_3':
    //                 $mapped[] = $row->charger_serial_number3 ?? '-';
    //                 break;
    //             case 'charger_serial_number_replacement_4':
    //                 $mapped[] = $row->charger_serial_number4 ?? '-';
    //                 break;
    //             case 'charger_serial_number_replacement_5':
    //                 $mapped[] = $row->charger_serial_number5 ?? '-';
    //                 break;
    //             case 'telematics_serial_number_replacement_1':
    //                 $mapped[] = $row->telematics_serial_number1 ?? '-';
    //                 break;
    //             case 'telematics_serial_number_replacement_2':
    //                 $mapped[] = $row->telematics_serial_number2 ?? '-';
    //                 break;
    //             case 'telematics_serial_number_replacement_3':
    //                 $mapped[] = $row->telematics_serial_number3 ?? '-';
    //                 break;
    //             case 'telematics_serial_number_replacement_4':
    //                 $mapped[] = $row->telematics_serial_number4 ?? '-';
    //                 break;
    //             case 'telematics_serial_number_replacement_5':
    //                 $mapped[] = $row->telematics_serial_number5 ?? '-';
    //                 break;
    //             default:
    //                 $mapped[] = $row->$key ?? '-';
    //                 break;
    //         }
    //     }
    
    //     return $mapped;
    // }
    
//     public function map($row): array
// {
//     return [

//         // Basic Vehicle Details
//         $row->chassis_number ?? '-',
//         $row->vehicle_category ?? '-',
//         $row->vehicle_type_name ?? '-',     // from SELECT vts.name
//         $row->vehicle_model ?? '-',
//         $row->make ?? '-',
//         $row->variant ?? '-',
//         $row->color_name ?? '-',
//         $row->motor_number ?? '-',
//         $row->vehicle_id ?? '-',

//         // Invoice
//         $row->tax_invoice_number ?? '-',
//         $row->tax_invoice_date ?? '-',
//         $row->tax_invoice_value ?? '-',

//         // Location
//         $row->city_name ?? '-',
//         $row->zone_name ?? '-',
//         $row->accountability_type ?? '-',

//         // GD Hubs
//         $row->gd_hub_name ?? '-',
//         $row->gd_hub_id ?? '-',

//         // Finance
//         $row->finance_type_name ?? '-',
//         $row->leasing_partner_name ?? '-',
//         $row->asset_ownership_name ?? '-',
//         $row->lease_start_date ?? '-',
//         $row->lease_end_date ?? '-',
//         $row->emi_lease_amount ?? '-',

//         // Hypothecation + Insurance
//         $row->hypothecation ?? '-',
//         $row->hypothecation_name ?? '-',
//         $row->insurer_name ?? '-',
//         $row->insurance_type_name ?? '-',
//         $row->insurance_number ?? '-',
//         $row->insurance_start_date ?? '-',
//         $row->insurance_expiry_date ?? '-',

//         // Registration
//         $row->registration_type_name ?? '-',
//         $row->temproary_reg_number ?? '-',
//         $row->temproary_reg_date ?? '-',
//         $row->temproary_reg_expiry_date ?? '-',
//         $row->permanent_reg_number ?? '-',
//         $row->permanent_reg_date ?? '-',
//         $row->reg_certificate_expiry_date ?? '-',
//         $row->fc_expiry_date ?? '-',

//         // Others
//         $row->servicing_dates ?? '-',
//         $row->road_tax_applicable ?? '-',
//         $row->road_tax_amount ?? '-',
//         $row->road_tax_renewal_frequency ?? '-',
//         $row->road_tax_next_renewal_date ?? '-',

//         // Battery
//         $row->battery_type ?? '-',
//         $row->battery_serial_no ?? '-',
//         $row->battery_serial_number1 ?? '-',
//         $row->battery_serial_number2 ?? '-',
//         $row->battery_serial_number3 ?? '-',
//         $row->battery_serial_number4 ?? '-',
//         $row->battery_serial_number5 ?? '-',

//         // Charger
//         $row->charger_variant_name ?? '-',
//         $row->charger_serial_no ?? '-',
//         $row->charger_serial_number1 ?? '-',
//         $row->charger_serial_number2 ?? '-',
//         $row->charger_serial_number3 ?? '-',
//         $row->charger_serial_number4 ?? '-',
//         $row->charger_serial_number5 ?? '-',

//         // Telematics
//         $row->telematics_variant_name ?? '-',
//         $row->telmatics_oem_name ?? '-',
//         $row->telematics_serial_no ?? '-',
//         $row->telematics_imei_number ?? '-',
//         $row->telematics_serial_number1 ?? '-',
//         $row->telematics_serial_number2 ?? '-',
//         $row->telematics_serial_number3 ?? '-',
//         $row->telematics_serial_number4 ?? '-',
//         $row->telematics_serial_number5 ?? '-',

//         // Final Fields
//         $row->customer_name ?? '-',
//         $row->vehicle_delivery_date ?? '-',
//         $row->inventory_location_name ?? '-',
//     ];
// }

public function map($row): array
{
    $mapped = [];

    foreach ($this->selectedFields as $key) 
    {
        switch($key)
        {
            case 'chassis_number': $mapped[] = $row->chassis_number; break;
            case 'vehicle_category': $mapped[] = $row->vehicle_category; break;
            case 'vehicle_type': $mapped[] = $row->vehicle_type_name; break;
            case 'model': $mapped[] = $row->vehicle_model; break;
            case 'make': $mapped[] = $row->make; break;
            case 'variant': $mapped[] = $row->variant; break;
            case 'color': $mapped[] = $row->color_name; break;
            case 'motor_number': $mapped[] = $row->motor_number; break;
            case 'vehicle_id': $mapped[] = $row->vehicle_id; break;

            // Tax / Invoice
            case 'tax_invoice_number': $mapped[] = $row->tax_invoice_number; break;
            case 'tax_invoice_date': $mapped[] = $row->tax_invoice_date; break;
            case 'tax_invoice_value': $mapped[] = $row->tax_invoice_value; break;

            // Location / Account
            case 'city': $mapped[] = $row->city_name; break;
            case 'zone': $mapped[] = $row->zone_name; break;
            case 'accountability_type': $mapped[] = $row->accountability_type; break;

            // GD Hub
            case 'gd_hub_id_allowcated': $mapped[] = $row->gd_hub_name; break;
            case 'gd_hub_id_existing': $mapped[] = $row->gd_hub_id; break;

            // Finance
            case 'financing_type': $mapped[] = $row->finance_type_name; break;
            case 'leasing_partner': $mapped[] = $row->leasing_partner_name; break;
            case 'asset_ownership': $mapped[] = $row->asset_ownership_name; break;
            case 'lease_start_date': $mapped[] = $row->lease_start_date; break;
            case 'lease_end_date': $mapped[] = $row->lease_end_date; break;
            case 'emi_lease_amount': $mapped[] = $row->emi_lease_amount; break;

            // Hypo + Insurance
            case 'hypothecation': $mapped[] = $row->hypothecation; break;
            case 'hypothecation_to': $mapped[] = $row->hypothecation_name; break;
            case 'insurer_name': $mapped[] = $row->insurer_name; break;
            case 'insurance_type': $mapped[] = $row->insurance_type_name; break;
            case 'insurance_number': $mapped[] = $row->insurance_number; break;
            case 'insurance_start_date': $mapped[] = $row->insurance_start_date; break;
            case 'insurance_expiry_date': $mapped[] = $row->insurance_expiry_date; break;

            // Registration
            case 'registration_type': $mapped[] = $row->registration_type_name; break;
            case 'temproary_reg_number': $mapped[] = $row->temproary_reg_number; break;
            case 'temproary_reg_date': $mapped[] = $row->temproary_reg_date; break;
            case 'temproary_reg_expiry_date': $mapped[] = $row->temproary_reg_expiry_date; break;
            case 'permanent_reg_number': $mapped[] = $row->permanent_reg_number; break;
            case 'permanent_reg_date': $mapped[] = $row->permanent_reg_date; break;
            case 'reg_certificate_expiry_date': $mapped[] = $row->reg_certificate_expiry_date; break;
            case 'fc_expiry_date': $mapped[] = $row->fc_expiry_date; break;

            // Others
            case 'servicing_dates': $mapped[] = $row->servicing_dates; break;
            case 'road_tax_applicable': $mapped[] = $row->road_tax_applicable; break;
            case 'road_tax_amount': $mapped[] = $row->road_tax_amount; break;
            case 'road_tax_renewal_frequency': $mapped[] = $row->road_tax_renewal_frequency; break;
            case 'road_tax_next_renewal_date': $mapped[] = $row->road_tax_next_renewal_date; break;

            // Battery
            case 'battery_type': $mapped[] = $row->battery_type; break;
            case 'battery_serial_no': $mapped[] = $row->battery_serial_no; break;
            case 'battery_serial_number_replacement_1': $mapped[] = $row->battery_serial_number1; break;
            case 'battery_serial_number_replacement_2': $mapped[] = $row->battery_serial_number2; break;
            case 'battery_serial_number_replacement_3': $mapped[] = $row->battery_serial_number3; break;
            case 'battery_serial_number_replacement_4': $mapped[] = $row->battery_serial_number4; break;
            case 'battery_serial_number_replacement_5': $mapped[] = $row->battery_serial_number5; break;

            // Charger
            case 'charger_variant_name': $mapped[] = $row->charger_variant_name; break;
            case 'charger_serial_no': $mapped[] = $row->charger_serial_no; break;
            case 'charger_serial_number_replacement_1': $mapped[] = $row->charger_serial_number1; break;
            case 'charger_serial_number_replacement_2': $mapped[] = $row->charger_serial_number2; break;
            case 'charger_serial_number_replacement_3': $mapped[] = $row->charger_serial_number3; break;
            case 'charger_serial_number_replacement_4': $mapped[] = $row->charger_serial_number4; break;
            case 'charger_serial_number_replacement_5': $mapped[] = $row->charger_serial_number5; break;

            // Telematics
            case 'telematics_variant_name': $mapped[] = $row->telematics_variant_name; break;
            case 'telematics_oem': $mapped[] = $row->telmatics_oem_name; break;
            case 'telematics_serial_no': $mapped[] = $row->telematics_serial_no; break;
            case 'telematics_imei_number': $mapped[] = $row->telematics_imei_number; break;
            case 'telematics_serial_number_replacement_1': $mapped[] = $row->telematics_serial_number1; break;
            case 'telematics_serial_number_replacement_2': $mapped[] = $row->telematics_serial_number2; break;
            case 'telematics_serial_number_replacement_3': $mapped[] = $row->telematics_serial_number3; break;
            case 'telematics_serial_number_replacement_4': $mapped[] = $row->telematics_serial_number4; break;
            case 'telematics_serial_number_replacement_5': $mapped[] = $row->telematics_serial_number5; break;

            // Final
            case 'client': $mapped[] = $row->customer_name; break;
            case 'vehicle_delivery_date': $mapped[] = $row->vehicle_delivery_date; break;
            case 'vehicle_status': $mapped[] = $row->inventory_location_name; break;

            default: $mapped[] = '-'; break;
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

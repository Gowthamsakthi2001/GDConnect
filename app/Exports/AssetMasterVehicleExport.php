<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Illuminate\Support\Facades\DB;

class AssetMasterVehicleExport implements FromQuery, WithHeadings, WithMapping ,WithChunkReading
// class AssetMasterVehicleExport implements FromCollection, WithHeadings, WithMapping
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
    protected $vehicle_type;
    protected $vehicle_model;
    protected $vehicle_make;
    public function __construct($status, $from_date, $to_date, $timeline, $selectedFields = '[]' , $selectedIds = '[]' ,$city = '[]', $zone = '[]',
    $customer = '[]' , $accountability_type,$vehicle_type = '[]',$vehicle_model = '[]',$vehicle_make = '[]')
    {
        // dd($status,$from_date,$to_date,$timeline,$selectedFields,$selectedIds);
        
        $this->city     = is_array($city) ? $city : json_decode($city, true);
        $this->zone         = is_array($zone) ? $zone : json_decode($zone, true);
        $this->customer     = is_array($customer) ? $customer : json_decode($customer, true);
        $this->vehicle_type = is_array($vehicle_type) ? $vehicle_type : json_decode($vehicle_type, true);
        $this->vehicle_model= is_array($vehicle_model) ? $vehicle_model : json_decode($vehicle_model, true);
        $this->vehicle_make = is_array($vehicle_make) ? $vehicle_make : json_decode($vehicle_make, true);
        $this->accountability_type = $accountability_type;
        $this->status = $status;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->timeline = $timeline;
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
    //   $query = AssetMasterVehicle::with('vehicle_type_relation' , 'quality_check' ,'quality_check.customer_relation' ,'quality_check.zone','quality_check.accountability_type_relation','vehicle_model_relation' ,'location_relation' ,'hypothecation_relation' ,'financing_type_relation' ,'asset_ownership_relation' ,'insurer_name_relation' ,'insurer_type_relation' ,'registration_type_relation' ,'telematics_oem_relation' ,'inventory_location_relation' , 'customer_relation' ,'color_relation' , 'leasing_partner_relation');


    //     if (!empty($this->selectedIds)) {
    //         $query->whereIn('id', $this->selectedIds);
    //     }
    //     else{

    //     $query->where('delete_status', 0);

    //     if ($this->status && $this->status != "all") {
    //         $query->where('is_status', $this->status);
    //     }
            
    //     if (!empty($this->city)) {
    //         $query->whereHas('quality_check', function ($q) {
    //             $q->whereIn('location', $this->city);
    //         });
    //     }
        
    //      if (!empty($this->zone)) {
    //         $query->whereHas('quality_check', function ($q) {
    //             $q->whereIn('zone_id', $this->zone);
    //         });
    //     }
        
    //     // if (!empty($this->customer)) {
    //     //     $query->whereHas('quality_check', function ($q) {
    //     //         $q->where('customer_id', $this->customer);
    //     //     });
    //     // }

    //      if (!empty($this->accountability_type)) {
    //         $query->whereHas('quality_check', function ($q) {
    //             $q->where('accountability_type', $this->accountability_type);
    //         });
    //     }
        
        
    //     if (!empty($this->customer) && $this->accountability_type == 2) { 
    //         // Customer accountability
    //         $customer = $this->customer;
    //         $query->whereHas('quality_check', function ($q) use ($customer) {
    //             $q->whereIn('customer_id', $customer);
    //         });
    //     }

    //     if (!empty($this->customer) && $this->accountability_type == 1) { 
    //         // Client accountability
    //         $query->whereIn('client', $this->customer);
    //     }
        
    //     if (!empty($this->vehicle_type) ) { 
    //         // Client accountability
    //         $query->whereIn('vehicle_type', $this->vehicle_type);
    //     }
        
    //     if (!empty($this->vehicle_model) ) { 
    //         // Client accountability
    //         $query->whereIn('model', $this->vehicle_model);
    //     }
        
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
    //                 $mapped[] = 
    //                     ($row->quality_check->accountability_type ?? null) == 2
    //                         ? ($row->quality_check->customer_relation->name ?? '-')
    //                         : ($row->customer_relation->name ?? '-');
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
    //             case 'leasing_partner':
    //                 $mapped[] = $row->leasing_partner_relation->name ?? '-';
    //             break;
    //             case 'city':
    //                 $mapped[] = $row->quality_check->location_relation->city_name ?? '-';
    //                 // $mapped[] = $row->location ?? '-';
    //                 break;
    //             case 'zone':
    //                 $mapped[] = $row->quality_check->zone->name ?? '-';
    //                 // $mapped[] = $row->location ?? '-';
    //                 break;
    //             case 'accountability_type':
    //                 $mapped[] = $row->quality_check->accountability_type_relation->name ?? '-';
    //                 // $mapped[] = $row->location ?? '-';
    //                 break;
    //             case 'road_tax_next_renewal_date':
    //                 try {
    //                     $mapped[] = !empty($row->road_tax_next_renewal_date)
    //                         ? \Carbon\Carbon::parse($row->road_tax_next_renewal_date)->format('d-m-Y')
    //                         : '-';
    //                 } catch (\Exception $e) {
    //                     $mapped[] = '-'; // Fallback if parsing fails
    //                 }
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
    //             case 'tax_invoice_attachment':
    //                  $mapped[] = !empty($row->tax_invoice_attachment)
    //                     ? asset("EV/asset_master/tax_invoice_attachments/" . $row->tax_invoice_attachment)
    //                     : '-';
    //                 break;
    //             case 'master_lease_agreement':
    //                         $mapped[] = !empty($row->master_lease_agreement)
    //                     ? asset("EV/asset_master/master_lease_agreements/" . $row->master_lease_agreement)
    //                     : '-';
    //                 break;
    //             case 'hypothecation_document':
    //                         $mapped[] = !empty($row->hypothecation_document)
    //                 ? asset("EV/asset_master/hypothecation_documents/" . $row->hypothecation_document)
    //                 : '-';

    //                 break;
    //             case 'insurance_attachment':
    //                       $mapped[] = !empty($row->insurance_attachment)
    //                     ? asset("EV/asset_master/insurance_attachments/" . $row->insurance_attachment)
    //                     : '-';
    //                 break;
    //             case 'temporary_registration_certificate_attachment':
    //                       $mapped[] = !empty($row->temproary_reg_attachment)
    //                     ? asset("EV/asset_master/temporary_certificate_attachments/" . $row->temproary_reg_attachment)
    //                     : '-';
    //                 break;
    //              case 'hsrp_copy_attachment':
    //                         $mapped[] = !empty($row->hsrp_copy_attachment)
    //                         ? asset("EV/asset_master/hsrp_certificate_attachments/" . $row->hsrp_copy_attachment)
    //                         : '-';
    //                 break;
    //             case 'reg_certificate_attachment':
    //                      $mapped[] = !empty($row->reg_certificate_attachment)
    //                     ? asset("EV/asset_master/reg_certificate_attachments/" . $row->reg_certificate_attachment)
    //                     : '-';
    //                 break;
    //             case 'fc_attachment':
    //                       $mapped[] = !empty($row->fc_attachment)
    //                     ? asset("EV/asset_master/fc_attachments/" . $row->fc_attachment)
    //                     : '-';
    //                 break;
    //             default:
    //             // Format if it's a date field
    //             if (
    //                 in_array($key, [
    //                     'tax_invoice_date',
    //                     'lease_start_date',
    //                     'lease_end_date',
    //                     'vehicle_delivery_date',
    //                     'insurance_start_date',
    //                     'insurance_expiry_date',
    //                     'permanent_reg_date',
    //                     'reg_certificate_expiry_date',
    //                     'fc_expiry_date',
    //                     'created_at',
    //                     'updated_at'
    //                 ])
    //             ) {
    //                 $mapped[] = $row->$key
    //                     ? Carbon::parse($row->$key)->format('d-m-Y')
    //                     : '-';
    //             } else {
    //                 $mapped[] = $row->$key ?? '-';
    //             }
    //             break;
    //         }
    //     }
    
    //     return $mapped;
    // }
    
    
public function query()
{
    $q = DB::table('ev_tbl_asset_master_vehicles AS v')
        ->leftJoin('vehicle_qc_check_lists AS qc', 'qc.id', '=', 'v.qc_id')
        ->leftJoin('vehicle_types AS vt', 'vt.id', '=', 'v.vehicle_type')
        ->leftJoin('ev_tbl_vehicle_models AS vm', 'vm.id', '=', 'v.model')
        ->leftJoin('ev_tbl_location_master AS loc', 'loc.id', '=', 'v.location')
        ->leftJoin('ev_tbl_hypothecations_master AS hypo', 'hypo.id', '=', 'v.hypothecation_to')
        ->leftJoin('ev_tbl_financing_type_master AS ft', 'ft.id', '=', 'v.financing_type')
        ->leftJoin('ev_tbl_asset_ownership_master AS ao', 'ao.id', '=', 'v.asset_ownership')
        ->leftJoin('ev_tbl_insurer_name_master AS ins', 'ins.id', '=', 'v.insurer_name')
        ->leftJoin('ev_tbl_insurance_types AS inst', 'inst.id', '=', 'v.insurance_type')
        ->leftJoin('ev_tbl_registration_types AS regt', 'regt.id', '=', 'v.registration_type')
        ->leftJoin('ev_tbl_telemetric_oem_master AS tel', 'tel.id', '=', 'v.telematics_oem')
        ->leftJoin('ev_tbl_inventory_location_master AS il', 'il.id', '=', 'v.vehicle_status')
        ->leftJoin('ev_tbl_customer_master AS cust', 'cust.id', '=', 'v.client')
        ->leftJoin('ev_tbl_color_master AS clr', 'clr.id', '=', 'v.color')
        ->leftJoin('ev_tbl_leasing_partner_master AS lp', 'lp.id', '=', 'v.leasing_partner')
        ->leftJoin('ev_tbl_city AS city', 'city.id', '=', 'qc.location')
        ->leftJoin('zones AS z', 'z.id', '=', 'qc.zone_id')
        ->leftJoin('ev_tbl_accountability_types AS atype', 'atype.id', '=', 'qc.accountability_type')
        ->leftJoin('ev_tbl_customer_master AS qcust', 'qcust.id', '=', 'qc.customer_id')

        ->select([
            // 🔹 Base columns from vehicle table (all needed for map)
            'v.id',
            'v.qc_id',
            'v.chassis_number',
            'v.vehicle_category',
            'v.vehicle_type',
            'v.make',
            'v.model',
            'v.variant',
            'v.color',
            'v.client',
            'v.motor_number',
            'v.vehicle_id',
            'v.tax_invoice_number',
            'v.tax_invoice_date',
            'v.tax_invoice_value',
            'v.tax_invoice_attachment',
            'v.location',
            'v.gd_hub_name',
            'v.financing_type',
            'v.asset_ownership',
            'v.master_lease_agreement',
            'v.leasing_partner',
            'v.lease_start_date',
            'v.lease_end_date',
            'v.vehicle_delivery_date',
            'v.emi_lease_amount',
            'v.hypothecation',
            'v.hypothecation_to',
            'v.insurer_name',
            'v.insurance_type',
            'v.insurance_number',
            'v.insurance_start_date',
            'v.insurance_expiry_date',
            'v.insurance_attachment',
            'v.registration_type',
            'v.registration_status',
            'v.permanent_reg_number',
            'v.permanent_reg_date',
            'v.reg_certificate_expiry_date',
            'v.reg_certificate_attachment',
            'v.fc_expiry_date',
            'v.fc_attachment',
            'v.battery_type',
            'v.battery_variant_name',
            'v.battery_serial_no',
            'v.charger_variant_name',
            'v.charger_serial_no',
            'v.telematics_variant_name',
            'v.telematics_serial_no',
            'v.vehicle_status',
            'v.gd_hub_id',
            'v.city_code',
            'v.temproary_reg_number',
            'v.temproary_reg_date',
            'v.temproary_reg_expiry_date',
            'v.servicing_dates',
            'v.road_tax_applicable',
            'v.road_tax_amount',
            'v.road_tax_renewal_frequency',
            'v.road_tax_next_renewal_date',
            'v.battery_serial_number1',
            'v.battery_serial_number2',
            'v.battery_serial_number3',
            'v.battery_serial_number4',
            'v.battery_serial_number5',
            'v.charger_serial_number1',
            'v.charger_serial_number2',
            'v.charger_serial_number3',
            'v.charger_serial_number4',
            'v.charger_serial_number5',
            'v.telematics_oem',
            'v.telematics_serial_number1',
            'v.telematics_serial_number2',
            'v.telematics_serial_number3',
            'v.telematics_serial_number4',
            'v.telematics_serial_number5',
            'v.hsrp_copy_attachment',
            'v.hypothecation_document',
            'v.temproary_reg_attachment',
            'v.telematics_imei_number',
            'v.created_at',
            'v.updated_at',

            // 🔹 Relation-based “name” aliases for map()
            'vt.name AS vehicle_type_name',
            'vm.vehicle_model',
            'vm.make AS make_name',
            'vm.variant AS model_variant',

            'clr.name AS color_name',
            'cust.trade_name AS customer_name',
            'lp.name AS leasing_partner_name',

            'ft.name AS financing_type_name',
            'ao.name AS asset_ownership_name',
            'hypo.name AS hypothecation_name',
            'ins.name AS insurer_name',
            'inst.name AS insurance_type_name',
            'regt.name AS registration_type_name',
            'tel.name AS telematics_oem_name',
            'il.name AS vehicle_status_name',

            'city.city_name',
            'z.name AS zone_name',
            'atype.name AS accountability_type_name',
            'qcust.trade_name AS qc_customer_full_name',
            
             DB::raw("
                CASE 
                    WHEN qc.accountability_type = 2 THEN qcust.trade_name 
                    ELSE cust.trade_name 
                END AS final_client_name
            "),


            
                    ]);

    // ===========================
    // 🔹 Same filtering as collection()
    // ===========================

    if (!empty($this->selectedIds)) {
        // When selectedIds given, only filter by IDs (same as old code)
        $q->whereIn('v.id', $this->selectedIds);
    } else {

        if (!empty($this->selectedIds)) {
            $q->whereIn('v.id', $this->selectedIds);
        }
        // delete_status = 0
        $q->where('v.delete_status', 0);

       if(!empty($this->status) && $this->status!="all"){
            $q->where('v.is_status',$this->status);
        }

        // city filter → qc.location
        if (!empty($this->city)) {
            $q->whereIn('qc.location', $this->city);
        }

        // zone filter → qc.zone_id
        if (!empty($this->zone)) {
            $q->whereIn('qc.zone_id', $this->zone);
        }

        // accountability_type filter
        if (!empty($this->accountability_type)) {
            $q->where('qc.accountability_type', $this->accountability_type);
        }

        // customer + accountability_type = 2 (customer accountability)
        if (!empty($this->customer) && $this->accountability_type == 2) {
            $q->whereIn('qc.customer_id', $this->customer);
        }

        // customer + accountability_type = 1 (client accountability)
        if (!empty($this->customer) && $this->accountability_type == 1) {
            $q->whereIn('v.client', $this->customer);
        }

        // vehicle_type filter
        if (!empty($this->vehicle_type)) {
            $q->whereIn('v.vehicle_type', $this->vehicle_type);
        }

        // vehicle_model filter
        if (!empty($this->vehicle_model)) {
            $q->whereIn('v.model', $this->vehicle_model);
        }

        // Timeline
        if ($this->timeline) {
            switch ($this->timeline) {
                case 'today':
                    $q->whereDate('v.created_at', today());
                    break;
                case 'this_week':
                    $q->whereBetween('v.created_at', [
                        now()->startOfWeek(), now()->endOfWeek()
                    ]);
                    break;
                case 'last_15_days':
                    $q->whereBetween('v.created_at', [
                        now()->subDays(14)->startOfDay(), now()->endOfDay()
                    ]);
                    break;
                case 'this_month':
                    $q->whereBetween('v.created_at', [
                        now()->startOfMonth(), now()->endOfMonth()
                    ]);
                    break;
                case 'this_year':
                    $q->whereBetween('v.created_at', [
                        now()->startOfYear(), now()->endOfYear()
                    ]);
                    break;
            }
        } else {
            if ($this->from_date) {
                $q->whereDate('v.created_at', '>=', $this->from_date);
            }
            if ($this->to_date) {
                $q->whereDate('v.created_at', '<=', $this->to_date);
            }
        }
    }

    // 🔹 EXACT same ordering as old: latest() → orderBy(created_at, DESC)
    return $q->orderByDesc('v.created_at');
}
    
public function map($row): array
{
    $mapped = [];

    foreach (array_filter($this->selectedFields) as $key) {

        // default value
        $val = property_exists($row, $key) ? $row->$key : '-';

        switch ($key) {
            case 'vehicle_category':
                $val = $row->vehicle_category ?? '-';
                break;

            // ==== Relation-based names via aliases ====
            case 'vehicle_type':
                $val = $row->vehicle_type_name ?? '-';
                break;
            case 'make':
                $val = $row->make_name ?? '-';
                break;
            case 'model':
                $val = $row->vehicle_model ?? '-';
                break;
            case 'variant':
                $val = $row->model_variant ?? '-';
                break;
            case 'color':
                $val = $row->color_name ?? '-';
                break;
            case 'client':
                $val = $row->final_client_name ?? '-';
                break;
            case 'leasing_partner':
                $val = $row->leasing_partner_name ?? '-';
                break;
            case 'financing_type':
                $val = $row->financing_type_name ?? '-';
                break;
            case 'asset_ownership':
                $val = $row->asset_ownership_name ?? '-';
                break;
            case 'hypothecation_to':
                $val = $row->hypothecation_name ?? '-';
                break;
            case 'insurer_name':
                $val = $row->insurer_name ?? '-';
                break;
            case 'insurance_type':
                $val = $row->insurance_type_name ?? '-';
                break;
            case 'registration_type':
                $val = $row->registration_type_name ?? '-';
                break;
            case 'telematics_oem':
                $val = $row->telematics_oem_name ?? '-';
                break;
            case 'vehicle_status':
                $val = $row->vehicle_status_name ?? '-';
                break;
            case 'city':
                $val = $row->city_name ?? '-';
                break;
            case 'zone':
                $val = $row->zone_name ?? '-';
                break;
            case 'accountability_type':
                $val = $row->accountability_type_name ?? '-';
                break;

            // ==== Special “renamed” fields ====
            case 'gd_hub_id_allowcated':
                $val = $row->gd_hub_name ?? '-';
                break;
            case 'gd_hub_id_existing':
                $val = $row->gd_hub_id ?? '-';
                break;

            // Battery replacement
            case 'battery_serial_number_replacement_1':
                $val = $row->battery_serial_number1 ?? '-';
                break;
            case 'battery_serial_number_replacement_2':
                $val = $row->battery_serial_number2 ?? '-';
                break;
            case 'battery_serial_number_replacement_3':
                $val = $row->battery_serial_number3 ?? '-';
                break;
            case 'battery_serial_number_replacement_4':
                $val = $row->battery_serial_number4 ?? '-';
                break;
            case 'battery_serial_number_replacement_5':
                $val = $row->battery_serial_number5 ?? '-';
                break;

            // Charger replacement
            case 'charger_serial_number_replacement_1':
                $val = $row->charger_serial_number1 ?? '-';
                break;
            case 'charger_serial_number_replacement_2':
                $val = $row->charger_serial_number2 ?? '-';
                break;
            case 'charger_serial_number_replacement_3':
                $val = $row->charger_serial_number3 ?? '-';
                break;
            case 'charger_serial_number_replacement_4':
                $val = $row->charger_serial_number4 ?? '-';
                break;
            case 'charger_serial_number_replacement_5':
                $val = $row->charger_serial_number5 ?? '-';
                break;

            // Telemetics replacement
            case 'telematics_serial_number_replacement_1':
                $val = $row->telematics_serial_number1 ?? '-';
                break;
            case 'telematics_serial_number_replacement_2':
                $val = $row->telematics_serial_number2 ?? '-';
                break;
            case 'telematics_serial_number_replacement_3':
                $val = $row->telematics_serial_number3 ?? '-';
                break;
            case 'telematics_serial_number_replacement_4':
                $val = $row->telematics_serial_number4 ?? '-';
                break;
            case 'telematics_serial_number_replacement_5':
                $val = $row->telematics_serial_number5 ?? '-';
                break;

            // Attachments → URL
            case 'tax_invoice_attachment':
                $val = !empty($row->tax_invoice_attachment)
                    ? asset('EV/asset_master/tax_invoice_attachments/'.$row->tax_invoice_attachment)
                    : '-';
                break;
            case 'master_lease_agreement':
                $val = !empty($row->master_lease_agreement)
                    ? asset('EV/asset_master/master_lease_agreements/'.$row->master_lease_agreement)
                    : '-';
                break;
            case 'hypothecation_document':
                $val = !empty($row->hypothecation_document)
                    ? asset('EV/asset_master/hypothecation_documents/'.$row->hypothecation_document)
                    : '-';
                break;
            case 'insurance_attachment':
                $val = !empty($row->insurance_attachment)
                    ? asset('EV/asset_master/insurance_attachments/'.$row->insurance_attachment)
                    : '-';
                break;
            case 'temporary_registration_certificate_attachment':
                $val = !empty($row->temproary_reg_attachment)
                    ? asset('EV/asset_master/temporary_certificate_attachments/'.$row->temproary_reg_attachment)
                    : '-';
                break;
            case 'hsrp_copy_attachment':
                $val = !empty($row->hsrp_copy_attachment)
                    ? asset('EV/asset_master/hsrp_certificate_attachments/'.$row->hsrp_copy_attachment)
                    : '-';
                break;
            case 'reg_certificate_attachment':
                $val = !empty($row->reg_certificate_attachment)
                    ? asset('EV/asset_master/reg_certificate_attachments/'.$row->reg_certificate_attachment)
                    : '-';
                break;
            case 'fc_attachment':
                $val = !empty($row->fc_attachment)
                    ? asset('EV/asset_master/fc_attachments/'.$row->fc_attachment)
                    : '-';
                break;

            // Dates – safe formatting (no Carbon error)
            default:
                if (in_array($key, [
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
                ])) {
                    $raw = property_exists($row, $key) ? $row->$key : null;

                    if (
                        $raw &&
                        $raw !== '0000-00-00' &&
                        $raw !== '-' &&
                        strtotime($raw)
                    ) {
                        try {
                            $val = Carbon::parse($raw)->format('d-m-Y');
                        } catch (\Exception $e) {
                            $val = '-';
                        }
                    } else {
                        $val = '-';
                    }
                }
                break;
        }

        $mapped[] = ($val === null || $val === '') ? '-' : $val;
    }

    return $mapped;
}

public function chunkSize(): int
{
    return 1000;
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

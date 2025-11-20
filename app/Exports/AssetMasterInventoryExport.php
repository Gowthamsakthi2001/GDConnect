<?php


namespace App\Exports;

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 0);

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\AssetMaster\Entities\AssetVehicleInventory;
use Modules\MasterManagement\Entities\InventoryLocationMaster;//updated by Mugesh.B
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;

class AssetMasterInventoryExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    use Exportable;
    
    protected $status;
    protected $from_date;
    protected $to_date;
    protected $timeline;
    protected $selectedFields;
    protected $selectedIds;
    protected $city;
    protected $customer;
    protected $zone;
    protected $accountability_type;

    public function __construct($status, $from_date, $to_date, $timeline, $selectedFields = [] , $selectedIds = [] ,$city,$customer , $zone , $accountability_type)
    {
        $this->status = $status;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->timeline = $timeline;
        $this->selectedFields = array_filter($selectedFields); // removes null/empty
        $this->selectedIds = array_filter($selectedIds ?? []);
        $this->city = $city;
        $this->customer = $customer;
        $this->zone = $zone;
        $this->accountability_type = $accountability_type;
        
    }

         public function query()
    {
        $inventory_locations = InventoryLocationMaster::where('status', 1)
            ->pluck('id')
            ->toArray();
    
        $query = \DB::table('asset_vehicle_inventories as avi')
            ->join('ev_tbl_asset_master_vehicles as amv', 'amv.id', '=', 'avi.asset_vehicle_id')
            ->leftJoin('ev_tbl_inventory_location_master as ilm', 'ilm.id', '=', 'avi.transfer_status')
            ->leftJoin('vehicle_qc_check_lists as qcl', 'qcl.id', '=', 'amv.qc_id')
            ->leftJoin('ev_tbl_accountability_types as atype', 'atype.id', '=', 'qcl.accountability_type')
            ->leftJoin('ev_tbl_vehicle_models as vms', 'vms.id', '=', 'amv.model')
            ->leftJoin('ev_tbl_color_master as cmr', 'cmr.id', '=', 'amv.color')
            ->leftJoin('ev_tbl_customer_master as cum', 'cum.id', '=', 'amv.client')
            ->leftJoin('vehicle_types as vts', 'vts.id', '=', 'amv.vehicle_type')
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
                'ilm.name as inventory_location_name'
                
            ]);
    
    
        if (!empty($this->selectedIds)) {
            $query->whereIn('avi.id', $this->selectedIds);
        }
    
        if (in_array($this->status, $inventory_locations)) {
            $query->where('avi.transfer_status', $this->status);
        }
    
        if (!empty($this->city)) {
            $query->where('qcl.location', $this->city);
        }
    
        if (!empty($this->zone)) {
            $query->where('qcl.zone_id', $this->zone);
        }
    
        if (!empty($this->customer) && $this->accountability_type == 2) {
            $query->where('qcl.customer_id', $this->customer);
        }
    
        if (!empty($this->customer) && $this->accountability_type == 1) {
            $query->where('amv.client', $this->customer);
        }
    
        // TIMELINE FILTERS
        if ($this->timeline) {
    
            switch ($this->timeline) {
                case 'today':
                    $query->whereDate('avi.created_at', today());
                    break;
    
                case 'this_week':
                    $query->whereBetween('avi.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
    
                case 'this_month':
                    $query->whereBetween('avi.created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
    
                case 'this_year':
                    $query->whereBetween('avi.created_at', [now()->startOfYear(), now()->endOfYear()]);
                    break;
            }
            
        $this->from_date = null;
        $this->to_date = null;
    
        } else {
    
            if (!empty($this->from_date)) {
                $query->whereDate('avi.created_at', '>=', $this->from_date);
            }
    
            if (!empty($this->to_date)) {
                $query->whereDate('avi.created_at', '<=', $this->to_date);
            }
        }
    
        return $query->orderBy('avi.id', 'desc');

        
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
                    $mapped[] = $row->vehicle_type_name ?? '-';
                    // $mapped[] = $row->vehicle_type ?? '-';
                    break;
                case 'make':
                    $mapped[] = $row->make ?? '-';
                    break;
                case 'model':
                    $mapped[] = $row->vehicle_model ?? '-';
                    // $mapped[] = $row->vehicle_model ?? '-';
                    break;
                case 'variant':
                    $mapped[] = $row->variant?? '-';
                    break;
                case 'client':
                    // $mapped[] = $row->client ?? '-';
                    $mapped[] = $row->customer_name ?? '' ;
                    break;
                case 'color':
                    $mapped[] = $row->color_name ?? '-';
                    break;
                case 'chassis_number':
                    $mapped[] = $row->chassis_number ?? '-';
                    break;
                case 'zone':
                    $mapped[] = $row->zone_name ?? '-';
                    break;
                case 'gd_hub_id_allowcated':
                    $mapped[] = $row->gd_hub_name ?? '-';
                    break;
                case 'gd_hub_id_existing':
                    $mapped[] = $row->gd_hub_id ?? '-';
                break;
                case 'city':
                    $mapped[] = $row->city_name ?? '-';
                    // $mapped[] = $row->location ?? '-';
                    break;
                case 'accountability_type':
                    $mapped[] = $row->accountability_type ?? '-';
                    // $mapped[] = $row->location ?? '-';
                    break;
                case 'road_tax_next_renewal_date':
                    $mapped[] = ($date = $row->road_tax_next_renewal_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) 
                    ? \Carbon\Carbon::parse($date)->format('d-m-Y') 
                    : '-';
                    break;
                 case 'financing_type':
                    $mapped[] = $row->finance_type_name ?? '-';
                    break;
                  case 'asset_ownership':
                    $mapped[] = $row->asset_ownership_name ?? '-';
                    break;
                  case 'hypothecation_to':
                    $mapped[] = $row->hypothecation_name ?? '-';
                    break;
                    case 'insurer_name':
                    $mapped[] = $row->insurer_name ?? '-';
                    break;
                    case 'insurance_type':
                    $mapped[] = $row->insurance_type_name ?? '-';
                    break;
                   case 'registration_type':
                    $mapped[] = $row->registration_type_name ?? '-';
                    break;
                 case 'telematics_oem':
                    $mapped[] = $row->telmatics_oem_name ?? '-';
                    break;
                 case 'vehicle_status':
                    $mapped[] = $row->inventory_location_name ?? '-';
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
    
    public function chunkSize(): int
    {
        return 1000;  // required method
    }

}

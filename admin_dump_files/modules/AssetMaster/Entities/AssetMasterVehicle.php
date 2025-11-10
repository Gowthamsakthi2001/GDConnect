<?php

namespace Modules\AssetMaster\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AssetMaster\Database\factories\AssetMasterVehicleFactory;
use Modules\AssetMaster\Entities\AssetStatus;
use Modules\AssetMaster\Entities\QualityCheck;
use Modules\VehicleManagement\Entities\VehicleType;
use Modules\AssetMaster\Entities\VehicleModelMaster;
use Modules\AssetMaster\Entities\LocationMaster;
use Modules\MasterManagement\Entities\HypothecationMaster;
use Modules\MasterManagement\Entities\InsurerNameMaster;
use Modules\MasterManagement\Entities\InsuranceTypeMaster;
use Modules\MasterManagement\Entities\RegistrationTypeMaster;
use Modules\MasterManagement\Entities\TelemetricOEMMaster;
use Modules\MasterManagement\Entities\InventoryLocationMaster;
use Modules\MasterManagement\Entities\AssetOwnershipMaster;
use Modules\MasterManagement\Entities\FinancingTypeMaster;
use Modules\MasterManagement\Entities\CustomerMaster;
use Modules\MasterManagement\Entities\ColorMaster;

class AssetMasterVehicle extends Model
{
    use HasFactory;

    protected $table = 'ev_tbl_asset_master_vehicles';
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
        'id',
        'qc_id',
        'qc_status',
        'chassis_number',
        'vehicle_category',
        'vehicle_type',
        'make',
        'model',
        'variant',
        'color',
        'client',
        'motor_number',
        'vehicle_id',
        'tax_invoice_number',
        'tax_invoice_date',
        'tax_invoice_value',
        'tax_invoice_attachment',
        'location',
        'gd_hub_name',
        'financing_type',
        'asset_ownership',
        'master_lease_agreement',
        'lease_start_date',
        'lease_end_date',
        'vehicle_delivery_date',
        'emi_lease_amount',
        'hypothecation',
        'hypothecation_to',
        'insurer_name',
        'insurance_type',
        'insurance_number',
        'insurance_start_date',
        'insurance_expiry_date',
        'insurance_attachment',
        'registration_type',
        'registration_status',
        'permanent_reg_number',
        'permanent_reg_date',
        'reg_certificate_expiry_date',
        'reg_certificate_attachment',
        'fc_expiry_date',
        'fc_attachment',
        'battery_type',
        'battery_variant_name',
        'battery_serial_no',
        'charger_variant_name',
        'charger_serial_no',
        'telematics_variant_name',
        'telematics_serial_no',
        'vehicle_status',
        'is_status',
        'gd_hub_id' ,
        'city_code',
        'temproary_reg_number',
        'temproary_reg_date',
        'temproary_reg_expiry_date',
        'servicing_dates',
        'road_tax_applicable',
        'road_tax_amount',
        'road_tax_renewal_frequency',
        'road_tax_next_renewal_date',
        'battery_serial_number1',
        'battery_serial_number2',
        'battery_serial_number3',
        'battery_serial_number4',
        'battery_serial_number5',
        'charger_serial_number1',
        'charger_serial_number2',
        'charger_serial_number3',
        'charger_serial_number4',
        'charger_serial_number5',
        'telematics_oem',
        'telematics_serial_number1',
        'telematics_serial_number2',
        'telematics_serial_number3',
        'telematics_serial_number4',
        'telematics_serial_number5',
        'hsrp_copy_attachment',
        'hypothecation_document',
        'temproary_reg_attachment',
        'telematics_imei_number' ,
        'created_by',
        'delete_status',
        'delete_remarks',
        'created_by',
        'created_at',
        'updated_at'
    ];

    public function quality_check()
    {
        return $this->belongsTo(QualityCheck::class, 'qc_id', 'id');
    }
    
    public function vehicle_model_relation()
    {
        return $this->belongsTo(VehicleModelMaster::class, 'model', 'id');
    }

    
    public function vehicle_type_relation()
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type','id');
    }
    
     public function location_relation()
    {
        return $this->belongsTo(LocationMaster::class, 'location' , 'id');
    }
    
         public function hypothecation_relation()
    {
        return $this->belongsTo(HypothecationMaster::class, 'hypothecation_to' , 'id');
    }
    
    public function financing_type_relation()
    {
        return $this->belongsTo(FinancingTypeMaster::class, 'financing_type' , 'id');
    }
    
        public function asset_ownership_relation()
    {
        return $this->belongsTo(AssetOwnershipMaster::class, 'asset_ownership' , 'id');
    }


    public function insurer_name_relation()
    {
        return $this->belongsTo(InsurerNameMaster::class, 'insurer_name' , 'id');
    }
    
    
    public function insurer_type_relation()
    {
        return $this->belongsTo(InsuranceTypeMaster::class, 'insurance_type' , 'id');
    }
    
        public function registration_type_relation()
    {
        return $this->belongsTo(RegistrationTypeMaster::class, 'registration_type' , 'id');
    }
    
    public function telematics_oem_relation()
    {
        return $this->belongsTo(TelemetricOEMMaster::class, 'telematics_oem' , 'id');
    }
    
            public function inventory_location_relation()
    {
        return $this->belongsTo(InventoryLocationMaster::class, 'vehicle_status' , 'id');
    }
    
        public function customer_relation()
    {
        return $this->belongsTo(CustomerMaster::class, 'client', 'id');
    }
    
            public function color_relation()
    {
        return $this->belongsTo(ColorMaster::class, 'color', 'id');
    }
    
    public function location()
    {
        return $this->belongsTo(LocationMaster::class, 'city_code', 'id');
    }
    
    
    
    // protected $fillable = [
    //     'Reg_No',
    //     'Model',
    //     'Manufacturer',
    //     'Original_Motor_ID',
    //     'Chassis_Serial_No',
    //     'Purchase_order_ID',
    //     'Warranty_Kilometers',
    //     'Hub',
    //     'Client',
    //     'Colour',
    //     'Asset_In_Use_Date',
    //     'Deployed_To',
    //     'Emp_ID',
    //     'Procurement_Lease_Start_Date',
    //     'Lease_Rental_End_Date',
    //     'PO_Description',
    //     'Registration_Type',
    //     'Ownership_Type',
    //     'Lease_Value',
    //     'AMS_Location',
    //     'Parking_Location',
    //     'Asset_Status',
    //     'Sub_Status',
    //     'is_swappable',
    //     'dm_id',
    //     'rc_book',
    // ];

    // protected $casts = [
    //     'Asset_In_Use_Date' => 'date',
    //     'Procurement_Lease_Start_Date' => 'date',
    //     'Lease_Rental_End_Date' => 'date',
    //     'Lease_Value' => 'decimal:2',
    //     'Warranty_Kilometers' => 'integer',
    // ];
    // public function actionBtn($tableId = 'asset-master-vehicle')
    // {
    //     return '
    //       <div class="d-flex align-items-center gap-1">
    //         <a href="' . route('admin.Green-Drive-Ev.asset-master.asset_master_vehicle_edit', $this->id) . '" class="btn btn-success-soft btn-sm me-1">
    //             <svg class="svg-inline--fa fa-pen-to-square" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="pen-to-square" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
    //                 <path fill="currentColor" d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.8 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"></path>
    //             </svg>
    //         </a>
    //         <button onclick="route_alert(\'' . route('admin.Green-Drive-Ev.asset-master.asset_master_vehicle_delete', $this->id) . '\', \'Delete this AssetMasterVehicle\')" class="btn btn-danger-soft btn-sm">
    //             <svg class="svg-inline--fa fa-trash" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="trash" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="">
    //                 <path fill="currentColor" d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"></path>
    //             </svg>
    //         </button>
    //     </div>';
    // }
    
    // public function asset_status()
    // {
    //     return $this->belongsTo(AssetStatus::class, 'Asset_Status', 'id');
    // }
}

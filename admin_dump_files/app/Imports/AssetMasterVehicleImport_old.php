<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Maatwebsite\Excel\Concerns\ToModel;

class AssetMasterVehicleImport implements ToModel, WithHeadingRow
{
    
    
    public function model(array $row)
{
    if (
        isset($row['registration_number'], $row['model'], $row['original_motor_id'], $row['chassis_serial_number']) && 
        $row['registration_number'] !== "" && $row['model'] !== "" && $row['original_motor_id'] !== "" && $row['chassis_serial_number'] !== ""
    ) {
        $asset = AssetMasterVehicle::where('Reg_No', $row['registration_number'])
            ->where('Model', $row['model'])
            ->where('Original_Motor_ID', $row['original_motor_id'])
            ->where('Chassis_Serial_No', $row['chassis_serial_number'])
            ->first();

        $data = [
            'Reg_No' => $row['registration_number'] ?? null,
            'Model' => $row['model'] ?? null,
            'Manufacturer' => $row['manufacturer'] ?? null,
            'Original_Motor_ID' => $row['original_motor_id'] ?? null,
            'Chassis_Serial_No' => $row['chassis_serial_number'] ?? null,
            'Purchase_order_ID' => $row['purchase_order_id'] ?? 0,
            'Warranty_Kilometers' => $row['warranty_kilometers'] ?? 0,
            'Hub' => $row['hub'] ?? null,
            'Client' => $row['client'] ?? null,
            'Colour' => $row['colour'] ?? null,
            'Asset_In_Use_Date' => $row['asset_in_use_date'] ?? null,
            'Deployed_To' => $row['deployed_to'] ?? null,
            'Emp_ID' => $row['employee_id'] ?? null,
            'Procurement_Lease_Start_Date' => $row['procurement_lease_start_date'] ?? null,
            'Lease_Rental_End_Date' => $row['lease_rental_end_date'] ?? null,
            'PO_Description' => $row['po_description'] ?? null,
            'Registration_Type' => $row['registration_type'] ?? null,
            'Ownership_Type' => $row['ownership_type'] ?? null,
            'Lease_Value' => $row['lease_value'] ?? null,
            'AMS_Location' => $row['ams_location'] ?? null,
            'Parking_Location' => $row['parking_location'] ?? null,
            'Asset_Status' => $row['asset_status'] ?? null,
            'Sub_Status' => $row['sub_status'] ?? null,
            'rc_book' => $row['rc_book'] ?? 'rc_book_default.webp',
            'is_swappable' => $row['is_swappable'] ?? null,
        ];

        if ($asset) {
            $asset->update($data);
        } else {
            AssetMasterVehicle::create($data);
        }
    }

    return null;
}

    
//   public function model(array $row)
//     {
//          if (
//             isset($row['registration_number'], $row['model'], $row['original_motor_id'], $row['chassis_serial_number']) && 
//             $row['registration_number'] !== "" && $row['model'] !== "" && $row['original_motor_id'] !== "" && $row['chassis_serial_number'] !== ""
//         ) {
//             $asset = AssetMasterVehicle::where('Reg_No', $row->registration_number)->where('Model',$row->model)->where('Original_Motor_ID',$row->original_motor_id)
//             ->where('Chassis_Serial_No',$row->chassis_serial_number)->first();
            
//             if($asset){
//                 $asset->update([
//                     'Reg_No' => $row['registration_number'] ?? $asset->registration_number,
//                     'Model' => $row['model'] ?? $asset->model,
//                     'Manufacturer' => $row['manufacturer'] ?? $asset->manufacturer,
//                     'Original_Motor_ID' => $row['original_motor_id'] ?? $asset->original_motor_id,
//                     'Chassis_Serial_No' => $row['chassis_serial_number'] ?? $asset->chassis_serial_number,
//                     'Purchase_order_ID' => $row['purchase_order_id'] ?? $asset->purchase_order_id,
//                     'Warranty_Kilometers' => $row['warranty_kilometers'] ?? $asset->warranty_kilometers,
//                     'Hub' => $row['hub'] ?? $asset->hub,
//                     'Client' => $row['client'] ?? $asset->client,
//                     'Colour' => $row['colour'] ?? $asset->colour,
//                     'Asset_In_Use_Date' => $row['asset_in_use_date'] ?? '',
//                     'Deployed_To' => $row['deployed_to'] ?? $asset->deployed_to,
//                     'Emp_ID' => $row['employee_id'] ?? $asset->employee_id,
//                     'Procurement_Lease_Start_Date' => $row['procurement_lease_start_sate'] ?? '',
//                     'Lease_Rental_End_Date' => $row['lease_rental_end_date'] ?? '',
//                     'PO_Description' => $row['po_description'] ?? $asset->po_description,
//                     'Registration_Type' => $row['registration_type'] ?? $asset->registration_type,
//                     'Ownership_Type' => $row['ownership_type'] ?? $asset->ownership_type,
//                     'Lease_Value' => $row['lease_value'] ?? $asset->lease_value,
//                     'AMS_Location' => $row['ams_location'] ?? $asset->ams_location,
//                     'Parking_Location' => $row['parking_location'] ?? $asset->parking_location,
//                     'Asset_Status' => $row['asset_status'] ?? $asset->asset_status,
//                     'Sub_Status' => $row['sub_status'] ?? $asset->sub_status,
//                     'rc_book' => $row['rc_book'] ?? 'rc_book_default.webp',
//                     'is_swappable' => $row['is_swappable'] ?? $asset->is_swappable,
//                 ]);
//             }else{
//                 $create_array = [
//                     'Reg_No' => $row['registration_number'] ?? $asset->registration_number,
//                     'Model' => $row['model'] ?? $asset->model,
//                     'Manufacturer' => $row['manufacturer'] ?? $asset->manufacturer,
//                     'Original_Motor_ID' => $row['original_motor_id'] ?? $asset->original_motor_id,
//                     'Chassis_Serial_No' => $row['chassis_serial_number'] ?? $asset->chassis_serial_number,
//                     'Purchase_order_ID' => $row['purchase_order_id'] ?? $asset->purchase_order_id,
//                     'Warranty_Kilometers' => $row['warranty_kilometers'] ?? $asset->warranty_kilometers,
//                     'Hub' => $row['hub'] ?? $asset->hub,
//                     'Client' => $row['client'] ?? $asset->client,
//                     'Colour' => $row['colour'] ?? $asset->colour,
//                     'Asset_In_Use_Date' => $row['asset_in_use_date'] ?? '',
//                     'Deployed_To' => $row['deployed_to'] ?? $asset->deployed_to,
//                     'Emp_ID' => $row['employee_id'] ?? $asset->employee_id,
//                     'Procurement_Lease_Start_Date' => $row['procurement_lease_start_sate'] ?? '',
//                     'Lease_Rental_End_Date' => $row['lease_rental_end_date'] ?? '',
//                     'PO_Description' => $row['po_description'] ?? $asset->po_description,
//                     'Registration_Type' => $row['registration_type'] ?? $asset->registration_type,
//                     'Ownership_Type' => $row['ownership_type'] ?? $asset->ownership_type,
//                     'Lease_Value' => $row['lease_value'] ?? $asset->lease_value,
//                     'AMS_Location' => $row['ams_location'] ?? $asset->ams_location,
//                     'Parking_Location' => $row['parking_location'] ?? $asset->parking_location,
//                     'Asset_Status' => $row['asset_status'] ?? $asset->asset_status,
//                     'Sub_Status' => $row['sub_status'] ?? $asset->sub_status,
//                     'rc_book' => $row['rc_book'] ?? 'rc_book_default.webp',
//                     'is_swappable' => $row['is_swappable'] ?? $asset->is_swappable,
//                 ];
//                 AssetMasterVehicle::create($create_array);
                
//             }
        
//         }
//         return null;
        
//     }
}

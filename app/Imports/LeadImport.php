<?php

namespace App\Imports;

use Modules\Leads\Entities\leads;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LeadImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (
            isset($row['telecaller_status'], $row['first_name'], $row['last_name'], $row['mobile_number']) && 
            $row['telecaller_status'] !== "" && $row['first_name'] !== "" && $row['last_name'] !== "" && $row['mobile_number'] !== ""
        ) {
            $mobile_number = preg_replace('/\D/', '', $row['mobile_number']);
            if (!str_starts_with($mobile_number, '91')) {
                $mobile_number = '91' . $mobile_number;
            }
            $mobile_number = '+' . $mobile_number;
            $lead = leads::where('phone_number', $mobile_number)->first();


            if ($lead) {
                $lead->update([
                    'telecaller_status' => $row['telecaller_status'] ?? $lead->telecaller_status,
                    'Source' => $row['source_id'] ?? $lead->Source,
                    'assigned' => $row['telecaller_id'] ?? $lead->assigned,
                    'f_name' => $row['first_name'] ?? $lead->f_name,
                    'l_name' => $row['last_name'] ?? $lead->l_name,
                    'current_city' => $row['current_city_id'] ?? $lead->current_city,
                    'intrested_city' => $row['interested_area_id'] ?? $lead->intrested_city,
                    'vehicle_type' => $row['vehicle_type_id'] ?? $lead->vehicle_type,
                    'description' => $row['description'] ?? $lead->description,
                ]);

                return $lead;
            } else {
                return new leads([
                    'telecaller_status' => $row['telecaller_status'] ?? null,
                    'Source' => $row['source_id'] ?? null,
                    'assigned' => $row['telecaller_id'] ?? null,
                    'f_name' => $row['first_name'] ?? null,
                    'l_name' => $row['last_name'] ?? null,
                    'phone_number' => $mobile_number,
                    'current_city' => $row['current_city_id'] ?? null,
                    'intrested_city' => $row['interested_area_id'] ?? null,
                    'vehicle_type' => $row['vehicle_type_id'] ?? null,
                    'register_date' => now(),
                    'description' => $row['description'] ?? null,
                ]);
            }
        }

        return null;
    }
}

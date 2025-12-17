<?php
namespace App\Services;

use App\Models\AssetMaster;
use App\Services\ImportErrorCollector;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Master imports
use Modules\VehicleManagement\Entities\VehicleType;
use Modules\AssetMaster\Entities\VehicleModelMaster;
use Modules\City\Entities\City;
use Modules\Zones\Entities\Zones;
use Modules\MasterManagement\Entities\FinancingTypeMaster;
use Modules\MasterManagement\Entities\AssetOwnershipMaster;
use Modules\MasterManagement\Entities\InsurerNameMaster;
use Modules\MasterManagement\Entities\InsuranceTypeMaster;
use Modules\MasterManagement\Entities\HypothecationMaster;
use Modules\MasterManagement\Entities\RegistrationTypeMaster;
use Modules\MasterManagement\Entities\TelemetricOEMMaster;
use Modules\MasterManagement\Entities\InventoryLocationMaster;
use Modules\MasterManagement\Entities\ColorMaster;
use Modules\MasterManagement\Entities\LeasingPartnerMaster;
use Modules\AssetMaster\Entities\VehicleTransferChassisLog;

class AssetMasterUpdateService
{
    public function updateVehicle($vehicle, $rowData)
    {
        $errors = [];// collect all row errors
        // -----------------------------------------------
        // 1. Prepare arrays
        // -----------------------------------------------
        $updateData = [];       
        $qc_update_data = [];    
        $updatedReadable = [];  

        // -----------------------------------------------
        // 2. CREATE A MAP OF FIELD => READABLE LABEL
        //    (You only maintain this once. Very clean.)
        // -----------------------------------------------
        $readableMap = [
            'vehicle_category'  => 'Vehicle Category',
            'vehicle_type'      => 'Vehicle Type',
            'model'             => 'Vehicle Model',
            'make'              => 'Vehicle Make',
            'variant'           => 'Vehicle Variant',
            'color'             => 'Vehicle Color',
            'city_code'         => 'City Code',
            'location'          => 'Location',
            'motor_number'      => 'Motor Number',
            'vehicle_id'        => 'Vehicle ID',
            'qc_status'         => 'QC Status',
            'qc_pass_date'      => 'QC Pass Date',
            'qc_remark'         => 'QC Remark',
        ];

        try {
            foreach ($rowData as $k => $v) {
                if (is_string($v)) $rowData[$k] = trim($v);
            }

            $numericChecks = ['tax_invoice_value', 'emi_lease_amount', 'road_tax_amount'];

            foreach ($numericChecks as $field) {
                if (isset($rowData[$field]) && $rowData[$field] !== '') {
                    $candidate = str_replace([',', '₹', '$', ' '], '', $rowData[$field]);
                    if (!is_numeric($candidate)) {
                        $errors[] = "Invalid number '{$rowData[$field]}' for field {$field}";
                        $rowData[$field] = null;
                        continue;
                    }
                    // normalized numeric
                    $rowData[$field] = (float) $candidate;
                } else {
                    $rowData[$field] = null;
                }
            }

            $dateChecks = [
                'tax_invoice_date','lease_start_date','lease_end_date','vehicle_delivery_date','insurance_start_date','insurance_expiry_date',
                'temproary_reg_date','temproary_reg_expiry_date','permanent_reg_date','reg_certificate_expiry_date','fc_expiry_date',
                'road_tax_next_renewal_date',
            ];

            foreach ($dateChecks as $field) {
                $raw = $rowData[$field] ?? null;
                if (!$raw || trim($raw) === '' || in_array(strtolower(trim($raw)), ['na','n/a','-'])) {
                    $rowData[$field] = null;
                    continue;
                }
                $parsed = $this->parseDate($raw);
                if (!$parsed) {
                    // collect error, don't return
                    $errors[] = "Invalid Date Format: '{$raw}' for field {$field}";
                    $rowData[$field] = null;
                    continue;
                }
                $rowData[$field] = $parsed;
            }

            $uniqueFields = [
                'telematics_serial_no'   => $rowData['telematics_serial_no'] ?? null,
                'telematics_imei_number' => $rowData['telematics_imei_number'] ?? null,
            ];

            foreach ($uniqueFields as $field => $value) {

                $value = trim($value ?? '');

                if ($value === '') {
                    continue; // skip empty fields
                }

                // check existing record except current chassis
                $exists = $vehicle->where($field, $value)
                    ->where('chassis_number', '!=', $rowData['chassis_number'])
                    ->where('delete_status', 0)
                    ->first();

                if ($exists) {

                    // collect error, don't return immediately
                    $errors[] = "{$field}: '{$value}' already exists for chassis: {$exists->chassis_number}";

                    continue; // continue checking other unique fields
                }
            }

            /*
             * MASTER LOOKUPS EARLY CHECK
             * We check the masters that we will need and collect errors if not found.
             * We do not abort here; we will abort before DB transaction if any errors exist.
             */
            $masterLookupsToCheck = [
                'vehicle_type'      => [VehicleType::class, 'name'],
                'model'             => [VehicleModelMaster::class, 'vehicle_model'],
                'color'             => [ColorMaster::class, 'name'],
                'city'              => [City::class, 'city_name'],
                'financing_type'    => [FinancingTypeMaster::class, 'name'],
                'asset_ownership'   => [AssetOwnershipMaster::class, 'name'],
                'hypothecation_to'  => [HypothecationMaster::class, 'name'],
                'insurer_name'      => [InsurerNameMaster::class, 'name'],
                'insurance_type'    => [InsuranceTypeMaster::class, 'name'],
                'registration_type' => [RegistrationTypeMaster::class, 'name'],
                'zone'              => [Zones::class, 'name'],
                'telematics_oem'    => [TelemetricOEMMaster::class, 'name'],
                'leasing_partner'    => [LeasingPartnerMaster::class, 'name'],
            ];

            $masterIds = [];
            foreach ($masterLookupsToCheck as $field => [$model, $column]) {
                $val = $rowData[$field] ?? null;
                if (is_string($val)) $val = trim($val);
                if ($val === null || $val === '') continue;

                $id = $this->lookup($model, $column, $val);
                if (!$id) {
                   $errors[] = "Value '{$val}' for '{$field}' does not exist in the master data.";
                } else {
                    $masterIds[$field] = $id;
                }
            }

            // If any errors so far, report and skip before starting transaction
            if (!empty($errors)) {
                foreach ($errors as $msg) {
                    ImportErrorCollector::add(null, $vehicle->chassis_number ?? null, $msg);
                    ImportErrorCollector::$failedChassis[] = $vehicle->chassis_number;
                }
                return false; // skip only now
            }

            DB::beginTransaction();

            // ---------------------------
            // Build updateData & collect readable labels for log
            // ---------------------------
            if (!empty($rowData['vehicle_category'])) {
                $updateData['vehicle_category'] = $this->normalizeCategory($rowData['vehicle_category']);
                $updatedReadable[] = 'Vehicle Category';
            }

            if (!empty($rowData['vehicle_type'])) {
                // prefer masterIds if available
                $id = $masterIds['vehicle_type'] ?? $this->lookup(VehicleType::class, 'name', $rowData['vehicle_type']);
                if ($id) {
                    $updateData['vehicle_type'] = $id;
                    $updatedReadable[] = 'Vehicle Type';
                }
            }

            if (!empty($rowData['model'])) {
                $id = $masterIds['model'] ?? $this->lookup(VehicleModelMaster::class, 'vehicle_model', $rowData['model']);
                if ($id) {
                    $updateData['model'] = $id;
                    $updatedReadable[] = 'Vehicle Model';
                }
            }

            if (!empty($rowData['make'])) {
                $updateData['make'] = $rowData['make'];
                $updatedReadable[] = 'Vehicle Make';
            }

            if (!empty($rowData['variant'])) {
                $updateData['variant'] = $rowData['variant'];
                $updatedReadable[] = 'Vehicle Variant';
            }

            if (!empty($rowData['color'])) {
                $id = $masterIds['color'] ?? $this->lookup(ColorMaster::class, 'name', $rowData['color']);
                if ($id) {
                    $updateData['color'] = $id;
                    $updatedReadable[] = 'Vehicle Color';
                }
            }

            if (!empty($rowData['city'])) {
                $id = $masterIds['city'] ?? $this->lookup(City::class, 'city_name', $rowData['city']);
                if ($id) {
                    $updateData['city_code'] = $id;
                    $updateData['location']  = $id;
                    $updatedReadable[] = 'City Code';
                    $updatedReadable[] = 'Location';
                } else {
                    // This should not happen because we validated earlier, but keep safe fallback
                    ImportErrorCollector::add(null, $vehicle->chassis_number ?? null, "Master lookup failed for city: '{$rowData['city']}'");
                    DB::rollBack();
                    return false;
                }
            }

            if (!empty($rowData['motor_number'])) {
                $updateData['motor_number'] = $rowData['motor_number'];
                $updatedReadable[] = 'Motor Number';
            }
            if (!empty($rowData['vehicle_id'])) {
                $updateData['vehicle_id'] = $rowData['vehicle_id'];
                $updatedReadable[] = 'Vehicle ID';
            }
            if (!empty($rowData['tax_invoice_number'])) {
                $updateData['tax_invoice_number'] = $rowData['tax_invoice_number'];
                $updatedReadable[] = 'Tax Invoice Number';
            }

            if (!empty($rowData['tax_invoice_date'])) {
                $updateData['tax_invoice_date'] = $this->parseDate($rowData['tax_invoice_date']);
                $updatedReadable[] = 'Tax Invoice Date';
            }

            if (!empty($rowData['tax_invoice_value'])) {
                $updateData['tax_invoice_value'] = $rowData['tax_invoice_value'];
                $updatedReadable[] = 'Tax Invoice Value';
            }

            if (!empty($rowData['gd_hub_id_allowcated'])) {
                $updateData['gd_hub_name'] = $rowData['gd_hub_id_allowcated'];
                $updatedReadable[] = 'GD Hub Name (Allocated)';
            }

            if (!empty($rowData['gd_hub_id_existing'])) {
                $updateData['gd_hub_id'] = $rowData['gd_hub_id_existing'];
                $updatedReadable[] = 'GD Hub ID (Existing)';
            }

            if (!empty($rowData['financing_type'])) {
                $id = $masterIds['financing_type'] ?? $this->lookup(FinancingTypeMaster::class, 'name', $rowData['financing_type']);
                if ($id) {
                    $updateData['financing_type'] = $id;
                    $updatedReadable[] = 'Financing Type';
                } else {
                    $errors[] = "Master lookup failed for financing_type: '{$rowData['financing_type']}'";
                }
            }

            if (!empty($rowData['asset_ownership'])) {
                $id = $masterIds['asset_ownership'] ?? $this->lookup(AssetOwnershipMaster::class, 'name', $rowData['asset_ownership']);
                if ($id) {
                    $updateData['asset_ownership'] = $id;
                    $updatedReadable[] = 'Asset Ownership';
                } else {
                    $errors[] = "Master lookup failed for asset_ownership: '{$rowData['asset_ownership']}'";
                }
            }
            if (!empty($rowData['leasing_partner'])) {
                $id = $masterIds['leasing_partner'] ?? $this->lookup(LeasingPartnerMaster::class, 'name', $rowData['leasing_partner']);
                if ($id) {
                    $updateData['leasing_partner'] = $id;
                    $updatedReadable[] = 'Leasing Partner';
                } else {
                    $errors[] = "Master lookup failed for leasing_partner: '{$rowData['leasing_partner']}'";
                }
            }

            // if (!empty($rowData['master_lease_agreement'])) {
            //     $updateData['master_lease_agreement'] = $rowData['master_lease_agreement'];
            //     $updatedReadable[] = 'Master Lease Agreement';
            // }

            if (!empty($rowData['lease_start_date'])) {
                $updateData['lease_start_date'] = $this->parseDate($rowData['lease_start_date']);
                $updatedReadable[] = 'Lease Start Date';
            }

            if (!empty($rowData['lease_end_date'])) {
                $updateData['lease_end_date'] = $this->parseDate($rowData['lease_end_date']);
                $updatedReadable[] = 'Lease End Date';
            }

            if (isset($rowData['emi_lease_amount']) && $rowData['emi_lease_amount'] !== '') {
                $updateData['emi_lease_amount'] = $rowData['emi_lease_amount'];
                $updatedReadable[] = 'EMI Lease Amount';
            }

            if (!empty($rowData['hypothecation'])) {
                $updateData['hypothecation'] = strtolower($rowData['hypothecation']);
                $updatedReadable[] = 'Hypothecation';
            }

            if (!empty($rowData['hypothecation_to'])) {
                $id = $masterIds['hypothecation_to'] ?? $this->lookup(HypothecationMaster::class, 'name', $rowData['hypothecation_to']);
                if ($id) {
                    $updateData['hypothecation_to'] = $id;
                    $updatedReadable[] = 'Hypothecation To';
                } else {
                    $errors[] = "Master lookup failed for hypothecation_to: '{$rowData['hypothecation_to']}'";
                }
            }

            if (!empty($rowData['insurer_name'])) {
                $id = $masterIds['insurer_name'] ?? $this->lookup(InsurerNameMaster::class, 'name', $rowData['insurer_name']);
                if ($id) {
                    $updateData['insurer_name'] = $id;
                    $updatedReadable[] = 'Insurer Name';
                } else {
                    $errors[] = "Master lookup failed for insurer_name: '{$rowData['insurer_name']}'";
                }
            }

            if (!empty($rowData['insurance_type'])) {
                $id = $masterIds['insurance_type'] ?? $this->lookup(InsuranceTypeMaster::class, 'name', $rowData['insurance_type']);
                if ($id) {
                    $updateData['insurance_type'] = $id;
                    $updatedReadable[] = 'Insurance Type';
                } else {
                    $errors[] = "Master lookup failed for insurance_type: '{$rowData['insurance_type']}'";
                }
            }

            if (!empty($rowData['insurance_number'])) {
                $updateData['insurance_number'] = $rowData['insurance_number'];
                $updatedReadable[] = 'Insurance Number';
            }

            if (!empty($rowData['insurance_start_date'])) {
                $updateData['insurance_start_date'] = $this->parseDate($rowData['insurance_start_date']);
                $updatedReadable[] = 'Insurance Start Date';
            }

            if (!empty($rowData['insurance_expiry_date'])) {
                $updateData['insurance_expiry_date'] = $this->parseDate($rowData['insurance_expiry_date']);
                $updatedReadable[] = 'Insurance Expiry Date';
            }

            if (!empty($rowData['registration_type'])) {
                $id = $masterIds['registration_type'] ?? $this->lookup(RegistrationTypeMaster::class, 'name', $rowData['registration_type']);
                if ($id) {
                    $updateData['registration_type'] = $id;
                    $updatedReadable[] = 'Registration Type';
                } else {
                    $errors[] = "Master lookup failed for registration_type: '{$rowData['registration_type']}'";
                }
            }

            if (!empty($rowData['registration_status'])) {
                $updateData['registration_status'] = $rowData['registration_status'];
                $updatedReadable[] = 'Registration Status';
            }

            if (!empty($rowData['temproary_reg_number'])) {
                $updateData['temproary_reg_number'] = $rowData['temproary_reg_number'];
                $updatedReadable[] = 'Temporary Reg Number';
            }

            if (!empty($rowData['temproary_reg_date'])) {
                $updateData['temproary_reg_date'] = $this->parseDate($rowData['temproary_reg_date']);
                $updatedReadable[] = 'Temporary Reg Date';
            }

            if (!empty($rowData['temproary_reg_expiry_date'])) {
                $updateData['temproary_reg_expiry_date'] = $this->parseDate($rowData['temproary_reg_expiry_date']);
                $updatedReadable[] = 'Temporary Reg Expiry Date';
            }

            if (!empty($rowData['permanent_reg_number'])) {
                $updateData['permanent_reg_number'] = $rowData['permanent_reg_number'];
                $updatedReadable[] = 'Permanent Reg Number';
            }

            if (!empty($rowData['permanent_reg_date'])) {
                $updateData['permanent_reg_date'] = $this->parseDate($rowData['permanent_reg_date']);
                $updatedReadable[] = 'Permanent Reg Date';
            }

            if (!empty($rowData['reg_certificate_expiry_date'])) {
                $updateData['reg_certificate_expiry_date'] = $this->parseDate($rowData['reg_certificate_expiry_date']);
                $updatedReadable[] = 'Reg Certificate Expiry Date';
            }

            if (!empty($rowData['fc_expiry_date'])) {
                $updateData['fc_expiry_date'] = $this->parseDate($rowData['fc_expiry_date']);
                $updatedReadable[] = 'FC Expiry Date';
            }

            if (!empty($rowData['servicing_dates'])) {
                $updateData['servicing_dates'] = $rowData['servicing_dates'];
                $updatedReadable[] = 'Servicing Dates';
            }

            if (!empty($rowData['road_tax_applicable'])) {
                $updateData['road_tax_applicable'] = strtolower($rowData['road_tax_applicable']);
                $updatedReadable[] = 'Road Tax Applicable';
            }

            if (!empty($rowData['road_tax_amount'])) {
                $updateData['road_tax_amount'] = $rowData['road_tax_amount'];
                $updatedReadable[] = 'Road Tax Amount';
            }

            if (!empty($rowData['road_tax_renewal_frequency'])) {
                $updateData['road_tax_renewal_frequency'] = $rowData['road_tax_renewal_frequency'];
                $updatedReadable[] = 'Road Tax Renewal Frequency';
            }

            if (!empty($rowData['road_tax_next_renewal_date'])) {
                $updateData['road_tax_next_renewal_date'] = $this->parseDate($rowData['road_tax_next_renewal_date']);
                $updatedReadable[] = 'Road Tax Next Renewal Date';
            }

            if (!empty($rowData['battery_type'])) {
                $updateData['battery_type'] = $this->getBatteryType($rowData['battery_type']);
                $updatedReadable[] = 'Battery Type';
            }

            if (!empty($rowData['battery_variant_name'])) {
                $updateData['battery_variant_name'] = $rowData['battery_variant_name'];
                $updatedReadable[] = 'Battery Variant Name';
            }

            if (!empty($rowData['battery_serial_no'])) {
                $updateData['battery_serial_no'] = $rowData['battery_serial_no'];
                $updatedReadable[] = 'Battery Serial No';
            }

            foreach (range(1, 5) as $i) {
                $key = "battery_serial_number_replacement_$i";
                if (!empty($rowData[$key])) {
                    $updateData["battery_serial_number$i"] = $rowData[$key];
                    $updatedReadable[] = "Battery Serial Number Replacement $i";
                }
            }

            if (!empty($rowData['charger_variant_name'])) {
                $updateData['charger_variant_name'] = $rowData['charger_variant_name'];
                $updatedReadable[] = 'Charger Variant Name';
            }

            if (!empty($rowData['charger_serial_no'])) {
                $updateData['charger_serial_no'] = $rowData['charger_serial_no'];
                $updatedReadable[] = 'Charger Serial No';
            }

            foreach (range(1, 5) as $i) {
                $key = "charger_serial_number_replacement_$i";
                if (!empty($rowData[$key])) {
                    $updateData["charger_serial_number$i"] = $rowData[$key];
                    $updatedReadable[] = "Charger Serial Number Replacement $i";
                }
            }

            if (!empty($rowData['telematics_variant_name'])) {
                $updateData['telematics_variant_name'] = $rowData['telematics_variant_name'];
                $updatedReadable[] = 'Telematics Variant Name';
            }

            if (!empty($rowData['telematics_oem'])) {
                $id = $masterIds['telematics_oem'] ?? $this->lookup(TelemetricOEMMaster::class, 'name', $rowData['telematics_oem']);
                if ($id) {
                    $updateData['telematics_oem'] = $id;
                    $updatedReadable[] = 'Telematics OEM';
                } else {
                    $errors[] = "Master lookup failed for telematics_oem: '{$rowData['telematics_oem']}'";
                }
            }

            if (!empty($rowData['telematics_serial_no'])) {
                $updateData['telematics_serial_no'] = $rowData['telematics_serial_no'];
                $updatedReadable[] = 'Telematics Serial No';
            }

            if (!empty($rowData['telematics_imei_number'])) {
                $updateData['telematics_imei_number'] = $rowData['telematics_imei_number'];
                $updatedReadable[] = 'Telematics IMEI Number';
            }

            foreach (range(1, 5) as $i) {
                $key = "telematics_serial_number_replacement_$i";
                if (!empty($rowData[$key])) {
                    $updateData["telematics_serial_number$i"] = $rowData[$key];
                    $updatedReadable[] = "Telematics Serial Number Replacement $i";
                }
            }

            if (!empty($rowData['vehicle_delivery_date'])) {
                $updateData['vehicle_delivery_date'] = $this->parseDate($rowData['vehicle_delivery_date']);
                $updatedReadable[] = 'Vehicle Delivery Date';
            }

            // If any lookup errors were discovered during updateData population, rollback and report
            if (!empty($errors)) {
                foreach ($errors as $msg) {
                    ImportErrorCollector::add(null, $vehicle->chassis_number ?? null, $msg);
                }
                DB::rollBack();
                return false;
            }

            // ---------------------------
            // Build QC update data & readable labels
            // ---------------------------
            if (!empty($rowData['vehicle_type'])) {
                $id = $masterIds['vehicle_type'] ?? $this->lookup(VehicleType::class, 'name', $rowData['vehicle_type']);
                if ($id) {
                    $qc_update_data['vehicle_type'] = $id;
                }
            }

            if (!empty($rowData['model'])) {
                $id = $masterIds['model'] ?? $this->lookup(VehicleModelMaster::class, 'vehicle_model', $rowData['model']);
                if ($id) $qc_update_data['vehicle_model'] = $id;
            }

            if (!empty($rowData['city'])) {
                $id = $masterIds['city'] ?? $this->lookup(City::class, 'city_name', $rowData['city']);
                if ($id) $qc_update_data['location'] = $id;
            }

            if (!empty($rowData['zone'])) {
                $id = $masterIds['zone'] ?? $this->lookup(Zones::class, 'name', $rowData['zone']);
                \Log::info("Zone Name ".$id);
                if ($id) $qc_update_data['zone_id'] = $id;
            }

            if (!empty($rowData['battery_serial_no'])) {
                $qc_update_data['battery_number'] = $rowData['battery_serial_no'];
            }

            if (!empty($rowData['telematics_serial_no'])) {
                $qc_update_data['telematics_number'] = $rowData['telematics_serial_no'];
            }

            if (!empty($rowData['motor_number'])) {
                $qc_update_data['motor_number'] = $rowData['motor_number'];
            }

            // do update with withoutEvents to save a little overhead
            $vehicle->withoutEvents(function() use ($vehicle, $updateData, $qc_update_data, $updatedReadable) {
                $vehicle->update($updateData);
                if ($vehicle->quality_check) {
                    $vehicle->quality_check->update($qc_update_data);
                }

                // build readable updated text and insert chassis log
                $updatedText = implode(', ', array_unique($updatedReadable));

               if ($updatedText === '') {
                    $updatedText = 'No fields were captured, but the record was updated.';
                }
                
                $remarks = "Bulk Upload Inventory: The following fields were updated: {$updatedText}.";
                \Log::info("The following fields were updated: {$updatedText}.");
                
                // INSERT CHASSIS LOG
                VehicleTransferChassisLog::create([
                    'chassis_number' => $vehicle->chassis_number,
                    'vehicle_id'     => $vehicle->id,
                    'remarks'        => $remarks,
                    'created_by'     => auth()->id(),
                    'is_status'      => 'updated',
                    'status'         => 'updated',
                ]);
                
                
                $user     = auth()->user();
                $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
                if(!empty($roleName)){
                    audit_log_after_commit([
                    'module_id'         => 4,
                        'short_description' => "Bulk Upload Inventory – Vehicle ID: {$vehicle->id}, Chassis: {$vehicle->chassis_number} has been updated.",
                        'long_description'  => $remarks,
                        'role'              => $roleName,
                        'user_id'           => auth()->user()->id,
                        'user_type'         => 'gdc_admin_dashboard',
                        'dashboard_type'    => 'web',
                        'page_name'         => 'asset_master.inventory.bulk_upload',
                        'ip_address'        => null,
                        'user_device'       => null,
                    ]);
                }
                

            });

            DB::commit();
            ImportErrorCollector::$updatedChassis[] = $vehicle->chassis_number;

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            // map DB error to friendly message and store
            ImportErrorCollector::add(null, $vehicle->chassis_number ?? null, $this->mapSqlError($e->getMessage()));
            Log::error("Bulk Row Error: " . $e->getMessage());
            return false;
        }
    }

    /* --------------------------------------------
        SUPPORT
    ---------------------------------------------*/

    private function lookup($model, $column, $value)
    {
        if (empty($value)) return null;

        return $model::whereRaw("LOWER($column) = ?", [strtolower(trim($value))])
            ->value('id');
    }

    private function parseDate($date)
    {
        if (empty($date) || trim($date) === '' ||
            in_array(strtoupper(trim($date)), ['NA', 'N/A', '-'])) {
            return null;
        }
        $clean = trim(str_replace(['.', '/'], '-', $date));
        $formats = [
            'd-m-Y', 'm-d-Y', 'Y-m-d',
            'd-m-y', 'm-d-y',
            'Y/m/d', 'd/m/Y', 'm/d/Y',
            'd.m.Y', 'm.d.Y'
        ];
        foreach ($formats as $f) {
            $parsed = \DateTime::createFromFormat($f, $clean);
            if ($parsed && $parsed->format($f) === $clean) {
                return $parsed->format('Y-m-d');
            }
        }
        try {
            return \Carbon\Carbon::parse($clean)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }

    private function normalizeCategory($val)
    {
        if (!$val) return null;

        $v = strtolower(str_replace([' ', '-', '_'], '', $val));

        if (in_array($v, ['regularvehicle', 'reqularvehicle'])) return 'regular_vehicle';
        if ($v === 'lowspeedvehicle') return 'low_speed_vehicle';

        return null;
    }

    private function getBatteryType($val)
    {
        if (!$val || trim($val) === '' || in_array(strtolower(trim($val)), ['na','n/a','-'])) {
            return null;
        }

        $clean = strtolower(trim(preg_replace('/[^a-zA-Z\s]/', '', $val)));

        $normalized = str_replace(' ', '', $clean);

        return match ($normalized) {
            'selfcharging' => '1',
            'portable'     => '2',
            default        => null
        };
    }

    private function mapSqlError($msg)
    {
        if (str_contains($msg, 'Incorrect double value')) {
            preg_match("/'([^']+)'/", $msg, $m);
            return "Invalid number: '" . ($m[1] ?? '') . "'";
        }

        if (str_contains($msg, 'Incorrect datetime value')) {
            preg_match("/'([^']+)'/", $msg, $m);
            return "Invalid date: '" . ($m[1] ?? '') . "'";
        }

        if (str_contains($msg, 'Data too long')) {
            return "Text too long";
        }

        if (str_contains($msg, 'Incorrect integer value')) {
            preg_match("/'([^']+)'/", $msg, $m);
            return "Invalid integer: '" . ($m[1] ?? '') . "'";
        }

        return "Invalid field value";
    }
}

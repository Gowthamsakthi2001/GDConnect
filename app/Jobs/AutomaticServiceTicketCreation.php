<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\B2B\Entities\ServiceTicket;
use Modules\B2B\Entities\B2BServiceRequest;
use Modules\B2B\Entities\B2BVehicleAssignment;//updated by Mugesh.B
use Modules\B2B\Entities\B2BVehicleAssignmentLog;//updated by Mugesh.B
use Modules\VehicleServiceTicket\Entities\FieldProxyTicket;//updated by Mugesh.B
use Modules\VehicleServiceTicket\Entities\FieldProxyLog;//updated by Mugesh.B
use Carbon\Carbon;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\VehicleServiceTicket\Entities\VehicleTicket;
use Modules\City\Entities\City;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\CustomHandler;
class AutomaticServiceTicketCreation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ticket_id;

    /**
     * Create a new job instance.
     */
    public function __construct($ticket_id)
    {
        $this->ticket_id = $ticket_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();
        $assignment = null; 
        $service    = null;
        $customer   = null;
        $fp         = null;
        $vehicle    = null;

         try {
         $service = B2BServiceRequest::where('ticket_id', $this->ticket_id)->first();
             if(!empty($service)){
                 
                $assignment = B2BVehicleAssignment::where('id', $service->assign_id)->first();
                 
                   $vehicle = AssetMasterVehicle::where('id', $assignment->asset_vehicle_id)
                    ->first();
                    
                    if (empty($vehicle)) {
                         Log::info("Vehicle not found for assignment. Ticket ID: " . $this->ticket_id);
                         return;
                    }
                    
                    $new_ticket_id = CustomHandler::GenerateTicketId($vehicle->quality_check->location);
                    
                    if ($new_ticket_id == "" || $new_ticket_id == null) {
                         Log::info("Ticket Creation Failed: " . $vehicle->chassis_number);
                         return;
                    }
                    
                    $customer = $assignment->VehicleRequest->customerLogin->customer_relation;
                    
                    $fp = FieldProxyTicket::where('greendrive_ticketid' , $this->ticket_id)->first();
                    
                    $this->CreateServiceRequest($new_ticket_id , $vehicle , $customer  , $assignment , $service , $fp);
             }else{
                 
                    $fp = FieldProxyTicket::where('greendrive_ticketid' , $this->ticket_id)->first();
                    
                        if (empty($fp) || empty($fp->chassis_number)) {
                            Log::info("FieldProxyTicket not found or missing chassis_number. Ticket ID: {$this->ticket_id}");
                            return;
                        }
                    
                    $vehicle = AssetMasterVehicle::where('chassis_number', $fp->chassis_number ?? null)->first();
                    
                    if (empty($vehicle)) {
                        Log::info("Vehicle not found by chassis. Ticket ID: {$this->ticket_id}");
                        return;
                    }

                
                    
                    $new_ticket_id = CustomHandler::GenerateTicketId($vehicle->quality_check->location);
                    
                    if ($new_ticket_id == "" || $new_ticket_id == null) {
                         Log::info("Ticket Creation Failed: " . $vehicle->chassis_number);
                         return;
                    }
                    
                     $accountability_type = $vehicle->quality_check->accountability_type;
                     
                     $customer = null;
                                    
                        if(!empty($accountability_type) && $accountability_type == 1){
                            
                             $customer = $vehicle->customer_relation;
                        }
                        elseif(!empty($accountability_type) && $accountability_type == 2){
                            
                            $customer = $vehicle->quality_check->customer_relation;
                        }
                                    
                    
                    $this->CreateServiceRequest($new_ticket_id , $vehicle , $customer  , $assignment , $service , $fp);
             }
              DB::commit();  
         } catch (\Throwable $e) {
                DB::rollBack();
                Log::error("AutomaticServiceTicketCreation failed", [
                    'ticket_id' => $this->ticket_id,
                    'message'   => $e->getMessage(),
                    'file'      => $e->getFile(),
                    'line'      => $e->getLine(),
                    'trace'     => collect($e->getTrace())->take(5)->toArray(), 
                ]);

        }
    }
    
   private function CreateServiceRequest( $ticket_id, $vehicle, $customer = null, $assignment = null, $service = null  , $fp): void
   {
         try {
            if(!empty($assignment)){
    
                $service = B2BServiceRequest::create([
                        'assign_id'       => $assignment->id,
                        'ticket_id'       =>$ticket_id ?? '',
                        'vehicle_number'  => $vehicle['permanent_reg_number'],
                        'description'     => 'Service request automatically generated due to audit failure',
                        'address'         => '',
                        'repair_type'     => 2,
                        'city'            => $vehicle->quality_check->location ,
                        'zone_id'         =>   $vehicle->quality_check->zone_id ,
                        'gps_pin_address'   => '',
                        'poc_name'          => $fp->customer_name ?? '',
                        'poc_number'    => $fp->customer_number ?? '',
                        'driver_name'   => $fp->driver_name ?? '',
                        'driver_number'   => $fp->driver_number ?? '',
                        'current_status'   => 'open',
                        'latitude'               =>  '',
                        'longitude'              =>  '',
                        'status'          => 'unassigned',
                        'created_by'      => $fp->created_by,
                        'type'            => $fp->type
                ]);
                
                
                B2BVehicleAssignmentLog::create([
                    'assignment_id' => $assignment->id,
                    'status'        => 'unassigned',
                    'current_status' => 'open',
                    'remarks'       => "Service request raised for vehicle {$vehicle['permanent_reg_number']}",
                    'action_by'     => $fp->created_by,
                    'type'          => $fp->type,
                    'request_type'  => 'service_request',
                    'request_type_id' => $service->id
                ]);
                
            }
        
        
        $ticket = VehicleTicket::create([
                'ticket_id'         => $ticket_id,
                'vehicle_no'        => $vehicle['permanent_reg_number'],
                'city_id'           => $vehicle->quality_check->location,
                'area_id'           => $vehicle->quality_check->zone_id,
                'vehicle_type'      => $vehicle->quality_check->vehicle_type ?? '',
                'poc_name'          => $fp->customer_name ?? '',
                'poc_contact_no'    => $fp->customer_number ?? '',
                'issue_remarks'     => 'Service request automatically generated due to audit failure',
                'repair_type'       => 2,
                'address'           => '',
                'gps_pin_address'   => '',
                'lat'               => '',
                'long'              => '',
                'driver_name'       => $fp->driver_name ?? '',
                'driver_number'     => $fp->driver_number ?? '',
                'image'             => '',
                'created_datetime'  => now(),                                                                                                          
                'created_by'        => '',
                'created_role'      => '',
                'customer_id'             => '',
                'web_portal_status' => 0,
                'platform'          => 'audit_fail',
                'ticket_status'     => 0,
            ]);
        
            $city = City::find($vehicle->quality_check->location);

            $createdDatetime = Carbon::now()->utc();
            
            $customerLongitude = null;
            $customerLatitude  = null;
                
            $pointOfContact = '';

            if (!empty($fp->customer_number) && !empty($fp->customer_name)) {
                $pointOfContact = $fp->customer_number . ' - ' . $fp->customer_name;
            } elseif (!empty($fp->driver_number) && !empty($fp->driver_name)) {
                $pointOfContact = $fp->driver_number . ' - ' . $fp->driver_name;
            }

             $ticketData = [
                "vehicle_number" => $vehicle['permanent_reg_number'],
                "updatedAt" => $createdDatetime,
                "ticket_status" => "unassigned",
                "chassis_number" => $vehicle->chassis_number ?? null,
                "telematics" => $vehicle->telematics_imei_number ?? null,
                "battery" => $vehicle->battery_serial_no ?? null,
                "vehicle_type" => $vehicle->quality_check->vehicle_type_relation->name ?? null,
                "state" => $city->state->state_name ?? '',
                "priority" => 'High',
                "point_of_contact_info" => $pointOfContact,
                "job_type" => 'Running Repair' ?? null,
                "issue_description" => 'Service request automatically generated due to audit failure',
                'image' => [],
                'address'   => '',
                "greendrive_ticketid" => $ticket_id,
                'driver_name'   => $fp->driver_name ?? '',
                'driver_number'   => $fp->driver_number ?? '',
                "customer_number" => $fp->customer_number ?? '',
                "customer_name" => $fp->customer_name ?? '',
                'customer_email' => $fp->customer_email ?? '',
                'customer_location' => [
                    $customerLongitude,
                    $customerLatitude
                ], 
                "current_status" => 'open',
                "createdAt" => $createdDatetime,
                "city" => $city->city_name ?? null,
            ];
            
            
            $fieldProxyTicket = FieldProxyTicket::create(array_merge($ticketData, [
                'created_by' => '',
                'type'       => 'audit_fail',
            ]));
            
            
            
            FieldProxyLog::create([
                'fp_id'      => $fieldProxyTicket->id,   
                'status'     => 'unassigned',  // ticket status
                "current_status" => 'open',
                'remarks'    => "Service request raised for vehicle {$vehicle['permanent_reg_number']}",
                'created_by' => '',
                'type'       => 'audit_fail',
            ]);
            
            $apiTicketData = $ticketData;
            $apiTicketData['driver_number'] = preg_replace('/^\+91/', '', $ticketData['driver_number']);
            $apiTicketData['customer_number'] = preg_replace('/^\+91/', '', $ticketData['customer_number']);

            $apiData = [
                "sheetId" => "tickets",
                "tableData" => $apiTicketData
            ];
            
           
            $fieldproxy_base_url = BusinessSetting::where('key_name', 'fieldproxy_base_url')->value('value');
            $fieldproxy_create_endpoint = BusinessSetting::where('key_name', 'fieldproxy_create_enpoint')->value('value');
            $apiUrl = $fieldproxy_base_url . $fieldproxy_create_endpoint;
            $apiKey = env('FIELDPROXY_API_KEY', null); 
            
            
            if (empty($apiUrl) || empty($apiKey)) {
                Log::info("FieldProxy API settings missing. Ticket ID: {$ticket_id}");
                DB::rollBack();
                return;

            }
            
    
            $ch = curl_init($apiUrl);
            $payload = json_encode($apiData);
    
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "x-api-key: {$apiKey}",
                "Content-Type: application/json",
                "Accept: application/json"
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
            $responseBody = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
    
            $fieldproxyResult = null;
            // ========== THROW ERROR TO TRIGGER ROLLBACK ==========
            if ($curlError) {
                throw new \Exception("FieldProxy cURL Error: {$curlError}");
            }
    
            if ($httpCode >= 400) {
                throw new \Exception("FieldProxy HTTP {$httpCode} Error: {$responseBody}");
            }
    
            $decoded = json_decode($responseBody, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("FieldProxy returned invalid JSON");
            }
            // ======================================================
            
                try {
                    $this->send_whatsappmessage($customer , $vehicle , $fp);
                } catch (\Throwable $e) {
                    Log::info("WhatsApp failed for ticket {$ticket_id}: " . $e->getMessage());
                }
                
                try {
                    $this->sendEmail($customer , $vehicle , $ticket_id , $fp);
                } catch (\Throwable $e) {
                    Log::info("Email failed for ticket {$ticket_id}: " . $e->getMessage());
                }
                
                return;

        } catch (\Throwable $e) {
            Log::info("CreateServiceRequest error for greendrive_ticketid {$ticket_id}: " . $e->getMessage());
            DB::rollBack();
            return;

        }
    }
    
        private function send_whatsappmessage($customer, $vehicle, $fp): void
        {
            try {
                // ==============================
                // VEHICLE DETAILS
                // ==============================
                $vehicleNo   = $fp->vehicle_number ?? 'N/A';
                $vehicleType = $fp->vehicle_type ?? 'N/A';
        
                // ==============================
                // CUSTOMER DETAILS
                // ==============================
                $customerPhone = !empty($fp->customer_number) ? $fp->customer_number : null;
                $customerName  = $fp->customer_name ?? null;
                $customerEmail = $fp->customer_email ?? null;
        
                // ==============================
                // DRIVER DETAILS
                // ==============================
                $driverPhone = !empty($fp->driver_number) ? $fp->driver_number : null;
                $driverName  = $fp->driver_name ?? null;
        
                // ==============================
                // ISSUE DESCRIPTION
                // ==============================
                $issueDescription = "Vehicle failed audit and requires immediate maintenance. Service request auto-generated.";
        
                // ==============================
                // FOOTER
                // ==============================
                $footerText = BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
                $footerContentText = $footerText ?: 
                    "For any assistance, please reach out to Admin Support.\n"
                    . "Email: support@greendrivemobility.com\n"
                    . "Thank you,\nGreenDriveConnect Team";
        
                // ==========================================================
                // SEND MESSAGE TO CUSTOMER (if available)
                // ==========================================================
                if ($customerPhone) {
        
                    $customerMessage =
                        "Hello *" . ($customerName ?? 'Customer') . "*,\n\n"
                        . "A new *Service Request* has been raised automatically because your vehicle failed the audit.\n\n"
                        . "*Vehicle Details:*\n"
                        . "• Vehicle No: {$vehicleNo}\n"
                        . "• Type: {$vehicleType}\n\n"
                        . "*Issue Identified:*\n"
                        . "{$issueDescription}\n\n"
                        . $footerContentText;
        
                    CustomHandler::user_whatsapp_message($customerPhone, $customerMessage);
                }
        
                // ==========================================================
                // SEND MESSAGE TO DRIVER (if available)
                // ==========================================================
                if ($driverPhone) {
        
                    $driverMessage =
                        "Hello *" . ($driverName ?? 'Driver') . "*,\n\n"
                        . "A new *Service Request* has been raised automatically because the vehicle failed the audit.\n\n"
                        . "*Vehicle Details:*\n"
                        . "• Vehicle No: {$vehicleNo}\n"
                        . "• Type: {$vehicleType}\n\n"
                        . "*Issue Identified:*\n"
                        . "{$issueDescription}\n\n"
                        . $footerContentText;
        
                    CustomHandler::user_whatsapp_message($driverPhone, $driverMessage);
                }
        
                // ==========================================================
                // SEND MESSAGE TO ADMIN (customer preferred, else driver)
                // ==========================================================
                $adminPhone = BusinessSetting::where('key_name', 'admin_whatsapp_no')->value('value');
        
                if (!empty($adminPhone)) {
        
                    // Decide point of contact for admin
                    if ($customerPhone) {
                        $contactLabel = 'Customer';
                        $contactName  = $customerName ?? 'N/A';
                        $contactPhone = $customerPhone;
                        $contactEmail = $customerEmail ?? 'N/A';
                    } elseif ($driverPhone) {
                        $contactLabel = 'Driver';
                        $contactName  = $driverName ?? 'N/A';
                        $contactPhone = $driverPhone;
                        $contactEmail = 'N/A';
                    } else {
                        $contactLabel = 'N/A';
                        $contactName  = 'N/A';
                        $contactPhone = 'N/A';
                        $contactEmail = 'N/A';
                    }
        
                    $adminMessage =
                        "Hello Admin,\n\n"
                        . "A new *Service Request* has been auto-generated due to *Audit Failure*.\n\n"
                        . "*Primary Contact ({$contactLabel}):*\n"
                        . "• Name: {$contactName}\n"
                        . "• Phone: {$contactPhone}\n"
                        . "• Email: {$contactEmail}\n\n"
                        . "*Vehicle Details:*\n"
                        . "• Vehicle No: {$vehicleNo}\n"
                        . "• Type: {$vehicleType}\n\n"
                        . "*Issue Identified:*\n"
                        . "{$issueDescription}\n\n"
                        . $footerContentText;
        
                    CustomHandler::admin_whatsapp_message($adminMessage);
                }
        
            } catch (\Throwable $e) {
                Log::info("WhatsApp Send Error: " . $e->getMessage());
            }
        }



        private function sendEmail($customer, $vehicle, $ticket_id, $fp): void
        {
            try {
                // ==============================
                // BASIC DETAILS
                // ==============================
                $serviceTicketId = $ticket_id;
        
                // ------------------------------
                // CUSTOMER DETAILS
                // ------------------------------
                $customerName  = $fp->customer_name ?? null;
                $customerEmail = $fp->customer_email ?? null;
                $customerPhone = $fp->customer_number ?? null;
        
                // ------------------------------
                // DRIVER DETAILS
                // ------------------------------
                $driverName  = $fp->driver_name ?? null;
                $driverPhone = $fp->driver_number ?? null;
        
                // ------------------------------
                // VEHICLE DETAILS
                // ------------------------------

                $vehicleNo      = $fp->vehicle_number ?? 'N/A';
                $vehicleType    = $fp->vehicle_type ?? 'N/A';
        
                // ------------------------------
                // ISSUE DESCRIPTION
                // ------------------------------
                $issueDescription = "Vehicle failed audit and requires immediate maintenance. Service request auto-generated.";
        
                // ------------------------------
                // FOOTER
                // ------------------------------
                $footerText = BusinessSetting::where('key_name', 'email_footer')->value('value');
                $footerContentText = $footerText ?: 
                    "For any assistance, please reach out to Admin Support.<br>
                     Email: support@greendrivemobility.com<br>
                     Thank you,<br>GreenDriveConnect Team";
        
                // ==========================================================
                // SEND EMAIL TO CUSTOMER (if available)
                // ==========================================================
                if (!empty($customerEmail)) {
        
                    $customerSubject = "Service Request Confirmation – Ticket #{$serviceTicketId}";
        
                    $customerBody = "
                    <html>
                    <body style='font-family: Arial, sans-serif; background:#f7f7f7; padding:20px;'>
                        <table width='100%' style='max-width:650px; margin:auto; background:#fff; border-radius:8px; overflow:hidden;'>
                            <tr>
                                <td style='background:#4a90e2; color:white; text-align:center; padding:20px;'>
                                    <h2>Service Request Created</h2>
                                    <p>Ticket ID: {$serviceTicketId}</p>
                                </td>
                            </tr>
        
                            <tr>
                                <td style='padding:20px; color:#333;'>
                                    <p>Hello <strong>{$customerName}</strong>,</p>
                                    <p>A service request has been automatically generated due to a failed vehicle audit.</p>
        
                                    <h3>Vehicle Details</h3>
                                    <table width='100%' cellpadding='6' style='border:1px solid #ddd; border-collapse:collapse;'>
                                        <tr><td><strong>Vehicle No</strong></td><td>{$vehicleNo}</td></tr>
                                        <tr><td><strong>Type</strong></td><td>{$vehicleType}</td></tr>
                                    </table>
        
                                    <h3>Reported Issue</h3>
                                    <p>{$issueDescription}</p>
        
                                    <p style='margin-top:20px;'>{$footerContentText}</p>
                                </td>
                            </tr>
        
                            <tr>
                                <td style='background:#eee; text-align:center; padding:10px; font-size:12px;'>
                                    © " . date('Y') . " GreenDriveConnect. All rights reserved.
                                </td>
                            </tr>
                        </table>
                    </body>
                    </html>";
        
                    CustomHandler::sendEmail([$customerEmail], $customerSubject, $customerBody);
                }
        
                // ==========================================================
                // ADMIN EMAIL (Customer → Driver → N/A)
                // // ==========================================================
                $adminEmails = DB::table('users')
                    ->whereIn('role', [1, 13])
                    ->where('status', 'Active')
                    ->pluck('email')
                    ->filter()
                    ->toArray();
                    
        
                if (!empty($adminEmails)) {
        
                    // Decide contact person
                    if (!empty($customerPhone)) {
                        $contactLabel = 'Customer';
                        $contactName  = $customerName;
                        $contactPhone = $customerPhone;
                        $contactEmail = $customerEmail ?? 'N/A';
                    } elseif (!empty($driverPhone)) {
                        $contactLabel = 'Driver';
                        $contactName  = $driverName;
                        $contactPhone = $driverPhone;
                        $contactEmail = 'N/A';
                    } else {
                        $contactLabel = 'N/A';
                        $contactName  = 'N/A';
                        $contactPhone = 'N/A';
                        $contactEmail = 'N/A';
                    }
        
                    $adminSubject = "New Service Request Auto-Generated – Ticket #{$serviceTicketId}";
        
                    $adminBody = "
                    <html>
                    <body style='font-family: Arial, sans-serif; background:#f4f6f8; padding:20px;'>
                        <table width='100%' style='max-width:650px; margin:auto; background:#fff; border-radius:10px; overflow:hidden;'>
                            <tr>
                                <td style='background:#4a90e2; color:white; text-align:center; padding:20px;'>
                                    <h2>New Service Request</h2>
                                    <p>Ticket ID: {$serviceTicketId}</p>
                                </td>
                            </tr>
        
                            <tr>
                                <td style='padding:20px; color:#333;'>
                                    <p>Hello Admin,</p>
                                    <p>A service request has been automatically created due to <strong>audit failure</strong>.</p>
        
                                    <h3>Primary Contact ({$contactLabel})</h3>
                                    <table width='100%' cellpadding='6' style='border:1px solid #ddd; border-collapse:collapse;'>
                                        <tr><td><strong>Name</strong></td><td>{$contactName}</td></tr>
                                        <tr><td><strong>Phone</strong></td><td>{$contactPhone}</td></tr>
                                        <tr><td><strong>Email</strong></td><td>{$contactEmail}</td></tr>
                                    </table>
        
                                    <h3>Vehicle Details</h3>
                                    <table width='100%' cellpadding='6' style='border:1px solid #ddd; border-collapse:collapse;'>
                                        <tr><td><strong>Vehicle No</strong></td><td>{$vehicleNo}</td></tr>
                                        <tr><td><strong>Type</strong></td><td>{$vehicleType}</td></tr>
                                    </table>
        
                                    <h3>Reported Issue</h3>
                                    <p>{$issueDescription}</p>
        
                                    <p style='margin-top:20px;'>{$footerContentText}</p>
                                </td>
                            </tr>
        
                            <tr>
                                <td style='background:#eee; text-align:center; padding:10px; font-size:12px;'>
                                    © " . date('Y') . " GreenDriveConnect. All rights reserved.
                                </td>
                            </tr>
                        </table>
                    </body>
                    </html>";
        
                    CustomHandler::sendEmail($adminEmails, $adminSubject, $adminBody);
                }
        
            } catch (\Throwable $e) {
                Log::info("Email Send Error for Ticket {$ticket_id}: " . $e->getMessage());
            }
        }

    }
    


<?php

require __DIR__ . '/vendor/autoload.php';

// Initialize logging
$logFile = __DIR__ . '/cron_job.log';
$logMessage = function($message, $type = 'INFO') use ($logFile) {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$type] $message" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    echo $logEntry; // Also output to console
};

// Start execution log
$logMessage("Cron job execution started", 'START');

// Bootstrap Laravel
try {
    $logMessage("Bootstrapping Laravel application");
    $app = require_once __DIR__ . '/bootstrap/app.php';
    
    $logMessage("Making kernel instance");
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    $logMessage("Bootstrapping kernel");
    $kernel->bootstrap();
    $logMessage("Laravel bootstrapped successfully");
} catch (Exception $e) {
    $logMessage("Laravel bootstrap failed: " . $e->getMessage(), 'ERROR');
    exit(1);
}

// Now you can use Laravel facades
use Illuminate\Support\Facades\Mail;
use App\Mail\SampleMail;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

function getEnvValue($key)
{
    $envFile = __DIR__ . '/.env'; 

    if (!file_exists($envFile)) {
        throw new Exception('.env file not found');
    }

    $lines = file($envFile);
    foreach ($lines as $line) {
        if (strpos($line, $key) === 0) {
            list($keyName, $value) = explode('=', $line, 2) + [NULL, NULL];
            return trim($value);
        }
    }

    return null;
}

function whatsapp_message($pdo, $id, $mobile_number, $user_type, $logMessage) {
    
    $logMessage("Sending WhatsApp message to $user_type: $mobile_number");
    
    // Find delivery man by ID
    $stmt = $pdo->prepare("SELECT * FROM ev_tbl_delivery_men WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $dm = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$dm) {
        $logMessage("Delivery man not found with ID: $id", 'ERROR');
        return [
            'status' => 'error',
            'message' => 'Delivery man not found with ID: ' . $id
        ];
    }
    
    if($user_type == 'dm'){
       $message = "Dear " . $dm['first_name'] . " " . $dm['last_name'] . ",\n\n" .
                           "We hope this message finds you well.\n\n" .
                           "This is to inform you that your account has been temporarily suspended due to inactivity for 3 consecutive days without clocking in.\n\n" .
                           "To reactivate your account, please contact HR department at your earliest convenience.\n\n" .
                           "Best regards,\n" .
                           "GreenDriveConnect Team\n" ; 
        $phone = str_replace('+', '', $dm['mobile_number']);
    } else {
        $message = "Dear HR Team,\n\n" .
                   "This is to notify you that a delivery partner has been automatically suspended due to inactivity.\n\n" .
                   "Partner Details:\n" .
                   "â€¢ Name: " . $dm['first_name'] . " " . $dm['last_name'] . "\n" .
                   "â€¢ Contact: " . $dm['mobile_number'] . "\n" .
                   "â€¢ Employee ID: " . ($dm['emp_id'] ?? 'N/A') . "\n" .
                   "â€¢ Reason: 3 days of inactivity (no punch records)\n\n" .
                   "Action Required: Please review this case and follow up with the partner.\n\n" .
                   "Best regards,\n" .
                   "GreenDriveConnect System"; 
                           
        $phone = str_replace('+', '', $mobile_number);
    }
    
    $api_key = getEnvValue('WHATSAPP_API_KEY');
    if (!$api_key) {
        $logMessage("WhatsApp API key not found in environment", 'ERROR');
        return [
            'status' => 'error',
            'message' => 'WhatsApp API key not found'
        ];
    }
    
    $postdata = array(
        "contact" => array(
            array(
                "number" => $phone,
                "message" => $message,
            ),
        ),
    );
    
    $logMessage("WhatsApp API payload: " . json_encode($postdata));
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://whatshub.in/api/whatsapp/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postdata),
        CURLOPT_HTTPHEADER => array(
            'Api-key: 08d2b864-aaf7-424d-b9ee-49b3a8648540',
            'Content-Type: application/json',
        ),
    ));
    
    $response = curl_exec($curl);
    
    if (curl_errno($curl)) {
        $error_msg = curl_error($curl);
        curl_close($curl);
        $logMessage("cURL error: $error_msg", 'ERROR');
        return [
            'status' => 'error',
            'message' => 'cURL error: ' . $error_msg
        ];
    }
    
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    $response_data = json_decode($response, true);
    $logMessage("WhatsApp API response: HTTP $http_code - " . json_encode($response_data));
    
    if ($http_code !== 200) {
        $logMessage("API request failed with HTTP code: $http_code", 'ERROR');
        return [
            'status' => 'error',
            'message' => 'API request failed with HTTP code: ' . $http_code,
            'error_details' => $response_data
        ];
    }
    
    if (isset($response_data['status']) && $response_data['status'] != 'success') {
        $logMessage("Failed to send WhatsApp message", 'ERROR');
        return [
            'status' => 'error',
            'message' => 'Failed to send WhatsApp message',
            'error_details' => $response_data
        ];
    }
    
    $logMessage("WhatsApp message sent successfully to $phone");
    return [
        'status' => 'success',
        'message' => 'WhatsApp message sent successfully',
        'data' => $response_data
    ];
}

// Database connection
try {
    $logMessage("Establishing database connection");
    $dbName = getEnvValue('DB_DATABASE');
    $dbUser = getEnvValue('DB_USERNAME');
    $dbPassword = getEnvValue('DB_PASSWORD');
    
    $dsn = "mysql:host=localhost;dbname=" . $dbName;
    $pdo = new PDO($dsn, $dbUser, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $logMessage("Database connected successfully");
} catch (PDOException $e) {
    $logMessage("Database connection failed: " . $e->getMessage(), 'ERROR');
    exit(1);
}

date_default_timezone_set('Asia/Kolkata');
$logMessage("Timezone set to Asia/Kolkata");


// Main logic
$logMessage("Starting to process delivery men for suspension");
$sql = "SELECT a.id,a.first_name,a.last_name,a.mobile_number,a.email,a.emp_id, MAX(b.updated_at) AS punched_out  
        FROM `ev_tbl_delivery_men` AS a 
        LEFT JOIN ev_delivery_man_logs AS b 
        ON a.id = b.user_id 
        WHERE a.work_type NOT IN ('helper') 
        GROUP BY a.id 
        ORDER BY punched_out DESC";

try {
    $stmt = $pdo->query($sql);
    $get_suspended_employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $logMessage("Found " . count($get_suspended_employees) . " delivery men to process");
} catch (PDOException $e) {
    $logMessage("Database query failed: " . $e->getMessage(), 'ERROR');
    exit(1);
}

$suspendedCount = 0;
$processedCount = 0;

foreach ($get_suspended_employees as $employee) {
    $processedCount++;
    $logMessage("Processing employee {$employee['id']}: {$employee['first_name']} {$employee['last_name']} ($processedCount of " . count($get_suspended_employees) . ")");
    
    if (!empty($employee['punched_out'])) {
        // Get approved leaves
        $leaveSql = "SELECT * FROM ev_leave_requests 
                     WHERE dm_id = :dm_id 
                     AND approve_status = 1 
                     AND CURDATE() BETWEEN start_date AND end_date";
        $leaveStmt = $pdo->prepare($leaveSql);
        $leaveStmt->execute(['dm_id' => $employee['id']]);
        $leaves = $leaveStmt->fetchAll(PDO::FETCH_ASSOC);
        $logMessage("Found " . count($leaves) . " approved leaves for employee {$employee['id']}");
        
        // Get holidays
        $holidaySql = "SELECT * FROM ev_master_holidays 
                       WHERE `date` BETWEEN :punched_out and CURDATE()";
        $holidayStmt = $pdo->prepare($holidaySql);  
        $holidayStmt->execute(['punched_out' => date('Y-m-d', strtotime($employee['punched_out']))]);
        $holidays = $holidayStmt->fetchAll(PDO::FETCH_ASSOC);
        $logMessage("Found " . count($holidays) . " holidays since last punch for employee {$employee['id']}");
        
        $punchedOutTimestamp = strtotime($employee['punched_out']);
        $currentTimestamp = time();
        $diffInSeconds = $currentTimestamp - $punchedOutTimestamp;
        $diffInHours = $diffInSeconds / 3600;
        
        $adjustedHours = $diffInHours;
        $leaveHoursToSubtract = 0;
        $holidayHoursToSubtract = 0;
        
        // Calculate leave hours
        foreach ($leaves as $leave) {
            if (!$leave['permission_date']) {
                $leaveHoursToSubtract += 24;
            } else {
                $startTime = strtotime($leave['start_time']);
                $endTime = strtotime($leave['end_time']);
                $leaveHoursToSubtract += ($endTime - $startTime) / 3600;
            }
        }
        
        // Calculate holiday hours
        foreach ($holidays as $holiday) {
            $holidayHoursToSubtract += 24;
        }
        
        // Subtract leave and holiday hours
        $adjustedHours = $diffInHours - $leaveHoursToSubtract - $holidayHoursToSubtract;
        
        $logMessage("Employee {$employee['id']} - Original hours: " . round($diffInHours, 2) . 
                   ", Leave adjustment: " . round($leaveHoursToSubtract, 2) . 
                   ", Holiday adjustment: " . round($holidayHoursToSubtract, 2) . 
                   ", Adjusted hours: " . round($adjustedHours, 2));
        
        if ($adjustedHours >= 72) {
            $updateSql = "UPDATE `ev_tbl_delivery_men` SET rider_status = 0 WHERE id = :id AND rider_status = 1";
            $updateStmt = $pdo->prepare($updateSql);
            $response = $updateStmt->execute(['id' => $employee['id']]);
            $rowsAffected = $updateStmt->rowCount();
            
            if ($rowsAffected > 0) {
                $suspendedCount++;
                $logMessage("SUCCESS: Suspended employee {$employee['id']} - {$employee['first_name']} {$employee['last_name']}");
                
                // Send WhatsApp to driver
                $whatsappResponse = whatsapp_message($pdo, $employee['id'], $employee['mobile_number'], "dm", $logMessage);
                if ($whatsappResponse['status'] === 'error') {
                    $logMessage("WhatsApp to driver failed: " . $whatsappResponse['message'], 'ERROR');
                }
                
                // Send email to driver
                if (!empty($employee['email'])) {
                    try {
                        $data = [
                            'subject' => 'Account Suspension Notification - GreenDriveConnect',
                            'employee' => [
                                'first_name' => $employee['first_name'],
                                'last_name' => $employee['last_name'],
                                'email' => $employee['email'],
                                'mobile_number' => $employee['mobile_number'],
                                'emp_id' => $employee['emp_id']
                            ],
                            'userType' => 'dm'
                        ];
                        
                        Mail::to($data['employee']['email'])->send(new SampleMail($data));
                        $logMessage("Email sent successfully to driver: " . $employee['email']);
                    } catch (Exception $e) {
                        $logMessage("Email to driver failed: " . $e->getMessage(), 'ERROR');
                    }
                }
                
                // Notify HR team
                $Hrsql = "SELECT id,phone,email FROM users WHERE role = 4";
                $HrData = $pdo->prepare($Hrsql);
                $HrData->execute();
                $HrResponse = $HrData->fetchAll(PDO::FETCH_ASSOC);
                
                if ($HrResponse) {
                    foreach ($HrResponse as $hr) {
                        // WhatsApp to HR
                        $hrWhatsappResponse = whatsapp_message($pdo, $employee['id'], $hr['phone'], "hr", $logMessage);
                        if ($hrWhatsappResponse['status'] === 'error') {
                            $logMessage("WhatsApp to HR {$hr['id']} failed: " . $hrWhatsappResponse['message'], 'ERROR');
                        }
                        
                        // Email to HR
                        if (!empty($hr['email'])) {
                            try {
                                $data = [
                                    'subject' => "Driver Suspension: " . $employee['first_name'] . " " . $employee['last_name'],
                                    'employee' => [
                                        'first_name' => $employee['first_name'],
                                        'last_name' => $employee['last_name'],
                                        'email' => $employee['email'],
                                        'mobile_number' => $employee['mobile_number'],
                                        'emp_id' => $employee['emp_id']
                                    ],
                                    'userType' => 'hr'
                                ];
                                
                                Mail::to($hr['email'])->send(new SampleMail($data));
                                $logMessage("Email sent successfully to HR: " . $hr['email']);
                            } catch (Exception $e) {
                                $logMessage("Email to HR {$hr['id']} failed: " . $e->getMessage(), 'ERROR');
                            }
                        }
                    }
                }
            } else {
                $logMessage("Employee {$employee['id']} was already suspended or not found");
            }
        } else {
            $logMessage("Employee {$employee['id']} does not meet suspension criteria (adjusted hours: " . round($adjustedHours, 2) . ")");
        }
    } else {
        $logMessage("Employee {$employee['id']} has no punch-out record");
    }
}

// Final summary
$logMessage("Cron job completed. Processed: $processedCount, Suspended: $suspendedCount", 'SUMMARY');
$logMessage("Cron job execution finished", 'END');

?>
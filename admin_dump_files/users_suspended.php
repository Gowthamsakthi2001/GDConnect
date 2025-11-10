<?php

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
// Usage
$dbName = getEnvValue('DB_DATABASE');
$dbUser = getEnvValue('DB_USERNAME');
$dbPassword = getEnvValue('DB_PASSWORD');

try {
    $dsn = "mysql:host=localhost;dbname=" . $dbName;
    $pdo = new PDO($dsn, $dbUser, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


date_default_timezone_set('Asia/Kolkata'); //note : custom chnage for time based on server



try {
    $currentDateTime = new DateTime(); // Get the current time
    $sql = "SELECT * FROM `ev_tbl_delivery_men` WHERE work_type = 'adhoc'"; //adhoc rider_status update 
    $stmt = $pdo->query($sql);
    $get_suspended_adhocs = $stmt->fetchAll();
    foreach ($get_suspended_adhocs as $adhoc) {
        if (!empty($adhoc['active_date'])) { 
            $activeDateTime = new DateTime($adhoc['active_date']); // Example: 2025-03-15 06:00:00 AM
            $after_24_hrComplete = clone $activeDateTime; // Clone to avoid modifying original object
            $after_24_hrComplete->modify('+24 hours');  // Now: 2025-03-15 06:00:00 PM
            
            if ($currentDateTime > $after_24_hrComplete) {
                $updateSql = "UPDATE `ev_tbl_delivery_men` SET rider_status = 0 WHERE id = :id";
                $stmt2 = $pdo->prepare($updateSql);
                $stmt2->execute(['id' => $adhoc['id']]);

                echo "Updated rider_status for user ID: " . $adhoc['id'] . " (active_date: " . $adhoc['active_date'] . ")<br>";
            }
        }
    }

    echo "Update process completed successfully.";

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


// Fetch non-adhoc employees and their latest punch-in time

// $sql = "SELECT a.id, MAX(b.punched_in) AS punched_in  
//         FROM `ev_tbl_delivery_men` AS a 
//         LEFT JOIN ev_delivery_man_logs AS b 
//         ON a.id = b.user_id 
//         WHERE a.work_type IN ('in-house', 'deliveryman') 
//         GROUP BY a.id 
//         ORDER BY punched_in DESC";

// $stmt = $pdo->query($sql);
// $get_suspended_employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// foreach ($get_suspended_employees as $employee) {
//     if (!empty($employee['punched_in'])) {
//         $punchedInDate = new DateTime($employee['punched_in']);
//         $currentDate = new DateTime();
//         $daysSinceLastPunch = $punchedInDate->diff($currentDate)->days;

//         if ($daysSinceLastPunch >= 3) {
//             // Update rider status to 0 if last punch-in was 3+ days ago
//             $updateSql = "UPDATE `ev_tbl_delivery_men` SET rider_status = 0 WHERE id = :id";
//             $stmt2 = $pdo->prepare($updateSql);
//             $stmt2->execute(['id' => $employee['id']]);

//             echo "Rider status updated for user ID: " . $employee['id'] . "<br>";
//         }
//     }
// }

?>







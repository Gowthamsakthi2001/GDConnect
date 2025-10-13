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


//  $sql = "SELECT a.*, MAX(b.punched_in) AS punched_in 
//             FROM `ev_tbl_delivery_men` AS a 
//             LEFT JOIN ev_delivery_man_logs AS b 
//             ON a.id = b.user_id 
//             WHERE a.work_type = 'adhoc' 
//             GROUP BY a.id 
//             ORDER BY punched_in DESC";

//     // Execute query
//     $stmt = $pdo->query($sql);
//     $get_suspended_adhocs = $stmt->fetchAll();

//     foreach ($get_suspended_adhocs as $adhoc) {
//         // Get latest record
//         $sql = "SELECT * FROM `ev_tbl_delivery_men` WHERE id = :id";
//         $stmt1 = $pdo->prepare($sql);
//         $stmt1->execute(['id' => $adhoc['id']]);
//         $is_adhoc = $stmt1->fetch(PDO::FETCH_ASSOC);

//         if (!empty($is_adhoc) && !empty($adhoc['punched_in'])) {
//             $punchedInDate = new DateTime($adhoc['punched_in']);
//             $currentDate = new DateTime();
//             $daysSinceLastPunch = $currentDate->diff($punchedInDate)->days;

//             if ($daysSinceLastPunch >= 3) {
//                 // Update the rider status
//                 $updateSql = "UPDATE `ev_tbl_delivery_men` SET rider_status = 0 WHERE id = :id";
//                 $stmt2 = $pdo->prepare($updateSql);
//                 $stmt2->execute(['id' => $adhoc['id']]);
//             }
//         }
//     }



// try {
//     $currentDateTime = new DateTime();
//     $sql = "SELECT * FROM `ev_tbl_delivery_men` WHERE work_type = 'adhoc'"; //adhoc rider status update
//     $stmt = $pdo->query($sql);
//     $get_suspended_adhocs = $stmt->fetchAll();
//     foreach ($get_suspended_adhocs as $adhoc) {
//         if (!empty($adhoc['active_date'])) { 
//             $activeDateTime = new DateTime($adhoc['active_date']);
//             if ($currentDateTime > $activeDateTime) {
//                 $updateSql = "UPDATE `ev_tbl_delivery_men` SET rider_status = 0 WHERE id = :id";
//                 $stmt2 = $pdo->prepare($updateSql);
//                 $stmt2->execute(['id' => $adhoc['id']]);

//                 echo "Updated rider_status for user ID: " . $adhoc['id'] . " (active_date: " . $adhoc['active_date'] . ")<br>";
//             }
//         }
//     }

//     echo "Update process completed successfully.";

// } catch (PDOException $e) {
//     echo "Connection failed: " . $e->getMessage();
// }


// Fetch latest punched_in time for adhoc
// $sql = "SELECT a.*, MAX(b.punched_in) AS punched_in  
//         FROM `ev_tbl_delivery_men` AS a 
//         LEFT JOIN ev_delivery_man_logs AS b 
//         ON a.id = b.user_id 
//         WHERE a.work_type = 'adhoc' 
//         GROUP BY a.id";

// $stmt = $pdo->query($sql);
// $get_auto_punchout_adhocs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// if (!empty($get_auto_punchout_adhocs)) {
//     foreach ($get_auto_punchout_adhocs as $adhoc) {
//         if (!empty($adhoc['active_date'])) {
//             $activeDate = new DateTime($adhoc['active_date']);
//             $currentDate = new DateTime();
            
//             // Calculate punch-out time (24 hours after active_date)
//             $punchoutDate = clone $activeDate;
//             $punchoutDate->modify('+24 hours');

//             // Check if user has not punched out and 24 hours have passed from active_date
//             $checkSql = "SELECT id FROM `ev_delivery_man_logs` 
//                          WHERE user_id = :id AND punched_out IS NULL 
//                          ORDER BY punched_in DESC LIMIT 1";
//             $stmt2 = $pdo->prepare($checkSql);
//             $stmt2->execute(['id' => $adhoc['id']]);
//             $logEntry = $stmt2->fetch(PDO::FETCH_ASSOC);

//             if ($logEntry && $currentDate > $punchoutDate) { 
//                 // Update punch-out time
//                 $updateSql = "UPDATE ev_delivery_man_logs 
//                               SET punched_out = :punched_out 
//                               WHERE id = :log_id";
//                 $stmt3 = $pdo->prepare($updateSql);
//                 $stmt3->execute([
//                     'punched_out' => $punchoutDate->format('Y-m-d H:i:s'),
//                     'log_id' => $logEntry['id']
//                 ]);

//                 echo "Punch-out updated for User ID: " . $adhoc['id'] . " | Punched Out: " . $punchoutDate->format('Y-m-d H:i:s') . "<br>";
//             }
//         }
//     }
// }


// Fetch the probation period candidates
$sql = "SELECT * FROM `ev_tbl_delivery_men` 
        WHERE kyc_verify = 1 
          AND rider_status = 3 
          AND probation_from_date IS NOT NULL";

$stmt = $pdo->query($sql);
$get_auto_live_move_candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($get_auto_live_move_candidates)) {
    foreach ($get_auto_live_move_candidates as $candidate) {
        $from_date = new DateTime($candidate['probation_from_date']);
        $currentDate = new DateTime();

        // Calculate the difference in days
        $interval = $from_date->diff($currentDate);
        $different_days = $interval->days;

        // If more than 6 days have passed, update status to live
        if ($different_days > 6) {
            $updateSql = "UPDATE ev_tbl_delivery_men
                          SET rider_status = :rider_status
                          WHERE id = :id";

            $stmt3 = $pdo->prepare($updateSql);
            $stmt3->execute([
                ':rider_status' => 1, // Assuming 1 is for "live"
                ':id' => $candidate['id']
            ]);
        }
    }
}


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







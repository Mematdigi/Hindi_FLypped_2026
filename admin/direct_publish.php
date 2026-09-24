<?php
// ==================== DIRECT PUBLISH FOR DO SERVER ====================

// DO DATABASE CREDENTIALS
$host = 'localhost';
$db   = 'hindi_flypped_production';  // ✅ Correct DO database
$user = 'hindi_user';                 // ✅ Correct DO user
$pass = 'HindiFlypped@2025';         // ✅ Correct DO password

// Set PHP timezone to IST
date_default_timezone_set('Asia/Kolkata');

$currentIST = date('Y-m-d H:i:s');    // Current IST
$currentUTC = gmdate('Y-m-d H:i:s');  // Current UTC

// Connect to database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Log file path
$logFile = '/var/www/html/hindi.flypped.com/admin/writable/logs/cron_publish.log';

// Create log directory if doesn't exist
$logDir = dirname($logFile);
if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}

// Log start
file_put_contents($logFile, "=== Cron started: IST=$currentIST | UTC=$currentUTC ===\n", FILE_APPEND);

// SQL to publish scheduled posts
$sql = "UPDATE wp_posts 
        SET post_status = 'publish',
            post_modified = ?,
            post_modified_gmt = ?
        WHERE post_status = 'future'
          AND post_type = 'post'
          AND post_date <= ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('sss', $currentIST, $currentUTC, $currentIST);

if ($stmt->execute()) {
    $affected = $stmt->affected_rows;
    $message = "$currentIST - Published $affected post(s)\n";
    echo $message;
    file_put_contents($logFile, $message, FILE_APPEND);
} else {
    $errorMsg = "Error: " . $stmt->error . "\n";
    echo $errorMsg;
    file_put_contents($logFile, $errorMsg, FILE_APPEND);
}

$stmt->close();
$conn->close();
?>
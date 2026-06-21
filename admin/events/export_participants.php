<?php
session_start();
require_once __DIR__ . '/../../config.php';

// 1. Security: Admin Only
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    die("Access Denied.");
}

// 2. Get Event ID
if (!isset($_GET['event_id'])) {
    die("No event selected.");
}
$event_id = intval($_GET['event_id']);

// 3. Fetch Event Name (for filename)
$e_sql = "SELECT event_name FROM events WHERE event_id = $event_id";
$e_res = $conn->query($e_sql);
if ($e_res->num_rows == 0) die("Event not found.");
$event_name = $e_res->fetch_assoc()['event_name'];
$filename = "Participants_" . preg_replace('/[^A-Za-z0-9\-]/', '_', $event_name) . "_" . date('Y-m-d') . ".csv";

// 4. Fetch Participants Data
$sql = "SELECT u.name, u.email, u.phone, u.organization, r.status, r.registration_date 
        FROM registrations r 
        JOIN users u ON r.user_id = u.user_id 
        WHERE r.event_id = $event_id";
$result = $conn->query($sql);

// 5. Send Headers to Force Download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '";');

// 6. Generate CSV Content
$output = fopen('php://output', 'w');

// Add Column Headers
fputcsv($output, ['Full Name', 'Email Address', 'Phone', 'Organization', 'Status', 'Registration Date']);

// Add Data Rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>
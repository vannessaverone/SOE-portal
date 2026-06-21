<?php
session_start();
require_once __DIR__ . '/../config.php';

// Restricted to Admin role only 
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    die("Unauthorized access.");
}

$report_type = $_GET['type'] ?? 'category';
$filename = "SOE_Report_" . date('Y-m-d') . ".csv";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '";');

$output = fopen('php://output', 'w');

if ($report_type === 'category') {
    // Generate Report by Category 
    fputcsv($output, ['Category Name', 'Total Events', 'Total Participants']);
    
    $sql = "SELECT c.categoryName, COUNT(e.event_id) as event_count, COUNT(r.registration_id) as participant_count
            FROM event_category c
            LEFT JOIN events e ON c.category_id = e.category_id
            LEFT JOIN registrations r ON e.event_id = r.event_id
            GROUP BY c.categoryName";
} else {
    // Generate Monthly Report 
    fputcsv($output, ['Month', 'Total Events Created']);
    
    $sql = "SELECT DATE_FORMAT(event_date, '%M %Y') as month, COUNT(event_id) as total 
            FROM events 
            GROUP BY month 
            ORDER BY event_date DESC";
}

$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>
<?php
session_start();
require_once __DIR__ . '/../../config.php';

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    exit("Unauthorized");
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    // Toggle highlight status
    if ($action === 'highlight') {
        $stmt = $conn->prepare("UPDATE events SET is_highlighted = NOT is_highlighted WHERE event_id = ?");
        $stmt->bind_param("i", $id);
    } 
    // Update moderation status
    elseif (in_array($action, ['Approved', 'Archived', 'Pending'])) {
        $stmt = $conn->prepare("UPDATE events SET admin_status = ? WHERE event_id = ?");
        $stmt->bind_param("si", $action, $id);
    }

    if (isset($stmt) && $stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: manage.php?msg=Success");
        exit();
    }
}
$conn->close();
?>
<?php
session_start();
require_once __DIR__ . '/../../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    exit("Unauthorized");
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $new_status = $_GET['status']; // 'Active' or 'Suspended'

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ? AND role != 'Admin'");
    $stmt->bind_param("si", $new_status, $id);
    
    if ($stmt->execute()) {
        header("Location: users.php?msg=StatusUpdated");
        exit();
    }
}
?>
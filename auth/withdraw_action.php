<?php
session_start();
require_once __DIR__ . '/../config.php';

// 1. Authentication Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $registration_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // 2. Security: Ensure the registration belongs to the logged-in user
    // This prevents users from deleting other people's registrations via URL tampering
    $sql = "DELETE FROM registrations WHERE registration_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $registration_id, $user_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Success: Record was found and deleted
            echo "<script>alert('You have successfully withdrawn from the event.'); window.location='my_registrations.php';</script>";
        } else {
            // Failure: No record matched (unauthorized or already deleted)
            echo "<script>alert('Error: You do not have permission to perform this action.'); window.location='my_registrations.php';</script>";
        }
    } else {
        echo "Database Error: " . $conn->error;
    }

    $stmt->close();
} else {
    header("Location: my_registrations.php");
}

$conn->close();
?>
<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) exit();
$user_id = $_SESSION['user_id'];

// Handle Personal Detail Updates
if (isset($_POST['update_details'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $org = $_POST['organization'];

    $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, organization = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $name, $phone, $org, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['user_name'] = $name; // Update active session name
        echo "<script>alert('Details updated!'); window.location='../profile.php';</script>";
    }
}

// Handle Password Updates
if (isset($_POST['update_password'])) {
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass === $confirm_pass) {
        $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Password changed successfully!'); window.location='../profile.php';</script>";
        }
    } else {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
    }
}
$conn->close();
?>
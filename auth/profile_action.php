<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $organization = trim($_POST['organization']);
    $password = $_POST['password'];

    // 1. Basic Update Query
    $sql = "UPDATE users SET name=?, email=?, phone=?, organization=? WHERE user_id=?";
    
    // 2. Check if password needs updating
    if (!empty($password)) {
        // Hash the new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET name=?, email=?, phone=?, organization=?, password=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $email, $phone, $organization, $hashed_password, $user_id);
    } else {
        // Update without changing password
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $email, $phone, $organization, $user_id);
    }

    if ($stmt->execute()) {
        // Update session name in case it changed
        $_SESSION['user_name'] = $name;
        echo "<script>alert('Profile updated successfully!'); window.location='../profile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile.'); window.location='../profile.php';</script>";
    }
    $stmt->close();
}
$conn->close();
?>
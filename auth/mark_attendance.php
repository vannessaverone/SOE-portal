<?php
session_start();
require_once __DIR__ . '/../config.php';

// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? '';

if (!isset($_GET['reg_id'], $_GET['status'], $_GET['event_id'])) {
    header("Location: ../index.php");
    exit();
}

$reg_id = intval($_GET['reg_id']);
$status = intval($_GET['status']); // 1 = Present, 0 = Absent
$event_id = intval($_GET['event_id']);

// status must be 0 or 1 only
if (!in_array($status, [0, 1], true)) {
    die("<script>alert('Invalid attendance value.'); window.location='manage_participants.php?event_id=$event_id';</script>");
}

//Check permission (Admin OR event creator)
$owner_sql = "SELECT created_by FROM events WHERE event_id = ?";
$owner_stmt = $conn->prepare($owner_sql);
$owner_stmt->bind_param("i", $event_id);
$owner_stmt->execute();
$owner = $owner_stmt->get_result()->fetch_assoc();
$owner_stmt->close();

if (!$owner) {
    die("<script>alert('Event not found.'); window.location='../index.php';</script>");
}

if ($user_role !== 'Admin' && intval($owner['created_by']) !== intval($user_id)) {
    die("<script>alert('Unauthorized action.'); window.location='../index.php';</script>");
}

// Only allow attendance update if participant is Approved
$check_sql = "SELECT status FROM registrations WHERE registration_id = ? AND event_id = ?";
$stmtCheck = $conn->prepare($check_sql);
$stmtCheck->bind_param("ii", $reg_id, $event_id);
$stmtCheck->execute();
$reg = $stmtCheck->get_result()->fetch_assoc();
$stmtCheck->close();

if (!$reg) {
    die("<script>alert('Registration not found.'); window.location='manage_participants.php?event_id=$event_id';</script>");
}

if ($reg['status'] !== 'Approved') {
    die("<script>alert('Only approved participants can be marked for attendance.'); window.location='manage_participants.php?event_id=$event_id';</script>");
}

// Update attendance (ensure correct event)
$sql = "UPDATE registrations SET attendance = ? WHERE registration_id = ? AND event_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $status, $reg_id, $event_id);

if ($stmt->execute()) {

    // Notification
$info_sql = "SELECT r.user_id, e.event_name
             FROM registrations r
             JOIN events e ON r.event_id = e.event_id
             WHERE r.registration_id = ? AND r.event_id = ?";
$info_stmt = $conn->prepare($info_sql);
$info_stmt->bind_param("ii", $reg_id, $event_id);
$info_stmt->execute();
$info = $info_stmt->get_result()->fetch_assoc();
$info_stmt->close();

    if ($info) {
        $participant_id = $info['user_id'];
        $event_name = $info['event_name'];
        $attText = ($status == 1) ? "Present" : "Absent";
        $msg = "Attendance has been marked as $attText for event '$event_name'.";
        $notif_sql = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
        $notif_stmt = $conn->prepare($notif_sql);
        $notif_stmt->bind_param("is", $participant_id, $msg);
        $notif_stmt->execute();
        $notif_stmt->close();
    }
header("Location: manage_participants.php?event_id=" . $event_id);
exit();
} else {
    die("<script>alert('Error updating attendance: " . addslashes($stmt->error) . "'); window.location='manage_participants.php?event_id=$event_id';</script>");
}

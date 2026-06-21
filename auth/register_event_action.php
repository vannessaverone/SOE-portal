<?php
session_start();
require_once __DIR__ . '/../config.php';

// Must logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Must have event ID
if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$event_id = intval($_GET['id']);
$user_id  = $_SESSION['user_id'];

// Fetch event info (limit + close date)
$sql_event = "SELECT event_id, max_participants, registration_close_date 
              FROM events WHERE event_id = ?";
$stmt_event = $conn->prepare($sql_event);
$stmt_event->bind_param("i", $event_id);
$stmt_event->execute();
$event = $stmt_event->get_result()->fetch_assoc();
$stmt_event->close();

if (!$event) {
    echo "<script>alert('Event not found.'); window.location='../index.php';</script>";
    exit();
}

// Check registration close date
if (!empty($event['registration_close_date'])) {
    $today = date("Y-m-d");
    if ($today > $event['registration_close_date']) {
        echo "<script>alert('Registration for this event is closed.'); window.location='../index.php';</script>";
        exit();
    }
}

// Prevent duplicate registration
$check_sql = "SELECT registration_id FROM registrations WHERE event_id = ? AND user_id = ?";
$stmt_check = $conn->prepare($check_sql);
$stmt_check->bind_param("ii", $event_id, $user_id);
$stmt_check->execute();
$res_check = $stmt_check->get_result();

if ($res_check->num_rows > 0) {
    echo "<script>alert('You are already registered for this event!'); window.location='my_registrations.php';</script>";
    exit();
}
$stmt_check->close();

// Check event capacity (exclude 0 = unlimited)
$count_sql = "SELECT COUNT(*) AS current_count FROM registrations WHERE event_id = ?";
$stmt_count = $conn->prepare($count_sql);
$stmt_count->bind_param("i", $event_id);
$stmt_count->execute();
$current = $stmt_count->get_result()->fetch_assoc();
$stmt_count->close();

if ($event['max_participants'] > 0 && $current['current_count'] >= $event['max_participants']) {
    echo "<script>alert('Sorry, this event is full!'); window.location='../index.php';</script>";
    exit();
}

// Insert registration
$sql = "INSERT INTO registrations (event_id, user_id, registration_date) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $event_id, $user_id);

if ($stmt->execute()) {

    // Insert notification 
    $event_name = "this event";
    $name_sql = "SELECT event_name FROM events WHERE event_id = ?";
    $name_stmt = $conn->prepare($name_sql);
    $name_stmt->bind_param("i", $event_id);
    $name_stmt->execute();
    $name_result = $name_stmt->get_result()->fetch_assoc();
    if ($name_result) {
        $event_name = $name_result['event_name'];
    }
    $name_stmt->close();

    $msg = " You successfully registered for the event '$event_name'.";

    $notif_sql = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
    $notif_stmt = $conn->prepare($notif_sql);
    $notif_stmt->bind_param("is", $user_id, $msg);
    $notif_stmt->execute();
    $notif_stmt->close();

    echo "<script>alert('Successfully registered!'); window.location='my_registrations.php';</script>";

} else {
    echo "<script>alert('Error registering: " . addslashes($stmt->error) . "'); window.location='../index.php';</script>";
}


$stmt->close();
$conn->close();
?>

<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $event_id = intval($_POST['event_id']);
    $user_id  = $_SESSION['user_id'];
    $rating   = intval($_POST['rating']);
    if ($rating < 1 || $rating > 5) {
    die("<script>alert('Invalid rating.'); window.location='my_registrations.php';</script>"); }
    $comments = trim($_POST['comments']);

    // prevent duplicate feedback
    $dup = $conn->prepare("SELECT feedback_id FROM feedback WHERE event_id=? AND user_id=?");
    $dup->bind_param("ii", $event_id, $user_id);
    $dup->execute();
    if ($dup->get_result()->num_rows > 0) {
        echo "<script>alert('You already submitted feedback for this event.'); window.location='my_registrations.php';</script>";
        exit();
    }
    $dup->close();

    // insert feedback
    $sql = "INSERT INTO feedback (event_id, user_id, rating, comments) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $event_id, $user_id, $rating, $comments);

    if ($stmt->execute()) {

        // fetch event name 
        $ename = "Event";
        $stmtE = $conn->prepare("SELECT event_name FROM events WHERE event_id=?");
        $stmtE->bind_param("i", $event_id);
        $stmtE->execute();
        $r = $stmtE->get_result()->fetch_assoc();
        if ($r) $ename = $r['event_name'];
        $stmtE->close();

        //  notify organizer
        $notif_msg = "New feedback received for your event: " . $ename;
        $sql_notif = "INSERT INTO notifications (user_id, message)
                      SELECT created_by, ? FROM events WHERE event_id = ?";
        $stmt_notif = $conn->prepare($sql_notif);
        $stmt_notif->bind_param("si", $notif_msg, $event_id);
        $stmt_notif->execute();
        $stmt_notif->close();

        echo "<script>alert('Thank you for your feedback!'); window.location='my_registrations.php';</script>";
        exit();

    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

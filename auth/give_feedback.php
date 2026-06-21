<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['event_id'])) {
    header("Location: my_registrations.php");
    exit();
}

$event_id = intval($_GET['event_id']);
$user_id = $_SESSION['user_id'];

// Verify attendance before allowing feedback
$check = $conn->prepare("SELECT attendance FROM registrations WHERE event_id = ? AND user_id = ?");
$check->bind_param("ii", $event_id, $user_id);
$check->execute();
if ($check->get_result()->fetch_assoc()['attendance'] != 1) {
    die("You must be marked as present to give feedback.");
}

$dup = $conn->prepare("SELECT feedback_id FROM feedback WHERE event_id=? AND user_id=?");
$dup->bind_param("ii", $event_id, $user_id);
$dup->execute();
if ($dup->get_result()->num_rows > 0) {
    die("<script>alert('You already submitted feedback for this event.'); window.location='my_registrations.php';</script>");
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Give Feedback - SOE</title>
    <link rel="stylesheet" href="<?= BASE_PATH_CSS ?>style.css">
</head>
<body>
    <?php include '../include/topNav.php'; ?>
    <main class="section-content">
        <h3>Rate Your Experience</h3>
        <form action="submit_feedback.php" method="POST">
            <input type="hidden" name="event_id" value="<?= $event_id ?>">
            
            <label>Rating:</label><br>
            <select name="rating" required style="width: 100%; padding: 10px; margin-bottom: 20px;">
                <option value="5">5 - Excellent</option>
                <option value="4">4 - Very Good</option>
                <option value="3">3 - Good</option>
                <option value="2">2 - Fair</option>
                <option value="1">1 - Poor</option>
            </select>

            <label>Comments:</label><br>
            <textarea name="comments" rows="5" placeholder="Share your thoughts..." required style="width: 100%; padding: 10px;"></textarea>

            <button type="submit" class="btn" style="margin-top: 20px;">Submit Feedback</button>
        </form>
    </main>
</body>
</html>
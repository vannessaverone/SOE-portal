<?php
session_start();
require_once __DIR__ . '/../../config.php';

// Role Check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

$message_status = "";

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_id = $_POST['event_id'];
    $title = $_POST['title'];
    $announcement_text = $_POST['message'];
    $admin_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO announcements (event_id, admin_id, title, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $event_id, $admin_id, $title, $announcement_text);
    
    if ($stmt->execute()) {
        $message_status = "<p style='color: green; font-weight: bold;'>Announcement posted successfully!</p>";
    } else {
        $message_status = "<p style='color: red;'>Error: Could not post announcement.</p>";
    }
}

// Fetch events for the dropdown
$events_result = $conn->query("SELECT event_id, event_name FROM events ORDER BY event_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<?php include(ROOT_PATH_ADMIN . 'include/head.php'); ?>
<body style="background-color: #f4f7f6;">
    <?php include(ROOT_PATH_ADMIN . 'include/header.php'); ?>
    
    <div class="admin-container">
    <?php include(ROOT_PATH_ADMIN . 'include/sidebar.php'); ?>

    <main class="main-content">
        <h2 style="color: #001a2c;">Post Event Announcement</h2>
        
        <div style="background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-top: 20px; max-width: 700px;">
            <?= $message_status ?>
            
            <form action="post_announcement.php" method="POST">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #001a2c; font-weight: bold;">Select Event</label>
                    <select name="event_id" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px;">
                        <option value="">-- Choose an Event --</option>
                        <?php while($row = $events_result->fetch_assoc()): ?>
                            <option value="<?= $row['event_id'] ?>"><?= htmlspecialchars($row['event_name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #001a2c; font-weight: bold;">Subject / Title</label>
                    <input type="text" name="title" placeholder="e.g., Change of Venue" required 
                           style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #001a2c; font-weight: bold;">Message Content</label>
                    <textarea name="message" rows="6" placeholder="Write your announcement here..." required 
                              style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-family: inherit;"></textarea>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn" style="background: #4fd99d; color: #001a2c; border: none; padding: 12px 25px; border-radius: 5px; font-weight: bold; cursor: pointer;">
                        <i class="fas fa-paper-plane"></i> Send Announcement
                    </button>
                    <a href="index.php" style="padding: 12px 25px; color: #555; text-decoration: none;">Cancel</a>
                </div>
            </form>
        </div>
    </main>
    </div>

    <?php include(ROOT_PATH_ADMIN ."include/footer.php"); ?> 
</body>
</html>
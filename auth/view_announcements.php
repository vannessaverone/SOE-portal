<?php
session_start();
require_once __DIR__ . '/../config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

// Fetch announcements for this specific event
$sql = "SELECT a.*, e.event_name 
        FROM announcements a 
        JOIN events e ON a.event_id = e.event_id 
        WHERE a.event_id = ? 
        ORDER BY a.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch event name for the header if result is empty
if ($result->num_rows == 0) {
    $event_query = $conn->query("SELECT event_name FROM events WHERE event_id = $event_id");
    $event_info = $event_query->fetch_assoc();
    $display_name = $event_info['event_name'] ?? "Event";
} else {
    // Peek at the first row to get the name
    $first_row = $result->fetch_assoc();
    $display_name = $first_row['event_name'];
    $result->data_seek(0); // Reset pointer for the loop
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Announcements - <?= htmlspecialchars($display_name) ?></title>
    <link rel="stylesheet" href="<?= BASE_PATH_CSS ?>style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Specific styles for the announcement "cards" using your colors */
        .announcement-item {
            background: #001a2c; /* Logo Navy */
            border: 1px solid #2c8ca0; /* Logo Teal */
            border-left: 5px solid #4fd99d; /* Accent color */
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: left;
        }
        .announcement-item h4 {
            color: #4fd99d;
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        .announcement-item p {
            line-height: 1.6;
            color: #e0e0e0;
        }
        .time-stamp {
            font-size: 0.8rem;
            color: #2c8ca0;
            margin-bottom: 10px;
            display: block;
        }
        .no-data {
            padding: 40px;
            color: #888;
        }
    </style>
</head>
<body>

    <?php include '../include/topNav.php'; ?>
    <?php include '../include/header.php'; ?>

    <main>
        <section class="listing">
            <h3>Announcements for: <br><span style="color: #fff;"><?= htmlspecialchars($display_name) ?></span></h3>
            
            <div class="section-content" style="max-width: 800px; background: transparent; box-shadow: none; border: none;">
                
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="announcement-item">
                            <span class="time-stamp">
                                <i class="fas fa-clock"></i> <?= date('F d, Y | h:i A', strtotime($row['created_at'])) ?>
                            </span>
                            <h4><?= htmlspecialchars($row['title']) ?></h4>
                            <p><?= nl2br(htmlspecialchars($row['message'])) ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-comment-slash fa-3x"></i>
                        <p style="margin-top:15px;">No announcements have been made for this event yet.</p>
                    </div>
                <?php endif; ?>

                <div class="filter-container">
                    <a href="my_registrations.php" class="btn">
                        <i class="fas fa-arrow-left"></i> Back to My Events
                    </a>
                </div>
            </div>
        </section>
    </main>

    <?php include '../include/footer.php'; ?>
</body>
</html>
<?php
require_once __DIR__ . '/../../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit();
}
// Read session (then unlock session to prevent lag/session lock issues)
$user_id   = (int)($_SESSION['user_id'] ?? 0);
$user_role = $_SESSION['user_role'] ?? '';
session_write_close();
// Validate event_id
$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;
if ($event_id <= 0) {
    die("Invalid event ID.");
}

// Fetch event 
$stmt = $conn->prepare("SELECT event_id, event_name, created_by FROM events WHERE event_id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$event) {
    die("Event not found.");
}

$created_by = (int)$event['created_by'];

// Permission: Admin OR creator of the event
if ($user_role !== 'Admin' && $created_by !== $user_id) {
    die("Unauthorized access.");
}

// Fetch feedback + user
$stmt = $conn->prepare("
    SELECT f.feedback_id, f.rating, f.comments AS comment, f.submitted_at AS created_at,
    u.name AS user_name
    FROM feedback f
    LEFT JOIN users u ON f.user_id = u.user_id
    WHERE f.event_id = ?
    ORDER BY f.submitted_at DESC
");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$feedback_result = $stmt->get_result();


// Stats
$stmt2 = $conn->prepare("
    SELECT COUNT(*) AS total_feedback,
           AVG(rating) AS avg_rating
    FROM feedback
    WHERE event_id = ?
");
$stmt2->bind_param("i", $event_id);
$stmt2->execute();
$stats = $stmt2->get_result()->fetch_assoc();
$stmt2->close();

$total_feedback = (int)($stats['total_feedback'] ?? 0);
$avg_rating = $stats['avg_rating'] !== null ? number_format((float)$stats['avg_rating'], 2) : "0.00";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Feedback - SOE</title>

    <link rel="stylesheet" href="<?= BASE_PATH_CSS ?>admin.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .page-box{
            max-width: 1000px;
            margin: 30px auto;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 14px;
            padding: 20px;
        }
        .stats-grid{
            display:grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
            margin: 16px 0 24px 0;
        }
        .stat-card{
            padding: 16px;
            border-radius: 12px;
            background: rgba(255,105,180,0.10);
            border: 2px solid rgba(249,78,164,0.45);
        }
        .table-wrap{
            overflow-x:auto;
            border-radius: 12px;
            background: rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.15);
        }
        table{
            width:100%;
            border-collapse:collapse;
        }
        th, td{
            padding: 12px 10px;
            border-bottom: 1px solid rgba(255,255,255,0.12);
            vertical-align: top;
        }
        th{
            text-align:left;
            font-weight: 800;
        }
        .badge{
            display:inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-weight:700;
            font-size: 0.9rem;
            background: rgba(79, 217, 157, 0.25);
            border: 1px solid rgba(79, 217, 157, 0.4);
        }
        .btn-back{
            display:inline-block;
            text-decoration:none;
            padding: 10px 14px;
            border-radius: 10px;
            background: rgba(79,217,157,0.18);
            border: 1px solid rgba(79,217,157,0.35);
            font-weight: 800;
            margin-top: 10px;
        }
        .muted{ opacity: 0.85; }
    </style>
</head>

<body>

<?php include(ROOT_PATH_ADMIN . 'include/header.php'); ?>

<div class="admin-container">
    <?php include(ROOT_PATH_ADMIN . 'include/sidebar.php'); ?>

    <main class="main-content" id="main-content">
        <div class="page-box">
            <h2 style="margin-bottom:6px;">
                <i class="fas fa-comments"></i> Feedback for Event
            </h2>

            <p class="muted" style="margin-bottom:12px;">
                <b>Event:</b> <?= htmlspecialchars($event['event_name']) ?>
            </p>

            <div class="stats-grid">
                <div class="stat-card">
                    <div style="font-weight:800;"><i class="fas fa-star"></i> Average Rating</div>
                    <div style="font-size:1.8rem; font-weight:900; margin-top:8px;">
                        <?= htmlspecialchars($avg_rating) ?> / 5
                    </div>
                </div>

                <div class="stat-card">
                    <div style="font-weight:800;"><i class="fas fa-list"></i> Total Feedback</div>
                    <div style="font-size:1.8rem; font-weight:900; margin-top:8px;">
                        <?= $total_feedback ?>
                    </div>
                </div>

                <div class="stat-card">
                    <div style="font-weight:800;"><i class="fas fa-user-shield"></i> Viewer Access</div>
                    <div style="margin-top:8px;">
                        <?php if ($user_role === 'Admin'): ?>
                            <span class="badge">Admin</span>
                        <?php else: ?>
                            <span class="badge">Event Creator</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:160px;">User</th>
                            <th style="width:120px;">Rating</th>
                            <th>Comment</th>
                            <th style="width:180px;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($feedback_result && $feedback_result->num_rows > 0): ?>
                            <?php while($row = $feedback_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['user_name'] ?? 'Unknown') ?></td>
                                    <td><span class="badge"><?= htmlspecialchars($row['rating']) ?> ⭐</span></td>
                                    <td><?= nl2br(htmlspecialchars($row['comment'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars($row['created_at'] ?? '') ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding:18px;">
                                    No feedback has been submitted for this event yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <a class="btn-back" href="manage.php">
                <i class="fas fa-arrow-left"></i> Back to Manage Events
            </a>

        </div>
    </main>
</div>

<?php include(ROOT_PATH_ADMIN . "include/footer.php"); ?>

</body>
</html>

<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Mark all as read only if user clicks button
if (isset($_GET['mark']) && $_GET['mark'] === 'all') {
    $conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = $user_id");
    header("Location: notifications.php");
    exit();
}

// Fetch all notifications for this user
$sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notifications = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Notifications - SOE</title>
    <link rel="stylesheet" href="<?= BASE_PATH_CSS ?>style.css">

    <style>
        .notif-container{
    background: rgba(255, 105, 180, 0.10);   
    border: 2px solid rgba(255, 105, 180, 0.55);
    border-radius: 15px;
    padding: 30px;
    margin: 30px auto 40px auto;
    max-width: 1000px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    backdrop-filter: blur(10px);
    color: #fff;
}

.notif-card{
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 12px;
    border: 1px solid rgba(255,255,255,0.15);
    background: rgba(255, 255, 255, 0.08);
}

.notif-unread{
    border-left: 5px solid #ff69b4;          
    background: rgba(255, 105, 180, 0.12);
}

.notif-title{
    color: #ff69b4;                         
    margin: 0 0 15px 0;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.notif-topbar{
    display:flex;
    justify-content: space-between;
    align-items:center;
    gap: 15px;
    flex-wrap: wrap;
    margin-bottom: 15px;
}
    </style>
</head>

<body>
<?php include '../include/topNav.php'; ?>

<section class="hero" style="height: 40vh;">
    <div class="overlay"></div>
    <div class="hero-content">
        <h1>NOTIFICATIONS</h1>
        <p>Latest updates for your activities</p>
    </div>
</section>

<main>
    <div class="notif-container">

        <div class="notif-topbar">
            <h2 class="notif-title">Inbox Notifications</h2>

            <a href="notifications.php?mark=all" class="btn" style="padding:8px 14px;">
                Mark All Read
            </a>
        </div>

        <hr style="border:0; border-top:1px solid rgba(160, 44, 150, 0.3); margin: 15px 0;">

        <?php if ($notifications->num_rows > 0): ?>
            <?php while($n = $notifications->fetch_assoc()): ?>
                <div class="notif-card <?= ($n['is_read'] == 0) ? 'notif-unread' : '' ?>">
                    <p style="margin: 0;">
                        <?= htmlspecialchars($n['message']) ?>
                    </p>
                    <small style="color: rgba(255,255,255,0.7);">
                        <?= date('d M Y, h:i A', strtotime($n['created_at'])) ?>
                        <?= ($n['is_read'] == 0) ? " • Unread" : "" ?>
                    </small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color:#fff;">No new notifications.</p>
        <?php endif; ?>

    </div>
</main>
<?php include __DIR__ . '/../include/footer.php'; ?>

</body>
</html>

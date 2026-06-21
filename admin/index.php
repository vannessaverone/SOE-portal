<?php
session_start(); // Ensure session is started for role checking
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch System Analytics 
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_events = $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
$total_regs = $conn->query("SELECT COUNT(*) as count FROM registrations")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<?php include(ROOT_PATH_ADMIN . 'include/head.php'); ?>
<body>
    <?php include(ROOT_PATH_ADMIN . 'include/header.php'); ?>
    
    <div class="admin-container">
    <?php include(ROOT_PATH_ADMIN . 'include/sidebar.php'); ?>

    <main class="main-content" id="main-content">
        <h2>Admin Dashboard Overview</h2>
        
        <div class="stats-container" style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 20px;">
    <div class="stat-card" style="background: #2a1530; padding: 25px; border-radius: 10px; flex: 1; min-width: 200px; border-left: 5px solid #ff4fa3;">
        <h3 style="color: #fff; margin-bottom: 10px;"><i class="fas fa-users"></i> Total Users</h3>
        <p style="font-size: 2.5rem; color: #ff7ac4; font-weight: bold; margin: 0;"><?= $total_users ?></p>
    </div>

    <div class="stat-card" style="background: #2a1530; padding: 25px; border-radius: 10px; flex: 1; min-width: 200px; border-left: 5px solid #ff4fa3;">
        <h3 style="color: #fff; margin-bottom: 10px;"><i class="fas fa-calendar-alt"></i> Total Events</h3>
        <p style="font-size: 2.5rem; color: #ff7ac4; font-weight: bold; margin: 0;"><?= $total_events ?></p>
    </div>

    <div class="stat-card" style="background: #2a1530; padding: 25px; border-radius: 10px; flex: 1; min-width: 200px; border-left: 5px solid #ff4fa3;">
        <h3 style="color: #fff; margin-bottom: 10px;"><i class="fas fa-file-signature"></i> Registrations</h3>
        <p style="font-size: 2.5rem; color: #ff7ac4; font-weight: bold; margin: 0;"><?= $total_regs ?></p>
    </div>
</div>

<section style="margin-top: 40px; background: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    <h3 style="color: #2a1530; border-bottom: 2px solid #ff4fa3; padding-bottom: 10px; margin-bottom: 15px;">
        <i class="fas fa-file-download"></i> System Reports
    </h3>
    <p style="color: #555; margin-bottom: 20px;">Generate and export administrative records for auditing purposes.</p>
    
    <div style="display: flex; gap: 15px;">
        <a href="generate_report.php?type=category" class="btn" style="background: #ff4fa3; padding: 12px 25px; text-decoration: none; color: white; border-radius: 5px; font-weight: bold; transition: background 0.3s;">
            <i class="fas fa-chart-pie"></i> Export by Category
        </a>
        <a href="generate_report.php?type=monthly" class="btn" style="background: #ff7ac4; padding: 12px 25px; text-decoration: none; color: white; border-radius: 5px; font-weight: bold; transition: background 0.3s;">
            <i class="fas fa-calendar-check"></i> Export Monthly Stats
        </a>
    </div>
</section>

<section style="margin-top: 20px; background: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    <h3 style="color: #2a1530; border-bottom: 2px solid #ff4fa3; padding-bottom: 10px; margin-bottom: 15px;">
        <i class="fas fa-bullhorn"></i> Event Announcements
    </h3>
    <p style="color: #555; margin-bottom: 20px;">Send important updates or notifications to users registered for specific events.</p>
    
    <div style="display: flex; gap: 15px;">
        <a href="events/post_announcement.php" class="btn" style="background: #ff4fa3; padding: 12px 25px; text-decoration: none; color: #ffffff; border-radius: 5px; font-weight: bold; transition: background 0.3s;">
            <i class="fas fa-plus-circle"></i> Create New Announcement
        </a>
    </div>
</section>


    </main>

    </div>
    <?php include(ROOT_PATH_ADMIN ."include/footer.php"); ?> 
</body>
</html>
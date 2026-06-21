<?php
// NOTE: dashboard.php is INCLUDED inside index.php
// So index.php already has session_start + config.php + topNav.php

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? "User";
$user_role = $_SESSION['user_role'] ?? "Student";

// Total users
$totalUsers = 0;
$q = $conn->query("SELECT COUNT(*) AS total FROM users");
if ($q) $totalUsers = $q->fetch_assoc()['total'] ?? 0;

// Total events
$totalEvents = 0;
$q = $conn->query("SELECT COUNT(*) AS total FROM events");
if ($q) $totalEvents = $q->fetch_assoc()['total'] ?? 0;

// Total registrations
$totalRegs = 0;
$q = $conn->query("SELECT COUNT(*) AS total FROM registrations");
if ($q) $totalRegs = $q->fetch_assoc()['total'] ?? 0;

// Total feedback
$totalFeedback = 0;
$q = $conn->query("SELECT COUNT(*) AS total FROM feedback");
if ($q) $totalFeedback = $q->fetch_assoc()['total'] ?? 0;

// My created events
$myEvents = 0;
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM events WHERE created_by = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$myEvents = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$stmt->close();

// My registrations
$myRegs = 0;
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM registrations WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$myRegs = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$stmt->close();

// Pending registrations for my events
$pendingRegs = 0;
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM registrations r
    JOIN events e ON r.event_id = e.event_id
    WHERE e.created_by = ? AND r.status = 'Pending'
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pendingRegs = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$stmt->close();
?>


<section class="listing">

  <h3 style="text-align:center;">SOE Event Management Hub</h3>
  <p style="text-align:center; margin-top:-10px; opacity:0.85;">
    Welcome, <strong><?= htmlspecialchars($user_name) ?></strong> (<?= htmlspecialchars($user_role) ?>)
  </p>

  <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
              gap:14px; margin: 18px 0 24px 0;">

    <div style="border-radius:16px; padding:16px; background: rgba(255,105,180,0.10);
                border: 2px solid rgba(249,78,164,0.45); backdrop-filter: blur(8px);">
      <div style="font-weight:700;"><i class="fas fa-users"></i> Total Users</div>
      <div style="font-size:1.7rem; font-weight:800; margin-top:8px;"><?= $totalUsers ?></div>
    </div>

    <div style="border-radius:16px; padding:16px; background: rgba(255,105,180,0.10);
                border: 2px solid rgba(249,78,164,0.45); backdrop-filter: blur(8px);">
      <div style="font-weight:700;"><i class="fas fa-calendar"></i> Total Events</div>
      <div style="font-size:1.7rem; font-weight:800; margin-top:8px;"><?= $totalEvents ?></div>
    </div>

    <div style="border-radius:16px; padding:16px; background: rgba(255,105,180,0.10);
                border: 2px solid rgba(249,78,164,0.45); backdrop-filter: blur(8px);">
      <div style="font-weight:700;"><i class="fas fa-ticket-alt"></i> Total Registrations</div>
      <div style="font-size:1.7rem; font-weight:800; margin-top:8px;"><?= $totalRegs ?></div>
    </div>

    <div style="border-radius:16px; padding:16px; background: rgba(255,105,180,0.10);
                border: 2px solid rgba(249,78,164,0.45); backdrop-filter: blur(8px);">
      <div style="font-weight:700;"><i class="fas fa-comment-dots"></i> Total Feedback</div>
      <div style="font-size:1.7rem; font-weight:800; margin-top:8px;"><?= $totalFeedback ?></div>
    </div>

    <div style="border-radius:16px; padding:16px; background: rgba(255,105,180,0.10);
                border: 2px solid rgba(249,78,164,0.45); backdrop-filter: blur(8px);">
      <div style="font-weight:700;"><i class="fas fa-clipboard-list"></i> My Events</div>
      <div style="font-size:1.7rem; font-weight:800; margin-top:8px;"><?= $myEvents ?></div>
    </div>

    <div style="border-radius:16px; padding:16px; background: rgba(255,105,180,0.10);
                border: 2px solid rgba(249,78,164,0.45); backdrop-filter: blur(8px);">
      <div style="font-weight:700;"><i class="fas fa-check-circle"></i> Events I Joined</div>
      <div style="font-size:1.7rem; font-weight:800; margin-top:8px;"><?= $myRegs ?></div>
    </div>

    <div style="border-radius:16px; padding:16px; background: rgba(255,105,180,0.10);
                border: 2px solid rgba(249,78,164,0.45); backdrop-filter: blur(8px);">
      <div style="font-weight:700;"><i class="fas fa-hourglass-half"></i> Pending Requests</div>
      <div style="font-size:1.7rem; font-weight:800; margin-top:8px;"><?= $pendingRegs ?></div>
    </div>

  </div>

  <h3 style="text-align:center; margin-top: 10px;">Quick Actions</h3>

  <div class="filter-container" style="justify-content:center;">
    <a href="<?= BASE_URL ?>/profile.php" class="btn">
      <i class="fas fa-user"></i> My Profile
    </a>

    <a href="<?= BASE_URL ?>/auth/my_registrations.php" class="btn">
      <i class="fas fa-calendar-check"></i> Events I Joined
    </a>

    <a href="<?= BASE_URL ?>/admin/events/manage.php" class="btn">
      <i class="fas fa-tasks"></i> Manage My Events
    </a>

    <a href="<?= BASE_URL ?>/auth/registrations.php" class="btn">
      <i class="fas fa-list-ul"></i> View Registrations
    </a>

    <a href="<?= BASE_URL ?>/admin/events/create.php" class="btn">
      <i class="fas fa-plus"></i> Create Event
    </a>

    <a href="<?= BASE_URL ?>/auth/notifications.php" class="btn">
      <i class="fas fa-bell"></i> Notifications
    </a>
  </div>

</section>

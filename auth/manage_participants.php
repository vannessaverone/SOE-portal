<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['event_id'])) {
    header("Location: ../index.php");
    exit();
}

$event_id = intval($_GET['event_id']);
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? "";

// Allow Admin OR event organizer
if ($user_role !== 'Admin') {
    $owner_check = $conn->prepare("SELECT event_name FROM events WHERE event_id = ? AND created_by = ?");
    $owner_check->bind_param("ii", $event_id, $user_id);
} else {
    $owner_check = $conn->prepare("SELECT event_name FROM events WHERE event_id = ?");
    $owner_check->bind_param("i", $event_id);
}

$owner_check->execute();
$eventRow = $owner_check->get_result()->fetch_assoc();

if (!$eventRow) {
    die("Access Denied: You are not the organizer of this event.");
}

$event_name = $eventRow['event_name'] ?? "Event";

// Fetch all registered participants
$sql = "SELECT r.registration_id, u.name, u.email, u.organization, r.status, r.attendance
        FROM registrations r
        JOIN users u ON r.user_id = u.user_id
        WHERE r.event_id = ?
        ORDER BY r.status ASC, u.name ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$participants = $stmt->get_result();

//  Stats
$total = 0; $pending = 0; $approved = 0; $rejected = 0; $present = 0; $absent = 0;
$allRows = [];

while ($p = $participants->fetch_assoc()) {
    $allRows[] = $p;
    $total++;

    if ($p['status'] === 'Pending') $pending++;
    if ($p['status'] === 'Approved') $approved++;
    if ($p['status'] === 'Rejected') $rejected++;

    if ($p['status'] === 'Approved') {
        if (intval($p['attendance']) === 1) $present++;
        else $absent++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SOE - Manage Participants</title>

  <link rel="stylesheet" href="<?= BASE_PATH_CSS ?>admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    .stats-row{
      display:flex; gap:12px; flex-wrap:wrap; margin:18px 0;
    }
    .stat-card{
      flex:1; min-width:160px;
      padding:12px 14px;
      border-radius:12px;
      border:1px solid rgba(255,255,255,0.15);
      background: rgba(255,255,255,0.06);
      color:#fff;
    }
    .stat-card strong{ display:block; font-size:1.2rem; margin-top:4px; }
    .badge{
      padding:4px 10px; border-radius:999px; font-size:0.85rem; font-weight:600;
      display:inline-block;
    }
    .b-pending{ background: rgba(255,165,0,0.18); border:1px solid rgba(255,165,0,0.35); color:orange; }
    .b-approved{ background: rgba(79,217,157,0.12); border:1px solid rgba(79,217,157,0.35); color:#4fd99d; }
    .b-rejected{ background: rgba(255,0,0,0.10); border:1px solid rgba(255,0,0,0.30); color:#ff6b6b; }

    table { width:100%; border-collapse: collapse; margin-top: 16px; }
    th, td { padding: 12px; border-bottom: 1px solid rgba(255,255,255,0.10); }
    th { text-align: left; color:#fff; font-weight:700; }
    td { color:#eaeaea; }
    .actions a{ margin-right:10px; text-decoration:none; font-weight:600; }
    .a-approve{ color:#4fd99d; }
    .a-reject{ color:#ff6b6b; }
    .muted { color: rgba(255,255,255,0.55); }
    .tiny-link{ font-size:0.85rem; opacity:0.85; }
  </style>
</head>
<body>

<?php include(ROOT_PATH_ADMIN . 'include/header.php'); ?>

<div class="admin-container">
  <?php include(ROOT_PATH_ADMIN . 'include/sidebar.php'); ?>

  <main class="main-content" id="main-content">
    <h2>Manage Participants</h2>
    <p class="muted">
      Event: <strong><?= htmlspecialchars($event_name) ?></strong>
    </p>

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-card">Total Registrations<strong><?= $total ?></strong></div>
      <div class="stat-card">Pending<strong><?= $pending ?></strong></div>
      <div class="stat-card">Approved<strong><?= $approved ?></strong></div>
      <div class="stat-card">Rejected<strong><?= $rejected ?></strong></div>
      <div class="stat-card">Present<strong><?= $present ?></strong></div>
      <div class="stat-card">Absent<strong><?= $absent ?></strong></div>
    </div>

    <a href="<?= BASE_PATH_ADMIN ?>events/manage.php" class="btn" style="display:inline-block; margin-bottom:14px;">
      ← Back to Manage Events
    </a>

    <?php if ($total === 0): ?>
      <p class="muted">No participants registered yet.</p>
    <?php else: ?>

    <table>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Organization</th>
        <th>Status</th>
        <th>Attendance</th>
        <th>Actions</th>
      </tr>

      <?php foreach($allRows as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td><?= htmlspecialchars($p['email']) ?></td>
        <td><?= htmlspecialchars($p['organization']) ?></td>

        <td>
        <?php if ($p['status'] === 'Approved'): ?>
        <span class="badge b-approved">Approved</span>
        <?php elseif ($p['status'] === 'Rejected'): ?>
        <span class="badge b-rejected">Rejected</span>
        <?php else: ?>
        <span class="badge b-pending">Pending</span>
        <?php endif; ?>
        </td>

        <td>
          <?php if ($p['status'] === 'Approved'): ?>
            <?php if (intval($p['attendance']) === 1): ?>
              <span style="color:#4fd99d; font-weight:700;">
                <i class="fas fa-check-circle"></i> Present
              </span>
              <a class="tiny-link" href="mark_attendance.php?reg_id=<?= $p['registration_id'] ?>&status=0&event_id=<?= $event_id ?>">
                (Undo)
              </a>
            <?php else: ?>
              <a class="btn" style="padding:6px 10px; font-size:0.85rem;"
                 href="mark_attendance.php?reg_id=<?= $p['registration_id'] ?>&status=1&event_id=<?= $event_id ?>">
                 Mark Present
              </a>
            <?php endif; ?>
          <?php else: ?>
            <span class="muted">N/A</span>
          <?php endif; ?>
        </td>

        <td class="actions">
          <?php if ($p['status'] === 'Pending'): ?>
            <a class="a-approve"
               href="update_participant.php?reg_id=<?= $p['registration_id'] ?>&status=Approved&event_id=<?= $event_id ?>"
               onclick="return confirm('Approve this participant?')">
              Approve
            </a>

            <a class="a-reject"
               href="update_participant.php?reg_id=<?= $p['registration_id'] ?>&status=Rejected&event_id=<?= $event_id ?>"
               onclick="return confirm('Reject this participant?')">
              Reject
            </a>
          <?php else: ?>
            <span class="muted">Locked</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>

    <?php endif; ?>
  </main>
</div>

<?php include(ROOT_PATH_ADMIN . "include/footer.php"); ?>

<script>
  document.querySelectorAll('.submenu-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      btn.nextElementSibling.classList.toggle('show');
    });
  });
</script>

</body>
</html>

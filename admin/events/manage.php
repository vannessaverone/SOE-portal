<?php
session_start();
require_once __DIR__ . '/../../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

/// Fetch events based on role
if ($role === 'Admin') {
    // Use e.* to ensure admin_status and is_highlighted are included
    $sql = "SELECT e.*, c.categoryName, 
            (SELECT COUNT(*) FROM registrations WHERE event_id = e.event_id) AS joined_count
            FROM events AS e 
            INNER JOIN event_category AS c ON e.category_id = c.category_id";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT e.*, c.categoryName, 
            (SELECT COUNT(*) FROM registrations WHERE event_id = e.event_id) AS joined_count
            FROM events AS e 
            INNER JOIN event_category AS c ON e.category_id = c.category_id 
            WHERE e.created_by = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}


$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SOE - Manage Events</title>
  <link rel="stylesheet" href="<?= BASE_PATH_CSS ?>admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <?php include(ROOT_PATH_ADMIN . 'include/header.php'); ?>
  
  <div class="admin-container">
    <?php include(ROOT_PATH_ADMIN . 'include/sidebar.php'); ?>
    
    <main class="main-content">
        <h2>Manage Events</h2>

            <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Event Name</th>
                    <th>Participants</th>
                    <th>Rating</th>
                    <th>Category</th>
                    <th>Venue</th>
                    <th>Date</th>
                    <?php if ($role === 'Admin'): ?>
                        <th>Moderation</th> <?php endif; ?>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): 
                    $curr_id = $row['event_id'];

                    $max = $row['max_participants'];
                    $joined = $row['joined_count'];
                    $participant_display = ($max > 0) ? "$joined / $max" : "$joined (Unlimited)";
                    $is_full = ($max > 0 && $joined >= $max);
                    
                    // Module 8: Get Average Rating for this specific event
                    $rating_sql = "SELECT AVG(rating) as avg_score, COUNT(*) as total_reviews 
                                   FROM feedback WHERE event_id = ?";
                    $r_stmt = $conn->prepare($rating_sql);
                    $r_stmt->bind_param("i", $curr_id);
                    $r_stmt->execute();
                    $rating_data = $r_stmt->get_result()->fetch_assoc();
                    $display_rating = $rating_data['total_reviews'] > 0 
                                      ? round($rating_data['avg_score'], 1) . " / 5" 
                                      : "No ratings";
                ?>
                    <tr>
                        <td><?= $row['event_id'] ?></td>
                        <td><?= htmlspecialchars($row['event_name']) ?></td>
                        <td>
                            <?= $participant_display ?>
                            <?php if ($is_full): ?>
                                <span title="Event Full" style="color: red;">&#9888;</span>
                            <?php endif; ?>
                        </td>
                        <td>
                    <a href="view_feedback.php?event_id=<?= $row['event_id'] ?>"
     style="color:#d37cf6; font-weight:bold; text-decoration:none;"
     onmouseover="this.style.textDecoration='underline'"
     onmouseout="this.style.textDecoration='none'"
     title="View Feedback">
                <?= $display_rating ?>
                </a>
                </td>
                        <td><?= $row['categoryName'] ?></td>
                        <td><?= htmlspecialchars($row['venue']) ?></td>
                        <td><?= $row['event_date'] ?></td>

                        <?php if ($role === 'Admin'): ?>
                            <td>
                                <div class="admin-controls" style="display: flex; align-items: center;">
                                    <select onchange="window.location.href='admin_action.php?id=<?= $row['event_id'] ?>&action=' + this.value">
                                        <option value="Pending" <?= $row['admin_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Approved" <?= $row['admin_status'] == 'Approved' ? 'selected' : '' ?>>Approve</option>
                                        <option value="Archived" <?= $row['admin_status'] == 'Archived' ? 'selected' : '' ?>>Archive</option>
                                    </select>
                                    <a href="admin_action.php?id=<?= $row['event_id'] ?>&action=highlight" 
                                       style="color: <?= $row['is_highlighted'] ? '#f6ee7c' : '#bdc3c7' ?>; margin-left: 10px;"
                                       title="Toggle Highlight">
                                        <i class="fas fa-star"></i>
                                    </a>
                                    
                                </div>
                            </td>
                        <?php endif; ?>

                        <td>
                            <a href="edit.php?id=<?= $row['event_id'] ?>" class="action-btn edit">Edit</a>
                            <a href="delete.php?id=<?= $row['event_id'] ?>" class="action-btn delete" onclick="return confirm('Delete this event?')">Delete</a>
                            <a href="view_feedback.php?event_id=<?= $row['event_id'] ?>" 
                            class="action-btn" 
                            style="background-color:#8e44ad;" 
                            title="View Feedback">
                            <i class="fas fa-comments"></i>
                            </a>
                            <a href="../../auth/manage_participants.php?event_id=<?= $row['event_id'] ?>" class="action-btn" style="background-color: #ae2795;" title="Manage Participants">
                            <i class="fas fa-users"></i>
                            </a>
                            <a href="export_participants.php?event_id=<?= $row['event_id'] ?>" 
                               class="action-btn" 
                               style="background-color: #ae2791;" 
                               title="Download CSV">
                               <i class="fas fa-file-csv"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="text-align:center;">No events found.</p>
            <?php endif; ?>

            <p style="text-align:center; margin-top:1.5rem;">
                <a href="create.php" class="action-btn edit">➕ Create New Event</a>
            </p>
    </main>
  </div>
  <?php include(ROOT_PATH_ADMIN ."include/footer.php"); ?> 
</body>
</html>
<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$user_id   = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['user_role'] ?? null;

// IMPORTANT: unlock session early
session_write_close();

require_once __DIR__ . '/../config.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT e.event_id, e.event_name, e.event_date, e.venue, e.mode, 
               r.registration_date, r.registration_id, r.status, r.attendance
        FROM registrations r
        JOIN events e ON r.event_id = e.event_id
        WHERE r.user_id = ?
        ORDER BY e.event_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Joined Events - SOE</title>
    <link rel="stylesheet" href="<?= BASE_PATH_CSS ?>style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <?php include '../include/topNav.php'; ?>
    <?php include '../include/header.php'; ?>

    <main>
        <section class="listing">
            <h3>Events I'm Attending</h3>
            
            <table id="event_table">
                <thead>
                    <tr style="background-color: #d94fd7; color: #540032;">
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Attendance</th>
                        <th>Announcements</th> <th>Action</th>
                        <th>Feedback</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): 
                            $status_color = ($row['status'] == 'Approved') ? '#d94fce' : (($row['status'] == 'Rejected') ? '#ff4d4d' : '#ffa500');
                            ?>
                            <tr>
                                <td><b><?= htmlspecialchars($row['event_name']) ?></b></td>
                                <td><?= date('d M Y', strtotime($row['event_date'])) ?></td>
                                <td style="font-weight: bold; color: <?= $status_color ?>;">
                                    <?= $row['status'] ?>
                                </td>
                                <td>
                                    <?php if ($row['attendance'] == 1): ?>
                                        <span style="color: #f05cc6;"><i class="fas fa-check-circle"></i> Present</span>
                                    <?php else: ?>
                                        <span style="color: #c0a6be;"><i class="fas fa-times-circle"></i> Absent</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="view_announcements.php?event_id=<?= $row['event_id'] ?>" style="color: #4fd99d; text-decoration: none;">
                                        <i class="fas fa-bullhorn"></i> View Updates
                                    </a>
                                </td>
                                <td>
                                    <a href="withdraw_action.php?id=<?= $row['registration_id'] ?>" 
                                       style="color: #ff4d4d;" 
                                       onclick="return confirm('Withdraw from this event?')">
                                       <i class="fas fa-times-circle"></i> Withdraw
                                    </a>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'Approved' && $row['attendance'] == 1): ?>
                                        <a href="give_feedback.php?event_id=<?= $row['event_id'] ?>" 
                                            class="btn" style="background: #f10f5e; color: #000; padding: 5px 10px; font-size: 0.8rem;">
                                            <i class="fas fa-star"></i> Feedback
                                        </a>
                                    <?php else: ?>
                                         <span style="color: #888; font-size: 0.8rem;">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 20px;">
                                You haven't joined any events yet. <br>
                                <a href="../index.php" style="color:#4fd99d;">Explore Events</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <?php include '../include/footer.php'; ?>
</body>
</html>
<?php
session_start();
// 1. Adjusted path to reach config.php in the root [cite: 2025-12-11]
require_once __DIR__ . '/../config.php';

// Authentication Check [cite: 2025-12-11]
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

// 2. Fetch registrations for events created by the logged-in user [cite: 2025-12-11]
if ($role === 'Admin') {
    $sql = "SELECT r.*, e.event_name, u.name as participant_name, u.email as participant_email 
            FROM registrations r
            JOIN events e ON r.event_id = e.event_id
            JOIN users u ON r.user_id = u.user_id
            ORDER BY r.registration_date DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT r.*, e.event_name, u.name as participant_name, u.email as participant_email 
            FROM registrations r
            JOIN events e ON r.event_id = e.event_id
            JOIN users u ON r.user_id = u.user_id
            WHERE e.created_by = ?
            ORDER BY r.registration_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registrations - SOE</title>
    <link rel="stylesheet" href="<?= BASE_PATH_CSS ?>style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <?php include '../include/topNav.php'; ?>

    <header class="hero">
        <div class="overlay"></div>
        <div class="hero-content">
            <h1>Event Registrations</h1>
            <p>Managing participants for organized events.</p>
        </div>
    </header>

    <main>
        <section class="listing">
            <h3>Participant List</h3>
            
            <table id="event_table">
                <thead>
                    <tr style="background-color: #df015e; color: #001a2c;">
                        <th>Event Name</th>
                        <th>Participant</th>
                        <th>Email</th>
                        <th>Date Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['event_name']) ?></td>
                                <td><?= htmlspecialchars($row['participant_name']) ?></td>
                                <td><?= htmlspecialchars($row['participant_email']) ?></td>
                                <td><?= date('d M Y', strtotime($row['registration_date'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 20px;">No registrations found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="filter-container" style="margin-top: 20px;">
                <a href="../index.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            </div>
        </section>
    </main>

    <?php include ROOT_PATH . '/include/footer.php'; ?> 

</body>
</html>
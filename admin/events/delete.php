<?php
session_start();
require_once __DIR__ . '/../../config.php'; 

// 1. Authentication Check [cite: 2025-12-11]
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role']; // Verified from your users table role enum
$message = "";

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $event_id = intval($_GET['id']);

    // 2. Authorization Check: Fetch ownership data [cite: 2025-12-11]
    $sql_check = "SELECT poster_path, created_by FROM events WHERE event_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $event_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($row = $result->fetch_assoc()) {
        $poster_path = $row['poster_path'];
        $owner_id = $row['created_by'];

        // 3. Permission Logic: Admin can delete anything; Others only their own [cite: 2025-12-11]
        if ($role === 'Admin' || $owner_id == $user_id) {
            
            // Delete record [cite: 2025-12-11]
            $sql_delete = "DELETE FROM events WHERE event_id = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("i", $event_id);

            if ($stmt_delete->execute()) {
                // Delete image file if exists [cite: 2025-12-11]
                $fullPosterPath = ROOT_PATH . '/uploads/' . $poster_path;
                if (!empty($poster_path) && file_exists($fullPosterPath)) {
                    unlink($fullPosterPath);
                }
                $message = "✅ Event (ID: {$event_id}) deleted successfully.";
            } else {
                $message = "❌ Error deleting event.";
            }
            $stmt_delete->close();
        } else {
            $message = "🚫 Access Denied: You cannot delete an event you did not create.";
        }
    } else {
        $message = "⚠️ Event not found.";
    }
    $stmt_check->close();
} else {
    $message = "⚠️ Invalid event ID.";
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SOE - Admin Dashboard</title>

  <link rel="stylesheet" href="<?= BASE_PATH_CSS ?>admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <?php include(ROOT_PATH_ADMIN . 'include/header.php'); ?>

  <div class="admin-container">
    <!-- Include Sidebar -->
    <?php include(ROOT_PATH_ADMIN . 'include/sidebar.php'); ?>

    <!-- Main Content -->
    <main class="main-content" id="main-content">
      <h2>Delete Event</h2>

      <p style="padding: 1rem; background-color: #f2f2f2; border-radius: 6px;">
        <?php echo $message; ?>
      </p>

      <a href="manage.php" style="display:inline-block; margin-top:10px;">&larr; Back to Manage Events</a>
    </main>
  </div>

  <?php
     include(ROOT_PATH_ADMIN ."include/footer.php");
  ?> 

  <script>
    // Toggle submenu
    document.querySelectorAll('.submenu-toggle').forEach(btn => {
      btn.addEventListener('click', () => {
        btn.nextElementSibling.classList.toggle('show');
      });
    });
  </script>
</body>
</html>

<?php
session_start();
require_once __DIR__ . '/../../config.php';

// 1. Security Guard: Admin Only
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit();
}

// 2. Handle Delete Action
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    // Prevent Admin from deleting themselves
    if ($del_id != $_SESSION['user_id']) {
        $conn->query("DELETE FROM users WHERE user_id = $del_id");
        $msg = "User deleted successfully.";
    } else {
        $error = "You cannot delete your own account.";
    }
}

// 3. Fetch All Users
$sql = "SELECT user_id, name, email, role, category, created_at, status FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SOE - User Management</title>
  <link rel="stylesheet" href="<?= BASE_PATH_CSS ?>admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <?php include(ROOT_PATH_ADMIN . 'include/header.php'); ?>
  
  <div class="admin-container">
    <?php include(ROOT_PATH_ADMIN . 'include/sidebar.php'); ?>
    
    <main class="main-content">
        <h2><i class="fas fa-users-cog"></i> User Management</h2>

        <?php if(isset($msg)) echo "<p style='color:green; background:#dfd; padding:10px;'>$msg</p>"; ?>
        <?php if(isset($error)) echo "<p style='color:red; background:#fdd; padding:10px;'>$error</p>"; ?>

        <div style="overflow-x:auto; margin-top: 20px;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Category</th> <th>Joined Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $row['user_id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <span style="font-weight:bold; color: <?= $row['role'] === 'Admin' ? 'red' : '#2c8ca0' ?>">
                                <?= $row['role'] ?>
                            </span>
                        </td>
                        <td>
                            <span style="color: <?= ($row['status'] === 'Active') ? 'green' : 'red' ?>; font-weight: bold;">
                            <?= $row['status'] ?>
                            </span>
                        </td>
                        <td><?= $row['category'] ?></td>
                        <td><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
                        <td>
                            <?php if ($row['role'] !== 'Admin'): ?>
                                <a href="toggle_user_status.php?id=<?= $row['user_id'] ?>&status=<?= ($row['status'] === 'Active') ? 'Suspended' : 'Active' ?>" 
                                    class="btn" 
                                    style="background: <?= ($row['status'] === 'Active') ? '#e67e22' : '#27ae60' ?>; padding: 5px 10px; font-size: 12px;"
                                    onclick="return confirm('Change status to <?= ($row['status'] === 'Active') ? 'Suspended' : 'Active' ?>?')">
                                <i class="fas fa-ban"></i> <?= ($row['status'] === 'Active') ? 'Suspend' : 'Activate' ?>
                                </a>

                                <a href="users.php?delete_id=<?= $row['user_id'] ?>" 
                                   class="btn" style="background:red; padding:5px 10px; font-size:12px;"
                                   onclick="return confirm('Are you sure you want to remove this user? This will verify User Management logic.')">
                                   <i class="fas fa-trash"></i> Delete
                                </a>
                            <?php else: ?>
                                <span style="color:#ccc;">Locked</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
  </div>
  <?php include(ROOT_PATH_ADMIN ."include/footer.php"); ?> 
</body>
</html>
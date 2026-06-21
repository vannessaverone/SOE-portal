<?php 

$unread_count = 0;

if (isset($_SESSION['user_id'])) {

    // cache cause my lptop is lagging so bad
    if (!isset($_SESSION['unread_cache_time']) || (time() - $_SESSION['unread_cache_time'] > 30)) {

        $u_id = $_SESSION['user_id'];
        $count_query = "SELECT COUNT(*) as total FROM notifications WHERE user_id = ? AND is_read = 0";
        $stmt_count = $conn->prepare($count_query);
        $stmt_count->bind_param("i", $u_id);
        $stmt_count->execute();
        $unread_count = $stmt_count->get_result()->fetch_assoc()['total'] ?? 0;
        $stmt_count->close();

        $_SESSION['unread_cache'] = $unread_count;
        $_SESSION['unread_cache_time'] = time();
    }

    $unread_count = $_SESSION['unread_cache'] ?? 0;
}

?>

<nav class="navbar">
    <div class="nav-container">
      <a href="<?php echo BASE_URL; ?>/index.php" class="brand">SOE</a>
      
      <div class="menu-icon" id="menu-icon">
        <i class="fas fa-bars"></i>
      </div>
      
      <ul class="nav-links" id="nav-links">
        <li>
          <a href="<?php echo BASE_URL; ?>/index.php">
            Home
          </a>
        </li>

        <?php if (isset($_SESSION['user_id'])): ?>
          <li>
            <a href="<?php echo BASE_URL; ?>/auth/notifications.php">
              <i class="fas fa-bell"></i>
              <?php if ($unread_count > 0): ?>
                <span class="badge" style="background: #ff4d85; color: white; padding: 2px 6px; border-radius: 50%; font-size: 10px; position: relative; top: -10px; left: -5px;">
                  <?= $unread_count ?>
                </span>
              <?php endif; ?>
            </a>
          </li>
          <li><a href="<?php echo BASE_URL; ?>/profile.php">My Profile</a></li>
          
          <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin'): ?>
            <li><a href="<?php echo BASE_URL; ?>/admin/index.php">Admin Dashboard</a></li>
          <?php endif; ?>
          
          <li><a href="<?php echo BASE_URL; ?>/auth/logout_action.php">Logout</a></li>
        <?php else: ?>
          <li><a href="<?php echo BASE_URL; ?>/auth/register.php">Register</a></li>
          <li><a href="<?php echo BASE_URL; ?>/auth/login.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
</nav>
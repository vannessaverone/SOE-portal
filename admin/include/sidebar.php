<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config.php';
}

$role = $_SESSION['user_role'] ?? 'User';
?>
<aside class="sidebar" id="sidebar">
  <nav class="menu">
    <a href="<?= BASE_URL ?>/auth/dashboard.php" class="menu-item active">Dashboard</a>
    
    <?php if ($role === 'Admin'): ?>
        <a href="<?= BASE_URL ?>/admin/users/users.php" class="menu-item">Users</a>
    <?php endif; ?>

    <div class="submenu">
      <button class="submenu-toggle">Events ▾</button>
      <div class="submenu-content">
        <a href="<?= BASE_URL ?>/admin/events/manage.php">Manage Events</a>
        <a href="<?= BASE_URL ?>/admin/events/create.php">Create Event</a>
      </div>
    </div>

    <a href="<?= BASE_URL ?>/profile.php" class="menu-item">My Profile</a>
    <a href="<?= BASE_URL ?>/auth/logout_action.php" class="menu-item">Logout</a>
  </nav>
</aside>
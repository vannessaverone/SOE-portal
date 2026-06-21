<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// read session stuff you need first
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['user_role'] ?? null;

// release lock ASAP
session_write_close();

require_once __DIR__ . '/../config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SOE - Dashboard</title>
  <link rel="stylesheet" href="<?= BASE_PATH_CSS ?>style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include __DIR__ . '/../include/topNav.php'; ?>

<main style="padding-top:20px;">
  <?php include __DIR__ . '/dashboard_section.php'; ?>
</main>

<?php include __DIR__ . '/../include/footer.php'; ?>

</body>
</html>


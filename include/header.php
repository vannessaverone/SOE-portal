<?php
// Ensure session is started to check login status
?>
<header class="hero">
    <div class="overlay"></div>
    <div class="hero-content">
        <?php if (isset($_SESSION['user_id'])): ?>
            <img src="<?php echo BASE_PATH_IMG; ?>logo.png" alt="SOE Logo" class="logo">
            <p>Welcome back, <span style="color: #d94fa2; font-weight: bold;">
                <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>
            </span></p>
        <?php else: ?>
            <img src="<?= BASE_URL; ?>/assets/img/logo.png" alt="SOE Logo" class="logo">
            <h1>Student Organization Event Portal (SOE Portal)</h1>
            <p>Organize, manage, and participate in campus events seamlessly</p>
        <?php endif; ?>
    </div>
</header>
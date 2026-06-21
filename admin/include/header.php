    <header class="hero">
        <div class="overlay"></div>
        <div class="hero-content">
            <h1>
                <?php echo (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin') ? "SOE - Admin Dashboard" : "SOE - My Event Hub"; ?>
            </h1>
        </div>
    </header>
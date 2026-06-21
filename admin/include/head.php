<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        <?php echo (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin') ? "SOE - Admin Dashboard" : "SOE - User Dashboard"; ?>
    </title>
    <link rel="stylesheet" href="<?= BASE_PATH_CSS ?>admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
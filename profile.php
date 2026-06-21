<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_write_close();  
require_once __DIR__ . '/config.php';

// Security: Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Fetch current user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - SOE</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }   
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #001a2c;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn-update {
            background: #2c8ca0;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
        }
        .btn-update:hover {
            background: #4fd99d;
            color: #001a2c;
        }
    </style>
</head>
<body>

    <?php include 'include/topNav.php'; ?>
    
    <header class="hero" style="height: 200px; min-height: 200px;">
        <div class="overlay"></div>
        <div class="hero-content">
            <h1>My Profile</h1>
            <p>Manage your account settings</p>
        </div>
    </header>

    <main>
        <div class="profile-container">
            <form action="auth/profile_action.php" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Organization / Department</label>
                    <input type="text" name="organization" value="<?= htmlspecialchars($user['organization']) ?>" required>
                </div>
                
                <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
                
                <div class="form-group">
                    <label>New Password <small style="font-weight: normal; color: #666;">(Leave blank to keep current)</small></label>
                    <input type="password" name="password" placeholder="Enter new password">
                </div>

                <button type="submit" class="btn-update">Update Profile</button>
            </form>
            
            <div style="text-align: center; margin-top: 15px;">
                <a href="index.php" style="color: #666; text-decoration: none;">Cancel</a>
            </div>
        </div>
    </main>

    <?php include  'include/footer.php'; ?>

</body>
</html>
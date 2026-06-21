<?php
// Get the data
require_once __DIR__ . '/../config.php';
$registration_success = false;
$category = $_POST['category'] ?? 'N/A';
$name = htmlspecialchars($_POST['name'] ?? 'N/A');
$email = htmlspecialchars($_POST['email'] ?? 'N/A');
$phone = htmlspecialchars($_POST['phone'] ?? 'N/A');
$organization = htmlspecialchars($_POST['organization'] ?? 'N/A');
$password = htmlspecialchars($_POST['password'] ?? '');
$events = isset($_POST['event']) ? $_POST['event'] : []; 

// Here you would typically save the data to a database
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($password)) {
    // Hash password for security 
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 3. Database Insertion: Users Table 
    // The column list should NOT include user_id
    $stmt = $conn->prepare("INSERT INTO users (category, name, email, phone, organization, password, role) VALUES (?, ?, ?, ?, ?, ?, 'User')");

    // Bind exactly 6 values (category, name, email, phone, organization, password)
    $stmt->bind_param("ssssss", $category, $name, $email, $phone, $organization, $hashed_password);
    if ($stmt->execute()) {
        $user_id = $conn->insert_id;
        $registration_success = true;

        // 4. Database Insertion: Recommendations Table 
        $stmt_rec = $conn->prepare("INSERT INTO user_event_recommend (user_id, name) VALUES (?, ?)");
        foreach ($events as $event_name) {
            $stmt_rec->bind_param("is", $user_id, $event_name);
            $stmt_rec->execute();
        }
        $stmt_rec->close();
    }
    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="<?php echo BASE_PATH_CSS; ?>style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <title>Registration Confirmation</title>  
</head>

<body>
  <main>
    <div class="section-content">
      <?php if ($registration_success): ?>
        <h3>Registration Successful</h3>
        <p style="text-align: center; margin-bottom: 20px;">Welcome to the platform. Here is your profile summary:</p>
        
        <div style="border-top: 1px solid #2c8ca0; padding-top: 20px;">
          <p><strong>Name:</strong> <?= $name ?></p>
          <p><strong>Email:</strong> <?= $email ?></p>
          <p><strong>Phone:</strong> <?= $phone ?></p>
          <p><strong>Organization:</strong> <?= $organization ?></p>
          <p><strong>Category:</strong> <?= ucfirst($category) ?></p>

          <?php if (!empty($events)): ?>
            <p><strong>Interested Events:</strong></p>
            <ul style="list-style: none; padding-left: 0;">
              <?php foreach ($events as $event): ?>
                <li style="background: rgba(79, 217, 157, 0.1); margin: 5px 0; padding: 10px; border-left: 3px solid #4fd99d; border-radius: 4px;">
                  <i class="fas fa-check-circle" style="color: #4fd99d; margin-right: 10px;"></i>
                  <?= htmlspecialchars($event) ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

      <?php else: ?>
        <h3 style="color: #ff4d4d;">Registration Failed</h3>
        <p>There was an error processing your request. Please ensure all details are correct.</p>
        <a href="register.php" class="btn" style="background: #ff4d4d; color: white;">Try Again</a>
      <?php endif; ?>
      
      <div style="margin-top: 30px; text-align: center;">
        <a class="btn" href="<?php echo BASE_URL; ?>/index.php">
          <i class="fas fa-home"></i> Back to Home
        </a>
      </div>
    </div>
  </main>
</body>
</html>
<?php $conn->close(); ?>
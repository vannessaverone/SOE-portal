<?php
session_start();
require_once __DIR__ . '/../../config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Collect form data
    $created_by = $_SESSION['user_id'];

    $event_name = $_POST['event_name'] ?? "";
    $description = $_POST['description'] ?? "";
    $category = $_POST['category'] ?? "";
    $venue = $_POST['venue'] ?? "";
    $date = $_POST['date'] ?? "";
    $time = $_POST['time'] ?? "";

    $registration_close_date = $_POST['registration_close_date'] ?? "";
    $contact_person = $_POST['contact_person'] ?? "";
    $contact_number = $_POST['contact_number'] ?? "";
    $fee = $_POST['fee'] ?? "0.00";

    $mode = $_POST['mode'] ?? "";
    $remarks = $_POST['remarks'] ?? "";
    $max_participants = intval($_POST['max_participants'] ?? 0);
    $visibility = $_POST['visibility'] ?? "Public";

    // Upload directory
    $upload_dir = ROOT_PATH . '/uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $poster1_name = null;
    $poster2_name = null;
    $poster3_name = null;
    $poster4_name = null;

    // Poster 1 (required)
    if (isset($_FILES['poster1']) && $_FILES['poster1']['error'] == 0) {
        $poster1_name = time() . '_1_' . basename($_FILES["poster1"]["name"]);
        move_uploaded_file($_FILES["poster1"]["tmp_name"], $upload_dir . $poster1_name);
    } else {
        $message = " Poster 1 is required.";
    }

    // Poster 2 (optional)
    if (isset($_FILES['poster2']) && $_FILES['poster2']['error'] == 0) {
        $poster2_name = time() . '_2_' . basename($_FILES["poster2"]["name"]);
        move_uploaded_file($_FILES["poster2"]["tmp_name"], $upload_dir . $poster2_name);
    }

    // Poster 3 (optional)
    if (isset($_FILES['poster3']) && $_FILES['poster3']['error'] == 0) {
        $poster3_name = time() . '_3_' . basename($_FILES["poster3"]["name"]);
        move_uploaded_file($_FILES["poster3"]["tmp_name"], $upload_dir . $poster3_name);
    }

    // Poster 4 (optional)
    if (isset($_FILES['poster4']) && $_FILES['poster4']['error'] == 0) {
        $poster4_name = time() . '_4_' . basename($_FILES["poster4"]["name"]);
        move_uploaded_file($_FILES["poster4"]["tmp_name"], $upload_dir . $poster4_name);
    }

    // Insert into Database only if required poster exists
    if ($message === "") {
        $stmt = $conn->prepare("INSERT INTO events (
            event_name, description, category_id, venue, event_date, event_time,
            registration_close_date, contact_person, contact_number, fee,
            mode, remarks,
            poster_path, poster2_path, poster3_path, poster4_path,
            created_by, max_participants, visibility
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "ssissssssds" . "ssssii" . "ss",
            $event_name,
            $description,
            $category,
            $venue,
            $date,
            $time,
            $registration_close_date,
            $contact_person,
            $contact_number,
            $fee,
            $mode,
            $remarks,
            $poster1_name,
            $poster2_name,
            $poster3_name,
            $poster4_name,
            $created_by,
            $max_participants,
            $visibility
        );

        if ($stmt->execute()) {
            $message = " Event created successfully.";
        } else {
            $message = " Error creating event: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SOE - Create Event</title>

  <link rel="stylesheet" href="<?= BASE_PATH_CSS ?>admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <?php include(ROOT_PATH_ADMIN . 'include/header.php'); ?>

  <div class="admin-container">
    <?php include(ROOT_PATH_ADMIN . 'include/sidebar.php'); ?>

    <main class="main-content" id="main-content">
      <h2>Create Event</h2>

      <p style="padding: 1rem; background-color: #eef; border-radius: 6px;">
        <?php echo $message; ?>
      </p>

      <a href="manage.php" style="display:inline-block; margin-top:10px;">
        &larr; Back to Manage Events
      </a>
    </main>
  </div>

  <?php include(ROOT_PATH_ADMIN ."include/footer.php"); ?> 

  <script>
    document.querySelectorAll('.submenu-toggle').forEach(btn => {
      btn.addEventListener('click', () => {
        btn.nextElementSibling.classList.toggle('show');
      });
    });
  </script>
</body>
</html>

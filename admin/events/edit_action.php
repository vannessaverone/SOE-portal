<?php
session_start();
require_once __DIR__ . '/../../config.php'; //global config

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $event_id = intval($_POST['event_id']);

    $event_name = trim($_POST['event_name'] ?? "");
    $description = trim($_POST['description'] ?? "");
    $category_id = intval($_POST['category_id'] ?? 0);
    $venue = trim($_POST['venue'] ?? "");
    $event_date = $_POST['date'] ?? "";
    $event_time = $_POST['time'] ?? "";

    $registration_close_date = $_POST['registration_close_date'] ?? "";
    $contact_person = trim($_POST['contact_person'] ?? "");
    $contact_number = trim($_POST['contact_number'] ?? "");
    $fee = $_POST['fee'] ?? "0.00";

    $mode = $_POST['mode'] ?? "";
    $remarks = trim($_POST['remarks'] ?? "");
    $max_participants = intval($_POST['max_participants'] ?? 0);
    $visibility = $_POST['visibility'] ?? "Public";

    $sqlOld = "SELECT poster_path, poster2_path, poster3_path, poster4_path FROM events WHERE event_id = ?";
    $stmtOld = $conn->prepare($sqlOld);
    $stmtOld->bind_param("i", $event_id);
    $stmtOld->execute();
    $old = $stmtOld->get_result()->fetch_assoc();
    $stmtOld->close();

    $poster1_name = $old['poster_path'] ?? null;
    $poster2_name = $old['poster2_path'] ?? null;
    $poster3_name = $old['poster3_path'] ?? null;
    $poster4_name = $old['poster4_path'] ?? null;

    // Upload dir
    $upload_dir = ROOT_PATH . '/uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    //  Poster 1 (optional replace)
    if (isset($_FILES['poster1']) && $_FILES['poster1']['error'] == 0) {
        $poster1_name = time() . '_1_' . basename($_FILES["poster1"]["name"]);
        move_uploaded_file($_FILES["poster1"]["tmp_name"], $upload_dir . $poster1_name);
    }

    //  Poster 2 (optional replace)
    if (isset($_FILES['poster2']) && $_FILES['poster2']['error'] == 0) {
        $poster2_name = time() . '_2_' . basename($_FILES["poster2"]["name"]);
        move_uploaded_file($_FILES["poster2"]["tmp_name"], $upload_dir . $poster2_name);
    }

    //  Poster 3 (optional replace)
    if (isset($_FILES['poster3']) && $_FILES['poster3']['error'] == 0) {
        $poster3_name = time() . '_3_' . basename($_FILES["poster3"]["name"]);
        move_uploaded_file($_FILES["poster3"]["tmp_name"], $upload_dir . $poster3_name);
    }

    //  Poster 4 (optional replace)
    if (isset($_FILES['poster4']) && $_FILES['poster4']['error'] == 0) {
        $poster4_name = time() . '_4_' . basename($_FILES["poster4"]["name"]);
        move_uploaded_file($_FILES["poster4"]["tmp_name"], $upload_dir . $poster4_name);
    }

    //  Update event
$sql = "UPDATE events SET
            event_name = ?,
            description = ?,
            category_id = ?,
            venue = ?,
            event_date = ?,
            event_time = ?,
            registration_close_date = ?,
            contact_person = ?,
            contact_number = ?,
            fee = ?,
            mode = ?,
            remarks = ?,
            poster_path = ?,
            poster2_path = ?,
            poster3_path = ?,
            poster4_path = ?,
            max_participants = ?,
            visibility = ?
        WHERE event_id = ?";

$stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssissssssds" . "sssssi" . "si",
        $event_name,
        $description,
        $category_id,
        $venue,
        $event_date,
        $event_time,
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
        $max_participants,
        $visibility,
        $event_id
    );

    if ($stmt->execute()) {
        $message = " Event updated successfully.";
    } else {
        $message = " Error updating event: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SOE - Edit Event</title>

  <link rel="stylesheet" href="<?= BASE_PATH_CSS ?>admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <?php include(ROOT_PATH_ADMIN . 'include/header.php'); ?>
  <div class="admin-container">
  <?php include(ROOT_PATH_ADMIN . 'include/sidebar.php'); ?>
    <main class="main-content" id="main-content">
      <h2>Edit Event</h2>
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

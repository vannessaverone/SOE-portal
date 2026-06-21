<?php
session_start();
require_once __DIR__ . '/../../config.php'; //global config

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
$event_id = intval($_GET['id']);

// 2. Fetch event and owner ID
$sql = "SELECT * FROM events WHERE event_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

// 3. Ownership Verification: Admin bypasses; others must be the creator
if (!$event || ($role !== 'Admin' && $event['created_by'] != $user_id)) {
    die("<script>alert('Unauthorized access: You do not own this event.'); window.location='manage.php';</script>");
}

// --- Validate and fetch event by ID ---
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request. No event ID provided.");
}

$event_id = intval($_GET['id']);

$sql = "SELECT * FROM events WHERE event_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Event not found.");
}

$event = $result->fetch_assoc();

// --- Fetch all categories for dropdown ---
$categories = [];
$cat_sql = "SELECT category_id, categoryName FROM event_category ORDER BY categoryName ASC";
$cat_result = $conn->query($cat_sql);

if ($cat_result && $cat_result->num_rows > 0) {
    while ($row = $cat_result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Event - SOE Admin</title>

  <link rel="stylesheet" href="<?= BASE_PATH_CSS ?>admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <?php include(ROOT_PATH_ADMIN . 'include/header.php'); ?>
  
  <div class="admin-container">
    <!-- Include Sidebar -->
    <?php include(ROOT_PATH_ADMIN . 'include/sidebar.php'); ?>

    <!-- Main Content -->
    <main class="main-content" id="main-content">
      <h2>Edit Event</h2>

      <form action="edit_action.php" method="POST" enctype="multipart/form-data">
  <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">

  <div>
    <label for="event_name">Event Name</label>
    <input type="text" id="event_name" name="event_name"
           value="<?php echo htmlspecialchars($event['event_name']); ?>" required />
  </div>

  <div>
    <label for="description">Description</label>
    <textarea id="description" name="description" required><?php echo htmlspecialchars($event['description']); ?></textarea>
  </div>

  <div>
    <label for="category_id">Category</label>
    <select id="category_id" name="category_id" required>
      <option value="">-- Select Category --</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= htmlspecialchars($cat['category_id']) ?>"
          <?= ($event['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($cat['categoryName']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div>
    <label for="venue">Venue</label>
    <input type="text" id="venue" name="venue"
           value="<?php echo htmlspecialchars($event['venue']); ?>" required />
  </div>

  <div>
    <label for="date">Date</label>
    <input type="date" id="date" name="date"
           value="<?php echo htmlspecialchars($event['event_date']); ?>" required />
  </div>

  <div>
    <label for="time">Time</label>
    <input type="time" id="time" name="time"
           value="<?php echo htmlspecialchars($event['event_time'] ?? ''); ?>" required />
  </div>


  <div>
    <label for="registration_close_date">Registration Close Date</label>
    <input type="date" id="registration_close_date" name="registration_close_date"
           value="<?php echo htmlspecialchars($event['registration_close_date'] ?? ''); ?>" required />
  </div>


  <div>
    <label for="contact_person">Contact Person</label>
    <input type="text" id="contact_person" name="contact_person"
           value="<?php echo htmlspecialchars($event['contact_person'] ?? ''); ?>" required />
  </div>

  <div>
    <label for="contact_number">Contact Number</label>
    <input type="text" id="contact_number" name="contact_number"
           value="<?php echo htmlspecialchars($event['contact_number'] ?? ''); ?>" required />
  </div>

  <div>
    <label for="fee">Fee (RM)</label>
    <input type="number" id="fee" name="fee" min="0" step="0.01"
           value="<?php echo htmlspecialchars($event['fee'] ?? '0.00'); ?>" required />
    <small style="display:block; color:#666;">Set to 0 for free event.</small>
  </div>

  <div>
    <label for="mode">Mode</label>
    <select id="mode" name="mode" required>
      <?php
      $modes = ['Physical', 'Online', 'Hybrid'];
      foreach ($modes as $m) {
        $selected = ($event['mode'] === $m) ? 'selected' : '';
        echo "<option value='$m' $selected>$m</option>";
      }
      ?>
    </select>
  </div>

  <div>
    <label for="remarks">Remarks / Notes</label>
    <textarea id="remarks" name="remarks"><?php echo htmlspecialchars($event['remarks']); ?></textarea>
  </div>

  <div>
    <label for="max_participants">Maximum Participants (0 for unlimited)</label>
    <input type="number" id="max_participants" name="max_participants"
           value="<?php echo $event['max_participants']; ?>" min="0">
  </div>

  <div>
    <label for="visibility">Event Visibility</label>
    <select id="visibility" name="visibility" required>
      <option value="Public" <?= ($event['visibility'] === 'Public') ? 'selected' : '' ?>>
        Public (Visible to everyone)
      </option>
      <option value="University" <?= ($event['visibility'] === 'University') ? 'selected' : '' ?>>
        University Only (Login Required)
      </option>
    </select>
  </div>

  <div>
    <label>Poster 1 (Required)</label><br>
    <?php if (!empty($event['poster_path'])): ?>
      <img src="<?= BASE_PATH_UPLOADS . htmlspecialchars($event['poster_path']); ?>" width="150" style="margin-bottom:10px;"><br>
    <?php endif; ?>
    <input type="file" name="poster1" accept="image/*">
    <small>Leave blank to keep existing Poster 1</small>
  </div>

  <div>
    <label>Poster 2 (Optional)</label><br>
    <?php if (!empty($event['poster2_path'])): ?>
      <img src="<?= BASE_PATH_UPLOADS . htmlspecialchars($event['poster2_path']); ?>" width="150" style="margin-bottom:10px;"><br>
    <?php endif; ?>
    <input type="file" name="poster2" accept="image/*">
    <small>Leave blank to keep existing Poster 2</small>
  </div>

  <div>
    <label>Poster 3 (Optional)</label><br>
    <?php if (!empty($event['poster3_path'])): ?>
      <img src="<?= BASE_PATH_UPLOADS . htmlspecialchars($event['poster3_path']); ?>" width="150" style="margin-bottom:10px;"><br>
    <?php endif; ?>
    <input type="file" name="poster3" accept="image/*">
    <small>Leave blank to keep existing Poster 3</small>
  </div>

  <div>
    <label>Poster 4 (Optional)</label><br>
    <?php if (!empty($event['poster4_path'])): ?>
      <img src="<?= BASE_PATH_UPLOADS . htmlspecialchars($event['poster4_path']); ?>" width="150" style="margin-bottom:10px;"><br>
    <?php endif; ?>
    <input type="file" name="poster4" accept="image/*">
    <small>Leave blank to keep existing Poster 4</small>
  </div>

  <button type="submit">Update Event</button>
</form>

      
    </main>
  </div>

  <?php
     include(ROOT_PATH_ADMIN ."include/footer.php");
  ?> 

  <script>
    document.querySelectorAll('.submenu-toggle').forEach(btn => {
      btn.addEventListener('click', () => {
        btn.nextElementSibling.classList.toggle('show');
      });
    });
  </script>
</body>
</html>

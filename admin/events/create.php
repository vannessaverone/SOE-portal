<?php
// Always include config first
require_once __DIR__ . '/../../config.php'; //global config
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initialscale=1.0" />
    <title>SOE - Create Event</title>
    <!-- Use BASE_PATH_CSS constant -->
    <link rel="stylesheet" href="<?= BASE_PATH_CSS ?>admin.css">
    <link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/fontawesome/6.5.0/css/all.min.css">

</head>

<body>
    <!-- ======= Hero Section ======= -->
    <?php include(ROOT_PATH_ADMIN . 'include/header.php'); ?>

    <div class="admin-container">
    <!-- Include Sidebar -->
    <?php include(ROOT_PATH_ADMIN . 'include/sidebar.php'); ?>

    <!-- Main Content -->
    <main class="main-content" id="main-content">
            <h2>
                <?php echo (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin') ? "Welcome to the SOE Admin Dashboard" : ""; ?>
            </h2>
            <p style="text-align:center;">
                <?php echo (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin') ? "Select a menu item to view or manage content." : ""; ?>
            </p>
       <h2>Create Event</h2>

<form action="create_action.php" method="POST" enctype="multipart/form-data">

    <div>
        <label for="event_name">Event Name</label>
        <input type="text" id="event_name" name="event_name" required />
    </div>

    <div>
        <label for="description">Description</label>
        <textarea id="description" name="description" required></textarea>
    </div>

    <div>
        <label for="category">Type</label>
        <select id="category" name="category" required>
            <option value="">-- Select Type --</option>
            <option value="1">Workshop</option>
            <option value="2">Seminar</option>
            <option value="3">Competition</option>
            <option value="4">Festival</option>
            <option value="5">Sport</option>
            <option value="6">Course</option>
        </select>
    </div>

    <div>
        <label for="venue">Venue</label>
        <input type="text" id="venue" name="venue" required />
    </div>

    <div>
        <label for="date">Date</label>
        <input type="date" id="date" name="date" required />
    </div>

    <div>
        <label for="time">Time</label>
        <input type="time" id="time" name="time" required />
    </div>

    <div>
        <label for="registration_close_date">Registration Close Date</label>
        <input type="date" id="registration_close_date" name="registration_close_date" required />
    </div>

    <div>
        <label for="contact_person">Contact Person</label>
        <input type="text" id="contact_person" name="contact_person" required />
    </div>

    <div>
        <label for="contact_number">Contact Number</label>
        <input type="text" id="contact_number" name="contact_number" required />
    </div>
    <div>
        <label for="fee">Fee (RM)</label>
        <input type="number" id="fee" name="fee" min="0" step="0.01" value="0.00" required />
        <small style="display:block; color:#666;">Set to 0 for free event.</small>
    </div>

    <div>
        <label for="mode">Mode</label>
        <select id="mode" name="mode" required>
            <option value="">-- Select Mode --</option>
            <option>Physical</option>
            <option>Online</option>
            <option>Hybrid</option>
        </select>
    </div>

    <div>
        <label for="remarks">Remarks / Notes</label>
        <textarea id="remarks" name="remarks"></textarea>
    </div>

    <div>
        <label for="max_participants">Maximum Participants (0 for unlimited)</label>
        <input type="number" id="max_participants" name="max_participants" min="0" value="0">
    </div>

    <div>
        <label for="visibility">Event Visibility</label>
        <select id="visibility" name="visibility" required>
            <option value="Public">Public (Visible to everyone)</option>
            <option value="University">University Only (Login Required)</option>
        </select>
    </div>
    <div>
        <label for="poster1">Event Poster 1 (Required)</label>
        <input type="file" id="poster1" name="poster1" accept="image/*" required />
    </div>

    <div>
        <label for="poster2">Event Poster 2 (Optional)</label>
        <input type="file" id="poster2" name="poster2" accept="image/*" />
    </div>

    <div>
        <label for="poster3">Event Poster 3 (Optional)</label>
        <input type="file" id="poster3" name="poster3" accept="image/*" />
    </div>

    <div>
        <label for="poster4">Event Poster 4 (Optional)</label>
        <input type="file" id="poster4" name="poster4" accept="image/*" />
    </div>

    <button type="submit">Create Event</button>
</form>


    </main>

    </div>
    <?php
     include(ROOT_PATH_ADMIN ."include/footer.php");
  ?> 
  <script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".submenu-toggle").forEach((btn) => {
        btn.addEventListener("click", () => {
            // toggle button style
            btn.classList.toggle("active");

            // toggle submenu open/close
            const submenuContent = btn.nextElementSibling;
            if (submenuContent) {
                submenuContent.classList.toggle("active");
            }
        });
    });
});
</script>

    
</body>
</html>

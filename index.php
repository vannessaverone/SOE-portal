<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_write_close(); 
require_once __DIR__ . '/config.php';

//  Only show approved events
$visibility_clause = " AND admin_status = 'Approved'";

//  Public users only see Public events
if (!isset($_SESSION['user_id'])) {
    $visibility_clause .= " AND visibility = 'Public'";
}

//  Category filter (optional)
if (isset($_GET['cat']) && $_GET['cat'] != '0') {
    $cat_id = intval($_GET['cat']);
    $sql = "SELECT * FROM events WHERE category_id = $cat_id" . $visibility_clause;
} else {
    $sql = "SELECT * FROM events WHERE 1=1" . $visibility_clause;
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Organization Event Portal (SOE Portal)</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <!-- ======= Header Section ======= -->
  <?php include("include/header.php");?>  

  <!-- ======= Top Navigation ======= -->
  <?php include("include/topNav.php");?>  

  <main>
    <section class="intro">
      <h2>Welcome to SOE</h2>
      <p>
        <?php echo (isset($_SESSION['user_role'])) ? "" : " This system helps you manage and explore upcoming campus events efficiently.
        Register or log in to get started."; ?>
       
      </p>
    </section>
  <!-- ======= after login ======= -->
    <?php if (isset($_SESSION['user_id'])): ?>
  <section class="listing" style="text-align:center;">
    <a href="<?= BASE_URL ?>/auth/dashboard.php" class="btn">
      <i class="fas fa-chart-line"></i> Open Dashboard
    </a>
  </section>
<?php endif; ?>


    <section class="listing">
      <h2>Event Listing</h2>
        <div class="filter-container">
		      <table border="1" align="center">
            <tr>
              <td><a href="index.php?cat=0">All</a></td>
              <td><a href="index.php?cat=1">Workshop</a></td>
              <td><a href="index.php?cat=2">Seminar</a></td>
              <td><a href="index.php?cat=3">Competition</a></td>
              <td><a href="index.php?cat=4">Festival</a></td>
              <td><a href="index.php?cat=5">Sport</a></td>
              <td><a href="index.php?cat=6">Course</a></td>
            </tr>

		      </table>
		    </div>
		    <div>
		      <table border='1' width="90%" id = "event_table">
		        <tr>
              <?php
              $count = 0;
              if ($result && $result->num_rows > 0) {

                while ($row = $result->fetch_assoc()) {
                  $poster = htmlspecialchars($row['poster_path']);
                  $name = htmlspecialchars($row['event_name']);
                  $event_id = $row['event_id'];
                  if ($count > 0 && $count % 3 == 0) {
                    echo "</tr><tr>";
                  }
                  echo "<td align='center'>";

                  if (isset($_SESSION['user_id'])) {
                    $link = "auth/event_details.php?id=$event_id";
                    $onclick = "";
                  } else {
                    $link = "auth/login.php";
                    $onclick = "";
                  }
                  echo "<a href='$link' $onclick style='text-decoration:none; color:inherit; cursor:pointer;'>";
                  echo "<img src='" . BASE_URL . "/uploads/" . $poster . "' alt='$name' style='width:200px; height:auto; border-radius:8px; transition: transform 0.2s;' onmouseover='this.style.transform=\"scale(1.05)\"' onmouseout='this.style.transform=\"scale(1)\"'><br>";
                  echo "<b style='display:block; margin-top:10px;'>$name</b>";
                  echo "</a>";

                  $count++;
                }
              } else {
                  echo "<td colspan='3' align='center'>No events available.</td>";
              }

              if (isset($result)) {
                mysqli_free_result($result);
              }
              ?>
            </tr>

		      </table>
		    </div>
        </section>
  </main>

  <?php
     include("include/footer.php");
  ?>  

  <script>
    // Toggle mobile menu
    const menuIcon = document.getElementById('menu-icon');
    const navLinks = document.getElementById('nav-links');
    menuIcon.onclick = () => navLinks.classList.toggle('active');
  </script>
</body>
</html>

<?php
// Include global configuration
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Campus Event Management System (SOE)</title>
  <link rel="stylesheet" href="<?php echo BASE_PATH_CSS; ?>style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <!-- ======= Hero Section ======= -->
  <?php include ROOT_PATH . '/include/header.php'; ?>  

  <!-- ======= Top Navigation ======= -->
  <?php include ROOT_PATH . '/include/topNav.php'; ?> 

  <main>
    <section class="section-content">
      <h3>Register</h3>
      <form action="register_action.php" method="post" name="registerForm">
        <fieldset>
          <legend>Category</legend>
          <label>
            <input type="radio" name="category" value="staff" required>
            Staff
          </label>
          <label>
            <input type="radio" name="category" value="student">
            Student
          </label>
          <label>
            <input type="radio" name="category" value="public">
            Public
          </label>
        </fieldset>

        <div>
          <label for="name">Full Name</label><br>
          <input type="text" id="name" name="name" required autocomplete="name" placeholder="Your Full Name">
        </div>

        <div>
          <label for="email">Email</label><br>
          <input type="email" id="email" name="email" required autocomplete="email" placeholder="Your Email Address">
        </div>

        <div>
          <label for="phone">Phone</label><br>
          <input type="tel" id="phone" name="phone" required autocomplete="tel" placeholder="your phone number">
        </div>    
        
        <div>
            <label>Organization:</label>
            <input type="text" name="organization" required placeholder="Your Organization">
        </div>

        <div>
          <label for="password">Password</label><br>
          <input type="password" id="password" name="password" required minlength="6" autocomplete="new-password" placeholder="Choose a password">
        </div>

        <div>
            <label>Recommend event about:</label><br>
            <input type="checkbox" name="event[]" value="workshop"> Workshop<br>
            <input type="checkbox" name="event[]" value="seminar"> Seminar<br>
            <input type="checkbox" name="event[]" value="competition"> Competition<br>
            <input type="checkbox" name="event[]" value="festival"> Festival<br>
            <input type="checkbox" name="event[]" value="sport"> Sport<br>
            <input type="checkbox" name="event[]" value="course"> Course<br>
        </div>

        <div>
          <button type="submit">Register</button>
          <button type="reset">Reset</button>
        </div>
      </form>
      <p id="output"></p>
    </section>
  </main>

  <?php include ROOT_PATH . '/include/footer.php'; ?> 

  <script>
    // Toggle mobile menu
    const menuIcon = document.getElementById('menu-icon');
    const navLinks = document.getElementById('nav-links');
    menuIcon.onclick = () => navLinks.classList.toggle('active');

    // Form Validation
    document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");    

      form.addEventListener("submit", function (e) {
        const checkboxes = document.querySelectorAll('input[name="event[]"]');
        let checked = false;

        // Check if at least one checkbox is selected
        for (const box of checkboxes) {
          if (box.checked) {
            checked = true;
            break;
          }
        }

        if (!checked) {
          e.preventDefault(); // Stop form submission
          alert("Please select at least one recommended event.");
          const output = document.getElementById("output");
          output.style.color = "red";
          output.textContent = `Please select at least one recommended event.`;
          return;
        }    

        this.submit();

      });
    }); 
  </script>
</body>
</html>

<?php
session_start();
require_once __DIR__ . '/../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Retrieve user data based on unique email
    $sql = "SELECT user_id, name, password, role, status FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Verify the hashed password against user input
        if (password_verify($password, $user['password'])) {
            // Regenerate session ID for security
            session_regenerate_id(true);

            // Set session variables for use in dashboards [cite: 2025-12-11]
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['status'] === 'Suspended') {
                echo "<script>alert('Your account is suspended. Please contact Admin.'); window.location='login.php';</script>";
                exit();
            }
            // Role-based redirection
            if ($user['role'] === 'Admin') {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../index.php");
            }
            exit();
        } else {
            echo "<script>alert('Invalid password'); window.location=' ../auth/login.php';</script>";
        }
    } else {
        echo "<script>alert('No account found with that email'); window.location=' ../auth/login.php';</script>";
    }
    $stmt->close();
}


$conn->close();
?>
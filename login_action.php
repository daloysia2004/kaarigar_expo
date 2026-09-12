<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Query user by email
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($password, $user['password'])) {
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];

            // Universal Redirection based on role
            if ($user['role'] === 'admin') {
                header("Location: admin.php");
                exit();
            } elseif ($user['role'] === 'kaarigar') {
                header("Location: kaarigar_dashboard.php");
                exit();
            } else { // Visitor
                header("Location: index.php");
                exit();
            }

        } else {
            echo "<script>alert('Invalid password.'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('No account found with this email.'); window.location.href='login.php';</script>";
    }
}
?>
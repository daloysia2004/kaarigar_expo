<?php
session_start();
include 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE LOWER(TRIM(email)) = LOWER(TRIM(?))");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $user_id = $user['id'];
            $stored_hash = $user['password'];

            $is_valid = false;

            // 1. Check standard BCrypt hash
            if (password_verify($password, $stored_hash)) {
                $is_valid = true;
            } 
            // 2. Fallback check for unhashed plain text passwords (and convert them to secure hashes)
            elseif ($password === $stored_hash) {
                $is_valid = true;
                $new_hash = password_hash($password, PASSWORD_BCRYPT);
                $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $new_hash, $user_id);
                $update_stmt->execute();
                $update_stmt->close();
            }

            if ($is_valid) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name']    = $user['name'];
                $_SESSION['email']   = $user['email'];
                $_SESSION['role']    = strtolower(trim($user['role']));

                if ($_SESSION['role'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                // Wrong password entered
                $error = "Invalid email or password.";
            }
        } else {
            // Email not found
            $error = "Invalid email or password.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Kaarigar Expo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

    <div class="card border-0 shadow-sm p-4" style="width: 100%; max-width: 420px;">
        <div class="text-center mb-3">
            <h3 class="fw-bold">🎨 Welcome Back</h3>
            <p class="text-muted small">Login to access your Kaarigar Expo account</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="name@example.com">
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Password</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-danger w-100 py-2 mt-2">Sign In</button>
        </form>

        <div class="text-center mt-3">
            <span class="text-muted small">Don't have an account? </span>
            <a href="register.php" class="small text-decoration-none">Register here</a>
        </div>
    </div>

</body>
</html>
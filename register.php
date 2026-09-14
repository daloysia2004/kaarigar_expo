<?php
session_start();
include 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    // Fallback/normalize role input to lowercase 'visitor' if empty or invalid
    $role     = strtolower(trim($_POST['role'] ?? 'visitor'));
    if (!in_array($role, ['visitor', 'kaarigar'])) {
        $role = 'visitor';
    }

    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error = "Please fill in all required fields.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "This email is already registered. Please login.";
            $stmt->close();
        } else {
            $stmt->close();
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insert into users table
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;
                $stmt->close();

                // If registering as a Kaarigar, save their extra profile details
                if ($role === 'kaarigar') {
                    $craft_type  = trim($_POST['craft_type'] ?? '');
                    $description = trim($_POST['description'] ?? '');
                    $photo_path  = '';

                    // Handle profile photo upload
                    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                        $target_dir = __DIR__ . "/uploads/";
                        if (!file_exists($target_dir)) {
                            mkdir($target_dir, 0777, true);
                        }
                        $filename    = time() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($_FILES["photo"]["name"]));
                        $destination = $target_dir . $filename;

                        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $destination)) {
                            $photo_path = "uploads/" . $filename;
                        }
                    }

                    // Insert into kaarigar_profiles
                    $stmt_profile = $conn->prepare("INSERT INTO kaarigar_profiles (user_id, craft_type, description, photo) VALUES (?, ?, ?, ?)");
                    $stmt_profile->bind_param("isss", $user_id, $craft_type, $description, $photo_path);
                    $stmt_profile->execute();
                    $stmt_profile->close();
                }

                $success = "Registration successful! You can now <a href='login.php' class='alert-link'>Login here</a>.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Kaarigar Expo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navigation -->
    <nav class="navbar navbar-dark bg-dark px-4">
        <a class="navbar-brand fw-bold" href="index.php">🎨 Kaarigar Expo</a>
        <div>
            <a href="login.php" class="btn btn-outline-light btn-sm me-2">Login</a>
            <a href="register.php" class="btn btn-warning btn-sm">Register</a>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm p-4">
                    <h4 class="fw-bold text-center mb-3">Create an Account</h4>
                    <p class="text-muted text-center small mb-4">Join as a Visitor or a Kaarigar (Artisan)</p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2 small"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success py-2 small"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form action="register.php" method="POST" enctype="multipart/form-data">
                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control" required placeholder="Enter your full name">
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control" required placeholder="name@example.com">
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="Create a password">
                        </div>

                        <!-- Role Selection -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">I want to register as:</label>
                            <select name="role" id="roleSelect" class="form-select" onchange="toggleKaarigarFields()" required>
                                <option value="visitor" selected>Visitor</option>
                                <option value="kaarigar">Kaarigar (Artisan / Craftsman)</option>
                            </select>
                        </div>

                        <!-- Kaarigar Specific Fields (Hidden by default) -->
                        <div id="kaarigarFields" class="d-none border-top pt-3 mt-3">
                            <h6 class="fw-bold text-secondary mb-3">Kaarigar Profile Details</h6>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Craft Type / Specialty</label>
                                <input type="text" name="craft_type" class="form-control" placeholder="e.g. Pottery, Music, Terracotta, Embroidery">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Profile / Artisan Photo</label>
                                <input type="file" name="photo" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Short Bio / Description</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Tell us about your work..."></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 mt-2">Register</button>
                    </form>

                    <div class="text-center mt-3">
                        <span class="text-muted small">Already have an account? </span>
                        <a href="login.php" class="small text-decoration-none">Login here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toggle Script for Kaarigar Profile Fields -->
    <script>
        function toggleKaarigarFields() {
            const role = document.getElementById('roleSelect').value;
            const kaarigarFields = document.getElementById('kaarigarFields');
            
            if (role === 'kaarigar') {
                kaarigarFields.classList.remove('d-none');
            } else {
                kaarigarFields.classList.add('d-none');
            }
        }
    </script>
</body>
</html>
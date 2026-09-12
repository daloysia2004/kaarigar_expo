<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role     = trim($_POST['role']);

    if ($role === 'admin') {
        die("Unauthorized role registration.");
    }

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;

        if ($role === 'kaarigar') {
            $craft_type  = trim($_POST['craft_type']);
            $description = trim($_POST['description']);
            $photo_path  = null;

            if (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK) {
                $target_dir = "uploads/";
                
                // Create directory if it doesn't exist
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }

                // Sanitize file name to prevent duplicate overwrites or breaking characters
                $file_basename = preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($_FILES["photo_file"]["name"]));
                $filename    = time() . "_" . $file_basename;
                $target_file = $target_dir . $filename;

                if (move_uploaded_file($_FILES["photo_file"]["tmp_name"], $target_file)) {
                    $photo_path = $target_file;
                }
            }

            $stmt_k = $conn->prepare("INSERT INTO kaarigar_profiles (user_id, craft_type, description, photo) VALUES (?, ?, ?, ?)");
            $stmt_k->bind_param("isss", $user_id, $craft_type, $description, $photo_path);
            $stmt_k->execute();
            $stmt_k->close();
        }

        header("Location: login.php?msg=registered");
        exit();
    } else {
        echo "Registration error: " . $conn->error;
    }
    $stmt->close();
}
?>
<?php
session_start();
include 'db.php';

// Enable error reporting for debugging SQL issues if needed
mysqli_report(MYSQLI_REPORT_OFF);

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$action = $_GET['action'] ?? '';

if ($action === 'create_mela' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']);
    $location    = trim($_POST['location']);
    $event_date  = trim($_POST['event_date']);
    $description = trim($_POST['description']);
    $image_path  = "";

    // Handle File Upload
    if (isset($_FILES['mela_image']) && $_FILES['mela_image']['error'] === UPLOAD_ERR_OK) {
        // Use absolute directory path to prevent "No such file or directory" warning
        $target_dir = __DIR__ . "/uploads/";
        
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $filename    = time() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($_FILES["mela_image"]["name"]));
        $destination = $target_dir . $filename;

        if (move_uploaded_file($_FILES["mela_image"]["tmp_name"], $destination)) {
            $image_path = "uploads/" . $filename;
        }
    }

    // Prepare statement with error handling
    // Note: If your table column is 'image_url' instead of 'image', change 'image' below
    $stmt = $conn->prepare("INSERT INTO melas (title, location, event_date, image, description) VALUES (?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        // Fallback check if column is named 'image_url'
        $stmt = $conn->prepare("INSERT INTO melas (title, location, event_date, image_url, description) VALUES (?, ?, ?, ?, ?)");
    }

    if ($stmt) {
        $stmt->bind_param("sssss", $title, $location, $event_date, $image_path, $description);
        $stmt->execute();
        $stmt->close();
        header("Location: admin.php?msg=created");
        exit();
    } else {
        die("Database Error: " . $conn->error);
    }
}

if ($action === 'update_status') {
    $id     = $_GET['id'] ?? 0;
    $status = $_GET['status'] ?? '';

    if ($id && in_array($status, ['approved', 'rejected'])) {
        $stmt = $conn->prepare("UPDATE mela_applications SET status = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $status, $id);
            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: admin.php");
    exit();
}
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['mela_id'])) {
    $mela_id = intval($_GET['mela_id']);
    $visitor_id = $_SESSION['user_id'];

    // Check if visitor already registered for this specific event
    $check = $conn->prepare("SELECT id FROM visitor_rsvps WHERE mela_id = ? AND visitor_id = ?");
    $check->bind_param("ii", $mela_id, $visitor_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO visitor_rsvps (mela_id, visitor_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $mela_id, $visitor_id);
        if ($stmt->execute()) {
            $_SESSION['msg'] = "Registered for that event successfully!";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['msg'] = "Failed to register. Please try again.";
            $_SESSION['msg_type'] = "danger";
        }
        $stmt->close();
    } else {
        $_SESSION['msg'] = "You have already registered for this event.";
        $_SESSION['msg_type'] = "warning";
    }
    $check->close();
}

header("Location: index.php");
exit();
?>




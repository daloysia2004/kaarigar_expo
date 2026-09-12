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

    $check = $conn->prepare("SELECT id FROM visitor_rsvps WHERE mela_id = ? AND visitor_id = ?");
    $check->bind_param("ii", $mela_id, $visitor_id);
    $check->execute();

    if ($check->get_result()->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO visitor_rsvps (mela_id, visitor_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $mela_id, $visitor_id);
        $stmt->execute();
    }
}

header("Location: index.php?msg=rsvp_success");
exit();
?>
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kaarigar') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle application submission
if (isset($_POST['apply_mela_id'])) {
    $mela_id = intval($_POST['apply_mela_id']);
    
    // Check if already applied
    $check = $conn->prepare("SELECT id FROM mela_applications WHERE mela_id = ? AND kaarigar_id = ?");
    $check->bind_param("ii", $mela_id, $user_id);
    $check->execute();
    if ($check->get_result()->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO mela_applications (mela_id, kaarigar_id, status) VALUES (?, ?, 'pending')");
        $stmt->bind_param("ii", $mela_id, $user_id);
        $stmt->execute();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kaarigar Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark px-4">
        <a class="navbar-brand fw-bold" href="index.php">🪔 Kaarigar Portal</a>
        <div>
            <span class="text-light me-3">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container py-4">
        <h3 class="fw-bold mb-4">My Event Applications</h3>
        
        <div class="card p-3 shadow-sm mb-4">
            <h5>Available Events</h5>
            <div class="row g-3">
                <?php
                $melas = $conn->query("SELECT * FROM melas ORDER BY event_date ASC");
                while ($m = $melas->fetch_assoc()):
                ?>
                <div class="col-md-4">
                    <div class="card h-100 p-3">
                        <h6><?php echo htmlspecialchars($m['title']); ?></h6>
                        <p class="text-muted small">📍 <?php echo htmlspecialchars($m['location']); ?><br>📅 <?php echo $m['event_date']; ?></p>
                        <form method="POST">
                            <input type="hidden" name="apply_mela_id" value="<?php echo $m['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm w-100">Apply to Exhibit</button>
                        </form>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="card p-3 shadow-sm">
            <h5>Application History</h5>
            <table class="table align-middle">
                <thead>
                    <tr><th>Event Name</th><th>Applied On</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php
                    $app_sql = "SELECT m.title, ma.applied_at, ma.status 
                                FROM mela_applications ma 
                                JOIN melas m ON ma.mela_id = m.id 
                                WHERE ma.kaarigar_id = '$user_id'";
                    $res = $conn->query($app_sql);
                    while ($r = $res->fetch_assoc()):
                        $badge = $r['status'] == 'approved' ? 'bg-success' : ($r['status'] == 'rejected' ? 'bg-danger' : 'bg-warning text-dark');
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['title']); ?></td>
                        <td><?php echo date('d M Y', strtotime($r['applied_at'])); ?></td>
                        <td><span class="badge <?php echo $badge; ?>"><?php echo ucfirst($r['status']); ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
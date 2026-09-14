<?php
session_start();
include 'db.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all uploaded melas/events
$melas = $conn->query("SELECT * FROM melas ORDER BY event_date DESC");

// Fetch registered Kaarigars with their craft details
$kaarigars = $conn->query("SELECT u.name, u.email, kp.craft_type, kp.description, kp.photo 
                            FROM users u 
                            LEFT JOIN kaarigar_profiles kp ON u.id = kp.user_id 
                            WHERE LOWER(u.role) = 'kaarigar' 
                            ORDER BY u.created_at DESC");


// Fetch registered Visitors with Event RSVPs
$visitors_query = "SELECT 
                    u.id, 
                    u.name, 
                    u.email, 
                    u.created_at, 
                    GROUP_CONCAT(m.title SEPARATOR ', ') AS event_names
                   FROM users u
                   LEFT JOIN visitor_rsvps vr ON u.id = vr.visitor_id
                   LEFT JOIN melas m ON vr.mela_id = m.id
                   WHERE LOWER(u.role) = 'visitor' 
                      OR u.role IS NULL 
                      OR u.role = ''
                   GROUP BY u.id
                   ORDER BY u.id DESC";

$visitors = $conn->query($visitors_query);

// Fetch mela stall applications
$applications = $conn->query("SELECT ma.id, u.name AS kaarigar_name, m.title AS mela_title, ma.status, ma.applied_at 
                              FROM mela_applications ma 
                              JOIN users u ON ma.kaarigar_id = u.id 
                              JOIN melas m ON ma.mela_id = m.id 
                              ORDER BY ma.applied_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Kaarigar Expo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark px-4 shadow-sm">
        <a class="navbar-brand fw-bold" href="admin.php">⚙️ Admin Dashboard</a>
        <div>
            <span class="text-light me-3">Welcome, <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong></span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container py-4">
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'created'): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <strong>Success!</strong> Event created successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Dashboard Navigation Tabs -->
        <ul class="nav nav-pills mb-4 bg-white p-2 rounded shadow-sm" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold" id="add-event-tab" data-bs-toggle="tab" data-bs-target="#add-event" type="button" role="tab">➕ Add New Mela</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button" role="tab">🎪 Uploaded Melas</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="kaarigars-tab" data-bs-toggle="tab" data-bs-target="#kaarigars" type="button" role="tab">🎨 Registered Kaarigars</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="visitors-tab" data-bs-toggle="tab" data-bs-target="#visitors" type="button" role="tab">👤 Registered Visitors</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="applications-tab" data-bs-toggle="tab" data-bs-target="#applications" type="button" role="tab">📝 Stall Applications</button>
            </li>
        </ul>

        <!-- Tab Content Panes -->
        <div class="tab-content" id="adminTabsContent">
            
            <!-- 1. Add New Mela Tab -->
            <div class="tab-pane fade show active" id="add-event" role="tabpanel">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="card border-0 shadow-sm p-4">
                            <h5 class="fw-bold mb-3 border-bottom pb-2">Create New Mela / Event</h5>
                            <form action="admin_action.php?action=create_mela" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Mela Title</label>
                                    <input type="text" name="title" class="form-control" required placeholder="Enter event name">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Location</label>
                                    <input type="text" name="location" class="form-control" required placeholder="City or venue name">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Event Date</label>
                                    <input type="date" name="event_date" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Banner Image</label>
                                    <input type="file" name="mela_image" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Description</label>
                                    <textarea name="description" class="form-control" rows="4" placeholder="Event details..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-success w-100 py-2 fw-bold">Create Event</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Uploaded Melas Tab -->
            <div class="tab-pane fade" id="events" role="tabpanel">
                <div class="card border-0 shadow-sm p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">🎪 Uploaded Melas & Events</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Banner</th>
                                    <th>Title</th>
                                    <th>Location</th>
                                    <th>Event Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($melas && $melas->num_rows > 0) {
                                    while ($m = $melas->fetch_assoc()) {
                                        $imgPath = !empty($m['image']) ? $m['image'] : (!empty($m['image_url']) ? $m['image_url'] : '');
                                        $bannerImg = !empty($imgPath) ? '<img src="'.htmlspecialchars($imgPath).'" width="70" height="45" class="rounded object-fit-cover">' : '<span class="text-muted">No Image</span>';
                                        
                                        echo '<tr>
                                            <td>'.$bannerImg.'</td>
                                            <td class="fw-bold">'.htmlspecialchars($m['title']).'</td>
                                            <td>'.htmlspecialchars($m['location']).'</td>
                                            <td>'.htmlspecialchars($m['event_date']).'</td>
                                        </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="4" class="text-center text-muted py-4">No events uploaded yet.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 3. Registered Kaarigars Tab -->
            <div class="tab-pane fade" id="kaarigars" role="tabpanel">
                <div class="card border-0 shadow-sm p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">🎨 Registered Kaarigars (Artisans)</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Craft Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($kaarigars && $kaarigars->num_rows > 0) {
                                    while ($k = $kaarigars->fetch_assoc()) {
                                        $photoImg = !empty($k['photo']) ? '<img src="'.htmlspecialchars($k['photo']).'" width="45" height="45" class="rounded-circle object-fit-cover">' : '<div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:45px; height:45px;">👤</div>';
                                        echo '<tr>
                                            <td>'.$photoImg.'</td>
                                            <td class="fw-bold">'.htmlspecialchars($k['name']).'</td>
                                            <td>'.htmlspecialchars($k['email']).'</td>
                                            <td><span class="badge bg-info text-dark">'.htmlspecialchars($k['craft_type'] ?? 'Handicraft').'</span></td>
                                        </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="4" class="text-center text-muted py-4">No Kaarigars registered yet.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 4. Registered Visitors Tab -->
            <div class="tab-pane fade" id="visitors" role="tabpanel">
                <div class="card border-0 shadow-sm p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">👤 Registered Visitors & RSVPs</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Registered Event</th>
                                    <th>Registration Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
if ($visitors && $visitors->num_rows > 0) {
    while ($v = $visitors->fetch_assoc()) {
        $regDate = !empty($v['created_at']) ? date("Y-m-d", strtotime($v['created_at'])) : 'N/A';
        
        if (!empty($v['event_names'])) {
            $eventName = '<span class="badge bg-success">'.htmlspecialchars($v['event_names']).'</span>';
        } else {
            $eventName = '<span class="badge bg-secondary">No Event RSVP</span>';
        }
        
        echo '<tr>
            <td class="fw-bold">'.htmlspecialchars($v['name']).'</td>
            <td>'.htmlspecialchars($v['email']).'</td>
            <td>'.$eventName.'</td>
            <td>'.htmlspecialchars($regDate).'</td>
        </tr>';
    }
} else {
    echo '<tr><td colspan="4" class="text-center text-muted py-4">No visitors registered yet.</td></tr>';
}
?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 5. Stall Applications Tab -->
            <div class="tab-pane fade" id="applications" role="tabpanel">
                <div class="card border-0 shadow-sm p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">📝 Mela Stall Applications</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Artisan</th>
                                    <th>Mela</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($applications && $applications->num_rows > 0) {
                                    while ($app = $applications->fetch_assoc()) {
                                        $statusClass = $app['status'] == 'approved' ? 'bg-success' : ($app['status'] == 'rejected' ? 'bg-danger' : 'bg-warning text-dark');
                                        echo '<tr>
                                            <td class="fw-bold">'.htmlspecialchars($app['kaarigar_name']).'</td>
                                            <td>'.htmlspecialchars($app['mela_title']).'</td>
                                            <td><span class="badge '.$statusClass.'">'.ucfirst($app['status']).'</span></td>
                                            <td>
                                                <a href="admin_action.php?action=update_status&id='.$app['id'].'&status=approved" class="btn btn-sm btn-outline-success me-1">Approve</a>
                                                <a href="admin_action.php?action=update_status&id='.$app['id'].'&status=rejected" class="btn btn-sm btn-outline-danger">Reject</a>
                                            </td>
                                        </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="4" class="text-center text-muted py-4">No stall applications submitted yet.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
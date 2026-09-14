<?php
session_start();
include 'db.php';

// Fetch all upcoming melas
$melas = $conn->query("SELECT * FROM melas ORDER BY event_date ASC");

// Pre-fetch all approved kaarigars grouped by mela_id
$kaarigars_by_mela = [];
$kaarigar_query = $conn->query("SELECT ma.mela_id, u.name, kp.craft_type, kp.photo 
                                FROM mela_applications ma 
                                JOIN users u ON ma.kaarigar_id = u.id 
                                LEFT JOIN kaarigar_profiles kp ON u.id = kp.user_id 
                                WHERE ma.status = 'approved'");

if ($kaarigar_query && $kaarigar_query->num_rows > 0) {
    while ($row = $kaarigar_query->fetch_assoc()) {
        $kaarigars_by_mela[$row['mela_id']][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kaarigar Expo - Heritage & Crafts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <style>
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1606744888344-493238951221?q=80&w=1200') center/cover;
            color: white;
            padding: 80px 0;
        }
        .mela-card img {
            height: 180px;
            object-fit: cover;
        }
    </style>
</head>
<body class="bg-light">

    <!-- Navigation -->
    <nav class="navbar navbar-dark bg-dark px-4">
        <a class="navbar-brand fw-bold" href="index.php">🎨 Kaarigar Expo</a>
        <div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="text-light me-3">Hi, <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong></span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-light btn-sm me-2">Login</a>
                <a href="register.php" class="btn btn-warning btn-sm">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section text-center">
        <div class="container">
            <h1 class="display-5 fw-bold mb-3">Experience authentic handmade crafts directly from verified Kaarigars.</h1>
            <div>
                <a href="#events-section" class="btn btn-danger me-2">Explore Melas</a>
                <a href="register.php" class="btn btn-outline-light">Join as Kaarigar</a>
            </div>
        </div>
    </div>

    <!-- Alert Notification Banner -->
    <?php if (isset($_SESSION['msg'])): ?>
        <div class="container mt-3">
            <div class="alert alert-<?php echo isset($_SESSION['msg_type']) ? $_SESSION['msg_type'] : 'info'; ?> alert-dismissible fade show text-center shadow-sm" role="alert">
                <strong><?php echo $_SESSION['msg']; ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        <?php 
        unset($_SESSION['msg']); 
        unset($_SESSION['msg_type']); 
        ?>
    <?php endif; ?>

    <!-- Main Content Container -->
    <div id="events-section" class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Upcoming Melas & Exhibitions</h2>
            <p class="text-muted">Explore events, discover artisans, and attend live exhibitions.</p>
        </div>

        <div class="row g-4">
            <?php if ($melas && $melas->num_rows > 0): ?>
                <?php while ($mela = $melas->fetch_assoc()): ?>
                    <?php 
                        $mela_id = $mela['id'];
                        $imgPath = !empty($mela['image']) ? $mela['image'] : (!empty($mela['image_url']) ? $mela['image_url'] : '');
                        $melaKaarigars = $kaarigars_by_mela[$mela_id] ?? [];
                    ?>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm mela-card overflow-hidden">
                            <?php if ($imgPath): ?>
                                <img src="<?php echo htmlspecialchars($imgPath); ?>" class="card-img-top" alt="Mela Banner">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <span class="badge bg-danger">📍 <?php echo htmlspecialchars($mela['location']); ?></span>
                                </div>
                                <h5 class="card-title fw-bold"><?php echo htmlspecialchars($mela['title']); ?></h5>
                                <p class="text-muted small mb-2">📅 Date: <?php echo date("d M Y", strtotime($mela['event_date'])); ?></p>
                                <p class="card-text text-secondary small flex-grow-1">
                                    <?php echo htmlspecialchars(substr($mela['description'] ?? '', 0, 90)) . '...'; ?>
                                </p>
                                
                                <div class="d-grid gap-2 mt-3">
                                    <!-- View Details Button -->
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#melaModal<?php echo $mela_id; ?>">
                                        🔍 View Details & Kaarigars
                                    </button>
                                    
                                    <!-- Fixed Parameter: mela_id -->
                                    <a href="rsvp_action.php?mela_id=<?php echo $mela_id; ?>" class="btn btn-outline-danger btn-sm">RSVP / Apply Now</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mela Details Modal -->
                    <div class="modal fade" id="melaModal<?php echo $mela_id; ?>" tabindex="-1" aria-labelledby="modalLabel<?php echo $mela_id; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold" id="modalLabel<?php echo $mela_id; ?>"><?php echo htmlspecialchars($mela['title']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <?php if ($imgPath): ?>
                                        <img src="<?php echo htmlspecialchars($imgPath); ?>" class="img-fluid rounded mb-3 w-100" style="max-height: 250px; object-fit: cover;">
                                    <?php endif; ?>
                                    
                                    <p><strong>Location:</strong> 📍 <?php echo htmlspecialchars($mela['location']); ?></p>
                                    <p><strong>Date:</strong> 📅 <?php echo date("d F Y", strtotime($mela['event_date'])); ?></p>
                                    <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($mela['description'] ?? 'No description provided.')); ?></p>
                                    
                                    <hr>
                                    <h6 class="fw-bold mb-3">🎨 Participating Kaarigars</h6>
                                    
                                    <?php if (!empty($melaKaarigars)): ?>
                                        <div class="row g-3">
                                            <?php foreach ($melaKaarigars as $k): ?>
                                                <div class="col-sm-6 col-md-4">
                                                    <div class="d-flex align-items-center p-2 border rounded bg-light">
                                                        <?php if (!empty($k['photo'])): ?>
                                                            <img src="<?php echo htmlspecialchars($k['photo']); ?>" width="50" height="50" class="rounded-circle object-fit-cover me-3">
                                                        <?php else: ?>
                                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                                                👤
                                                            </div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <div class="fw-bold small"><?php echo htmlspecialchars($k['name']); ?></div>
                                                            <div class="text-muted small"><?php echo htmlspecialchars($k['craft_type'] ?? 'Handicraft'); ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted small">No approved Kaarigars registered for this event yet.</p>
                                    <?php endif; ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted">
                    <p>No upcoming events available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
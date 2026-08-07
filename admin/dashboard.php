<?php
session_start();
require_once __DIR__ . '/../config/database.php';

require_admin();

$pageTitle = 'Admin Dashboard';
$stats = [
    'medicines' => $pdo->query('SELECT COUNT(*) as total FROM medicines')->fetch()['total'],
    'categories' => $pdo->query('SELECT COUNT(*) as total FROM categories')->fetch()['total'],
    'pharmacists' => $pdo->query('SELECT COUNT(*) as total FROM pharmacists')->fetch()['total'],
    'gallery' => $pdo->query('SELECT COUNT(*) as total FROM gallery')->fetch()['total'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 sidebar text-white p-3 d-flex flex-column">
            <div class="mb-4">
                <h4 class="fw-bold mb-1">HealthCare Admin</h4>
                <small class="text-white-50">Welcome, <?php echo sanitize_input($_SESSION['admin_username'] ?? 'Admin'); ?></small>
            </div>
            <ul class="nav flex-column flex-grow-1">
                <li class="nav-item mb-2"><a class="nav-link text-white active" href="dashboard.php"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a></li>
                <li class="nav-item mb-2"><a class="nav-link text-white" href="medicines.php"><i class="fa-solid fa-capsules me-2"></i>Medicines</a></li>
                <li class="nav-item mb-2"><a class="nav-link text-white" href="categories.php"><i class="fa-solid fa-tags me-2"></i>Categories</a></li>
                <li class="nav-item mb-2"><a class="nav-link text-white" href="pharmacists.php"><i class="fa-solid fa-user-doctor me-2"></i>Pharmacists</a></li>
                <li class="nav-item mb-2"><a class="nav-link text-white" href="gallery.php"><i class="fa-solid fa-images me-2"></i>Gallery</a></li>
            </ul>
            <a class="btn btn-outline-light btn-sm" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
        </nav>
        <main class="col-md-9 col-lg-10 admin-main p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Dashboard</h2>
                    <p class="text-muted mb-0">Manage your pharmacy inventory and content from one place.</p>
                </div>
                <a href="../index.php" class="btn btn-outline-primary" target="_blank">View Site</a>
            </div>
            <div class="row g-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm p-3 stat-card">
                        <h6 class="text-muted">Medicines</h6>
                        <h3 class="fw-bold"><?php echo $stats['medicines']; ?></h3>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm p-3 stat-card">
                        <h6 class="text-muted">Categories</h6>
                        <h3 class="fw-bold"><?php echo $stats['categories']; ?></h3>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm p-3 stat-card">
                        <h6 class="text-muted">Pharmacists</h6>
                        <h3 class="fw-bold"><?php echo $stats['pharmacists']; ?></h3>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm p-3 stat-card">
                        <h6 class="text-muted">Gallery</h6>
                        <h3 class="fw-bold"><?php echo $stats['gallery']; ?></h3>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>

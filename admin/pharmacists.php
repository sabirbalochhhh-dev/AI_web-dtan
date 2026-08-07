<?php
session_start();
require_once __DIR__ . '/../config/database.php';

require_admin();

$pageTitle = 'Manage Pharmacists';
$action = $_GET['action'] ?? 'view';
$id = (int)($_GET['id'] ?? 0);
$search = trim($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 8;
$offset = ($page - 1) * $limit;
$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $specialty = trim($_POST['specialty'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    if ($name === '') {
        $errors[] = 'Name is required.';
    }
    if ($specialty === '') {
        $errors[] = 'Specialty is required.';
    }
    if ($bio === '') {
        $errors[] = 'Bio is required.';
    }

    $uploadFile = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $tmpName = $_FILES['photo']['tmp_name'];
        $fileType = mime_content_type($tmpName);
        $size = $_FILES['photo']['size'];
        if (!in_array($fileType, $allowed, true) || $size > 2 * 1024 * 1024) {
            $errors[] = 'Photo must be JPG, PNG, or WEBP and under 2MB.';
        } else {
            $uploadDir = __DIR__ . '/../uploads/pharmacists';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $safeName = bin2hex(random_bytes(8)) . '.' . $ext;
            $destination = $uploadDir . '/' . $safeName;
            if (!move_uploaded_file($tmpName, $destination)) {
                $errors[] = 'Could not upload photo.';
            } else {
                $uploadFile = $safeName;
            }
        }
    }

    if (empty($errors)) {
        if ($action === 'add') {
            $stmt = $pdo->prepare('INSERT INTO pharmacists (name, specialty, bio, photo) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $specialty, $bio, $uploadFile ?? 'default.jpg']);
            $message = 'Pharmacist added successfully.';
        } elseif ($action === 'edit' && $id) {
            if ($uploadFile) {
                $stmt = $pdo->prepare('UPDATE pharmacists SET name=?, specialty=?, bio=?, photo=? WHERE id=?');
                $stmt->execute([$name, $specialty, $bio, $uploadFile, $id]);
            } else {
                $stmt = $pdo->prepare('UPDATE pharmacists SET name=?, specialty=?, bio=? WHERE id=?');
                $stmt->execute([$name, $specialty, $bio, $id]);
            }
            $message = 'Pharmacist updated successfully.';
        }
        header('Location: pharmacists.php?message=' . urlencode($message));
        exit;
    }
}

if ($action === 'delete' && $id) {
    $stmt = $pdo->prepare('DELETE FROM pharmacists WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: pharmacists.php?message=' . urlencode('Pharmacist deleted successfully.'));
    exit;
}

if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare('SELECT * FROM pharmacists WHERE id = ?');
    $stmt->execute([$id]);
    $pharmacist = $stmt->fetch();
}

$query = 'SELECT * FROM pharmacists WHERE 1=1';
$params = [];
if ($search !== '') {
    $query .= ' AND (name LIKE ? OR specialty LIKE ? OR bio LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}
$query .= ' ORDER BY id DESC LIMIT ? OFFSET ?';
$params[] = $limit;
$params[] = $offset;
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$pharmacists = $stmt->fetchAll();

$countStmt = $pdo->prepare('SELECT COUNT(*) as total FROM pharmacists WHERE 1=1' . ($search !== '' ? ' AND (name LIKE ? OR specialty LIKE ? OR bio LIKE ?)' : ''));
if ($search !== '') {
    $countStmt->execute(['%' . $search . '%', '%' . $search . '%', '%' . $search . '%']);
} else {
    $countStmt->execute();
}
$total = $countStmt->fetch()['total'];
$totalPages = max(1, (int)ceil($total / $limit));

if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
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
        <nav class="col-md-3 col-lg-2 sidebar text-white p-3">
            <h4 class="fw-bold mb-4">HealthCare Admin</h4>
            <ul class="nav flex-column">
                <li class="nav-item mb-2"><a class="nav-link text-white" href="dashboard.php"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a></li>
                <li class="nav-item mb-2"><a class="nav-link text-white" href="medicines.php"><i class="fa-solid fa-capsules me-2"></i>Medicines</a></li>
                <li class="nav-item mb-2"><a class="nav-link text-white" href="categories.php"><i class="fa-solid fa-tags me-2"></i>Categories</a></li>
                <li class="nav-item mb-2"><a class="nav-link text-white" href="pharmacists.php"><i class="fa-solid fa-user-doctor me-2"></i>Pharmacists</a></li>
                <li class="nav-item mb-2"><a class="nav-link text-white" href="gallery.php"><i class="fa-solid fa-images me-2"></i>Gallery</a></li>
                <li class="nav-item mb-2"><a class="nav-link text-white" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
            </ul>
        </nav>
        <main class="col-md-9 col-lg-10 admin-main p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="fw-bold">Pharmacists</h2>
                <a href="pharmacists.php?action=add" class="btn btn-primary">Add Pharmacist</a>
            </div>
            <?php if ($message): ?><div class="alert alert-success"><?php echo sanitize_input($message); ?></div><?php endif; ?>
            <?php if (!empty($errors)): ?><div class="alert alert-danger"><?php echo sanitize_input(implode('<br>', $errors)); ?></div><?php endif; ?>
            <form class="row g-2 mb-3" method="get">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control" placeholder="Search pharmacists" value="<?php echo sanitize_input($search); ?>">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-outline-primary w-100" type="submit">Search</button>
                </div>
            </form>
            <?php if ($action === 'add' || $action === 'edit'): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold"><?php echo $action === 'add' ? 'Add' : 'Edit'; ?> Pharmacist</h5>
                        <form method="post" enctype="multipart/form-data" class="row g-3">
                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo isset($pharmacist) ? sanitize_input($pharmacist['name']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Specialty</label>
                                <input type="text" name="specialty" class="form-control" value="<?php echo isset($pharmacist) ? sanitize_input($pharmacist['specialty']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Photo</label>
                                <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/webp">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bio</label>
                                <textarea name="bio" class="form-control" rows="4" required><?php echo isset($pharmacist) ? sanitize_input($pharmacist['bio']) : ''; ?></textarea>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-success" type="submit">Save Pharmacist</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>#</th><th>Name</th><th>Specialty</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($pharmacists as $pharmacist): ?>
                            <tr>
                                <td><?php echo $pharmacist['id']; ?></td>
                                <td><?php echo sanitize_input($pharmacist['name']); ?></td>
                                <td><?php echo sanitize_input($pharmacist['specialty']); ?></td>
                                <td>
                                    <a href="pharmacists.php?action=edit&id=<?php echo $pharmacist['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="pharmacists.php?action=delete&id=<?php echo $pharmacist['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this pharmacist?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <nav class="mt-4">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="pharmacists.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </main>
    </div>
</div>
</body>
</html>

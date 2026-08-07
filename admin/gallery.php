<?php
session_start();
require_once __DIR__ . '/../config/database.php';

require_admin();

$pageTitle = 'Manage Gallery';
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
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title === '') {
        $errors[] = 'Title is required.';
    }

    $uploadFile = null;
    $existingImage = null;

    if ($action === 'edit' && $id) {
        $stmt = $pdo->prepare('SELECT image FROM gallery WHERE id = ?');
        $stmt->execute([$id]);
        $existingImage = $stmt->fetchColumn();
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $tmpName = $_FILES['image']['tmp_name'];
        $fileType = mime_content_type($tmpName);
        $size = $_FILES['image']['size'];
        if (!in_array($fileType, $allowed, true) || $size > 2 * 1024 * 1024) {
            $errors[] = 'Image must be JPG, PNG, or WEBP and under 2MB.';
        } else {
            $uploadDir = __DIR__ . '/../uploads/gallery';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $safeName = bin2hex(random_bytes(8)) . '.' . $ext;
            if (!move_uploaded_file($tmpName, $uploadDir . '/' . $safeName)) {
                $errors[] = 'Could not upload image.';
            } else {
                $uploadFile = $safeName;
            }
        }
    } elseif ($action === 'add') {
        $errors[] = 'An image is required.';
    }

    if (empty($errors)) {
        if ($action === 'add') {
            $stmt = $pdo->prepare('INSERT INTO gallery (title, description, image) VALUES (?, ?, ?)');
            $stmt->execute([$title, $description, $uploadFile ?? 'default.jpg']);
            $message = 'Gallery item added successfully.';
        } elseif ($action === 'edit' && $id) {
            if ($uploadFile) {
                $stmt = $pdo->prepare('UPDATE gallery SET title=?, description=?, image=? WHERE id=?');
                $stmt->execute([$title, $description, $uploadFile, $id]);
                if ($existingImage && $existingImage !== 'default.jpg' && file_exists($uploadDir . '/' . $existingImage)) {
                    unlink($uploadDir . '/' . $existingImage);
                }
            } else {
                $stmt = $pdo->prepare('UPDATE gallery SET title=?, description=? WHERE id=?');
                $stmt->execute([$title, $description, $id]);
            }
            $message = 'Gallery item updated successfully.';
        }
        header('Location: gallery.php?message=' . urlencode($message));
        exit;
    }
}

if ($action === 'delete' && $id) {
    $stmt = $pdo->prepare('SELECT image FROM gallery WHERE id = ?');
    $stmt->execute([$id]);
    $imageToDelete = $stmt->fetchColumn();
    $stmt = $pdo->prepare('DELETE FROM gallery WHERE id = ?');
    $stmt->execute([$id]);
    if ($imageToDelete && $imageToDelete !== 'default.jpg') {
        $filePath = __DIR__ . '/../uploads/gallery/' . $imageToDelete;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    header('Location: gallery.php?message=' . urlencode('Gallery item deleted successfully.'));
    exit;
}

if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare('SELECT * FROM gallery WHERE id = ?');
    $stmt->execute([$id]);
    $galleryItem = $stmt->fetch();
}

$query = 'SELECT * FROM gallery WHERE 1=1';
$params = [];
if ($search !== '') {
    $query .= ' AND (title LIKE ? OR description LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}
$query .= ' ORDER BY id DESC LIMIT ? OFFSET ?';
$params[] = $limit;
$params[] = $offset;
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$gallery = $stmt->fetchAll();

$countStmt = $pdo->prepare('SELECT COUNT(*) as total FROM gallery WHERE 1=1' . ($search !== '' ? ' AND (title LIKE ? OR description LIKE ?)' : ''));
if ($search !== '') {
    $countStmt->execute(['%' . $search . '%', '%' . $search . '%']);
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
                <h2 class="fw-bold">Gallery</h2>
                <a href="gallery.php?action=add" class="btn btn-primary">Add Image</a>
            </div>
            <?php if ($message): ?><div class="alert alert-success"><?php echo sanitize_input($message); ?></div><?php endif; ?>
            <?php if (!empty($errors)): ?><div class="alert alert-danger"><?php echo sanitize_input(implode('<br>', $errors)); ?></div><?php endif; ?>
            <form class="row g-2 mb-3" method="get">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control" placeholder="Search gallery" value="<?php echo sanitize_input($search); ?>">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-outline-primary w-100" type="submit">Search</button>
                </div>
            </form>
            <?php if ($action === 'add' || $action === 'edit'): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold"><?php echo $action === 'add' ? 'Add' : 'Edit'; ?> Gallery Item</h5>
                        <form method="post" enctype="multipart/form-data" class="row g-3">
                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                            <div class="col-md-6">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" value="<?php echo isset($galleryItem) ? sanitize_input($galleryItem['title']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Image</label>
                                <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4" required><?php echo isset($galleryItem) ? sanitize_input($galleryItem['description']) : ''; ?></textarea>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-success" type="submit">Save Image</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row g-4">
                <?php foreach ($gallery as $item): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card shadow-sm h-100">
                            <img src="../uploads/gallery/<?php echo $item['image']; ?>" class="card-img-top img-cover" alt="<?php echo sanitize_input($item['title']); ?>">
                            <div class="card-body">
                                <h6 class="fw-bold"><?php echo sanitize_input($item['title']); ?></h6>
                                <p class="small text-muted"><?php echo sanitize_input($item['description']); ?></p>
                                <a href="gallery.php?action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="gallery.php?action=delete&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?')">Delete</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <nav class="mt-4">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="gallery.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </main>
    </div>
</div>
</body>
</html>

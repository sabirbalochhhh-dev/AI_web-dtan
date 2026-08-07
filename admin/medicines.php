<?php
session_start();
require_once __DIR__ . '/../config/database.php';

require_admin();

$pageTitle = 'Manage Medicines';
$action = $_GET['action'] ?? 'view';
$id = (int)($_GET['id'] ?? 0);
$search = trim($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 8;
$offset = ($page - 1) * $limit;
$message = '';

$stmt = $pdo->query('SELECT * FROM categories ORDER BY name ASC');
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $availability = isset($_POST['availability']) ? 1 : 0;
    $status = isset($_POST['status']) ? 1 : 0;
    $errors = [];

    if ($name === '') {
        $errors[] = 'Medicine name is required.';
    }
    if ($price <= 0) {
        $errors[] = 'Price must be greater than zero.';
    }
    if (mb_strlen($description) < 10) {
        $errors[] = 'Description must be at least 10 characters.';
    }

    $uploadFile = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $tmpName = $_FILES['image']['tmp_name'];
        $fileType = mime_content_type($tmpName);
        $size = $_FILES['image']['size'];
        if (!in_array($fileType, $allowed, true) || $size > 2 * 1024 * 1024) {
            $errors[] = 'Image must be JPG, PNG, or WEBP and under 2MB.';
        } else {
            $uploadDir = __DIR__ . '/../uploads/medicines';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $safeName = bin2hex(random_bytes(8)) . '.' . $ext;
            $destination = $uploadDir . '/' . $safeName;
            if (!move_uploaded_file($tmpName, $destination)) {
                $errors[] = 'Could not upload image.';
            } else {
                $uploadFile = $safeName;
            }
        }
    }

    if (empty($errors)) {
        if ($action === 'add') {
            $stmt = $pdo->prepare('INSERT INTO medicines (name, category_id, price, description, availability, status, image) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $category_id, $price, $description, $availability, $status, $uploadFile ?? 'default.jpg']);
            $message = 'Medicine added successfully.';
        } elseif ($action === 'edit' && $id) {
            if ($uploadFile) {
                $stmt = $pdo->prepare('UPDATE medicines SET name=?, category_id=?, price=?, description=?, availability=?, status=?, image=? WHERE id=?');
                $stmt->execute([$name, $category_id, $price, $description, $availability, $status, $uploadFile, $id]);
            } else {
                $stmt = $pdo->prepare('UPDATE medicines SET name=?, category_id=?, price=?, description=?, availability=?, status=? WHERE id=?');
                $stmt->execute([$name, $category_id, $price, $description, $availability, $status, $id]);
            }
            $message = 'Medicine updated successfully.';
        }
        header('Location: medicines.php?message=' . urlencode($message));
        exit;
    }
}

if ($action === 'delete' && $id) {
    $stmt = $pdo->prepare('DELETE FROM medicines WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: medicines.php?message=' . urlencode('Medicine deleted successfully.'));
    exit;
}

if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare('SELECT * FROM medicines WHERE id = ?');
    $stmt->execute([$id]);
    $medicine = $stmt->fetch();
}

$query = 'SELECT m.*, c.name as category_name FROM medicines m LEFT JOIN categories c ON m.category_id = c.id WHERE 1=1';
$params = [];
if ($search !== '') {
    $query .= ' AND (m.name LIKE ? OR c.name LIKE ? OR m.description LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}
$query .= ' ORDER BY m.id DESC LIMIT ? OFFSET ?';
$params[] = $limit;
$params[] = $offset;
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$medicines = $stmt->fetchAll();

$countStmt = $pdo->prepare('SELECT COUNT(*) as total FROM medicines m LEFT JOIN categories c ON m.category_id = c.id WHERE 1=1' . ($search !== '' ? ' AND (m.name LIKE ? OR c.name LIKE ? OR m.description LIKE ?)' : ''));
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
                <h2 class="fw-bold">Medicines</h2>
                <a href="medicines.php?action=add" class="btn btn-primary">Add Medicine</a>
            </div>
            <?php if ($message): ?><div class="alert alert-success"><?php echo sanitize_input($message); ?></div><?php endif; ?>
            <?php if (!empty($errors ?? [])): ?><div class="alert alert-danger"><?php echo sanitize_input(implode('<br>', $errors)); ?></div><?php endif; ?>
            <form class="row g-2 mb-3" method="get">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control" placeholder="Search medicines" value="<?php echo sanitize_input($search); ?>">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-outline-primary w-100" type="submit">Search</button>
                </div>
            </form>
            <?php if ($action === 'add' || $action === 'edit'): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold"><?php echo $action === 'add' ? 'Add' : 'Edit'; ?> Medicine</h5>
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" value="<?php echo isset($medicine) ? sanitize_input($medicine['name']) : ''; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Category</label>
                                    <select name="category_id" class="form-select">
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>" <?php echo (isset($medicine) && $medicine['category_id'] == $category['id']) ? 'selected' : ''; ?>><?php echo sanitize_input($category['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Price</label>
                                    <input type="number" step="0.01" min="0.01" name="price" class="form-control" value="<?php echo isset($medicine) ? $medicine['price'] : ''; ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Visibility</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="availability" <?php echo (isset($medicine) && $medicine['availability']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Available</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="status" <?php echo (isset($medicine) && $medicine['status']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Published</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="4" required><?php echo isset($medicine) ? sanitize_input($medicine['description']) : ''; ?></textarea>
                                </div>
                            </div>
                            <button class="btn btn-success mt-3" type="submit">Save</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($medicines as $medicine): ?>
                            <tr>
                                <td><?php echo $medicine['id']; ?></td>
                                <td><?php echo sanitize_input($medicine['name']); ?></td>
                                <td><?php echo sanitize_input($medicine['category_name'] ?? 'General'); ?></td>
                                <td>$<?php echo number_format($medicine['price'], 2); ?></td>
                                <td><?php echo $medicine['availability'] ? 'Available' : 'Unavailable'; ?></td>
                                <td>
                                    <a href="medicines.php?action=edit&id=<?php echo $medicine['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="medicines.php?action=delete&id=<?php echo $medicine['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this medicine?')">Delete</a>
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
                            <a class="page-link" href="medicines.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </main>
    </div>
</div>
</body>
</html>

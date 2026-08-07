<?php
session_start();
require_once __DIR__ . '/../config/database.php';

require_admin();

$pageTitle = 'Manage Categories';
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

    if ($name === '') {
        $errors[] = 'Category name is required.';
    } elseif (mb_strlen($name) < 2) {
        $errors[] = 'Category name must be at least 2 characters.';
    }

    if (empty($errors)) {
        if ($action === 'add') {
            $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (?)');
            $stmt->execute([$name]);
            $message = 'Category added successfully.';
        } elseif ($action === 'edit' && $id) {
            $stmt = $pdo->prepare('UPDATE categories SET name=? WHERE id=?');
            $stmt->execute([$name, $id]);
            $message = 'Category updated successfully.';
        }
        header('Location: categories.php?message=' . urlencode($message));
        exit;
    }
}

if ($action === 'delete' && $id) {
    $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: categories.php?message=' . urlencode('Category deleted successfully.'));
    exit;
}

if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    $category = $stmt->fetch();
}

$query = 'SELECT * FROM categories WHERE 1=1';
$params = [];
if ($search !== '') {
    $query .= ' AND name LIKE ?';
    $params[] = '%' . $search . '%';
}
$query .= ' ORDER BY id DESC LIMIT ? OFFSET ?';
$params[] = $limit;
$params[] = $offset;
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$categories = $stmt->fetchAll();

$countStmt = $pdo->prepare('SELECT COUNT(*) as total FROM categories WHERE 1=1' . ($search !== '' ? ' AND name LIKE ?' : ''));
if ($search !== '') {
    $countStmt->execute(['%' . $search . '%']);
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
                <h2 class="fw-bold">Categories</h2>
                <a href="categories.php?action=add" class="btn btn-primary">Add Category</a>
            </div>
            <?php if ($message): ?><div class="alert alert-success"><?php echo sanitize_input($message); ?></div><?php endif; ?>
            <?php if (!empty($errors)): ?><div class="alert alert-danger"><?php echo sanitize_input(implode('<br>', $errors)); ?></div><?php endif; ?>
            <form class="row g-2 mb-3" method="get">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control" placeholder="Search categories" value="<?php echo sanitize_input($search); ?>">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-outline-primary w-100" type="submit">Search</button>
                </div>
            </form>
            <?php if ($action === 'add' || $action === 'edit'): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold"><?php echo $action === 'add' ? 'Add' : 'Edit'; ?> Category</h5>
                        <form method="post" class="row g-3">
                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                            <div class="col-md-8">
                                <label class="form-label">Category Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo isset($category) ? sanitize_input($category['name']) : ''; ?>" required>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button class="btn btn-success w-100" type="submit">Save Category</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>#</th><th>Name</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo $category['id']; ?></td>
                                <td><?php echo sanitize_input($category['name']); ?></td>
                                <td>
                                    <a href="categories.php?action=edit&id=<?php echo $category['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="categories.php?action=delete&id=<?php echo $category['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this category?')">Delete</a>
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
                            <a class="page-link" href="categories.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </main>
    </div>
</div>
</body>
</html>

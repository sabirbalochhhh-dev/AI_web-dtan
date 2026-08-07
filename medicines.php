<?php
$pageTitle = 'Medicines | HealthCare Pharmacy';
include __DIR__ . '/includes/header.php';

$search = $_GET['search'] ?? '';
$categoryFilter = $_GET['category'] ?? '';

$query = 'SELECT m.*, c.name as category_name FROM medicines m LEFT JOIN categories c ON m.category_id = c.id WHERE 1=1';
$params = [];

if ($search !== '') {
    $query .= ' AND (m.name LIKE ? OR m.description LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

if ($categoryFilter !== '') {
    $query .= ' AND m.category_id = ?';
    $params[] = $categoryFilter;
}

$query .= ' ORDER BY m.id DESC';
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$medicines = $stmt->fetchAll();

$stmt = $pdo->query('SELECT * FROM categories ORDER BY name ASC');
$categories = $stmt->fetchAll();
?>

<section class="container py-5 reveal">
    <div class="text-center mb-4">
        <span class="badge bg-primary mb-3">Our inventory</span>
        <h1 class="fw-bold">Medicines and essentials</h1>
        <p class="text-muted">Search and explore trusted medicines available at HealthCare Pharmacy.</p>
    </div>

    <div class="card shadow-sm border-0 p-3 mb-4">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Search medicines</label>
                <input type="text" name="search" class="form-control" placeholder="Search medicines..." value="<?php echo sanitize_input($search); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Filter by category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo $categoryFilter == $category['id'] ? 'selected' : ''; ?>><?php echo sanitize_input($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" type="submit">Filter</button>
            </div>
        </form>
    </div>

    <div class="row g-4">
        <?php foreach ($medicines as $medicine): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 card-hover">
                    <img src="uploads/medicines/<?php echo $medicine['image']; ?>" class="card-img-top img-cover" alt="<?php echo sanitize_input($medicine['name']); ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0"><?php echo sanitize_input($medicine['name']); ?></h5>
                            <span class="badge badge-availability <?php echo $medicine['availability'] ? 'in-stock' : 'out-of-stock'; ?> text-white">
                                <?php echo $medicine['availability'] ? 'Available' : 'Unavailable'; ?>
                            </span>
                        </div>
                        <p class="text-muted small mb-2"><?php echo sanitize_input($medicine['description']); ?></p>
                        <p class="mb-1"><strong>Category:</strong> <?php echo sanitize_input($medicine['category_name'] ?? 'General'); ?></p>
                        <p class="mb-0"><strong>Price:</strong> $<?php echo number_format($medicine['price'], 2); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="container py-5 reveal">
    <div class="row g-4 align-items-center">
        <div class="col-lg-6">
            <h3 class="fw-bold">Need help choosing the right medicine?</h3>
            <p class="text-muted">Our pharmacists can guide you with safe usage instructions and alternatives.</p>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm p-4">
                <p class="mb-2"><strong>Call us:</strong> +1 (555) 012-3456</p>
                <p class="mb-0"><strong>Email:</strong> info@healthcarepharmacy.com</p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

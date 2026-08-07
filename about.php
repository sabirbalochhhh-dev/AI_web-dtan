<?php
$pageTitle = 'About Us | HealthCare Pharmacy';
include __DIR__ . '/includes/header.php';

$stmt = $pdo->query('SELECT * FROM pharmacists ORDER BY id DESC');
$pharmacists = $stmt->fetchAll();
?>

<section class="container py-5 reveal">
    <div class="row g-4 align-items-center">
        <div class="col-lg-6">
            <span class="badge bg-primary mb-3">Our story</span>
            <h1 class="fw-bold mb-3">About HealthCare Pharmacy</h1>
            <p class="text-muted">HealthCare Pharmacy has been serving the local community with dependable pharmaceutical care, trusted medicines, and a patient-first approach for years.</p>
            <p class="text-muted">We combine medical knowledge with warm customer care to ensure everyone feels supported when they visit our store.</p>
        </div>
        <div class="col-lg-6">
            <img src="https://images.unsplash.com/photo-1576602976047-174e57a47881?auto=format&fit=crop&w=900&q=80" class="img-fluid rounded shadow" alt="Pharmacy team">
        </div>
    </div>
</section>

<section class="container py-4 reveal">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100 p-4 stat-card">
                <h3 class="fw-bold">Mission</h3>
                <p class="text-muted mb-0">To provide accessible, safe, and affordable healthcare solutions to our community with professionalism and compassion.</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100 p-4 stat-card">
                <h3 class="fw-bold">Vision</h3>
                <p class="text-muted mb-0">To become the preferred local pharmacy known for trust, innovation, and exceptional patient care.</p>
            </div>
        </div>
    </div>
</section>

<section class="container py-5 reveal">
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center">
                <h3 class="fw-bold text-primary">15+</h3>
                <p class="mb-0 text-muted">Years of service</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center">
                <h3 class="fw-bold text-primary">5000+</h3>
                <p class="mb-0 text-muted">Happy patients</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center">
                <h3 class="fw-bold text-primary">98%</h3>
                <p class="mb-0 text-muted">Satisfaction rate</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center">
                <h3 class="fw-bold text-primary">24/7</h3>
                <p class="mb-0 text-muted">Support access</p>
            </div>
        </div>
    </div>
</section>

<section class="container py-5 reveal">
    <div class="text-center mb-4">
        <h2 class="fw-bold">Our qualified pharmacists</h2>
        <p class="text-muted">Experienced professionals committed to safe medication care and patient education.</p>
    </div>
    <div class="row g-4">
        <?php foreach ($pharmacists as $pharmacist): ?>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100 text-center card-hover">
                    <img src="uploads/pharmacists/<?php echo $pharmacist['photo']; ?>" class="card-img-top img-cover" alt="<?php echo sanitize_input($pharmacist['name']); ?>">
                    <div class="card-body">
                        <h5><?php echo sanitize_input($pharmacist['name']); ?></h5>
                        <p class="text-muted mb-0"><?php echo sanitize_input($pharmacist['specialty']); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="container py-5 reveal">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100 p-4">
                <h3 class="fw-bold">Certifications</h3>
                <ul class="mb-0 text-muted">
                    <li>Licensed Pharmacy Practice</li>
                    <li>GMP and Safety Compliance</li>
                    <li>Professional Health Standards</li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100 p-4">
                <h3 class="fw-bold">Store timings</h3>
                <ul class="mb-0 text-muted">
                    <li>Monday - Friday: 8:00 AM - 9:00 PM</li>
                    <li>Saturday: 9:00 AM - 7:00 PM</li>
                    <li>Sunday: 10:00 AM - 5:00 PM</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

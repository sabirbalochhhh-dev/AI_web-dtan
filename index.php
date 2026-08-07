<?php
$pageTitle = 'Home | HealthCare Pharmacy';
include __DIR__ . '/includes/header.php';

$stmt = $pdo->query('SELECT * FROM medicines WHERE status = 1 ORDER BY id DESC LIMIT 6');
$featuredMedicines = $stmt->fetchAll();

$stmt = $pdo->query('SELECT * FROM categories ORDER BY name ASC');
$categories = $stmt->fetchAll();
?>

<section class="hero-section">
    <div class="container py-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7 reveal">
                <span class="badge bg-light text-primary mb-3">Trusted local pharmacy</span>
                <h1 class="display-5 fw-bold mb-3">Care you can trust, medicines you can rely on.</h1>
                <p class="lead mb-4">HealthCare Pharmacy offers professional care, genuine medicines, and fast service for every family and every prescription.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="medicines.php" class="btn btn-light btn-lg">Browse Medicines</a>
                    <a href="contact.php" class="btn btn-outline-light btn-lg">Contact Us</a>
                </div>
                <div class="row g-3 mt-4">
                    <div class="col-sm-4">
                        <div class="hero-card p-3 rounded">
                            <h4 class="fw-bold mb-0">24/7</h4>
                            <small>Support guidance</small>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="hero-card p-3 rounded">
                            <h4 class="fw-bold mb-0">100%</h4>
                            <small>Genuine products</small>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="hero-card p-3 rounded">
                            <h4 class="fw-bold mb-0">4.9★</h4>
                            <small>Customer rating</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 reveal">
                <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=900&q=80" class="img-fluid rounded shadow" alt="Pharmacist helping a customer">
            </div>
        </div>
    </div>
</section>

<section class="container py-5 reveal">
    <div class="text-center mb-4">
        <h2 class="section-title fw-bold">Why families choose us</h2>
        <p class="text-muted">A modern pharmacy experience built around trust, convenience, and expertise.</p>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-3 card-hover">
                <div class="feature-icon mb-3"><i class="fa-solid fa-user-doctor"></i></div>
                <h5>Qualified pharmacists</h5>
                <p class="text-muted mb-0">Professional guidance for prescriptions, wellness advice, and everyday health support.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-3 card-hover">
                <div class="feature-icon mb-3"><i class="fa-solid fa-truck-fast"></i></div>
                <h5>Fast delivery</h5>
                <p class="text-muted mb-0">Quick pickup and delivery options to keep your routine running smoothly.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-3 card-hover">
                <div class="feature-icon mb-3"><i class="fa-solid fa-shield-halved"></i></div>
                <h5>Safe & certified</h5>
                <p class="text-muted mb-0">Every product is handled with care and meets strict safety standards.</p>
            </div>
        </div>
    </div>
</section>

<section class="container py-5 reveal">
    <div class="text-center mb-4">
        <h2 class="section-title fw-bold">Featured medicines</h2>
        <p class="text-muted">Top quality medicines chosen for everyday wellness and treatment needs.</p>
    </div>
    <div class="row g-4">
        <?php foreach ($featuredMedicines as $medicine): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 card-hover border-0 shadow-sm">
                    <img src="<?php echo get_medicine_image_path($medicine['image']); ?>" class="card-img-top img-cover" alt="<?php echo sanitize_input($medicine['name']); ?>" loading="lazy">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0"><?php echo sanitize_input($medicine['name']); ?></h5>
                            <span class="badge badge-availability <?php echo $medicine['availability'] ? 'in-stock' : 'out-of-stock'; ?> text-white">
                                <?php echo $medicine['availability'] ? 'In Stock' : 'Out of Stock'; ?>
                            </span>
                        </div>
                        <p class="text-muted small mb-2"><?php echo sanitize_input($medicine['description']); ?></p>
                        <p class="fw-semibold text-primary">$<?php echo number_format($medicine['price'], 2); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="container py-5 reveal">
    <div class="text-center mb-4">
        <h2 class="section-title fw-bold">Shop by category</h2>
        <p class="text-muted">Browse the medication categories most commonly requested by our customers.</p>
    </div>
    <div class="row g-4">
        <?php foreach ($categories as $category): ?>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-center p-3 shadow-sm border-0 card-hover">
                    <i class="fa-solid fa-capsules fa-2x text-primary mb-2"></i>
                    <h5><?php echo sanitize_input($category['name']); ?></h5>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="container py-5 reveal">
    <div class="row g-4 align-items-center">
        <div class="col-lg-6">
            <h2 class="fw-bold mb-3">A pharmacy experience designed for comfort and care</h2>
            <p class="text-muted">From refill reminders to personalized advice, we make every visit simple, safe, and stress-free.</p>
            <ul class="list-unstyled">
                <li class="mb-3"><i class="fa-solid fa-circle-check text-success me-2"></i> Licensed professionals and authentic products</li>
                <li class="mb-3"><i class="fa-solid fa-circle-check text-success me-2"></i> Affordable pricing with transparent guidance</li>
                <li class="mb-3"><i class="fa-solid fa-circle-check text-success me-2"></i> Friendly service and responsive support</li>
            </ul>
        </div>
        <div class="col-lg-6">
            <img src="https://images.unsplash.com/photo-1587854692152-cbe660dbde88?auto=format&fit=crop&w=900&q=80" class="img-fluid rounded shadow" alt="Pharmacy store interior">
        </div>
    </div>
</section>

<section class="container py-5 reveal">
    <div class="text-center mb-4">
        <h2 class="section-title fw-bold">What customers say</h2>
        <p class="text-muted">Trusted by local families and healthcare providers.</p>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <p>"Excellent service and very helpful staff. They guided me through my prescription needs with care."</p>
                    <strong>— Sarah M.</strong>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <p>"Fast, professional, and always stocked with the medicines I need. Highly recommended."</p>
                    <strong>— James T.</strong>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <p>"A dependable pharmacy with great customer care and a welcoming atmosphere."</p>
                    <strong>— Maria L.</strong>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container py-5 reveal">
    <div class="bg-primary text-white rounded p-5 text-center">
        <h2 class="fw-bold">Need medical advice or a prescription refill?</h2>
        <p class="mb-4">Our team is ready to help you with safe, professional support.</p>
        <a href="contact.php" class="btn btn-light btn-lg">Get in Touch</a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

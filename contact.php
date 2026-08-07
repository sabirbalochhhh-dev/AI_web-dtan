<?php
$pageTitle = 'Contact | HealthCare Pharmacy';
include __DIR__ . '/includes/header.php';

$message = '';
$messageType = '';
$formData = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'message' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $messageText = trim($_POST['message'] ?? '');

    $formData = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'message' => $messageText
    ];

    $errors = [];
    if ($name === '' || mb_strlen($name) > 150) {
        $errors[] = 'Please enter a valid name (up to 150 characters).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
        $errors[] = 'Please enter a valid email address.';
    }
    if ($phone !== '' && mb_strlen($phone) > 50) {
        $errors[] = 'Phone number is too long.';
    }
    if ($messageText === '' || mb_strlen($messageText) > 1000) {
        $errors[] = 'Please enter a message between 1 and 1000 characters.';
    }

    if ($errors) {
        $message = implode('<br>', $errors);
        $messageType = 'danger';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, $phone, $messageText]);
            $message = 'Thank you! Your message has been received and stored securely.';
            $messageType = 'success';
            $formData = ['name' => '', 'email' => '', 'phone' => '', 'message' => ''];
        } catch (PDOException $e) {
            $message = 'We could not save your message right now. Please try again later.';
            $messageType = 'danger';
        }
    }
}
?>

<section class="container py-5 reveal">
    <div class="row g-4">
        <div class="col-lg-6">
            <span class="badge bg-primary mb-3">Get in touch</span>
            <h1 class="fw-bold mb-3">Contact HealthCare Pharmacy</h1>
            <p class="text-muted">We are here to help with medication needs, prescription questions, or general inquiries.</p>
            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm p-3">
                        <i class="fa-solid fa-location-dot text-primary mb-2"></i>
                        <strong>Address</strong>
                        <p class="mb-0 text-muted">123 Wellness Avenue, Downtown</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm p-3">
                        <i class="fa-solid fa-phone text-primary mb-2"></i>
                        <strong>Phone</strong>
                        <p class="mb-0 text-muted">+1 (555) 012-3456</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm p-3">
                        <i class="fa-solid fa-envelope text-primary mb-2"></i>
                        <strong>Email</strong>
                        <p class="mb-0 text-muted">info@healthcarepharmacy.com</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm p-3">
                        <i class="fa-solid fa-clock text-primary mb-2"></i>
                        <strong>Working hours</strong>
                        <p class="mb-0 text-muted">Mon-Sun: 8:00 AM - 9:00 PM</p>
                    </div>
                </div>
            </div>
            <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.123456789!2d-74.0060!3d40.7128!2m3!1f0!2f0!3f0!3m2!1m1!2s0x0%3A0x0!5e0!3m2!1m1!2s!5e0" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
        <div class="col-lg-6 reveal">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-3">Send a message</h3>
                    <?php if ($message): ?><div class="alert alert-<?php echo $messageType; ?>"><?php echo sanitize_input($message); ?></div><?php endif; ?>
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                        <div class="mb-3">
                            <label class="form-label">Your Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo sanitize_input($formData['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo sanitize_input($formData['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo sanitize_input($formData['phone']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="message" class="form-control" rows="5" required><?php echo sanitize_input($formData['message']); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

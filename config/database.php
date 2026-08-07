<?php
// Database configuration for HealthCare Pharmacy
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'healthcare_pharmacy';

$pdoOptions = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, $pdoOptions);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, $pdoOptions);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        die('Invalid CSRF token.');
    }
}

function sanitize_input($data): string {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function is_admin_logged_in(): bool {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function require_admin(): void {
    if (!is_admin_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
?>

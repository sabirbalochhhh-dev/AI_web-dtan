<?php
require_once __DIR__ . '/config/database.php';

$sql = [];
$sql[] = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$sql[] = "CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$sql[] = "CREATE TABLE IF NOT EXISTS medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category_id INT,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    availability TINYINT(1) DEFAULT 1,
    status TINYINT(1) DEFAULT 1,
    image VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
)";

$sql[] = "CREATE TABLE IF NOT EXISTS pharmacists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    specialty VARCHAR(150) NOT NULL,
    bio TEXT,
    photo VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$sql[] = "CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    image VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$sql[] = "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(50) DEFAULT '',
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

foreach ($sql as $query) {
    $pdo->exec($query);
}

$adminExists = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$adminExists->execute(['admin']);
if (!$adminExists->fetch()) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)')->execute(['admin', $hash]);
}

echo 'Database setup completed successfully.';

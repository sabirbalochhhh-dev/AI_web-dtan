<?php
require 'config/database.php';
try {
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM users');
    $row = $stmt->fetch();
    echo 'users_count=' . $row['total'] . PHP_EOL;
} catch (Throwable $e) {
    echo 'users_error=' . $e->getMessage() . PHP_EOL;
}

try {
    $stmt = $pdo->query('SHOW TABLES');
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo 'table=' . $row[0] . PHP_EOL;
    }
} catch (Throwable $e) {
    echo 'tables_error=' . $e->getMessage() . PHP_EOL;
}

try {
    $stmt = $pdo->query('SELECT username, password_hash FROM users');
    while ($row = $stmt->fetch()) {
        echo 'user=' . $row['username'] . ' hash=' . $row['password_hash'] . PHP_EOL;
        echo 'verify_admin123=' . (password_verify('admin123', $row['password_hash']) ? 'yes' : 'no') . PHP_EOL;
    }
} catch (Throwable $e) {
    echo 'verify_error=' . $e->getMessage() . PHP_EOL;
}

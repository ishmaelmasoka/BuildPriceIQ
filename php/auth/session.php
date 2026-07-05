<?php
require_once '../config/database.php';

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'logged_in' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role'],
            'supplier_id' => $_SESSION['supplier_id'] ?? null
        ]
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
?>
<?php
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get suppliers that are approved 
$stmt = $pdo->prepare("
    SELECT s.*, u.email, u.name, u.created_at
    FROM suppliers s
    JOIN users u ON s.user_id = u.id
    WHERE s.is_approved = 1
    ORDER BY s.created_at DESC
");
$stmt->execute();
$suppliers = $stmt->fetchAll();

echo json_encode($suppliers);
?>
<?php
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$stmt = $pdo->prepare("
    SELECT s.*, u.email, u.name 
    FROM suppliers s
    JOIN users u ON s.user_id = u.id
    WHERE s.is_approved = 0
");
$stmt->execute();
$suppliers = $stmt->fetchAll();

echo json_encode($suppliers);
?>
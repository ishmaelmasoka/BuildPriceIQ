<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$supplierId = $data['supplier_id'] ?? 0;
$action = $data['action'] ?? '';

if ($action === 'approve') {
    $stmt = $pdo->prepare("UPDATE suppliers SET is_approved = 1 WHERE id = ?");
    $stmt->execute([$supplierId]);
    echo json_encode(['success' => true, 'message' => 'Supplier approved']);
} elseif ($action === 'reject') {
    // Get user_id first
    $stmt = $pdo->prepare("SELECT user_id FROM suppliers WHERE id = ?");
    $stmt->execute([$supplierId]);
    $supplier = $stmt->fetch();
    
    // Delete supplier and user
    $stmt = $pdo->prepare("DELETE FROM suppliers WHERE id = ?");
    $stmt->execute([$supplierId]);
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$supplier['user_id']]);
    
    echo json_encode(['success' => true, 'message' => 'Supplier rejected']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
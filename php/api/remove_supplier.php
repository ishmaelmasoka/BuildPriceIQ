<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$supplierId = $data['supplier_id'] ?? 0;

if (!$supplierId) {
    echo json_encode(['success' => false, 'message' => 'Supplier ID required']);
    exit();
}

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Get user_id from supplier
    $stmt = $pdo->prepare("SELECT user_id FROM suppliers WHERE id = ?");
    $stmt->execute([$supplierId]);
    $supplier = $stmt->fetch();
    
    if (!$supplier) {
        echo json_encode(['success' => false, 'message' => 'Supplier not found']);
        exit();
    }
    
    $userId = $supplier['user_id'];
    
    // Delete price history first (foreign key constraint)
    $stmt = $pdo->prepare("DELETE FROM price_history WHERE supplier_id = ?");
    $stmt->execute([$supplierId]);
    
    // Delete supplier record
    $stmt = $pdo->prepare("DELETE FROM suppliers WHERE id = ?");
    $stmt->execute([$supplierId]);
    
    // Delete user account
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    
    // Commit transaction
    $pdo->commit();
    
    echo json_encode(['success' => true, 'message' => 'Supplier removed successfully']);
    
} catch(Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

?>
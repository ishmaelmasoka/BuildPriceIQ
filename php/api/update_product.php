<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit();
}

$materialId = $data['product_id'] ?? 0;
$price = floatval($data['price'] ?? 0);
$supplierId = $_SESSION['supplier_id'];

if ($price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Valid price required']);
    exit();
}

try {
    // Insert new price record
    $stmt = $pdo->prepare("INSERT INTO price_history (supplier_id, material_id, price) VALUES (?, ?, ?)");
    $stmt->execute([$supplierId, $materialId, $price]);
    
    echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
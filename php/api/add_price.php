<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$materialName = trim($data['material'] ?? '');
$price = floatval($data['price'] ?? 0);
$stock = $data['stock'] ?? '';
$supplierId = $_SESSION['supplier_id'];

if (empty($materialName) || $price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Valid material and price required']);
    exit();
}

try {
    // Check if material exists
    $materialStmt = $pdo->prepare("SELECT id FROM materials WHERE name LIKE ?");
    $materialStmt->execute(["%$materialName%"]);
    $material = $materialStmt->fetch();
    
    if (!$material) {
        $insertMaterial = $pdo->prepare("INSERT INTO materials (name, category, unit) VALUES (?, 'other', 'piece')");
        $insertMaterial->execute([$materialName]);
        $materialId = $pdo->lastInsertId();
    } else {
        $materialId = $material['id'];
    }
    
    // Insert price
    $priceStmt = $pdo->prepare("INSERT INTO price_history (supplier_id, material_id, price) VALUES (?, ?, ?)");
    $priceStmt->execute([$supplierId, $materialId, $price]);
    
    // Update stock 
    if (!empty($stock)) {
        $stockStmt = $pdo->prepare("UPDATE suppliers SET stock_items = CONCAT(IFNULL(stock_items, ''), ?) WHERE id = ?");
        $stockStmt->execute(["$materialName: $stock\n", $supplierId]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Price added successfully']);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
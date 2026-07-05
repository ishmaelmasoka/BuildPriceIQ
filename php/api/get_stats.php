<?php
require_once '../config/database.php';

try {
    // Get supplier count
    $supplierStmt = $pdo->query("SELECT COUNT(*) as count FROM suppliers WHERE is_approved = 1");
    $supplierCount = $supplierStmt->fetch()['count'];
    
    // Get material count
    $materialStmt = $pdo->query("SELECT COUNT(*) as count FROM materials");
    $materialCount = $materialStmt->fetch()['count'];
    
    // Get price update count 
    $priceStmt = $pdo->query("SELECT COUNT(*) as count FROM price_history WHERE date_recorded >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $priceUpdates = $priceStmt->fetch()['count'];
    
    echo json_encode([
        'supplier_count' => $supplierCount,
        'material_count' => $materialCount,
        'price_updates' => $priceUpdates
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'supplier_count' => 0,
        'material_count' => 0,
        'price_updates' => 0
    ]);
}
?>
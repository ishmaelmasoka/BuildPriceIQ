<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$supplierId = $_SESSION['supplier_id'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            m.id,
            m.name as material_name,
            m.category,
            ph.price,
            ph.date_recorded as last_updated
        FROM price_history ph
        JOIN materials m ON ph.material_id = m.id
        WHERE ph.supplier_id = ?
        ORDER BY ph.date_recorded DESC
    ");
    $stmt->execute([$supplierId]);
    $products = $stmt->fetchAll();
    
    // Return empty array if no products 
    echo json_encode($products);
    
} catch(Exception $e) {
    echo json_encode([]); // Return empty array on error
}
?>
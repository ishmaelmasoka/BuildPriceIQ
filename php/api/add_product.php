<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

// Check if logged in as supplier
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if ($_SESSION['user_role'] !== 'supplier') {
    echo json_encode(['success' => false, 'message' => 'Not a supplier account']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit();
}

$materialName = trim($data['material_name'] ?? '');
$price = floatval($data['price'] ?? 0);
$stock = trim($data['stock'] ?? '');
$category = trim($data['category'] ?? 'other');
$supplierId = $_SESSION['supplier_id'];

if (empty($materialName)) {
    echo json_encode(['success' => false, 'message' => 'Material name required']);
    exit();
}

if ($price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Valid price required']);
    exit();
}

try {
    // Check if material exists
    $stmt = $pdo->prepare("SELECT id FROM materials WHERE name = ?");
    $stmt->execute([$materialName]);
    $material = $stmt->fetch();
    
    if (!$material) {
        // Insert new material
        $stmt = $pdo->prepare("INSERT INTO materials (name, category, unit) VALUES (?, ?, 'piece')");
        $stmt->execute([$materialName, $category]);
        $materialId = $pdo->lastInsertId();
    } else {
        $materialId = $material['id'];
    }
    
    // Insert price
    $stmt = $pdo->prepare("INSERT INTO price_history (supplier_id, material_id, price) VALUES (?, ?, ?)");
    $stmt->execute([$supplierId, $materialId, $price]);
    
    // Update stock for materials
    if (!empty($stock)) {
        // Get current stock
        $stmt = $pdo->prepare("SELECT stock_items FROM suppliers WHERE id = ?");
        $stmt->execute([$supplierId]);
        $currentStock = $stmt->fetchColumn();
        
        // Parse existing stock
        $stockLines = [];
        if ($currentStock) {
            $lines = explode("\n", $currentStock);
            foreach ($lines as $line) {
                if (!empty(trim($line))) {
                    $parts = explode(':', $line, 2);
                    if (count($parts) == 2) {
                        $stockLines[trim($parts[0])] = trim($parts[1]);
                    }
                }
            }
        }
        
        // Update or add stock for this material
        $stockLines[$materialName] = $stock;
        
        // Rebuild stock string
        $newStock = '';
        foreach ($stockLines as $name => $qty) {
            $newStock .= $name . ': ' . $qty . "\n";
        }
        
        // Save back to database
        $stmt = $pdo->prepare("UPDATE suppliers SET stock_items = ? WHERE id = ?");
        $stmt->execute([trim($newStock), $supplierId]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Product added successfully']);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
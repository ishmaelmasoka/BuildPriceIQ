<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';

try {
    // If no search term inserted
    if (empty($query) && empty($category)) {
        echo json_encode(['message' => 'Enter a search term']);
        exit();
    }
    
    // Build query
    $sql = "
        SELECT 
            s.business_name as supplier_name,
            m.name as material_name,
            m.category,
            ph.price,
            s.location,
            s.phone as supplier_phone,
            s.stock_items as stock,
            ph.date_recorded
        FROM price_history ph
        JOIN suppliers s ON ph.supplier_id = s.id
        JOIN materials m ON ph.material_id = m.id
        WHERE s.is_approved = 1
    ";
    
    $params = [];
    
    if (!empty($query)) {
        $sql .= " AND (m.name LIKE :query OR m.category LIKE :query)";
        $params[':query'] = "%$query%";
    }
    
    if (!empty($category)) {
        $sql .= " AND m.category = :category";
        $params[':category'] = $category;
    }
    
    if (!empty($location)) {
        $sql .= " AND s.location = :location";
        $params[':location'] = $location;
    }
    
    $sql .= " ORDER BY ph.price ASC LIMIT 50";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
    
    // stock for each result
    foreach ($results as &$result) {
        $result['stock_display'] = '';
        if (!empty($result['stock'])) {
            $lines = explode("\n", $result['stock']);
            foreach ($lines as $line) {
                if (strpos($line, $result['material_name']) === 0) {
                    $result['stock_display'] = trim(str_replace($result['material_name'] . ':', '', $line));
                    break;
                }
            }
        }
    }
    
    echo json_encode($results);
    
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

$material = $_GET['material'] ?? 'cement';
$months = intval($_GET['months'] ?? 6);

try {
    // Get material ID (try category first, then name)
    $stmt = $pdo->prepare("SELECT id, name FROM materials WHERE category = ? OR name LIKE ? LIMIT 1");
    $stmt->execute([$material, "%$material%"]);
    $materialData = $stmt->fetch();
    
    if (!$materialData) {
        // No material found - return demo
        echo json_encode([
            'dates' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'prices' => [11.50, 11.80, 12.00, 12.30, 12.50, 12.75],
            'prediction' => ['next_month' => 13.00, 'trend' => 'increasing', 'change' => 2.0],
            'message' => 'Material not found'
        ]);
        exit();
    }
    
    // Get price history
    $stmt = $pdo->prepare("
        SELECT 
            DATE_FORMAT(date_recorded, '%Y-%m') as month,
            AVG(price) as avg_price
        FROM price_history
        WHERE material_id = ? AND date_recorded >= DATE_SUB(NOW(), INTERVAL ? MONTH)
        GROUP BY DATE_FORMAT(date_recorded, '%Y-%m')
        ORDER BY month ASC
    ");
    $stmt->execute([$materialData['id'], $months]);
    $results = $stmt->fetchAll();
    
    if (count($results) == 0) {
        // No price history - return demo
        echo json_encode([
            'dates' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'prices' => [11.50, 11.80, 12.00, 12.30, 12.50, 12.75],
            'prediction' => ['next_month' => 13.00, 'trend' => 'increasing', 'change' => 2.0],
            'message' => 'No price history for ' . $materialData['name']
        ]);
        exit();
    }
    
    $dates = [];
    $prices = [];
    foreach ($results as $row) {
        $dates[] = date('M Y', strtotime($row['month'] . '-01'));
        $prices[] = round($row['avg_price'], 2);
    }
    
    // Prediction
    $prediction = null;
    if (count($prices) >= 3) {
        $lastThree = array_slice($prices, -3);
        $trend = ($lastThree[2] - $lastThree[0]) / 2;
        $nextMonth = round($lastThree[2] + $trend, 2);
        $trendDirection = $trend > 0 ? 'increasing' : ($trend < 0 ? 'decreasing' : 'stable');
        $changePercent = round(abs($trend) / $lastThree[2] * 100, 1);
        $prediction = ['next_month' => $nextMonth, 'trend' => $trendDirection, 'change' => $changePercent];
    }
    
    echo json_encode([
        'dates' => $dates,
        'prices' => $prices,
        'prediction' => $prediction,
        'material' => $materialData['name']
    ]);
    
} catch(Exception $e) {
    echo json_encode([
        'dates' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        'prices' => [11.50, 11.80, 12.00, 12.30, 12.50, 12.75],
        'prediction' => ['next_month' => 13.00, 'trend' => 'increasing', 'change' => 2.0],
        'error' => $e->getMessage()
    ]);
}
?>
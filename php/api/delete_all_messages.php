<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

// Check if logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM contact_messages");
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'All messages deleted successfully']);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
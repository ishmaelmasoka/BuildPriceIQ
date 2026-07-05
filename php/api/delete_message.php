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
$messageId = $data['message_id'] ?? 0;

if (!$messageId) {
    echo json_encode(['success' => false, 'message' => 'Message ID required']);
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->execute([$messageId]);
    
    echo json_encode(['success' => true, 'message' => 'Message deleted successfully']);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
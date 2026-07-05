<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$messageId = $data['message_id'] ?? 0;

$stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
$stmt->execute([$messageId]);

echo json_encode(['success' => true]);
?>
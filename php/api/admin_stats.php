<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$pending = $pdo->query("SELECT COUNT(*) FROM suppliers WHERE is_approved = 0")->fetchColumn();
$approved = $pdo->query("SELECT COUNT(*) FROM suppliers WHERE is_approved = 1")->fetchColumn();
$total = $pdo->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();
$unread = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();

echo json_encode([
    'pending' => (int)$pending,
    'approved' => (int)$approved,
    'total' => (int)$total,
    'unread' => (int)$unread
]);
?>
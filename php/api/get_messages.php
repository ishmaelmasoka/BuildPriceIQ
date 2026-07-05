<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM contact_messages ORDER BY created_at DESC");
$stmt->execute();
$messages = $stmt->fetchAll();

echo json_encode($messages);
?>
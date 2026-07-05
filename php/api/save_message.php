<?php
require_once '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$subject = $data['subject'] ?? '';
$message = $data['message'] ?? '';

if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'All fields required']);
    exit();
}

$stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
$stmt->execute([$name, $email, $subject, $message]);

echo json_encode(['success' => true]);
?>
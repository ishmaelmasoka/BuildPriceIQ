<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit();
}

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password required']);
    exit();
}

try {
    // Get user with supplier info
    $stmt = $pdo->prepare("
        SELECT u.*, s.id as supplier_id, s.business_name, s.is_approved 
        FROM users u 
        LEFT JOIN suppliers s ON u.id = s.user_id 
        WHERE u.email = ?
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit();
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit();
    }
    
    // CHECK: If supplier account is NOT approved
    if ($user['role'] === 'supplier' && $user['is_approved'] != 1) {
        echo json_encode(['success' => false, 'message' => 'Account pending admin approval. You will be notified once approved.']);
        exit();
    }
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['supplier_id'] = $user['supplier_id'] ?? null;
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'supplier_id' => $user['supplier_id'] ?? null
        ]
    ]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
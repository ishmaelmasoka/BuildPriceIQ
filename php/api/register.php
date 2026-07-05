<?php
require_once '../config/database.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit();
}

// Validate required fields
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$role = $data['role'] ?? 'customer';

if (empty($name) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

try {
    // Check if email already exists
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->execute([$email]);
    
    if ($checkStmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit();
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Begin transaction
    $pdo->beginTransaction();
    
    // Insert user
    $userStmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $userStmt->execute([$name, $email, $hashedPassword, $role]);
    $userId = $pdo->lastInsertId();
    
    // If supplier, add supplier details
if ($role === 'supplier') {
    $businessName = trim($data['business_name'] ?? '');
    $location = trim($data['location'] ?? '');
    $phone = trim($data['phone'] ?? '');
    
    if (empty($businessName) || empty($location) || empty($phone)) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Supplier details are required']);
        exit();
    }
    
    // making is_approved FALSE by default
    $supplierStmt = $pdo->prepare("INSERT INTO suppliers (user_id, business_name, location, phone, is_approved) VALUES (?, ?, ?, ?, 0)");
    $supplierStmt->execute([$userId, $businessName, $location, $phone]);
}
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful! Awaiting Admin Approval.',
        'user_id' => $userId
    ]);
    
} catch(PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
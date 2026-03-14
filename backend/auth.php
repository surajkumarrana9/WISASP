<?php
// WISASP Auth Backend (PPT: password_hash + session management)
// Handles register/login, exact schema fields, role-based session

session_start();  // PPT: Session for user_id/role across api.php chat
header('Content-Type: application/json');
require_once 'db.php';

$action = $_POST['action'] ?? '';
$fields = [];  // Sanitized fields

// Input sanitization (PPT: Secure prepared stmts)
$email = trim($conn->real_escape_string($_POST['email'] ?? ''));
switch ($action) {
    case 'register':
        $fields = [
            'fullname' => trim($conn->real_escape_string($_POST['fullname'] ?? '')),
            'password' => $_POST['password'] ?? '',
            'role' => $_POST['role'] ?? '',
            'phone' => trim($conn->real_escape_string($_POST['phone'] ?? '')),
            'experience' => trim($conn->real_escape_string($_POST['experience'] ?? '')),
            'company_name' => trim($conn->real_escape_string($_POST['company_name'] ?? ''))
        ];
        break;
    case 'login':
        $fields['password'] = $_POST['password'] ?? '';
        $fields['role'] = $_POST['role'] ?? '';
        break;
}

if (empty($email) || empty($fields['password']) || empty($fields['role'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

if ($action === 'register') {
    // PPT: Duplicate email check + hash
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit;
    }

    $hashed = password_hash($fields['password'], PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (fullname, email, password, role, phone, experience, company_name) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $fields['fullname'], $email, $hashed, $fields['role'], $fields['phone'], $fields['experience'], $fields['company_name']);
    
    if ($stmt->execute()) {
        $user_id = $conn->insert_id;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $fields['role'];
        echo json_encode(['success' => true, 'message' => 'Registered successfully', 'user_id' => $user_id, 'role' => $fields['role']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $conn->error]);
    }
    $stmt->close();

} elseif ($action === 'login') {
    // PPT: password_verify + role match
    $sql = "SELECT id, password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        if (password_verify($fields['password'], $user['password']) && $fields['role'] === $user['role']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            echo json_encode(['success' => true, 'message' => 'Login successful', 'user_id' => $user['id'], 'role' => $user['role']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials or role mismatch']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>


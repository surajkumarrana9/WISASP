<?php
// WISASP AI Chat API (PPT: Session-driven JSON chat_log append, dynamic replies by topic)
// Data flow: Frontend POST msg+topic -> Validate session -> Ensure interview row -> Append log -> Dynamic reply

session_start();
ob_start();
header('Content-Type: application/json');
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["reply" => "Session expired. Please login again."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = trim($_POST['msg'] ?? '');
$topic = trim($_POST['topic'] ?? localStorage? 'General' : 'General');  // Fallback

if (empty($msg)) {
    echo json_encode(["reply" => "No message received."]);
    exit;
}

// PPT Step 1: Ensure interview row exists for user+topic (create if new)
$check_sql = "SELECT id FROM interviews WHERE user_id = ? AND topic = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("is", $user_id, $topic);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    // Create new interview (PPT: On-demand sessions)
    $create_sql = "INSERT INTO interviews (user_id, topic, status) VALUES (?, ?, 'active')";
    $create_stmt = $conn->prepare($create_sql);
    $create_stmt->bind_param("is", $user_id, $topic);
    $create_stmt->execute();
    $interview_id = $conn->insert_id;
    $create_stmt->close();
} 
$check_stmt->close();

// PPT Step 2: Dynamic AI reply by topic keywords (expandable)
$replies = [
    'Python' => ["Good Python knowledge. Show me a code example.", "Explain decorators with code.", "What's your approach to list comprehensions?"],
    'JavaScript' => ["React hooks best practices?", "Async/await vs Promises?", "Node.js event loop?"],
    'Data Science' => ["Pandas vs NumPy?", "Model deployment strategies?", "Feature engineering example?"],
    'HR Behavioral' => ["Tell me about a challenging team situation.", "Why do you want this role?", "Strengths and weaknesses?"],
    'DevOps' => ["CI/CD pipeline design?", "Kubernetes vs Docker Swarm?", "Infrastructure as Code?"],
    'Marketing' => ["Digital campaign ROI measurement?", "SEO vs PPC?", "Customer journey mapping?"],
    'default' => ["Interesting point. Can you elaborate?", "Tell me more about your experience.", "Great response!"]
];

$topic_key = strpos($topic, 'Python') !== false ? 'Python' : 
             (strpos($topic, 'JavaScript') !== false ? 'JavaScript' :
             (strpos($topic, 'Data Science') !== false ? 'Data Science' :
             (strpos($topic, 'HR') !== false ? 'HR Behavioral' :
             (strpos($topic, 'DevOps') !== false ? 'DevOps' :
             (strpos($topic, 'Marketing') !== false ? 'Marketing' : 'default')))));

$aiReply = $replies[$topic_key][array_rand($replies[$topic_key])];

// PPT Step 3: Append to JSON chat_log (atomic update)
$newEntry = json_encode(["u" => htmlspecialchars($msg), "a" => $aiReply]);
$update_sql = "UPDATE interviews SET chat_log = JSON_ARRAY_APPEND(COALESCE(chat_log, JSON_ARRAY()), '$', ?), status = 'active' WHERE user_id = ? AND topic = ?";
$stmt = $conn->prepare($update_sql);
$stmt->bind_param("sis", $newEntry, $user_id, $topic);

if ($stmt->execute()) {
    $response = ["reply" => $aiReply];
} else {
    $response = ["reply" => "Update failed: " . $conn->error];
}

$stmt->close();
$conn->close();
ob_end_clean();
echo json_encode($response);
?>


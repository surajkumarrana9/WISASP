<?php
// WISASP DB Connection (PPT: Port 3307, session-ready)
session_start();  // Ensure sessions available for auth/api

$conn = new mysqli("127.0.0.1", "root", "", "wisasp_db", 3307);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    die(json_encode(["error" => "DB connection failed: " . $conn->connect_error]));
}

// Set charset (PPT: UTF-8 for JSON chat_log)
$conn->set_charset("utf8mb4");
?>
</xai:function_call.




<xai:function_call name="create_file">
<parameter name="absolute_path">c:/xampp/htdocs/WISASP/frontend/lobby.html

<?php
session_name('user_session');
session_start();
require_once '../connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$conversation_id = isset($_POST['conversation_id']) ? intval($_POST['conversation_id']) : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if ($conversation_id <= 0 || empty($message)) {
    echo json_encode(['error' => 'Invalid data']);
    exit();
}

// Verify user is a participant
$verify = "SELECT * FROM conversation_participants WHERE conversation_id = $conversation_id AND user_id = $user_id";
$res = mysqli_query($conn, $verify);
if (mysqli_num_rows($res) == 0) {
    echo json_encode(['error' => 'Not a participant']);
    exit();
}

$message = mysqli_real_escape_string($conn, $message);
$insert = "INSERT INTO messages (conversation_id, sender_id, message) VALUES ($conversation_id, $user_id, '$message')";
if (mysqli_query($conn, $insert)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to send message']);
}

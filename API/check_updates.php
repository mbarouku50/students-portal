<?php
session_start();
require_once '../connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$conversation_id = isset($_GET['conversation_id']) ? intval($_GET['conversation_id']) : 0;
$last_message_id = isset($_GET['last_message_id']) ? intval($_GET['last_message_id']) : 0;

if ($conversation_id <= 0 || $last_message_id < 0) {
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

// Get new messages
$query = "SELECT m.*, u.fullname as sender_name, u.profile_picture FROM messages m INNER JOIN users u ON m.sender_id = u.user_id WHERE m.conversation_id = $conversation_id AND m.id > $last_message_id ORDER BY m.created_at ASC";
$result = mysqli_query($conn, $query);
$messages = [];
while ($msg = mysqli_fetch_assoc($result)) {
    $msg['is_me'] = ($msg['sender_id'] == $user_id);
    $msg['time'] = date('g:i A', strtotime($msg['created_at']));
    $messages[] = $msg;
}

echo json_encode(['messages' => $messages]);

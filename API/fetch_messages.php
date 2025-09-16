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
$conversation_id = isset($_GET['conversation_id']) ? intval($_GET['conversation_id']) : 0;

if ($conversation_id <= 0) {
    echo json_encode(['error' => 'Invalid conversation ID']);
    exit();
}

// Verify user is a participant
$verify = "SELECT * FROM conversation_participants WHERE conversation_id = $conversation_id AND user_id = $user_id";
$res = mysqli_query($conn, $verify);
if (mysqli_num_rows($res) == 0) {
    echo json_encode(['error' => 'Not a participant']);
    exit();
}

// Get messages
$query = "SELECT m.*, u.fullname as sender_name, u.profile_picture FROM messages m INNER JOIN users u ON m.sender_id = u.user_id WHERE m.conversation_id = $conversation_id ORDER BY m.created_at ASC";
$result = mysqli_query($conn, $query);
$messages = [];
while ($msg = mysqli_fetch_assoc($result)) {
    $msg['is_me'] = ($msg['sender_id'] == $user_id);
    $msg['time'] = date('g:i A', strtotime($msg['created_at']));
    $messages[] = $msg;
}

// Mark messages as read
$mark_read = "UPDATE messages SET is_read = 1 WHERE conversation_id = $conversation_id AND sender_id != $user_id";
mysqli_query($conn, $mark_read);

echo json_encode(['messages' => $messages]);

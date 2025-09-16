<?php
// API endpoint for creating a new group conversation
require_once '../connection.php';
session_name('user_session');
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$title = isset($_POST['title']) ? mysqli_real_escape_string($conn, trim($_POST['title'])) : '';
$user_ids = isset($_POST['user_ids']) ? $_POST['user_ids'] : [];

if (empty($title)) {
    echo json_encode(['success' => false, 'error' => 'Group title is required']);
    exit;
}
if (empty($user_ids)) {
    echo json_encode(['success' => false, 'error' => 'Please select at least one participant']);
    exit;
}

$is_group = (count($user_ids) > 1) ? 1 : 0;
$insert_conversation = "INSERT INTO conversations (title, is_group, created_at) VALUES ('$title', $is_group, NOW())";
if (mysqli_query($conn, $insert_conversation)) {
    $conversation_id = mysqli_insert_id($conn);
    $participants = array_merge($user_ids, [$user_id]);
    $participants = array_unique($participants);
    $success = true;
    foreach ($participants as $participant_id) {
        $participant_id = intval($participant_id);
        $add_participant = "INSERT INTO conversation_participants (conversation_id, user_id) VALUES ($conversation_id, $participant_id)";
        if (!mysqli_query($conn, $add_participant)) {
            $success = false;
            break;
        }
    }
    if ($success) {
        echo json_encode(['success' => true, 'conversation_id' => $conversation_id]);
        exit;
    } else {
        echo json_encode(['success' => false, 'error' => 'Error adding participants to the conversation']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Error creating conversation: ' . mysqli_error($conn)]);
    exit;
}

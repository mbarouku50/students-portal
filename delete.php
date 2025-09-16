<?php
// delete.php
session_name('user_session');
session_start();
require_once 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['conversation_id'])) {
    $conversation_id = intval($_POST['conversation_id']);
    
    // Verify user is a participant
    $verify_participant = "SELECT * FROM conversation_participants 
                          WHERE conversation_id = $conversation_id AND user_id = $user_id";
    $verify_result = mysqli_query($conn, $verify_participant);
    
    if (mysqli_num_rows($verify_result) > 0) {
        // Remove user from conversation participants
        $delete_participant = "DELETE FROM conversation_participants 
                              WHERE conversation_id = $conversation_id AND user_id = $user_id";
        $result = mysqli_query($conn, $delete_participant);
        
        if ($result) {
            // Check if there are any participants left
            $check_participants = "SELECT COUNT(*) as count FROM conversation_participants 
                                  WHERE conversation_id = $conversation_id";
            $result = mysqli_query($conn, $check_participants);
            $data = mysqli_fetch_assoc($result);
            
            if ($data['count'] == 0) {
                // No participants left, delete the conversation and all messages
                $delete_messages = "DELETE FROM messages WHERE conversation_id = $conversation_id";
                mysqli_query($conn, $delete_messages);
                
                $delete_conversation = "DELETE FROM conversations WHERE id = $conversation_id";
                mysqli_query($conn, $delete_conversation);
            }
            
            echo json_encode(['success' => true]);
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'You are not a participant of this conversation']);
        exit();
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
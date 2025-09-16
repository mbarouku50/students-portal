<?php
session_name('user_session');
session_start();

// charts.php
require_once 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include("temperate/header.php");

$user_id = $_SESSION['user_id'];
        
        // Handle conversation creation
if (isset($_POST['create_conversation'])) {
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $user_ids = isset($_POST['user_ids']) ? $_POST['user_ids'] : [];
    
    // Validate inputs
    if (empty($title)) {
        $error = "Group title is required";
    } elseif (empty($user_ids)) {
        $error = "Please select at least one participant";
    } else {
        // Create conversation
        $is_group = (count($user_ids) > 1) ? 1 : 0;
        $insert_conversation = "INSERT INTO conversations (title, is_group, created_at) 
                                VALUES ('$title', $is_group, NOW())";
        if (mysqli_query($conn, $insert_conversation)) {
            $conversation_id = mysqli_insert_id($conn);
            
            // Add participants
            $participants = array_merge($user_ids, [$user_id]); // Include current user
            $participants = array_unique($participants);
            
            $success = true;
            foreach ($participants as $participant_id) {
                $participant_id = intval($participant_id);
                $add_participant = "INSERT INTO conversation_participants (conversation_id, user_id) 
                                    VALUES ($conversation_id, $participant_id)";
                if (!mysqli_query($conn, $add_participant)) {
                    $success = false;
                    break;
                }
            }
            
            if ($success) {
                header("Location: charts.php?conversation_id=$conversation_id");
                exit();
            } else {
                $error = "Error adding participants to the conversation";
            }
        } else {
            $error = "Error creating conversation: " . mysqli_error($conn);
        }
    }
    
    // If there was an error, store it in session to display later
    if (isset($error)) {
        $_SESSION['error'] = $error;
    }
}

// Handle starting a new individual conversation
if (isset($_GET['start_chat'])) {
    $other_user_id = intval($_GET['start_chat']);
    $user_id = $_SESSION['user_id'];
    
    // Check if conversation already exists
    $check_conversation = "SELECT c.conversation_id 
                          FROM conversations c
                          INNER JOIN conversation_participants cp1 ON c.conversation_id = cp1.conversation_id
                          INNER JOIN conversation_participants cp2 ON c.conversation_id = cp2.conversation_id
                          WHERE cp1.user_id = $user_id 
                          AND cp2.user_id = $other_user_id 
                          AND c.is_group = 0";
    $result = mysqli_query($conn, $check_conversation);
    
    if (mysqli_num_rows($result) > 0) {
        // Conversation exists, redirect to it
        $conversation = mysqli_fetch_assoc($result);
        header("Location: charts.php?conversation_id=" . $conversation['conversation_id']);
        exit();
    } else {
        // Create new conversation
        $other_user_query = "SELECT fullname FROM users WHERE user_id = $other_user_id";
        $other_user_result = mysqli_query($conn, $other_user_query);
        $other_user = mysqli_fetch_assoc($other_user_result);
        $title = $other_user['fullname'];
        
        $insert_conversation = "INSERT INTO conversations (title, is_group, created_at) 
                                VALUES ('$title', 0, NOW())";
        mysqli_query($conn, $insert_conversation);
        $conversation_id = mysqli_insert_id($conn);
        
        // Add participants
        $add_participant1 = "INSERT INTO conversation_participants (conversation_id, user_id) 
                            VALUES ($conversation_id, $user_id)";
        mysqli_query($conn, $add_participant1);
        
        $add_participant2 = "INSERT INTO conversation_participants (conversation_id, user_id) 
                            VALUES ($conversation_id, $other_user_id)";
        mysqli_query($conn, $add_participant2);
        
        header("Location: charts.php?conversation_id=$conversation_id");
        exit();
    }
}

// Update user's last seen timestamp
$user_id = $_SESSION['user_id'];
$update_last_seen = "UPDATE users SET last_seen = NOW() WHERE user_id = $user_id";
mysqli_query($conn, $update_last_seen);

// Get active conversation ID
$active_conversation_id = isset($_GET['conversation_id']) ? intval($_GET['conversation_id']) : 0;

// Fetch user's conversations
// Replace the conversations query with this improved version
$conversations_query = "SELECT c.*, 
                        (SELECT message FROM messages WHERE conversation_id = c.conversation_id ORDER BY created_at DESC LIMIT 1) as last_message,
                        (SELECT created_at FROM messages WHERE conversation_id = c.conversation_id ORDER BY created_at DESC LIMIT 1) as last_message_time,
                        (SELECT COUNT(*) FROM messages WHERE conversation_id = c.conversation_id AND is_read = 0 AND sender_id != $user_id) as unread_count
                        FROM conversations c
                        INNER JOIN conversation_participants cp ON c.conversation_id = cp.conversation_id
                        WHERE cp.user_id = $user_id
                        GROUP BY c.conversation_id
                        ORDER BY last_message_time DESC";
                        
$conversations_result = mysqli_query($conn, $conversations_query);
$conversations = mysqli_fetch_all($conversations_result, MYSQLI_ASSOC);

// Format conversation data
foreach ($conversations as &$conv) {
    if ($conv['is_group']) {
        // For group chats, get participant count
        $count_query = "SELECT COUNT(*) as participant_count FROM conversation_participants WHERE conversation_id = {$conv['conversation_id']}";
        $count_result = mysqli_query($conn, $count_query);
        $count_data = mysqli_fetch_assoc($count_result);
        $conv['participants'] = $count_data['participant_count'];
    } else {
        // For individual chats, get the other participant's name
        $other_user_query = "SELECT u.user_id, u.fullname, u.profile_picture, u.last_seen 
                             FROM users u
                             INNER JOIN conversation_participants cp ON u.user_id = cp.user_id
                             WHERE cp.conversation_id = {$conv['conversation_id']} AND u.user_id != $user_id
                             LIMIT 1";
        $other_user_result = mysqli_query($conn, $other_user_query);
        $other_user = mysqli_fetch_assoc($other_user_result);
        
        if ($other_user) {
            $conv['name'] = $other_user['fullname'];
            $conv['profile_picture'] = $other_user['profile_picture'];
            $conv['last_seen'] = $other_user['last_seen'];
        }
    }
    
    // Format time
    if ($conv['last_message_time']) {
        $timestamp = strtotime($conv['last_message_time']);
        $now = time();
        $diff = $now - $timestamp;
        
        if ($diff < 60) {
            $conv['time'] = 'Just now';
        } elseif ($diff < 3600) {
            $conv['time'] = floor($diff / 60) . ' min ago';
        } elseif ($diff < 86400) {
            $conv['time'] = date('g:i A', $timestamp);
        } else {
            $conv['time'] = date('M j', $timestamp);
        }
    } else {
        $conv['time'] = '';
    }
}

// Fetch active conversation data if selected
$active_chat = null;
$messages = [];

if ($active_conversation_id > 0) {
    // Verify user is a participant
    $verify_participant = "SELECT * FROM conversation_participants WHERE conversation_id = $active_conversation_id AND user_id = $user_id";
    $verify_result = mysqli_query($conn, $verify_participant);
    
    if (mysqli_num_rows($verify_result) > 0) {
        // Get conversation details
        $conversation_query = "SELECT * FROM conversations WHERE conversation_id = $active_conversation_id";
        $conversation_result = mysqli_query($conn, $conversation_query);
        $active_chat = mysqli_fetch_assoc($conversation_result);
        
        // Get participants for group chats
        if ($active_chat['is_group']) {
            $participants_query = "SELECT u.user_id, u.fullname, u.profile_picture, u.last_seen 
                                   FROM users u
                                   INNER JOIN conversation_participants cp ON u.user_id = cp.user_id
                                   WHERE cp.conversation_id = $active_conversation_id";
            $participants_result = mysqli_query($conn, $participants_query);
            $active_chat['participants'] = mysqli_fetch_all($participants_result, MYSQLI_ASSOC);
        } else {
            // For individual chats, get the other participant
            $other_user_query = "SELECT u.user_id, u.fullname, u.profile_picture, u.last_seen 
                                 FROM users u
                                 INNER JOIN conversation_participants cp ON u.user_id = cp.user_id
                                 WHERE cp.conversation_id = $active_conversation_id AND u.user_id != $user_id
                                 LIMIT 1";
            $other_user_result = mysqli_query($conn, $other_user_query);
            $other_user = mysqli_fetch_assoc($other_user_result);
            
            if ($other_user) {
                $active_chat['name'] = $other_user['fullname'];
                $active_chat['profile_picture'] = $other_user['profile_picture'];
                $active_chat['last_seen'] = $other_user['last_seen'];
            }
        }
        
    // Messages are now fetched via AJAX from API/fetch_messages.php
    // ...existing code...
    } else {
        // User is not a participant, redirect to chat list
        header("Location: charts.php");
        exit();
    }
}

// Fetch other users for new conversation
$users_query = "SELECT user_id, fullname, profile_picture, last_seen FROM users WHERE user_id != $user_id ORDER BY fullname";
$users_result = mysqli_query($conn, $users_query);
$other_users = mysqli_fetch_all($users_result, MYSQLI_ASSOC);
?>

<!-- show if there any error-->
<?php if (isset($_SESSION['error'])): ?>
<div class="error-message" style="position: fixed; top: 80px; left: 50%; transform: translateX(-50%); background: #ffebee; color: #c62828; padding: 10px 20px; border-radius: 4px; z-index: 1000; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
    <?= $_SESSION['error'] ?>
    <button onclick="this.parentElement.style.display='none'" style="background: none; border: none; color: #c62828; margin-left: 10px; cursor: pointer;">×</button>
</div>
<?php unset($_SESSION['error']); ?>
<?php endif; ?>
<!--end  show if there any error-->

    <div class="chat-container">
        <div class="chat-sidebar">
            <div class="chat-header">
                <h2>Chats</h2>
                <div class="chat-actions">
                    <button class="icon-button" id="newChatBtn" title="New group conversation">
                        <i class="fas fa-users"></i>
                    </button>
                </div>
            </div>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search messages or users" id="chatSearch">
            </div>
            <div class="sidebar-section">
                <h4 style="margin:10px 0 5px 15px; color:#65676b;">All Users</h4>
                <div class="users-list" style="max-height:150px; overflow-y:auto; border-bottom:1px solid #e6e6e6;">
                    <?php foreach ($other_users as $user): ?>
                    <a href="charts.php?start_chat=<?= $user['user_id'] ?>" class="user-link" style="display:flex;align-items:center;padding:8px 15px;text-decoration:none;color:inherit;">
                        <div class="avatar small <?= isUserOnline($user['last_seen']) ? 'online' : '' ?>">
                            <?php if (!empty($user['profile_picture'])): ?>
                                <img src="<?= $user['profile_picture'] ?>" alt="<?= $user['fullname'] ?>">
                            <?php else: ?>
                                <i class="fas fa-user"></i>
                            <?php endif; ?>
                        </div>
                        <span style="flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($user['fullname']) ?></span>
                        <span style="font-size:12px;color:#31a24c; margin-left:8px;">
                            <?= isUserOnline($user['last_seen']) ? 'Online' : 'Offline' ?>
                        </span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="sidebar-section">
                <h4 style="margin:10px 0 5px 15px; color:#65676b;">Your Conversations</h4>
                <div class="conversations-list">
                    <?php if (count($conversations) > 0): ?>
                        <?php foreach ($conversations as $conv): ?>
                        <a href="charts.php?conversation_id=<?= $conv['conversation_id'] ?>" class="conversation-link">
                            <div class="conversation-item <?= $active_conversation_id == $conv['conversation_id'] ? 'active' : '' ?>">
                                <div class="avatar <?= $conv['is_group'] ? 'group' : '' ?> <?= !$conv['is_group'] && isUserOnline($conv['last_seen']) ? 'online' : '' ?>">
                                    <?php if ($conv['is_group']): ?>
                                        <i class="fas fa-users"></i>
                                    <?php else: ?>
                                        <?php if (!empty($conv['profile_picture'])): ?>
                                            <img src="<?= $conv['profile_picture'] ?>" alt="<?= $conv['name'] ?>">
                                        <?php else: ?>
                                            <i class="fas fa-user"></i>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="conversation-details">
                                    <div class="conversation-header">
                                        <h3><?= htmlspecialchars($conv['is_group'] ? $conv['title'] : $conv['name']) ?></h3>
                                        <span class="time"><?= $conv['time'] ?></span>
                                    </div>
                                    <div class="conversation-preview">
                                        <p><?= !empty($conv['last_message']) ? htmlspecialchars(substr($conv['last_message'], 0, 30) . (strlen($conv['last_message']) > 30 ? '...' : '')) : 'No messages yet' ?></p>
                                        <?php if ($conv['unread_count'] > 0): ?>
                                            <span class="unread-count"><?= $conv['unread_count'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-conversations">
                            <p>No conversations yet. Start a new chat!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="chat-main">
            <?php if ($active_conversation_id > 0 && $active_chat): ?>
                <div class="chat-main-header">
                    <div class="active-chat-info">
                        <div class="avatar <?= $active_chat['is_group'] ? 'group' : '' ?> <?= !$active_chat['is_group'] && isUserOnline($active_chat['last_seen']) ? 'online' : '' ?>">
                            <?php if ($active_chat['is_group']): ?>
                                <i class="fas fa-users"></i>
                            <?php else: ?>
                                <?php if (!empty($active_chat['profile_picture'])): ?>
                                    <img src="<?= $active_chat['profile_picture'] ?>" alt="<?= $active_chat['name'] ?>">
                                <?php else: ?>
                                    <i class="fas fa-user"></i>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="chat-info">
                            <h3><?= htmlspecialchars($active_chat['is_group'] ? $active_chat['title'] : $active_chat['name']) ?></h3>
                            <div class="active-users">
                                <?php if ($active_chat['is_group']): ?>
                                    <?php
                                    $online_count = 0;
                                    foreach ($active_chat['participants'] as $participant) {
                                        if (isUserOnline($participant['last_seen'])) $online_count++;
                                    }
                                    ?>
                                    <span class="online-status"><?= count($active_chat['participants']) ?> members, <?= $online_count ?> online</span>
                                <?php else: ?>
                                    <span class="online-status"><?= isUserOnline($active_chat['last_seen']) ? 'Online' : 'Last seen ' . formatLastSeen($active_chat['last_seen']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="chat-actions">
                        <?php if (!$active_chat['is_group']): ?>
                            <button class="icon-button" title="Voice call">
                                <i class="fas fa-phone"></i>
                            </button>
                        <?php endif; ?>
                        <button class="icon-button" title="Conversation info">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <button class="icon-button delete-conversation" title="Delete conversation" 
                                data-conversation-id="<?= $active_conversation_id ?>">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                
                <div class="messages-container" id="messagesContainer">
                    <div class="messages">
                        <?php if (count($messages) > 0): ?>
                            <?php foreach ($messages as $msg): ?>
                            <div class="message <?= $msg['is_me'] ? 'sent' : 'received' ?>">
                                <?php if (!$msg['is_me']): ?>
                                <div class="avatar small">
                                    <?php if (!empty($msg['profile_picture'])): ?>
                                        <img src="<?= $msg['profile_picture'] ?>" alt="<?= $msg['sender_name'] ?>">
                                    <?php else: ?>
                                        <i class="fas fa-user"></i>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <div class="message-content">
                                    <?php if (!$msg['is_me'] && $active_chat['is_group']): ?>
                                    <div class="sender-name"><?= htmlspecialchars($msg['sender_name']) ?></div>
                                    <?php endif; ?>
                                    <div class="message-bubble">
                                        <p><?= htmlspecialchars($msg['message']) ?></p>
                                        <div class="message-time"><?= $msg['time'] ?></div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-messages">
                                <p>No messages yet. Start the conversation!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <form id="sendMessageForm" class="message-input-container" autocomplete="off">
                    <input type="hidden" name="conversation_id" value="<?= $active_conversation_id ?>">
                    <div class="message-input-actions">
                        <button type="button" class="icon-button" title="Add emoji">
                            <i class="far fa-smile"></i>
                        </button>
                        <button type="button" class="icon-button" title="Attach file">
                            <i class="fas fa-paperclip"></i>
                        </button>
                    </div>
                    <div class="message-input">
                        <input type="text" name="message" id="messageInput" placeholder="Type a message..." required autocomplete="off">
                    </div>
                    <div class="message-send">
                        <button type="submit" class="icon-button" title="Send message">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="no-chat-selected">
                    <div class="no-chat-content">
                        <i class="fas fa-comments"></i>
                        <h3>Select a conversation</h3>
                        <p>Choose a conversation from the sidebar or start a new one</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- New Group Conversation Modal -->
  
<div class="modal" id="newChatModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>New Group Conversation</h3>
            <button type="button" class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <?php if (isset($error)): ?>
            <div class="error-message" style="background: #ffebee; color: #c62828; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>
            
            <form id="ajaxGroupForm">
                <div class="form-group">
                    <label for="conversation_title">Group Title:</label>
                    <input type="text" id="conversation_title" name="title" placeholder="Enter group title" required>
                </div>
                <div class="form-group">
                    <label>Select Participants (select at least one):</label>
                    <div class="users-list">
                        <?php foreach ($other_users as $user): ?>
                        <div class="user-checkbox">
                            <input type="checkbox" name="user_ids[]" value="<?= $user['user_id'] ?>" id="user_<?= $user['user_id'] ?>">
                            <label for="user_<?= $user['user_id'] ?>">
                                <div class="avatar small <?= isUserOnline($user['last_seen']) ? 'online' : '' ?>">
                                    <?php if (!empty($user['profile_picture'])): ?>
                                        <img src="<?= $user['profile_picture'] ?>" alt="<?= $user['fullname'] ?>">
                                    <?php else: ?>
                                        <i class="fas fa-user"></i>
                                    <?php endif; ?>
                                </div>
                                <span><?= htmlspecialchars($user['fullname']) ?></span>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary close-modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Group</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <style>
    /* CSS styles remain the same as in the previous implementation */
    .chat-container {
        display: flex;
        height: calc(100vh - 80px);
        background: #fff;
        margin: 20px auto;
        max-width: 1200px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .chat-sidebar {
        width: 350px;
        border-right: 1px solid #e6e6e6;
        display: flex;
        flex-direction: column;
    }
    
    .chat-header {
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e6e6e6;
    }
    
    .chat-header h2 {
        margin: 0;
        font-size: 1.5rem;
        color: var(--primary-color);
    }
    
    .icon-button {
        background: none;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #65676b;
        font-size: 16px;
        transition: background-color 0.3s;
    }
    
    .icon-button:hover {
        background-color: #f0f2f5;
    }
    
    .chat-actions {
        display: flex;
        gap: 5px;
    }
    
    .search-box {
        padding: 10px 15px;
        position: relative;
        border-bottom: 1px solid #e6e6e6;
    }
    
    .search-box i {
        position: absolute;
        left: 30px;
        top: 50%;
        transform: translateY(-50%);
        color: #65676b;
    }
    
    .search-box input {
        width: 100%;
        padding: 10px 10px 10px 35px;
        border-radius: 20px;
        border: none;
        background-color: #f0f2f5;
        font-size: 14px;
    }
    
    .search-box input:focus {
        outline: none;
        background-color: #e4e6e9;
    }
    
    .conversations-list {
        flex: 1;
        overflow-y: auto;
    }
    
    .conversation-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }
    
    .conversation-item {
        display: flex;
        padding: 10px 15px;
        cursor: pointer;
        transition: background-color 0.3s;
        border-bottom: 1px solid #f0f2f5;
    }
    
    .conversation-item:hover {
        background-color: #f5f6f8;
    }
    
    .conversation-item.active {
        background-color: #e9ebee;
    }
    
    .avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: var(--secondary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-right: 10px;
        position: relative;
        overflow: hidden;
    }
    
    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .avatar.small {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
    
    .avatar.group {
        background-color: #5b6bff;
    }
    
    .avatar.online::after {
        content: '';
        position: absolute;
        width: 12px;
        height: 12px;
        background-color: #31a24c;
        border: 2px solid white;
        border-radius: 50%;
        bottom: 0;
        right: 0;
    }
    
    .conversation-details {
        flex: 1;
        min-width: 0;
    }
    
    .conversation-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }
    
    .conversation-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: var(--dark-color);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .conversation-header .time {
        font-size: 12px;
        color: #65676b;
    }
    
    .conversation-preview {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .conversation-preview p {
        margin: 0;
        font-size: 14px;
        color: #65676b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 1;
    }
    
    .unread-count {
        background-color: var(--secondary-color);
        color: white;
        font-size: 12px;
        font-weight: bold;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 5px;
    }
    
    .no-conversations {
        padding: 20px;
        text-align: center;
        color: #65676b;
    }
    
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .chat-main-header {
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e6e6e6;
    }
    
    .active-chat-info {
        display: flex;
        align-items: center;
    }
    
    .chat-info {
        margin-left: 10px;
    }
    
    .chat-info h3 {
        margin: 0;
        font-size: 16px;
        color: var(--dark-color);
    }
    
    .online-status {
        font-size: 13px;
        color: #65676b;
    }
    
    .online-status:before {
        content: '';
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #31a24c;
        margin-right: 5px;
    }
    
    .messages-container {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background-color: #f0f2f5;
        display: flex;
        flex-direction: column;
    }
    
    .messages {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .message {
        display: flex;
        margin-bottom: 15px;
        max-width: 70%;
    }
    
    .message.sent {
        align-self: flex-end;
        flex-direction: row-reverse;
    }
    
    .message-content {
        margin: 0 10px;
    }
    
    .sender-name {
        font-size: 12px;
        color: #65676b;
        margin-bottom: 5px;
        margin-left: 10px;
    }
    
    .message-bubble {
        background-color: white;
        padding: 10px 15px;
        border-radius: 18px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        position: relative;
    }
    
    .message.sent .message-bubble {
        background-color: var(--secondary-color);
        color: white;
    }
    
    .message-time {
        font-size: 11px;
        color: #65676b;
        text-align: right;
        margin-top: 5px;
    }
    
    .message.sent .message-time {
        color: rgba(255, 255, 255, 0.8);
    }
    
    .no-messages {
        text-align: center;
        padding: 40px;
        color: #65676b;
    }
    
    .message-input-container {
        padding: 15px 20px;
        display: flex;
        align-items: center;
        border-top: 1px solid #e6e6e6;
        background-color: white;
    }
    
    .message-input-actions, .message-send {
        flex: 0 0 auto;
    }
    
    .message-input {
        flex: 1;
        margin: 0 10px;
    }
    
    .message-input input {
        width: 100%;
        padding: 12px 15px;
        border-radius: 20px;
        border: none;
        background-color: #f0f2f5;
        font-size: 14px;
    }
    
    .message-input input:focus {
        outline: none;
        background-color: #e4e6e9;
    }
    
    .no-chat-selected {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f0f2f5;
    }
    
    .no-chat-content {
        text-align: center;
        color: #65676b;
    }
    
    .no-chat-content i {
        font-size: 60px;
        margin-bottom: 20px;
        color: #c1c7cd;
    }
    
    .no-chat-content h3 {
        margin-bottom: 10px;
        font-size: 24px;
    }
    
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
    }
    
    .modal.active {
        display: flex;
    }
    
    .modal-content {
        background-color: white;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        max-height: 80vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    
    .modal-header {
        padding: 15px 20px;
        border-bottom: 1px solid #e6e6e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-header h3 {
        margin: 0;
    }
    
    .close-modal {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #65676b;
    }
    
    .modal-body {
        padding: 20px;
        overflow-y: auto;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
    }
    
    .form-group input[type="text"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }
    
    .users-list {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #eee;
        border-radius: 4px;
        padding: 10px;
    }
    
    .user-checkbox {
        margin-bottom: 10px;
    }
    
    .user-checkbox:last-child {
        margin-bottom: 0;
    }
    
    .user-checkbox label {
        display: flex;
        align-items: center;
        cursor: pointer;
        margin-bottom: 0;
        font-weight: normal;
    }
    
    .user-checkbox .avatar {
        margin-right: 10px;
    }
    
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    
    .btn {
        padding: 8px 16px;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
    }
    
    .btn-primary {
        background-color: var(--secondary-color);
        color: white;
    }
    
    .btn-secondary {
        background-color: #e4e6eb;
        color: #4b4f56;
    }
    .delete-conversation {
    color: #e74c3c;
}

.delete-conversation:hover {
    background-color: rgba(231, 76, 60, 0.1);
}
    
    /* Responsive design */
    @media (max-width: 900px) {
        .chat-sidebar {
            width: 300px;
        }
        
        .message {
            max-width: 85%;
        }
    }
    
    @media (max-width: 768px) {
        .chat-container {
            flex-direction: column;
            height: auto;
            min-height: calc(100vh - 80px);
        }
        
        .chat-sidebar {
            width: 100%;
            border-right: none;
            border-bottom: 1px solid #e6e6e6;
            flex: 0 0 auto;
            max-height: 40vh;
        }
        
        .chat-main {
            flex: 1;
        }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const messagesContainer = document.getElementById('messagesContainer');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        // New conversation modal
        const newChatBtn = document.getElementById('newChatBtn');
        const newChatModal = document.getElementById('newChatModal');
        const closeModalButtons = document.querySelectorAll('.close-modal');
        // AJAX group creation
        const ajaxGroupForm = document.getElementById('ajaxGroupForm');
        if (ajaxGroupForm) {
            ajaxGroupForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(ajaxGroupForm);
                // Show loading indicator
                const submitBtn = ajaxGroupForm.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Creating...';
                fetch('API/create_group.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Create Group';
                    if (data.success) {
                        window.location.href = 'charts.php?conversation_id=' + data.conversation_id;
                    } else {
                        // Show error
                        let errorDiv = ajaxGroupForm.querySelector('.error-message');
                        if (!errorDiv) {
                            errorDiv = document.createElement('div');
                            errorDiv.className = 'error-message';
                            errorDiv.style.background = '#ffebee';
                            errorDiv.style.color = '#c62828';
                            errorDiv.style.padding = '10px';
                            errorDiv.style.borderRadius = '4px';
                            errorDiv.style.marginBottom = '15px';
                            ajaxGroupForm.prepend(errorDiv);
                        }
                        errorDiv.textContent = data.error || 'Unknown error';
                    }
                })
                .catch(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Create Group';
                    let errorDiv = ajaxGroupForm.querySelector('.error-message');
                    if (!errorDiv) {
                        errorDiv = document.createElement('div');
                        errorDiv.className = 'error-message';
                        errorDiv.style.background = '#ffebee';
                        errorDiv.style.color = '#c62828';
                        errorDiv.style.padding = '10px';
                        errorDiv.style.borderRadius = '4px';
                        errorDiv.style.marginBottom = '15px';
                        ajaxGroupForm.prepend(errorDiv);
                    }
                    errorDiv.textContent = 'Network error. Please try again.';
                });
            });
        }

        // Delete conversation functionality
    const deleteButtons = document.querySelectorAll('.delete-conversation');
deleteButtons.forEach(button => {
    button.addEventListener('click', function() {
        const conversationId = this.getAttribute('data-conversation-id');
        if (confirm('Are you sure you want to delete this conversation? This action cannot be undone.')) {
            const formData = new FormData();
            formData.append('conversation_id', conversationId);
            
            fetch('delete.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    window.location.href = 'charts.php';
                } else {
                    alert('Error deleting conversation: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting conversation. Please check your connection and try again.');
            });
        }
    });
});
        
        if (newChatBtn && newChatModal) {
            newChatBtn.addEventListener('click', function() {
                newChatModal.classList.add('active');
            });
        }
        
        closeModalButtons.forEach(button => {
            button.addEventListener('click', function() {
                newChatModal.classList.remove('active');
            });
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === newChatModal) {
                newChatModal.classList.remove('active');
            }
        });
        
        // Search functionality
        const chatSearch = document.getElementById('chatSearch');
        if (chatSearch) {
            chatSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const conversationItems = document.querySelectorAll('.conversation-item');
                
                conversationItems.forEach(item => {
                    const conversationName = item.querySelector('h3').textContent.toLowerCase();
                    const conversationPreview = item.querySelector('p').textContent.toLowerCase();
                    
                    if (conversationName.includes(searchTerm) || conversationPreview.includes(searchTerm)) {
                        item.parentElement.style.display = 'block';
                    } else {
                        item.parentElement.style.display = 'none';
                    }
                });
            });
        }
        
        // Real-time message fetching and sending with AJAX
        <?php if ($active_conversation_id > 0): ?>
        function fetchMessages() {
            fetch('API/fetch_messages.php?conversation_id=<?= $active_conversation_id ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.messages) {
                        const messagesDiv = document.querySelector('.messages');
                        if (!messagesDiv) return;
                        messagesDiv.innerHTML = '';
                        data.messages.forEach(msg => {
                            const msgDiv = document.createElement('div');
                            msgDiv.className = 'message ' + (msg.is_me ? 'sent' : 'received');
                            let avatarHtml = '';
                            if (!msg.is_me) {
                                avatarHtml = `<div class=\"avatar small\">${msg.profile_picture ? `<img src='${msg.profile_picture}' alt='${msg.sender_name}'>` : `<i class='fas fa-user'></i>`}</div>`;
                            }
                            let senderNameHtml = '';
                            if (!msg.is_me && <?= $active_chat['is_group'] ? 'true' : 'false' ?>) {
                                senderNameHtml = `<div class='sender-name'>${msg.sender_name}</div>`;
                            }
                            msgDiv.innerHTML = `${avatarHtml}<div class='message-content'>${senderNameHtml}<div class='message-bubble'><p>${msg.message}</p><div class='message-time'>${msg.time}</div></div></div>`;
                            messagesDiv.appendChild(msgDiv);
                        });
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }
                });
        }
        setInterval(fetchMessages, 3000);
        fetchMessages();

        // AJAX send message
        const sendMessageForm = document.getElementById('sendMessageForm');
        const messageInput = document.getElementById('messageInput');
        if (sendMessageForm && messageInput) {
            sendMessageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const message = messageInput.value.trim();
                if (!message) return;
                const formData = new FormData(sendMessageForm);
                fetch('API/send_message.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messageInput.value = '';
                        fetchMessages();
                    }
                });
            });
        }
        <?php endif; ?>
    });
    </script>
</body>
</html>

<?php
// Helper functions
function isUserOnline($lastSeen) {
    if (!$lastSeen) return false;
    
    $lastSeenTime = strtotime($lastSeen);
    $currentTime = time();
    
    // Consider user online if they were active in the last 5 minutes
    return ($currentTime - $lastSeenTime) < 300;
}

function formatLastSeen($lastSeen) {
    if (!$lastSeen) return 'a long time ago';
    
    $lastSeenTime = strtotime($lastSeen);
    $currentTime = time();
    $diff = $currentTime - $lastSeenTime;
    
    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . ' minutes ago';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . ' hours ago';
    } else {
        return floor($diff / 86400) . ' days ago';
    }
}
?>
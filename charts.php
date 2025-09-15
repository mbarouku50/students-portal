<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// charts.php
include("temperate/header.php");

// Sample data for demonstration - you'll replace this with actual data from your database
$conversations = [
    [
        'id' => 1,
        'name' => 'Mathematics Study Group',
        'last_message' => 'John: Did anyone solve problem 5?',
        'time' => '2:45 PM',
        'unread' => 3,
        'online' => true,
        'participants' => 5,
        'is_group' => true
    ],
    [
        'id' => 2,
        'name' => 'Dr. Johnson',
        'last_message' => 'Please submit your assignments by Friday',
        'time' => 'Yesterday',
        'unread' => 0,
        'online' => false,
        'is_group' => false
    ],
    [
        'id' => 3,
        'name' => 'Computer Science Club',
        'last_message' => 'Sarah: Meeting postponed to next week',
        'time' => '12/10/2023',
        'unread' => 12,
        'online' => true,
        'participants' => 15,
        'is_group' => true
    ],
    [
        'id' => 4,
        'name' => 'Alex Morgan',
        'last_message' => 'Are you coming to the library today?',
        'time' => '12/08/2023',
        'unread' => 0,
        'online' => false,
        'is_group' => false
    ]
];

$active_chat = [
    'id' => 1,
    'name' => 'Mathematics Study Group',
    'is_group' => true,
    'participants' => [
        ['name' => 'John Doe', 'online' => true],
        ['name' => 'Sarah Smith', 'online' => true],
        ['name' => 'Mike Johnson', 'online' => false],
        ['name' => 'You', 'online' => true],
        ['name' => 'Emma Wilson', 'online' => true]
    ]
];

$messages = [
    [
        'id' => 1,
        'sender' => 'John Doe',
        'message' => 'Has everyone finished the assignment?',
        'time' => '2:30 PM',
        'is_me' => false
    ],
    [
        'id' => 2,
        'sender' => 'You',
        'message' => 'I\'m almost done, just question 5 left',
        'time' => '2:32 PM',
        'is_me' => true
    ],
    [
        'id' => 3,
        'sender' => 'Sarah Smith',
        'message' => 'I found question 5 really challenging. Any tips?',
        'time' => '2:35 PM',
        'is_me' => false
    ],
    [
        'id' => 4,
        'sender' => 'Mike Johnson',
        'message' => 'I used integration by parts for question 5',
        'time' => '2:40 PM',
        'is_me' => false
    ],
    [
        'id' => 5,
        'sender' => 'You',
        'message' => 'Thanks Mike! That helped me solve it',
        'time' => '2:42 PM',
        'is_me' => true
    ]
];
?>
    
    <div class="chat-container">
        <div class="chat-sidebar">
            <div class="chat-header">
                <h2>Chats</h2>
                <div class="chat-actions">
                    <button class="icon-button" title="New conversation">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="icon-button" title="Settings">
                        <i class="fas fa-cog"></i>
                    </button>
                </div>
            </div>
            
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search messages or users">
            </div>
            
            <div class="conversations-list">
                <?php foreach ($conversations as $conv): ?>
                <div class="conversation-item <?= $conv['id'] == $active_chat['id'] ? 'active' : '' ?>">
                    <div class="avatar <?= $conv['is_group'] ? 'group' : '' ?> <?= $conv['online'] ? 'online' : '' ?>">
                        <?php if ($conv['is_group']): ?>
                            <i class="fas fa-users"></i>
                        <?php else: ?>
                            <i class="fas fa-user"></i>
                        <?php endif; ?>
                    </div>
                    <div class="conversation-details">
                        <div class="conversation-header">
                            <h3><?= $conv['name'] ?></h3>
                            <span class="time"><?= $conv['time'] ?></span>
                        </div>
                        <div class="conversation-preview">
                            <p><?= $conv['last_message'] ?></p>
                            <?php if ($conv['unread'] > 0): ?>
                                <span class="unread-count"><?= $conv['unread'] ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="chat-main">
            <div class="chat-main-header">
                <div class="active-chat-info">
                    <div class="avatar <?= $active_chat['is_group'] ? 'group' : '' ?> online">
                        <?php if ($active_chat['is_group']): ?>
                            <i class="fas fa-users"></i>
                        <?php else: ?>
                            <i class="fas fa-user"></i>
                        <?php endif; ?>
                    </div>
                    <div class="chat-info">
                        <h3><?= $active_chat['name'] ?></h3>
                        <div class="active-users">
                            <?php 
                            $online_count = 0;
                            foreach ($active_chat['participants'] as $participant) {
                                if ($participant['online']) $online_count++;
                            }
                            ?>
                            <?php if ($active_chat['is_group']): ?>
                                <span class="online-status"><?= $online_count ?> online</span>
                            <?php else: ?>
                                <span class="online-status">Online</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="chat-actions">
                    <button class="icon-button" title="Video call">
                        <i class="fas fa-video"></i>
                    </button>
                    <button class="icon-button" title="Voice call">
                        <i class="fas fa-phone"></i>
                    </button>
                    <button class="icon-button" title="Search conversation">
                        <i class="fas fa-search"></i>
                    </button>
                    <button class="icon-button" title="More options">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                </div>
            </div>
            
            <div class="messages-container">
                <div class="messages">
                    <?php foreach ($messages as $msg): ?>
                    <div class="message <?= $msg['is_me'] ? 'sent' : 'received' ?>">
                        <?php if (!$msg['is_me']): ?>
                        <div class="avatar small">
                            <i class="fas fa-user"></i>
                        </div>
                        <?php endif; ?>
                        <div class="message-content">
                            <?php if (!$msg['is_me']): ?>
                            <div class="sender-name"><?= $msg['sender'] ?></div>
                            <?php endif; ?>
                            <div class="message-bubble">
                                <p><?= $msg['message'] ?></p>
                                <div class="message-time"><?= $msg['time'] ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <div class="typing-indicator">
                        <div class="avatar small">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="typing-content">
                            <div class="sender-name">Sarah Smith</div>
                            <div class="typing-bubble">
                                <div class="typing-dots">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="message-input-container">
                <div class="message-input-actions">
                    <button class="icon-button" title="Add emoji">
                        <i class="far fa-smile"></i>
                    </button>
                    <button class="icon-button" title="Attach file">
                        <i class="fas fa-paperclip"></i>
                    </button>
                </div>
                <div class="message-input">
                    <input type="text" placeholder="Type a message...">
                </div>
                <div class="message-send">
                    <button class="icon-button" title="Send message">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
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
        color: #31a24c;
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
    
    .typing-indicator {
        display: flex;
        margin-bottom: 15px;
        align-items: center;
    }
    
    .typing-content {
        margin-left: 10px;
    }
    
    .typing-bubble {
        background-color: white;
        padding: 10px 15px;
        border-radius: 18px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        display: inline-block;
    }
    
    .typing-dots {
        display: flex;
        align-items: center;
        height: 10px;
    }
    
    .typing-dots span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #65676b;
        margin: 0 2px;
        display: inline-block;
        animation: typing-animation 1.4s infinite ease-in-out both;
    }
    
    .typing-dots span:nth-child(1) {
        animation-delay: -0.32s;
    }
    
    .typing-dots span:nth-child(2) {
        animation-delay: -0.16s;
    }
    
    @keyframes typing-animation {
        0%, 80%, 100% { 
            transform: scale(0.8);
            opacity: 0.5;
        }
        40% { 
            transform: scale(1);
            opacity: 1;
        }
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
        // Conversation item click handler
        const conversationItems = document.querySelectorAll('.conversation-item');
        conversationItems.forEach(item => {
            item.addEventListener('click', function() {
                conversationItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                
                // In a real app, you would load the conversation data via AJAX here
            });
        });
        
        // Message send functionality
        const messageInput = document.querySelector('.message-input input');
        const sendButton = document.querySelector('.message-send button');
        
        function sendMessage() {
            const message = messageInput.value.trim();
            if (message) {
                // In a real app, you would send the message to the server via AJAX
                console.log('Sending message:', message);
                messageInput.value = '';
            }
        }
        
        sendButton.addEventListener('click', sendMessage);
        
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
        
        // Simulate receiving a new message after 5 seconds
        setTimeout(() => {
            const messagesContainer = document.querySelector('.messages');
            const typingIndicator = document.querySelector('.typing-indicator');
            
            if (typingIndicator) {
                typingIndicator.remove();
                
                const newMessage = document.createElement('div');
                newMessage.className = 'message received';
                newMessage.innerHTML = `
                    <div class="avatar small">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="message-content">
                        <div class="sender-name">Sarah Smith</div>
                        <div class="message-bubble">
                            <p>I think I finally understand it now!</p>
                            <div class="message-time">Just now</div>
                        </div>
                    </div>
                `;
                
                messagesContainer.appendChild(newMessage);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }, 5000);
    });
    </script>
</body>
</html>
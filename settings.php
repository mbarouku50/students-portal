<?php
session_start();
include("connection.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data from database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // User not found
    header("Location: logout.php");
    exit();
}

$user = $result->fetch_assoc();

// Update session variables with actual database values
$_SESSION['user_fullname'] = $user['fullname'];
if (!empty($user['profile_picture'])) {
    $_SESSION['profile_picture'] = $user['profile_picture'];
}

// Handle form submissions
$update_success = false;
$update_error = false;
$error_message = '';
$success_message = '';

// Process profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_avatar'])) {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_picture'];
        
        // Validate file type and size
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowed_types)) {
            $update_error = true;
            $error_message = "Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed.";
        } elseif ($file['size'] > $max_size) {
            $update_error = true;
            $error_message = "File is too large. Maximum size is 5MB.";
        } else {
            // Generate unique filename
            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
            $upload_path = 'uploads/profile_pictures/' . $filename;
            
            // Create directory if it doesn't exist
            if (!file_exists('uploads/profile_pictures')) {
                mkdir('uploads/profile_pictures', 0777, true);
            }
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                // Delete old profile picture if it exists
                if (!empty($user['profile_picture']) && file_exists($user['profile_picture'])) {
                    unlink($user['profile_picture']);
                }
                
                // Update database with new profile picture path
                $update_query = "UPDATE users SET profile_picture = ? WHERE user_id = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("si", $upload_path, $user_id);
                
                if ($update_stmt->execute()) {
                    $update_success = true;
                    $success_message = "Profile picture updated successfully!";
                    $_SESSION['profile_picture'] = $upload_path;
                    $user['profile_picture'] = $upload_path;
                } else {
                    $update_error = true;
                    $error_message = "Error updating profile picture: " . $conn->error;
                }
            } else {
                $update_error = true;
                $error_message = "Error uploading file. Please try again.";
            }
        }
    } else {
        $update_error = true;
        $error_message = "Please select a valid image file.";
    }
}

// Process account settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_account'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $program = trim($_POST['program']);
    $year = trim($_POST['year']);
    
    // Validate inputs
    if (empty($fullname) || empty($email)) {
        $update_error = true;
        $error_message = "Full name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $update_error = true;
        $error_message = "Invalid email format.";
    } else {
        // Check if email already exists (excluding current user)
        $email_check_query = "SELECT user_id FROM users WHERE email = ? AND user_id != ?";
        $email_check_stmt = $conn->prepare($email_check_query);
        $email_check_stmt->bind_param("si", $email, $user_id);
        $email_check_stmt->execute();
        $email_result = $email_check_stmt->get_result();
        
        if ($email_result->num_rows > 0) {
            $update_error = true;
            $error_message = "Email address is already in use by another account.";
        } else {
            // Update user in database
            $update_query = "UPDATE users SET fullname = ?, email = ?, program = ?, year = ? WHERE user_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ssssi", $fullname, $email, $program, $year, $user_id);
            
            if ($update_stmt->execute()) {
                $update_success = true;
                $success_message = "Account settings updated successfully!";
                $_SESSION['user_fullname'] = $fullname;
                
                // Refresh user data
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
            } else {
                $update_error = true;
                $error_message = "Error updating account: " . $conn->error;
            }
        }
    }
}

// Process password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $salt = "CBE_DOCS_2023"; // Use same salt as registration
    $fullname = $user['fullname'];
    $computed_hash = sha1($fullname . "_" . $current_password . $salt);
    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $update_error = true;
        $error_message = "All password fields are required.";
    } elseif (strlen($new_password) < 8) {
        $update_error = true;
        $error_message = "New password must be at least 8 characters long.";
    } elseif ($new_password !== $confirm_password) {
        $update_error = true;
        $error_message = "New passwords do not match.";
    } elseif ($computed_hash !== $user['password']) {
        $update_error = true;
        $error_message = "Current password is incorrect.<br>Computed hash: $computed_hash<br>Stored hash: {$user['password']}<br>Fullname used: $fullname";
    } else {
        // Hash new password and update
        $hashed_password = sha1($fullname . "_" . $new_password . $salt);
        $update_query = "UPDATE users SET password = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("si", $hashed_password, $user_id);
        if ($update_stmt->execute()) {
            $update_success = true;
            $success_message = "Password changed successfully!";
        } else {
            $update_error = true;
            $error_message = "Error changing password: " . $conn->error;
        }
    }
}

// Process notification preferences
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_notifications'])) {
    $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
    $course_updates = isset($_POST['course_updates']) ? 1 : 0;
    $assignment_alerts = isset($_POST['assignment_alerts']) ? 1 : 0;
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    
    // Update notification preferences
    $update_query = "UPDATE users SET email_notifications = ?, course_updates = ?, assignment_alerts = ?, newsletter = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("iiiii", $email_notifications, $course_updates, $assignment_alerts, $newsletter, $user_id);
    
    if ($update_stmt->execute()) {
        $update_success = true;
        $success_message = "Notification preferences updated successfully!";
    } else {
        $update_error = true;
        $error_message = "Error updating notification preferences: " . $conn->error;
    }
}

// Process privacy settings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_privacy'])) {
    $profile_visibility = isset($_POST['profile_visibility']) ? 1 : 0;
    $show_email = isset($_POST['show_email']) ? 1 : 0;
    $data_collection = isset($_POST['data_collection']) ? 1 : 0;
    
    // Update privacy settings
    $update_query = "UPDATE users SET profile_visibility = ?, show_email = ?, data_collection = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("iiii", $profile_visibility, $show_email, $data_collection, $user_id);
    
    if ($update_stmt->execute()) {
        $update_success = true;
        $success_message = "Privacy settings updated successfully!";
    } else {
        $update_error = true;
        $error_message = "Error updating privacy settings: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - CBE Student Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f7fa;
        }
        
        .main-content {
            padding: 2rem 0;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        /* Settings-specific styles */
        .settings-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .settings-header {
            margin-bottom: 2rem;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .settings-header h1 {
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
            font-size: 2.2rem;
        }
        
        .settings-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .profile-picture-container {
            position: relative;
            margin-right: 2rem;
        }
        
        .profile-picture-large {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--secondary-color);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .profile-picture-overlay {
            position: absolute;
            bottom: 0;
            right: 0;
            background: var(--primary-color);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .profile-picture-overlay:hover {
            background: var(--secondary-color);
            transform: scale(1.1);
        }
        
        .profile-info h1 {
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }
        
        .profile-info p {
            color: #666;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }
        
        .profile-info i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }
        
        .settings-tabs {
            display: flex;
            border-bottom: 2px solid #ddd;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .tab-button {
            padding: 1rem 1.5rem;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            color: #666;
            position: relative;
            transition: color 0.3s;
            display: flex;
            align-items: center;
        }
        
        .tab-button i {
            margin-right: 0.5rem;
        }
        
        .tab-button:hover {
            color: var(--primary-color);
        }
        
        .tab-button.active {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .tab-button.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--primary-color);
        }
        
        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .tab-content.active {
            display: block;
        }
        
        .settings-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .settings-card h2 {
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--light-color);
            display: flex;
            align-items: center;
        }
        
        .settings-card h2 i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s;
            text-align: center;
            border: none;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .alert i {
            margin-right: 0.75rem;
            font-size: 1.2rem;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .checkbox-group input[type="checkbox"] {
            margin-right: 0.75rem;
            width: 18px;
            height: 18px;
            accent-color: var(--primary-color);
        }
        
        .checkbox-group label {
            margin-bottom: 0;
            font-weight: normal;
        }
        
        .avatar-upload-form {
            background: var(--light-color);
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1.5rem;
        }
        
        .file-input-container {
            position: relative;
            margin-bottom: 1rem;
        }
        
        .file-input-label {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: var(--primary-color);
            color: white;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .file-input-label:hover {
            background: #2980b9;
        }
        
        .file-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-name {
            margin-left: 1rem;
            font-style: italic;
        }
        
        .danger-zone {
            border: 2px solid var(--danger-color);
            border-radius: 10px;
            padding: 2rem;
            margin-top: 2rem;
        }
        
        .danger-zone h3 {
            color: var(--danger-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .danger-zone h3 i {
            margin-right: 0.5rem;
        }
        
        .danger-zone p {
            margin-bottom: 1.5rem;
            color: #666;
        }
        
        .password-strength {
            margin-top: 0.5rem;
            height: 5px;
            border-radius: 3px;
            background: #eee;
        }
        
        .password-strength-meter {
            height: 100%;
            border-radius: 3px;
            width: 0;
            transition: width 0.3s;
        }
        
        @media (max-width: 768px) {
            .settings-tabs {
                flex-direction: column;
            }
            
            .tab-button {
                width: 100%;
                text-align: left;
                border-bottom: 1px solid #eee;
                padding: 1rem;
            }
            
            .tab-button.active::after {
                display: none;
            }
            
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-picture-container {
                margin-right: 0;
                margin-bottom: 1.5rem;
            }
            
            .settings-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include("temperate/header.php");?>
    
    <div class="main-content">
        <div class="container settings-container">
            <div class="settings-header">
                <h1><i class="fas fa-cog"></i> Account Settings</h1>
                <p>Manage your account preferences and settings</p>
            </div>
            
            <div class="profile-header">
                <div class="profile-picture-container">
                    <?php
                    $profile_pic = "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png";
                    if (!empty($user['profile_picture'])) {
                        $profile_pic = $user['profile_picture'];
                    }
                    ?>
                    <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" class="profile-picture-large" id="profile-picture-preview">
                    <div class="profile-picture-overlay" onclick="document.getElementById('profile-picture-input').click()">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                <div class="profile-info">
                    <h1><?php echo htmlspecialchars($user['fullname']); ?></h1>
                    <p><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($user['program']); ?> Student</p>
                    <p><i class="fas fa-calendar-alt"></i> Year <?php echo htmlspecialchars($user['year']); ?></p>
                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><i class="fas fa-clock"></i> Member since: <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
                </div>
            </div>
            
            <?php if ($update_success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($update_error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <div class="settings-tabs">
                <button class="tab-button active" data-tab="profile"><i class="fas fa-user"></i> Profile</button>
                <button class="tab-button" data-tab="account"><i class="fas fa-user-cog"></i> Account</button>
                <button class="tab-button" data-tab="security"><i class="fas fa-shield-alt"></i> Security</button>
                <button class="tab-button" data-tab="notifications"><i class="fas fa-bell"></i> Notifications</button>
                <button class="tab-button" data-tab="privacy"><i class="fas fa-lock"></i> Privacy</button>
            </div>
            
            <div class="tab-content active" id="profile-tab">
                <div class="settings-card">
                    <h2><i class="fas fa-user-circle"></i> Profile Picture</h2>
                    <p>Upload a new profile picture. Maximum file size is 5MB. Supported formats: JPG, PNG, GIF, WebP.</p>
                    
                    <form action="settings.php" method="POST" enctype="multipart/form-data" class="avatar-upload-form">
                        <div class="file-input-container">
                            <label class="file-input-label">
                                <i class="fas fa-upload"></i> Choose File
                                <input type="file" id="profile-picture-input" name="profile_picture" class="file-input" accept="image/*">
                            </label>
                            <span class="file-name" id="file-name">No file chosen</span>
                        </div>
                        
                        <button type="submit" name="upload_avatar" class="btn btn-primary">
                            <i class="fas fa-save"></i> Upload Picture
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="tab-content" id="account-tab">
                <div class="settings-card">
                    <h2><i class="fas fa-info-circle"></i> Account Information</h2>
                    <form action="settings.php" method="POST">
                        <div class="form-group">
                            <label for="fullname">Full Name</label>
                            <input type="text" id="fullname" name="fullname" class="form-control" 
                                   value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        
                      
                        
                        <div class="form-group">
                            <label for="year">Year of Study</label>
                            <select id="year" name="year" class="form-control" required>
                                <option value="1" <?php echo $user['year'] == '1' ? 'selected' : ''; ?>>Year 1</option>
                                <option value="2" <?php echo $user['year'] == '2' ? 'selected' : ''; ?>>Year 2</option>
                                <option value="3" <?php echo $user['year'] == '3' ? 'selected' : ''; ?>>Year 3</option>
                                <option value="4" <?php echo $user['year'] == '4' ? 'selected' : ''; ?>>Year 4</option>
                            </select>
                        </div>
                        
                        <button type="submit" name="update_account" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Account
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="tab-content" id="security-tab">
                <div class="settings-card">
                    <h2><i class="fas fa-key"></i> Change Password</h2>
                    <form action="settings.php" method="POST">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" required>
                            <div class="password-strength">
                                <div class="password-strength-meter" id="password-strength-meter"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        </div>
                        
                        <button type="submit" name="change_password" class="btn btn-primary">
                            <i class="fas fa-key"></i> Change Password
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="tab-content" id="notifications-tab">
                <div class="settings-card">
                    <h2><i class="fas fa-bell"></i> Notification Preferences</h2>
                    <form action="settings.php" method="POST">
                        <div class="checkbox-group">
                            <input type="checkbox" id="email_notifications" name="email_notifications" value="1" 
                                <?php echo (isset($user['email_notifications']) && $user['email_notifications']) ? 'checked' : ''; ?>>
                            <label for="email_notifications">Email Notifications</label>
                        </div>
                        
                        <div class="checkbox-group">
                            <input type="checkbox" id="course_updates" name="course_updates" value="1" 
                                <?php echo (isset($user['course_updates']) && $user['course_updates']) ? 'checked' : ''; ?>>
                            <label for="course_updates">Course Updates</label>
                        </div>
                        
                        <div class="checkbox-group">
                            <input type="checkbox" id="assignment_alerts" name="assignment_alerts" value="1" 
                                <?php echo (isset($user['assignment_alerts']) && $user['assignment_alerts']) ? 'checked' : ''; ?>>
                            <label for="assignment_alerts">Assignment Alerts</label>
                        </div>
                        
                        <div class="checkbox-group">
                            <input type="checkbox" id="newsletter" name="newsletter" value="1" 
                                <?php echo (isset($user['newsletter']) && $user['newsletter']) ? 'checked' : ''; ?>>
                            <label for="newsletter">Newsletter</label>
                        </div>
                        
                        <button type="submit" name="update_notifications" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Preferences
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="tab-content" id="privacy-tab">
                <div class="settings-card">
                    <h2><i class="fas fa-lock"></i> Privacy Settings</h2>
                    <p>Control how your information is shared and displayed on the platform.</p>
                    
                    <form action="settings.php" method="POST">
                        <div class="checkbox-group">
                            <input type="checkbox" id="profile_visibility" name="profile_visibility" value="1"
                                <?php echo (isset($user['profile_visibility']) && $user['profile_visibility']) ? 'checked' : ''; ?>>
                            <label for="profile_visibility">Make my profile visible to other students</label>
                        </div>
                        
                        <div class="checkbox-group">
                            <input type="checkbox" id="show_email" name="show_email" value="1"
                                <?php echo (isset($user['show_email']) && $user['show_email']) ? 'checked' : ''; ?>>
                            <label for="show_email">Show my email address to course instructors</label>
                        </div>
                        
                        <div class="checkbox-group">
                            <input type="checkbox" id="data_collection" name="data_collection" value="1"
                                <?php echo (isset($user['data_collection']) && $user['data_collection']) ? 'checked' : ''; ?>>
                            <label for="data_collection">Allow data collection for improving services</label>
                        </div>
                        
                        <button type="submit" name="update_privacy" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Privacy Settings
                        </button>
                    </form>
                </div>
                
                <div class="danger-zone">
                    <h3><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3>
                    <p>Once you delete your account, there is no going back. Please be certain.</p>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                        <i class="fas fa-trash-alt"></i> Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    // Tab functionality
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all tabs and contents
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked tab
            button.classList.add('active');
            
            // Show corresponding content
            const tabId = button.getAttribute('data-tab');
            document.getElementById(`${tabId}-tab`).classList.add('active');
        });
    });
    
    // File input display
    document.getElementById('profile-picture-input').addEventListener('change', function() {
        const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
        document.getElementById('file-name').textContent = fileName;
        
        // Preview image
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile-picture-preview').src = e.target.result;
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Password strength meter
    document.getElementById('new_password').addEventListener('input', function() {
        const password = this.value;
        const strengthMeter = document.getElementById('password-strength-meter');
        let strength = 0;
        
        if (password.length >= 8) strength += 25;
        if (/[A-Z]/.test(password)) strength += 25;
        if (/[0-9]/.test(password)) strength += 25;
        if (/[^A-Za-z0-9]/.test(password)) strength += 25;
        
        strengthMeter.style.width = strength + '%';
        
        if (strength < 50) {
            strengthMeter.style.backgroundColor = '#e74c3c';
        } else if (strength < 75) {
            strengthMeter.style.backgroundColor = '#f39c12';
        } else {
            strengthMeter.style.backgroundColor = '#2ecc71';
        }
    });
    
    function confirmDelete() {
        if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
            alert('Account deletion functionality would be implemented here.');
            // In a real application, you would redirect to a delete account script
            // window.location.href = 'delete_account.php';
        }
    }
    </script>
</body>
</html>
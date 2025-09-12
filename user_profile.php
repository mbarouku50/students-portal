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

// Handle profile updates
$update_success = false;
$update_error = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form data
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
        // Handle profile picture upload
        $profile_picture = $user['profile_picture'];
        
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_picture'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB
            
            if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
                $upload_dir = 'uploads/profile_pictures/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'user_' . $user_id . '_' . time() . '.' . $file_extension;
                $destination = $upload_dir . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    // Delete old profile picture if it exists
                    if (!empty($profile_picture) && file_exists($profile_picture)) {
                        unlink($profile_picture);
                    }
                    
                    $profile_picture = $destination;
                    $_SESSION['profile_picture'] = $profile_picture;
                }
            }
        }
        
        // Update user in database
        $update_query = "UPDATE users SET fullname = ?, email = ?, program = ?, year = ?, profile_picture = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("sssssi", $fullname, $email, $program, $year, $profile_picture, $user_id);
        
        if ($update_stmt->execute()) {
            $update_success = true;
            $_SESSION['user_fullname'] = $fullname;
            
            // Refresh user data
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
        } else {
            $update_error = true;
            $error_message = "Error updating profile: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - CBE Student Portal</title>
    <style>
        /* Profile-specific styles */
        .profile-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .profile-picture-large {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--secondary-color);
            margin-right: 2rem;
        }
        
        .profile-info h1 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .profile-info p {
            color: #666;
            margin-bottom: 0.5rem;
        }
        
        .profile-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        
        @media (max-width: 768px) {
            .profile-content {
                grid-template-columns: 1fr;
            }
            
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-picture-large {
                margin-right: 0;
                margin-bottom: 1rem;
            }
        }
        
        .profile-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .profile-card h2 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--light-color);
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
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
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .info-value {
            color: #666;
        }
        
        .upload-btn {
            display: inline-block;
            margin-top: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--light-color);
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .upload-btn:hover {
            background: #ddd;
        }
        
        #profile-picture-input {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Include the header -->
    <?php include 'header.php'; ?>
    
    <div class="main-content">
        <div class="container profile-container">
            <div class="profile-header">
                <?php
                $profile_pic = "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png";
                if (!empty($user['profile_picture'])) {
                    $profile_pic = $user['profile_picture'];
                }
                ?>
                <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" class="profile-picture-large" id="profile-picture-preview">
                <div class="profile-info">
                    <h1><?php echo htmlspecialchars($user['fullname']); ?></h1>
                    <p><?php echo htmlspecialchars($user['program']); ?> Student</p>
                    <p>Year <?php echo htmlspecialchars($user['year']); ?></p>
                    <p>Member since: <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
                </div>
            </div>
            
            <?php if ($update_success): ?>
                <div class="alert alert-success">
                    Profile updated successfully!
                </div>
            <?php endif; ?>
            
            <?php if ($update_error): ?>
                <div class="alert alert-error">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <div class="profile-content">
                <div class="profile-card">
                    <h2>Personal Information</h2>
                    <div class="info-item">
                        <span class="info-label">Registration Number:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['reg']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Program:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['program']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Year of Study:</span>
                        <span class="info-value">Year <?php echo htmlspecialchars($user['year']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Account Created:</span>
                        <span class="info-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
                    </div>
                </div>
                
                <div class="profile-card">
                    <h2>Edit Profile</h2>
                    <form action="user_profile.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="profile_picture">Profile Picture</label>
                            <input type="file" id="profile-picture-input" name="profile_picture" accept="image/*">
                            <div class="upload-btn" onclick="document.getElementById('profile-picture-input').click()">
                                <i class="fas fa-upload"></i> Change Picture
                            </div>
                        </div>
                        
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
                            <label for="program">Program</label>
                            <input type="text" id="program" name="program" class="form-control" 
                                   value="<?php echo htmlspecialchars($user['program']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="year">Year of Study</label>
                            <select id="year" name="year" class="form-control" required>
                                <option value="1" <?php echo $user['year'] == '1' ? 'selected' : ''; ?>>Year 1</option>
                                <option value="2" <?php echo $user['year'] == '2' ? 'selected' : ''; ?>>Year 2</option>
                                <option value="3" <?php echo $user['year'] == '3' ? 'selected' : ''; ?>>Year 3</option>
                                <option value="4" <?php echo $user['year'] == '4' ? 'selected' : ''; ?>>Year 4</option>
                                <option value="5" <?php echo $user['year'] == '5' ? 'selected' : ''; ?>>Year 5</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    // Preview profile picture before upload
    document.getElementById('profile-picture-input').addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('profile-picture-preview').src = e.target.result;
            }
            
            reader.readAsDataURL(this.files[0]);
        }
    });
    </script>
</body>
</html>
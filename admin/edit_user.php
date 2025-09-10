<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("../connection.php");
include("sidebar.php");




// Initialize variables
$errors = [];
$success = '';
$userData = [];

// Get user ID from URL parameter
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch user data from database
if ($user_id > 0) {
    $stmt = $conn->prepare("SELECT user_id, fullname, email, reg, password, program, year, created_at FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $userData = $result->fetch_assoc();
    } else {
        $errors['user'] = 'User not found';
    }
    $stmt->close();
} else {
    $errors['user'] = 'Invalid user ID';
}

// Fetch programs from database
$programs_result = $conn->query("SELECT course_code, course_name FROM courses ORDER BY course_name");
$programs = [];
while ($row = $programs_result->fetch_assoc()) {
    $programs[] = $row;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($errors)) {
    // Sanitize and validate inputs
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $reg = trim($_POST['reg']);
    $program = $_POST['program'];
    $year = $_POST['year'];
    
    // Validate full name
    if (empty($fullname)) {
        $errors['fullname'] = 'Full name is required';
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $fullname)) {
        $errors['fullname'] = 'Only letters and white space allowed';
    }
    
    // Validate email
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    } else {
        // Check if email already exists (excluding current user)
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $errors['email'] = 'Email already registered to another user';
        }
        $stmt->close();
    }
    
    // Validate registration number
    if (empty($reg)) {
        $errors['reg'] = 'Registration number is required';
    } elseif (!preg_match("/^\d{2}\.\d{4}\.\d{2}\.\d{2}\.\d{4}$/", $reg)) {
        $errors['reg'] = 'Invalid registration number format (e.g., 03.2481.01.01.2023)';
    } else {
        // Check if reg number already exists (excluding current user)
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE reg = ? AND user_id != ?");
        $stmt->bind_param("si", $reg, $user_id);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $errors['reg'] = 'Registration number already registered to another user';
        }
        $stmt->close();
    }
    
    // Validate program
    if (empty($program)) {
        $errors['program'] = 'Program of study is required';
    }
    
    // Validate year
    if (empty($year)) {
        $errors['year'] = 'Year of study is required';
    }
    
    // If password change is requested
    $password_change = isset($_POST['change_password']) && $_POST['change_password'] == '1';
    if ($password_change) {
        $password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validate password
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters';
        }
        
        // Validate confirm password
        if (empty($confirm_password)) {
            $errors['confirm_password'] = 'Please confirm your password';
        } elseif ($password != $confirm_password) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
    }
    
    // If no errors, update user in database
    if (empty($errors)) {
        if ($password_change) {
            // Hash password - using the same method as register.php
            $salt = "CBE_DOCS_2023";
            $encrypted_password = sha1($fullname . "_" . $password . $salt);
            
            $stmt = $conn->prepare("UPDATE users SET fullname = ?, email = ?, reg = ?, password = ?, program = ?, year = ? WHERE user_id = ?");
            $stmt->bind_param("ssssssi", $fullname, $email, $reg, $encrypted_password, $program, $year, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET fullname = ?, email = ?, reg = ?, program = ?, year = ? WHERE user_id = ?");
            $stmt->bind_param("sssssi", $fullname, $email, $reg, $program, $year, $user_id);
        }
        
        if ($stmt->execute()) {
            $success = 'User information updated successfully!';
            // Refresh user data
            $stmt = $conn->prepare("SELECT user_id, fullname, email, reg, password, program, year, created_at FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $userData = $result->fetch_assoc();
            $stmt->close();
        } else {
            $errors['database'] = 'Update failed: ' . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - CBE Doc's Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --secondary: #10b981;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --border: #e2e8f0;
            --shadow: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-lg: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 2rem;
            transition: all 0.3s ease;
        }
        
        .page-header {
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .page-title {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .page-subtitle {
            color: var(--gray);
            font-size: 1.1rem;
        }
        
        .form-container {
            background: white;
            border-radius: 0.75rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.95rem;
        }
        
        .form-input, .form-select {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border);
            border-radius: 0.5rem;
            font-size: 1rem;
            background: white;
            transition: all 0.3s ease;
        }
        
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        
        .error {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .success {
            color: var(--secondary);
            text-align: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: rgba(16, 185, 129, 0.1);
            border-radius: 0.5rem;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-secondary {
            background: var(--light);
            color: var(--dark);
            border: 2px solid var(--border);
        }
        
        .btn-secondary:hover {
            background: #e2e8f0;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
        }
        
        .user-info-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border);
        }
        
        .user-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .info-label {
            font-size: 0.875rem;
            color: var(--gray);
            font-weight: 500;
        }
        
        .info-value {
            font-weight: 600;
            color: var(--dark);
        }
        
        .password-section {
            background: #f8fafc;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-top: 2rem;
            border: 1px solid var(--border);
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .checkbox-group input {
            width: auto;
        }
        
        .password-fields {
            display: none;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }
        
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        @media (max-width: 1200px) {
            .main-content {
                margin-left: 0;
                padding: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .page-title {
                font-size: 1.75rem;
            }
            
            .user-info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>
    
    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Edit User</h1>
                <p class="page-subtitle">Update user information</p>
            </div>
            <a href="manage_users.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>
        
        <?php if (!empty($errors['user'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $errors['user']; ?>
            </div>
            <div style="text-align: center;">
                <a href="manage_users.php" class="btn btn-primary">Back to User Management</a>
            </div>
        <?php else: ?>
        
        <?php if ($success): ?>
            <div class="success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <div class="user-info-card">
            <div class="user-info-grid">
                <div class="info-item">
                    <span class="info-label">User ID</span>
                    <span class="info-value">#<?php echo htmlspecialchars($userData['user_id']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Account Created</span>
                    <span class="info-value"><?php echo date('M j, Y', strtotime($userData['created_at'])); ?></span>
                </div>
            </div>
        </div>
        
        <div class="form-container">
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="fullname">Full Name</label>
                        <input type="text" id="fullname" name="fullname" class="form-input" 
                               value="<?php echo isset($userData['fullname']) ? htmlspecialchars($userData['fullname']) : ''; ?>" required>
                        <?php if (isset($errors['fullname'])): ?>
                            <div class="error"><?php echo $errors['fullname']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-input" 
                               value="<?php echo isset($userData['email']) ? htmlspecialchars($userData['email']) : ''; ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="error"><?php echo $errors['email']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="reg">Registration Number</label>
                        <input type="text" id="reg" name="reg" class="form-input" 
                               placeholder="e.g., 03.2481.01.01.2023" 
                               value="<?php echo isset($userData['reg']) ? htmlspecialchars($userData['reg']) : ''; ?>" required>
                        <?php if (isset($errors['reg'])): ?>
                            <div class="error"><?php echo $errors['reg']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="program">Program of Study</label>
                        <select id="program" name="program" class="form-select" required>
                            <option value="">Select program</option>
                            <?php foreach ($programs as $program): ?>
                                <option value="<?php echo htmlspecialchars($program['course_code']); ?>" 
                                    <?php echo (isset($userData['program']) && $userData['program'] == $program['course_code']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($program['course_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['program'])): ?>
                            <div class="error"><?php echo $errors['program']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="year">Year of Study</label>
                        <select id="year" name="year" class="form-select" required>
                            <option value="">Select year</option>
                            <option value="1" <?php echo (isset($userData['year']) && $userData['year'] == '1') ? 'selected' : ''; ?>>First Year</option>
                            <option value="2" <?php echo (isset($userData['year']) && $userData['year'] == '2') ? 'selected' : ''; ?>>Second Year</option>
                            <option value="3" <?php echo (isset($userData['year']) && $userData['year'] == '3') ? 'selected' : ''; ?>>Third Year</option>
                            <option value="4" <?php echo (isset($userData['year']) && $userData['year'] == '4') ? 'selected' : ''; ?>>Fourth Year</option>
                        </select>
                        <?php if (isset($errors['year'])): ?>
                            <div class="error"><?php echo $errors['year']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="password-section">
                    <h3 class="section-title">
                        <i class="fas fa-lock"></i> Password Management
                    </h3>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" id="change_password" name="change_password" value="1" onchange="togglePasswordFields()">
                        <label for="change_password">Change Password</label>
                    </div>
                    
                    <div id="password_fields" class="password-fields">
                        <div class="form-group">
                            <label class="form-label" for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="form-input">
                            <?php if (isset($errors['password'])): ?>
                                <div class="error"><?php echo $errors['password']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-input">
                            <?php if (isset($errors['confirm_password'])): ?>
                                <div class="error"><?php echo $errors['confirm_password']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <?php if (isset($errors['database'])): ?>
                    <div class="error" style="margin: 1.5rem 0;"><?php echo $errors['database']; ?></div>
                <?php endif; ?>
                
                <div class="form-actions">
                    <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update User
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </main>

    <script>
        function togglePasswordFields() {
            var changePassword = document.getElementById('change_password').checked;
            var passwordFields = document.getElementById('password_fields');
            
            if (changePassword) {
                passwordFields.style.display = 'grid';
                document.getElementById('new_password').setAttribute('required', 'required');
                document.getElementById('confirm_password').setAttribute('required', 'required');
            } else {
                passwordFields.style.display = 'none';
                document.getElementById('new_password').removeAttribute('required');
                document.getElementById('confirm_password').removeAttribute('required');
            }
        }
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const changePassword = document.getElementById('change_password').checked;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (changePassword) {
                if (newPassword.length < 8) {
                    e.preventDefault();
                    alert('Password must be at least 8 characters long.');
                    return;
                }
                
                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match.');
                    return;
                }
            }
        });
    </script>
</body>
</html>
<?php
// Close the connection
$conn->close();
?>
<?php
include("../connection.php");
include("sidebar.php");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $email = $conn->real_escape_string($_POST['email']);
    $status = $conn->real_escape_string($_POST['status']);
    
    // Use the same encryption method as in admin_login.php
    $salt = "CBE_DOCS_2023";
    $password = sha1($_POST['password'] . $salt);
    
    // Handle profile picture upload
    $profile_picture = '';
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "../admin/uploads/profiles/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            $profile_picture = $filename;
        }
    }
    
    $sql = "INSERT INTO admin (fullname, email, password, profile_picture, status) 
            VALUES ('$fullname', '$email', '$password', '$profile_picture', '$status')";
    
    if ($conn->query($sql)) {
        $success_message = "Admin added successfully!";
    } else {
        $error_message = "Error adding admin: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Admin - CBE Doc's Store</title>
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
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: #f1f5f9;
            color: var(--dark);
            line-height: 1.6;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 2rem;
            transition: all 0.3s ease;
            min-height: 100vh;
        }
        
        .page-header {
            margin-bottom: 2rem;
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
        
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--secondary);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .form-container {
            background: white;
            border-radius: 1rem;
            padding: 2.5rem;
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--border);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .form-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        .form-subtitle {
            color: var(--gray);
            font-size: 1rem;
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
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .form-label .required {
            color: #ef4444;
        }
        
        .form-input, .form-select, .form-textarea {
            padding: 0.875rem 1.25rem;
            border: 2px solid var(--border);
            border-radius: 0.75rem;
            font-size: 1rem;
            background: white;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        
        .form-input::placeholder {
            color: #94a3b8;
        }
        
        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        
        .file-upload-input {
            width: 100%;
            padding: 0.875rem 1.25rem;
            border: 2px dashed var(--border);
            border-radius: 0.75rem;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .file-upload-input:hover {
            border-color: var(--primary);
            background: #f1f5f9;
        }
        
        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray);
            cursor: pointer;
        }
        
        .file-upload-label i {
            font-size: 1.5rem;
            color: var(--primary);
        }
        
        .file-upload-label span {
            font-size: 0.9rem;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
        }
        
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
            font-size: 1rem;
        }
        
        .password-container {
            position: relative;
        }
        
        .password-strength {
            margin-top: 0.5rem;
            height: 4px;
            border-radius: 2px;
            background: #e2e8f0;
            overflow: hidden;
        }
        
        .password-strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .password-strength-weak {
            background: #ef4444;
            width: 33%;
        }
        
        .password-strength-medium {
            background: #f59e0b;
            width: 66%;
        }
        
        .password-strength-strong {
            background: var(--secondary);
            width: 100%;
        }
        
        .password-hint {
            font-size: 0.8rem;
            color: var(--gray);
            margin-top: 0.25rem;
        }
        
        .preview-container {
            display: flex;
            justify-content: center;
            margin-top: 1rem;
        }
        
        .profile-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--border);
            box-shadow: var(--shadow);
            position: relative;
        }
        
        .profile-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-preview .default-avatar {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            font-size: 3rem;
            font-weight: 600;
        }
        
        .security-info {
            background-color: #f8fafc;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-top: 1rem;
            border-left: 4px solid var(--primary);
        }
        
        .security-info h4 {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .security-info p {
            font-size: 0.85rem;
            color: var(--gray);
            margin: 0;
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
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .form-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>
    
    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Add New Admin</h1>
                <p class="page-subtitle">Create a new administrator account</p>
            </div>
            <a href="manage_users.php?section=admins" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Admins
            </a>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <div class="form-header">
                <h2 class="form-title">Admin Information</h2>
                <p class="form-subtitle">Fill in the details to create a new admin account</p>
            </div>
            
            <form method="POST" enctype="multipart/form-data" id="adminForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">
                            Full Name <span class="required">*</span>
                        </label>
                        <input type="text" name="fullname" class="form-input" placeholder="Enter full name" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            Email Address <span class="required">*</span>
                        </label>
                        <input type="email" name="email" class="form-input" placeholder="admin@example.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            Password <span class="required">*</span>
                        </label>
                        <div class="password-container">
                            <input type="password" name="password" id="password" class="form-input" placeholder="Create a strong password" required>
                            <button type="button" class="password-toggle" id="passwordToggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength">
                            <div class="password-strength-fill" id="passwordStrength"></div>
                        </div>
                        <p class="password-hint">Use at least 8 characters with a mix of letters, numbers & symbols</p>
                        
                        <div class="security-info">
                            <h4><i class="fas fa-shield-alt"></i> Security Note</h4>
                            <p>Passwords are encrypted using SHA1 with salt "CBE_DOCS_2023" for compatibility with the existing login system.</p>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            Confirm Password <span class="required">*</span>
                        </label>
                        <div class="password-container">
                            <input type="password" id="confirmPassword" class="form-input" placeholder="Confirm your password" required>
                            <button type="button" class="password-toggle" id="confirmPasswordToggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <p id="passwordMatch" class="password-hint"></p>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Profile Picture</label>
                        <div class="file-upload">
                            <input type="file" name="profile_picture" id="profilePicture" accept="image/*" class="file-upload-input" hidden>
                            <label for="profilePicture" class="file-upload-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Click to upload or drag and drop</span>
                                <small>PNG, JPG, GIF up to 5MB</small>
                            </label>
                        </div>
                        <div class="preview-container">
                            <div class="profile-preview">
                                <div class="default-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            Status <span class="required">*</span>
                        </label>
                        <select name="status" class="form-select" required>
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset Form
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add Admin
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Password visibility toggle
        const passwordToggle = document.getElementById('passwordToggle');
        const confirmPasswordToggle = document.getElementById('confirmPasswordToggle');
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('confirmPassword');
        
        passwordToggle.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
        
        confirmPasswordToggle.addEventListener('click', function() {
            const type = confirmPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordField.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
        
        // Password strength indicator
        passwordField.addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            let strength = 0;
            
            // Check password length
            if (password.length >= 8) strength += 1;
            
            // Check for mixed case
            if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) strength += 1;
            
            // Check for numbers
            if (password.match(/([0-9])/)) strength += 1;
            
            // Check for special characters
            if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/)) strength += 1;
            
            // Update strength bar
            strengthBar.classList.remove('password-strength-weak', 'password-strength-medium', 'password-strength-strong');
            
            if (strength <= 2) {
                strengthBar.classList.add('password-strength-weak');
            } else if (strength === 3) {
                strengthBar.classList.add('password-strength-medium');
            } else if (strength >= 4) {
                strengthBar.classList.add('password-strength-strong');
            }
        });
        
        // Password confirmation check
        confirmPasswordField.addEventListener('input', function() {
            const password = passwordField.value;
            const confirmPassword = this.value;
            const matchText = document.getElementById('passwordMatch');
            
            if (confirmPassword === '') {
                matchText.textContent = '';
                matchText.style.color = '';
            } else if (password === confirmPassword) {
                matchText.textContent = 'Passwords match';
                matchText.style.color = '#10b981';
            } else {
                matchText.textContent = 'Passwords do not match';
                matchText.style.color = '#ef4444';
            }
        });
        
        // Profile picture preview
        const profilePictureInput = document.getElementById('profilePicture');
        const profilePreview = document.querySelector('.profile-preview');
        
        profilePictureInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePreview.innerHTML = `<img src="${e.target.result}" alt="Profile Preview">`;
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Form validation
        document.getElementById('adminForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match. Please check and try again.');
                return false;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long.');
                return false;
            }
        });
    </script>
</body>
</html>
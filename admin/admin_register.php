<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Start session before any output
session_name('admin_session');
session_start();

include("../connection.php");

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}


// Initialize variables
$errors = [];
$success = '';

// Fetch programs from database
$programs_result = $conn->query("SELECT course_code, course_name FROM courses ORDER BY course_name");
$programs = [];
while ($row = $programs_result->fetch_assoc()) {
    $programs[] = $row;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $reg = trim($_POST['reg']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
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
        // Check if email already exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $errors['email'] = 'Email already registered';
        }
        $stmt->close();
    }
    
    // Validate registration number
    if (empty($reg)) {
        $errors['reg'] = 'Registration number is required';
    } elseif (!preg_match("/^\d{2}\.\d{4}\.\d{2}\.\d{2}\.\d{4}$/", $reg)) {
        $errors['reg'] = 'Invalid registration number format (e.g., 03.2481.01.01.2023)';
    } else {
        // Check if reg number already exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE reg = ?");
        $stmt->bind_param("s", $reg);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $errors['reg'] = 'Registration number already registered';
        }
        $stmt->close();
    }
    
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
    
    // Validate program
    if (empty($program)) {
        $errors['program'] = 'Program of study is required';
    }
    
    // Validate year
    if (empty($year)) {
        $errors['year'] = 'Year of study is required';
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        // Hash password - using the same method as register.php
        $salt = "CBE_DOCS_2023";
        $encrypted_password = sha1($fullname . "_" . $password . $salt);
        
        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, reg, password, program, year) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $fullname, $email, $reg, $encrypted_password, $program, $year);
        
        if ($stmt->execute()) {
            $success = 'User registered successfully!';
            // Clear form
            $fullname = $email = $reg = $password = $confirm_password = $program = $year = '';
        } else {
            $errors['database'] = 'Registration failed: ' . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User - CBE Doc's Store</title>
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
            max-width: 800px;
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
        
        .password-rules {
            background: #f8fafc;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-top: 0.5rem;
            border: 1px solid var(--border);
            font-size: 0.875rem;
        }
        
        .password-rules ul {
            margin-left: 1.5rem;
            color: var(--gray);
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
        }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>
    
    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Add New User</h1>
                <p class="page-subtitle">Register a new user account</p>
            </div>
            <a href="manage_users.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>
        
        <?php if ($success): ?>
            <div class="success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="fullname">Full Name</label>
                        <input type="text" id="fullname" name="fullname" class="form-input" 
                               value="<?php echo isset($fullname) ? htmlspecialchars($fullname) : ''; ?>" required>
                        <?php if (isset($errors['fullname'])): ?>
                            <div class="error"><?php echo $errors['fullname']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-input" 
                               value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="error"><?php echo $errors['email']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="reg">Registration Number</label>
                        <input type="text" id="reg" name="reg" class="form-input" 
                               placeholder="e.g., 03.2481.01.01.2023" 
                               value="<?php echo isset($reg) ? htmlspecialchars($reg) : ''; ?>" required>
                        <?php if (isset($errors['reg'])): ?>
                            <div class="error"><?php echo $errors['reg']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="program">Program of Study</label>
                        <select id="program" name="program" class="form-select" required>
                            <option value="">Select program</option>
                            <?php foreach ($programs as $program_item): ?>
                                <option value="<?php echo htmlspecialchars($program_item['course_code']); ?>" 
                                    <?php echo (isset($program) && $program == $program_item['course_code']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($program_item['course_name']); ?>
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
                            <option value="1" <?php echo (isset($year) && $year == '1') ? 'selected' : ''; ?>>First Year</option>
                            <option value="2" <?php echo (isset($year) && $year == '2') ? 'selected' : ''; ?>>Second Year</option>
                            <option value="3" <?php echo (isset($year) && $year == '3') ? 'selected' : ''; ?>>Third Year</option>
                            <option value="4" <?php echo (isset($year) && $year == '4') ? 'selected' : ''; ?>>Fourth Year</option>
                        </select>
                        <?php if (isset($errors['year'])): ?>
                            <div class="error"><?php echo $errors['year']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-input" required>
                        <?php if (isset($errors['password'])): ?>
                            <div class="error"><?php echo $errors['password']; ?></div>
                        <?php endif; ?>
                        <div class="password-rules">
                            <strong>Password requirements:</strong>
                            <ul>
                                <li>At least 8 characters long</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
                        <?php if (isset($errors['confirm_password'])): ?>
                            <div class="error"><?php echo $errors['confirm_password']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if (isset($errors['database'])): ?>
                    <div class="error" style="margin: 1.5rem 0;"><?php echo $errors['database']; ?></div>
                <?php endif; ?>
                
                <div class="form-actions">
                    <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Register User
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long.');
                return;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match.');
                return;
            }
        });
        
        // Live password validation
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const errorElement = document.querySelector('#password + .error');
            
            if (password.length > 0 && password.length < 8) {
                if (!errorElement) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error';
                    errorDiv.textContent = 'Password must be at least 8 characters';
                    this.parentNode.appendChild(errorDiv);
                }
            } else if (errorElement) {
                errorElement.remove();
            }
        });
        
        // Confirm password validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const errorElement = document.querySelector('#confirm_password + .error');
            
            if (confirmPassword.length > 0 && password !== confirmPassword) {
                if (!errorElement) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error';
                    errorDiv.textContent = 'Passwords do not match';
                    this.parentNode.appendChild(errorDiv);
                }
            } else if (errorElement && errorElement.textContent === 'Passwords do not match') {
                errorElement.remove();
            }
        });
    </script>
</body>
</html>
<?php
// Close the connection
$conn->close();
?>
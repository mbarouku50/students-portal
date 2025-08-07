<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("connection.php");

// Initialize variables
$errors = [];
$success = '';

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
        $stmt = $conn->prepare("SELECT user_id  FROM users WHERE email = ?");
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
        // Hash password - using sha1 with salt (though consider using password_hash() for better security)
        $salt = "CBE_DOCS_2023"; // Add your own salt
        $encrypted_password = sha1($fullname . "_" . $password . $salt);
        
        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, reg, password, program, year) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $fullname, $email, $reg, $encrypted_password, $program, $year);
        
        if ($stmt->execute()) {
            $success = 'Registration successful! You can now <a href="login.php">login</a>.';
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
    <title>Register - CBE Doc's Store</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        header {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
        }
        
        .logo span {
            color: var(--secondary-color);
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-left: 1.5rem;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        nav ul li a:hover {
            color: var(--secondary-color);
        }
        
        .auth-container {
            max-width: 500px;
            margin: 3rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .auth-title {
            text-align: center;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--primary-color);
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .error {
            color: var(--accent-color);
            font-size: 0.9rem;
            margin-top: 0.3rem;
        }
        
        .success {
            color: #27ae60;
            text-align: center;
            margin-bottom: 1.5rem;
            padding: 0.8rem;
            background-color: rgba(39, 174, 96, 0.1);
            border-radius: 4px;
        }
        
        .btn {
            display: inline-block;
            background-color: var(--secondary-color);
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
            font-size: 1rem;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .auth-footer a {
            color: var(--secondary-color);
            text-decoration: none;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 2rem 0;
            text-align: center;
            margin-top: 3rem;
        }
        
        .footer-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .social-links {
            margin: 1rem 0;
        }
        
        .social-links a {
            color: white;
            margin: 0 0.5rem;
            font-size: 1.2rem;
            transition: color 0.3s;
        }
        
        .social-links a:hover {
            color: var(--secondary-color);
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">CBE <span>Doc's Store</span></div>
                <nav>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="login.php">Login</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="auth-container">
            <h2 class="auth-title">Create an Account</h2>
            
            <?php if ($success): ?>
                <div class="success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" value="<?php echo isset($fullname) ? htmlspecialchars($fullname) : ''; ?>" required>
                    <?php if (isset($errors['fullname'])): ?>
                        <div class="error"><?php echo $errors['fullname']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <div class="error"><?php echo $errors['email']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="reg">Registration Number</label>
                    <input type="text" id="reg" name="reg" placeholder="e.g., 03.2481.01.01.2023" value="<?php echo isset($reg) ? htmlspecialchars($reg) : ''; ?>" required>
                    <?php if (isset($errors['reg'])): ?>
                        <div class="error"><?php echo $errors['reg']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <?php if (isset($errors['password'])): ?>
                        <div class="error"><?php echo $errors['password']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <div class="error"><?php echo $errors['confirm_password']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="program">Program of Study</label>
                    <select id="program" name="program" required>
                        <option value="">Select your program</option>
                        <?php
                        // Fetch programs from database
                        $programQuery = "SELECT course_code, course_name FROM courses ORDER BY course_name";
                        $programResult = $conn->query($programQuery);
                        
                        if ($programResult && $programResult->num_rows > 0) {
                            while ($programRow = $programResult->fetch_assoc()) {
                                $selected = (isset($program) && $program == $programRow['course_code']) ? 'selected' : '';
                                echo '<option value="'.htmlspecialchars($programRow['course_code']).'" '.$selected.'>'
                                    .htmlspecialchars($programRow['course_name']).'</option>';
                            }
                        } else {
                            echo '<option value="">No programs available</option>';
                        }
                        ?>
                    </select>
                    <?php if (isset($errors['program'])): ?>
                        <div class="error"><?php echo $errors['program']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="year">Year of Study</label>
                    <select id="year" name="year" required>
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
                
                <?php if (isset($errors['database'])): ?>
                    <div class="error" style="margin-bottom: 1.5rem;"><?php echo $errors['database']; ?></div>
                <?php endif; ?>
                
                <button type="submit" class="btn">Register</button>
                
                <div class="auth-footer">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </form>
        </div>
    </div>
    
<?php
// Close the connection just before including the footer
$conn->close();
include("temperate/footer.php");
?>
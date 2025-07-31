<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include("connection.php");

// Initialize variables
$error = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate inputs
    if (empty($email) || empty($password)) {
        $error = "Email and password are required";
    } else {
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT user_id, fullname, email, password FROM users WHERE email = ?");
        if ($stmt === false) {
            $error = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Verify password (using the same encryption method as registration)
                $salt = "CBE_DOCS_2023"; // Must match the salt used in registration
                $encrypted_password = sha1($user['fullname'] . "_" . $password . $salt);
                
                if ($encrypted_password === $user['password']) {
                    // Authentication successful - set all session variables
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_fullname'] = $user['fullname'];
                    
                    // Debugging - uncomment to verify session data
                    // echo "<pre>Session Data: "; print_r($_SESSION); echo "</pre>"; exit();
                    
                    // Redirect to index.php
                    if (headers_sent()) {
                        die("Redirect failed. Please click this <a href='index.php'>link</a>");
                    } else {
                        header("Location: index.php");
                        exit();
                    }
                } else {
                    $error = "Invalid email or password";
                }
            } else {
                $error = "Invalid email or password";
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CBE Doc's Store</title>
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
        
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .error {
            color: var(--accent-color);
            text-align: center;
            margin-bottom: 1.5rem;
            padding: 0.8rem;
            background-color: rgba(231, 76, 60, 0.1);
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
                        <li><a href="register.php">Register</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    
     <div class="container">
        <div class="auth-container">
            <h2 class="auth-title">Login to Your Account</h2>
            
            <?php if (!empty($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn">Login</button>
                
                <div class="auth-footer">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                    <p><a href="forgot-password.php">Forgot password?</a></p>
                </div>
            </form>
        </div>
    </div>
    
<?php
include("temperate/footer.php")
?>
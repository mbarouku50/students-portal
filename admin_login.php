<?php
session_start();
include("connection.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Email and password are required";
    } else {
        // Try stationery table for stationery login first
        $stmt2 = $conn->prepare("SELECT stationery_id, name, email, password FROM stationery WHERE email = ?");
        $stmt2->bind_param("s", $email);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        if ($result2->num_rows === 1) {
            $stationary = $result2->fetch_assoc();
            $salt = "CBE_DOCS_2023";
            $encrypted_password = sha1($password . $salt); 
            if ($encrypted_password === $stationary['password']) {
                $_SESSION['stationary_admin_id'] = $stationary['stationery_id'];
                $_SESSION['stationary_admin_name'] = $stationary['name'];
                $_SESSION['stationary_id'] = $stationary['stationery_id'];
                $_SESSION['stationary_email'] = $stationary['email'];
                header("Location: stationary/stationary_dashboard.php");
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } else {
            // Try admin table for admin login
            $stmt = $conn->prepare("SELECT admin_id, fullname, email, password FROM admin WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                $admin = $result->fetch_assoc();
                $salt = "CBE_DOCS_2023";
                $encrypted_password = sha1($password . $salt); 
                if ($encrypted_password === $admin['password']) {
                    $_SESSION['admin_id'] = $admin['admin_id'];
                    $_SESSION['admin_fullname'] = $admin['fullname'];
                    $_SESSION['admin_email'] = $admin['email'];
                    header("Location: admin/admin_dashboard.php");
                    exit();
                } else {
                    $error = "Invalid email or password";
                }
            } else {
                $error = "Invalid email or password";
            }
            $stmt->close();
        }
        $stmt2->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - CBE Doc's Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --success: #4cc9f0;
            --warning: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --border-radius: 10px;
            --box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-container {
            display: flex;
            width: 900px;
            height: 550px;
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
        }
        
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-left::before {
            content: '';
            position: absolute;
            top: -70px;
            right: -70px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .login-left::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 250px;
            height: 250px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .logo {
            margin-bottom: 30px;
            text-align: center;
        }
        
        .logo h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .logo p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .features {
            margin-top: 30px;
        }
        
        .feature {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .feature i {
            font-size: 20px;
            margin-right: 15px;
            background: rgba(255, 255, 255, 0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .feature div {
            font-size: 14px;
        }
        
        .login-right {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h2 {
            color: var(--dark);
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: var(--gray);
            font-size: 14px;
        }
        
        .login-form {
            width: 100%;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
            font-size: 14px;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }
        
        .input-with-icon input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius);
            font-size: 14px;
            transition: var(--transition);
        }
        
        .input-with-icon input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
            cursor: pointer;
        }
        
        .error-message {
            background: rgba(247, 37, 133, 0.1);
            color: var(--warning);
            padding: 12px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .error-message i {
            margin-right: 10px;
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 10px;
        }
        
        .btn-login:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: var(--gray);
        }
        
        .login-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .login-footer a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }
        
        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
                height: auto;
                width: 100%;
                max-width: 500px;
            }
            
            .login-left {
                padding: 30px;
            }
            
            .features {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div class="logo">
                <h1><i class="fas fa-store"></i> CBE Doc's Store</h1>
                <p>Management System Portal</p>
            </div>
            
            <div class="features">
                <div class="feature">
                    <i class="fas fa-shield-alt"></i>
                    <div>
                        <strong>Secure Authentication</strong>
                        <p>Protected admin and stationery access</p>
                    </div>
                </div>
                <div class="feature">
                    <i class="fas fa-tachometer-alt"></i>
                    <div>
                        <strong>Dashboard Access</strong>
                        <p>Manage inventory, orders, and reports</p>
                    </div>
                </div>
                <div class="feature">
                    <i class="fas fa-users-cog"></i>
                    <div>
                        <strong>Role-based Access</strong>
                        <p>Different privileges for different roles</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="login-right">
            <div class="login-header">
                <h2>Admin & Stationery Login</h2>
                <p>Enter your credentials to access the portal</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form class="login-form" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <span class="password-toggle" id="passwordToggle">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <button type="submit" class="btn-login">Sign In to Portal</button>
            </form>
            
            <div class="login-footer">
                <p>For authorized personnel only. <a href="login.php">Student login here</a></p>
            </div>
        </div>
    </div>

    <script>
        // Password visibility toggle
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordInput = document.getElementById('password');
        
        passwordToggle.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle eye icon
            const eyeIcon = this.querySelector('i');
            if (type === 'password') {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        });
        
        // Form validation
        const form = document.querySelector('.login-form');
        form.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in all fields');
            }
        });
        
        // Add animation on load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.login-container').style.opacity = '0';
            document.querySelector('.login-container').style.transform = 'translateY(20px)';
            
            setTimeout(function() {
                document.querySelector('.login-container').style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                document.querySelector('.login-container').style.opacity = '1';
                document.querySelector('.login-container').style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>
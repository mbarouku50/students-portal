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
        $stmt = $conn->prepare("SELECT admin_id, fullname, email, password FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            // Verify password (using the same encryption method as registration)
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - CBE Doc's Store</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }
        
        .admin-login-container {
            width: 100%;
            max-width: 450px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .admin-login-header {
            background-color: var(--primary-color);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .admin-login-header h1 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        
        .admin-login-header p {
            opacity: 0.8;
        }
        
        .admin-login-form {
            padding: 2rem;
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
            padding: 0.8rem 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            border-color: var(--secondary-color);
            outline: none;
        }
        
        .error-message {
            color: var(--accent-color);
            text-align: center;
            margin-bottom: 1.5rem;
            padding: 0.8rem;
            background-color: rgba(231, 76, 60, 0.1);
            border-radius: 5px;
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 1rem;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .admin-login-footer {
            text-align: center;
            padding: 1rem 2rem 2rem;
            border-top: 1px solid #eee;
        }
        
        .admin-login-footer a {
            color: var(--secondary-color);
            text-decoration: none;
        }
        
        .admin-login-footer a:hover {
            text-decoration: underline;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-login-header">
            <h1><i class="fas fa-lock"></i> Admin Portal</h1>
            <p>CBE Doc's Store Management System</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form class="admin-login-form" method="POST">
            <div class="form-group">
                <label for="email">Admin Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Login as Admin</button>
        </form>
        
        <div class="admin-login-footer">
            <p>For authorized personnel only. <a href="login.php">Student login here</a></p>
        </div>
    </div>
</body>
</html>
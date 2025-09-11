<?php
session_start();
// Sample user data - you'll replace this with your actual user data
if (isset($_SESSION['user_id']) && !isset($_SESSION['user_fullname'])) {
    $_SESSION['user_fullname'] = "John Doe";
    $_SESSION['profile_picture'] = "https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CBE student-portal</title>
    <style>
        /* Your existing CSS remains intact */
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
            font-size: 1.0rem;
            font-weight: bold;
        }
        
        .logo span {
            color: var(--secondary-color);
        }
        
        nav ul {
            display: flex;
            list-style: none;
            align-items: center;
        }
        
        nav ul li {
            margin-left: 1.5rem;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            display: flex;
            align-items: center;
        }
        
        nav ul li a i {
            margin-right: 8px;
        }
        
        nav ul li a:hover {
            color: var(--secondary-color);
        }
        
        .hero {
            background: linear-gradient(rgba(44, 62, 80, 0.9), rgba(44, 62, 80, 0.9)), url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 4rem 0;
            text-align: center;
        }
        
        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 2rem;
        }
        
        .search-bar {
            max-width: 600px;
            margin: 0 auto;
            display: flex;
        }
        
        .search-bar input {
            flex: 1;
            padding: 0.8rem;
            border: none;
            border-radius: 4px 0 0 4px;
            font-size: 1rem;
        }
        
        .search-bar button {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 0 1.5rem;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .search-bar button:hover {
            background-color: #c0392b;
        }
        
        .main-content {
            padding: 3rem 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--dark-color);
        }
        
        .courses {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .course-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .course-img {
            height: 150px;
            background-color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: bold;
        }
        
        .course-info {
            padding: 1.5rem;
        }
        
        .course-info h3 {
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }
        
        .course-info p {
            color: #666;
            margin-bottom: 1rem;
        }
        
        .btn {
            display: inline-block;
            background-color: var(--secondary-color);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .document-types {
            background-color: var(--light-color);
            padding: 3rem 0;
        }
        
        .doc-types {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        
        .doc-type {
            background-color: white;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .doc-type i {
            font-size: 2rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }
        
        .doc-type h3 {
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }
        
        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 2rem 0;
            text-align: center;
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
        
        /* Responsive Navbar */
        .nav-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
            margin-left: 1rem;
        }
        .nav-toggle span {
            height: 3px;
            width: 25px;
            background: white;
            margin: 4px 0;
            border-radius: 2px;
            transition: 0.4s;
        }
        @media (max-width: 900px) {
            .header-content {
                flex-direction: row;
                flex-wrap: wrap;
            }
            nav ul {
                flex-direction: column;
                width: 100%;
                background: var(--primary-color);
                position: absolute;
                top: 60px;
                left: 0;
                display: none;
                z-index: 1000;
            }
            nav ul.show {
                display: flex;
            }
            nav ul li {
                margin: 1rem 0;
                text-align: center;
            }
            .nav-toggle {
                display: flex;
            }
        }
        @media (max-width: 480px) {
            .logo {
                font-size: 1.2rem;
            }
            .hero h1 {
                font-size: 1.1rem;
            }
        }

        /* NEW STYLES FOR ENHANCED HEADER */
        .user-profile {
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 50px;
            transition: background-color 0.3s;
        }
        
        .user-profile:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .profile-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--secondary-color);
            margin-right: 10px;
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .user-role {
            font-size: 0.7rem;
            opacity: 0.8;
        }
        
        /* Dropdown menu for user profile */
        .dropdown {
            position: relative;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background-color: white;
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 5px;
            overflow: hidden;
            margin-top: 10px;
        }
        
        .dropdown-content a {
            color: var(--dark-color);
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }
        
        .dropdown-content a i {
            color: var(--secondary-color);
            margin-right: 8px;
        }
        
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        
        .dropdown.active .dropdown-content {
            display: block;
        }
        
        /* Mobile adjustments for new elements */
        @media (max-width: 900px) {
            .dropdown-content {
                position: static;
                box-shadow: none;
                background-color: rgba(0,0,0,0.1);
                margin-top: 10px;
            }
            
            .dropdown-content a {
                color: white;
                text-align: center;
            }
            
            .dropdown-content a i {
                color: white;
            }
            
            .user-info {
                display: none;
            }
        }
        
        /* Logo icon */
        .logo i {
            margin-right: 10px;
            font-size: 2rem;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-graduation-cap"></i>
                    CBE <span>Student-Portal</span>
                </div>
                <div class="nav-toggle" id="navToggle" aria-label="Toggle navigation" tabindex="0">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <nav>
                    <ul id="navMenu">
                        <li><a href="../index.php"><i class="fas fa-home"></i> Home</a></li>
                        <li><a href="../courses.php"><i class="fas fa-book"></i> Courses</a></li>
                        <li><a href="../documents.php"><i class="fas fa-file-alt"></i> Document Types</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="../charts.php"><i class="fas fa-chart-bar"></i> Charts</a></li>
                            <li><a href="../stationary/index.php"><i class="fas fa-pencil-alt"></i> Stationary</a></li>
                            <li class="dropdown" id="userDropdown">
                                <div class="user-profile" id="dropdownToggle">
                                    <?php
                                    // Check if user has a profile picture
                                    $profile_pic = "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png"; // Default image
                                    if (isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture'])) {
                                        $profile_pic = $_SESSION['profile_picture'];
                                    }
                                    ?>
                                    <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" class="profile-picture">
                                    <div class="user-info">
                                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_fullname']); ?></span>
                                        <span class="user-role">Student</span>
                                    </div>
                                    <i class="fas fa-chevron-down" style="margin-left: 5px; font-size: 0.8rem;"></i>
                                </div>
                                <div class="dropdown-content">
                                    <a href="user_profile.php"><i class="fas fa-user"></i> My Profile</a>
                                    <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                                </div>
                            </li>
                        <?php else: ?>
                            <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    <script>
    // Hamburger menu toggle
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    navToggle.addEventListener('click', () => {
        navMenu.classList.toggle('show');
    });
    navToggle.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            navMenu.classList.toggle('show');
        }
    });
    
    // Improved dropdown functionality
    const dropdownToggle = document.getElementById('dropdownToggle');
    const userDropdown = document.getElementById('userDropdown');
    
    if (dropdownToggle && userDropdown) {
        // Toggle dropdown on click
        dropdownToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('active');
            
            // Close other dropdowns if any
            document.querySelectorAll('.dropdown').forEach(function(dropdown) {
                if (dropdown !== userDropdown && dropdown.classList.contains('active')) {
                    dropdown.classList.remove('active');
                }
            });
        });
        
        // Prevent dropdown from closing when clicking inside it
        userDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.dropdown').forEach(function(dropdown) {
            dropdown.classList.remove('active');
        });
    });
    
    // Close dropdown when pressing Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.dropdown').forEach(function(dropdown) {
                dropdown.classList.remove('active');
            });
        }
    });
    </script>
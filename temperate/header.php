<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CBE Doc's Store - University Document Repository</title>
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
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">CBE <span>Student-Portal</span></div>
                <div class="nav-toggle" id="navToggle" aria-label="Toggle navigation" tabindex="0">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <nav>
                    <ul id="navMenu">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="#courses">Courses</a></li>
                        <li><a href="#documents">Document Types</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="#">Charts</a></li>
                            <li><a href="stationary/index.php">Stationary</a></li>
                            <li><a href="logout.php">Logout</a></li>
                            <li><span style="color: white;">Welcome, <?php echo htmlspecialchars($_SESSION['user_fullname']); ?></span></li>
                        <?php else: ?>
                            <li><a href="login.php">Login</a></li>
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
    </script>
    
    <section class="hero">
        <div class="container">
            <h1>Your University Document Repository</h1>
            <p>Find and share assignments, exams, notes, and other academic resources for all CBE bachelor degree programs.</p>
            <div class="search-bar">
                <input type="text" placeholder="Search for documents...">
                <button>Search</button>
            </div>
        </div>
    </section>
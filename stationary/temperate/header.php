<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Premium Printing & Stationery</title>
    <!-- Responsive viewport for mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* header stylish */
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
        
        .main-content {
            padding: 3rem 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--dark-color);
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
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            nav ul {
                margin-top: 1rem;
                justify-content: center;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .courses {
                grid-template-columns: 1fr;
            }
        }





        /* stationary stylish */
         body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            color: #1f2937;
            font-size: 1rem;
            line-height: 1.6;
        }

        .hero-section {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%), url('https://source.unsplash.com/random/1920x1080/?abstract');
            background-blend-mode: overlay;
            background-size: cover;
            color: white;
            padding: 6rem 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.25rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero-section p {
            font-size: 1.5rem;
            max-width: 700px;
            margin: 0 auto 2.5rem;
            opacity: 0.9;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 4rem auto;
            padding: 0 1.5rem;
        }

        .service-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
            backdrop-filter: blur(8px);
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 1);
        }

        .service-card h3 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .service-card p {
            color: #4b5563;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .service-card a {
            display: inline-block;
            padding: 0.85rem 2rem;
            background: linear-gradient(90deg, #4f46e5, #7c3aed);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .service-card a:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
        }

        @media (max-width: 1024px) {
            .hero-section h1 {
                font-size: 2.8rem;
            }
            .hero-section p {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 4rem 1rem;
            }
            .hero-section h1 {
                font-size: 2.2rem;
            }
            .hero-section p {
                font-size: 1rem;
            }
            .services-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            body {
                font-size: 1.05rem;
            }
        }

        @media (max-width: 480px) {
            .hero-section {
                padding: 2.5rem 0.5rem;
            }
            .hero-section h1 {
                font-size: 1.4rem;
            }
            .hero-section p {
                font-size: 0.95rem;
            }
            .service-card {
                padding: 1rem;
            }
            body {
                font-size: 1rem;
            }
            .service-card h3 {
                font-size: 1.1rem;
            }
            .service-card p {
                font-size: 0.95rem;
            }
        }
    </style>
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
                        <li><a href="../index.php">Home</a></li>
                        <li><a href="../courses.php">Courses</a></li>
                        <li><a href="../documents.php">Document Types</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="../charts.php">Charts</a></li>
                            <li><a href="index.php">Stationary</a></li>
                            <li><a href="../logout.php">Logout</a></li>
                            <li><span style="color: white;">Welcome, <?php echo htmlspecialchars($_SESSION['user_fullname']); ?></span></li>
                        <?php else: ?>
                            <li><a href="login.php">Login</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
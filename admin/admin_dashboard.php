<?php

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include("connection.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CBE Doc's Store</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --sidebar-width: 280px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(to bottom, var(--primary-color), #1a2a3a);
            color: white;
            height: 100vh;
            position: fixed;
            box-shadow: 2px 0 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .sidebar-header h2 {
            margin-bottom: 0.5rem;
        }
        
        .sidebar-header p {
            opacity: 0.8;
            font-size: 0.9rem;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 1rem 0;
        }
        
        .sidebar-menu li {
            margin: 0.5rem 0;
        }
        
        .sidebar-menu a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 0.8rem 1.5rem;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: rgba(255,255,255,0.1);
            border-left: 3px solid var(--secondary-color);
        }
        
        .sidebar-menu i {
            margin-right: 1rem;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        .sidebar-menu .badge {
            margin-left: auto;
            background-color: var(--accent-color);
            color: white;
            border-radius: 10px;
            padding: 0.2rem 0.6rem;
            font-size: 0.75rem;
        }
        
        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }
        
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .page-title h1 {
            color: var(--primary-color);
            font-size: 1.8rem;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
        }
        
        .user-menu .user-info {
            text-align: right;
            margin-right: 1rem;
        }
        
        .user-menu .user-name {
            font-weight: 500;
        }
        
        .user-menu .user-role {
            font-size: 0.8rem;
            color: #666;
        }
        
        .user-menu .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .card {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .card-icon.users {
            background-color: #3498db;
        }
        
        .card-icon.documents {
            background-color: #2ecc71;
        }
        
        .card-icon.courses {
            background-color: #9b59b6;
        }
        
        .card-icon.storage {
            background-color: #f39c12;
        }
        
        .card-value {
            font-size: 2rem;
            font-weight: bold;
            margin: 0.5rem 0;
        }
        
        .card-title {
            color: #666;
            font-size: 0.9rem;
        }
        
        /* Recent Activity */
        .recent-activity {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        
        .section-title {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .activity-list {
            list-style: none;
        }
        
        .activity-item {
            display: flex;
            padding: 1rem 0;
            border-bottom: 1px solid #f5f5f5;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--light-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: var(--secondary-color);
            flex-shrink: 0;
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-text {
            margin-bottom: 0.3rem;
        }
        
        .activity-time {
            font-size: 0.8rem;
            color: #888;
        }
        
        /* Quick Stats */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            text-align: center;
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin: 0.5rem 0;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        /* Responsive Styles */
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
                overflow: hidden;
            }
            
            .sidebar-header h2, .sidebar-header p, .sidebar-menu span {
                display: none;
            }
            
            .sidebar-menu a {
                justify-content: center;
                padding: 0.8rem;
            }
            
            .sidebar-menu i {
                margin-right: 0;
                font-size: 1.3rem;
            }
            
            .main-content {
                margin-left: 80px;
            }
        }
        
        @media (max-width: 768px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            
            .quick-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 576px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .quick-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
      <!-- Sidebar Navigation -->
    <?php include('sidebar.php'); ?>

    <!-- Main Content Area -->
    <main class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="page-title">
                <h1>Admin Dashboard</h1>
            </div>
            <div class="user-menu">
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($_SESSION['admin_fullname']); ?></div>
                    <div class="user-role">Administrator</div>
                </div>
                <div class="avatar">
                    <?php echo strtoupper(substr($_SESSION['admin_fullname'], 0, 1)); ?>
                </div>
            </div>
        </div>

        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="card">
                <div class="card-header">
                    <h3>Total Users</h3>
                    <div class="card-icon users">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="card-value">
                    <?php
                    $query = "SELECT COUNT(*) as total FROM users";
                    $result = $conn->query($query);
                    $row = $result->fetch_assoc();
                    echo $row['total'];
                    ?>
                </div>
                <div class="card-title">Registered Users</div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Documents</h3>
                    <div class="card-icon documents">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
                <div class="card-value">
                    <?php
                    $query = "SELECT COUNT(*) as total FROM documents";
                    $result = $conn->query($query);
                    $row = $result->fetch_assoc();
                    echo $row['total'];
                    ?>
                </div>
                <div class="card-title">Uploaded Documents</div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Courses</h3>
                    <div class="card-icon courses">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
                <div class="card-value">
                    <?php
                    $query = "SELECT COUNT(DISTINCT program) as total FROM users";
                    $result = $conn->query($query);
                    $row = $result->fetch_assoc();
                    echo $row['total'];
                    ?>
                </div>
                <div class="card-title">Active Courses</div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Storage</h3>
                    <div class="card-icon storage">
                        <i class="fas fa-database"></i>
                    </div>
                </div>
                <div class="card-value">1.2GB</div>
                <div class="card-title">Used of 2GB</div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="section-title">
            <h2>Quick Statistics</h2>
        </div>
        <div class="quick-stats">
            <div class="stat-card">
                <i class="fas fa-user-graduate" style="color: #3498db; font-size: 1.5rem;"></i>
                <div class="stat-value">
                    <?php
                    $query = "SELECT COUNT(*) as total FROM users WHERE role = 'student'";
                    $result = $conn->query($query);
                    $row = $result->fetch_assoc();
                    echo $row['total'];
                    ?>
                </div>
                <div class="stat-label">Students</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-chalkboard-teacher" style="color: #e74c3c; font-size: 1.5rem;"></i>
                <div class="stat-value">
                    <?php
                    $query = "SELECT COUNT(*) as total FROM users WHERE role = 'lecturer'";
                    $result = $conn->query($query);
                    $row = $result->fetch_assoc();
                    echo $row['total'];
                    ?>
                </div>
                <div class="stat-label">Lecturers</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-file-pdf" style="color: #2ecc71; font-size: 1.5rem;"></i>
                <div class="stat-value">
                    <?php
                    $query = "SELECT COUNT(*) as total FROM documents WHERE file_type = 'pdf'";
                    $result = $conn->query($query);
                    $row = $result->fetch_assoc();
                    echo $row['total'];
                    ?>
                </div>
                <div class="stat-label">PDF Files</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-file-word" style="color: #9b59b6; font-size: 1.5rem;"></i>
                <div class="stat-value">
                    <?php
                    $query = "SELECT COUNT(*) as total FROM documents WHERE file_type = 'doc' OR file_type = 'docx'";
                    $result = $conn->query($query);
                    $row = $result->fetch_assoc();
                    echo $row['total'];
                    ?>
                </div>
                <div class="stat-label">Word Docs</div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="section-title">
            <h2>Recent Activity</h2>
            <a href="#" style="font-size: 0.9rem; color: var(--secondary-color);">View All</a>
        </div>
        <div class="recent-activity">
            <ul class="activity-list">
                <li class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-text">New user registered: John Doe (BBA Program)</div>
                        <div class="activity-time">10 minutes ago</div>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-file-upload"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-text">New document uploaded: Financial Accounting Notes.pdf</div>
                        <div class="activity-time">25 minutes ago</div>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-comment"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-text">New comment on Marketing Case Study by Jane Smith</div>
                        <div class="activity-time">1 hour ago</div>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-text">User role updated: Jane Smith promoted to Moderator</div>
                        <div class="activity-time">2 hours ago</div>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-text">Document downloaded 15 times: Taxation Principles Guide</div>
                        <div class="activity-time">3 hours ago</div>
                    </div>
                </li>
            </ul>
        </div>
    </main>

    <?php $conn->close(); ?>
</body>
</html>
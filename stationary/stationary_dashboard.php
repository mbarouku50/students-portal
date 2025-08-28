<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Check if stationary admin is logged in
if (!isset($_SESSION['stationary_admin_id'])) {
    header('Location: ../admin_login.php');
    exit();
}

// Database connection
include("../connection.php");
$stationary_id = $_SESSION['stationary_admin_id'];

// Fetch real data from database
// 1. Print Requests Count
$print_query = "SELECT COUNT(*) as count FROM print_jobs WHERE stationery_id = ? AND status IN ('pending', 'processing')";
$print_stmt = $conn->prepare($print_query);
$print_stmt->bind_param("i", $stationary_id);
$print_stmt->execute();
$print_result = $print_stmt->get_result();
$print_data = $print_result->fetch_assoc();
$print_count = $print_data['count'];

// 2. Pending Orders Count
$orders_query = "SELECT COUNT(*) as count FROM print_jobs WHERE stationery_id = ? AND status = 'pending'";
$orders_stmt = $conn->prepare($orders_query);
$orders_stmt->bind_param("i", $stationary_id);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();
$orders_data = $orders_result->fetch_assoc();
$orders_count = $orders_data['count'];

// 3. Completed Orders Count
$completed_query = "SELECT COUNT(*) as count FROM print_jobs WHERE stationery_id = ? AND status = 'completed'";
$completed_stmt = $conn->prepare($completed_query);
$completed_stmt->bind_param("i", $stationary_id);
$completed_stmt->execute();
$completed_result = $completed_stmt->get_result();
$completed_data = $completed_result->fetch_assoc();
$completed_count = $completed_data['count'];

// 4. Recent Activities (only from print_jobs)
$activities_query = "
    SELECT 'print' as type, user_name, created_at, 
    CASE 
        WHEN status = 'pending' THEN 'New print request'
        WHEN status = 'processing' THEN 'Print job in progress'
        WHEN status = 'completed' THEN 'Print job completed'
        ELSE 'Print job update'
    END as description, 
    job_id as reference_id,
    status
    FROM print_jobs 
    WHERE stationery_id = ? 
    ORDER BY created_at DESC 
    LIMIT 6
";
$activities_stmt = $conn->prepare($activities_query);
$activities_stmt->bind_param("i", $stationary_id);
$activities_stmt->execute();
$activities_result = $activities_stmt->get_result();
$recent_activities = $activities_result->fetch_all(MYSQLI_ASSOC);

// Function to format time difference
function timeAgo($timestamp) {
    $currentTime = time();
    $timestamp = strtotime($timestamp);
    $timeDiff = $currentTime - $timestamp;
    
    if ($timeDiff < 60) {
        return "Just now";
    } elseif ($timeDiff < 3600) {
        $minutes = floor($timeDiff / 60);
        return $minutes . " min ago";
    } elseif ($timeDiff < 86400) {
        $hours = floor($timeDiff / 3600);
        return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
    } else {
        $days = floor($timeDiff / 86400);
        return $days . " day" . ($days > 1 ? "s" : "") . " ago";
    }
}


// Fetch shop details
$shop_query = "SELECT * FROM stationery WHERE stationery_id = ?";
$shop_stmt = $conn->prepare($shop_query);
$shop_stmt->bind_param("i", $stationary_id);
$shop_stmt->execute();
$shop_result = $shop_stmt->get_result();
$shop_data = $shop_result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stationary Management Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.comcss2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --secondary: #3f37c9;
            --dark: #1a1a2e;
            --light: #f8f9fa;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #f72585;
            --gray: #6c757d;
            --white: #ffffff;
            --card-shadow: 0 10px 20px rgba(0, 0, 0, 0.05), 0 6px 6px rgba(0, 0, 0, 0.1);
            --sidebar-width: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fb 0%, #e8ecf1 100%);
            color: var(--dark);
            display: flex;
            min-height: 100vh;
            line-height: 1.6;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #4361ee 0%, #3a0ca3 100%);
            color: #fff;
            padding: 1.5rem 0;
            position: fixed;
            height: 100vh;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.15);
            overflow-y: auto;
            overflow-x: hidden;
        }
        .sidebar-header {
            display: flex;
            align-items: center;
            padding: 0 1.5rem 1.5rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar-header img {
            width: 45px;
            height: 45px;
            margin-right: 12px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .sidebar-header h3 {
            font-weight: 700;
            font-size: 1.3rem;
            letter-spacing: 0.5px;
            background: linear-gradient(45deg, #fff, #f72585);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .sidebar-menu {
            padding: 0 1rem;
        }
        .menu-title {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255, 255, 255, 0.6);
            padding: 0 1rem;
            margin: 1.5rem 0 0.75rem;
            font-weight: 600;
        }
        .menu-item {
            display: flex;
            align-items: center;
            padding: 0.85rem 1.25rem;
            border-radius: 0.75rem;
            margin-bottom: 0.35rem;
            cursor: pointer;
            transition: all 0.3s ease;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }
        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s ease;
        }
        .menu-item:hover::before {
            left: 100%;
        }
        .menu-item:hover {
            background-color: rgba(255, 255, 255, 0.12);
            color: #fff;
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .menu-item.active {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.1));
            color: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .menu-item.active::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: linear-gradient(180deg, #f72585, #4361ee);
            border-radius: 3px 0 0 3px;
        }
        .menu-item i {
            margin-right: 0.85rem;
            font-size: 1.15rem;
            width: 24px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .menu-item:hover i {
            transform: scale(1.1);
        }
        .menu-item span {
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .menu-item.active span {
            font-weight: 600;
        }
        .menu-badge {
            position: absolute;
            right: 1rem;
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
            transition: all 0.3s ease;
            width: calc(100% - var(--sidebar-width));
        }

        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            padding: 1.75rem 2rem;
            background: var(--white);
            border-radius: 1.25rem;
            box-shadow: var(--card-shadow);
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: 1px solid rgba(67, 97, 238, 0.1);
        }

        .header-title h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-title p {
            color: var(--gray);
            font-size: 1rem;
            margin: 0;
            font-weight: 400;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1.5rem;
            background: rgba(67, 97, 238, 0.08);
            border-radius: 50px;
            transition: all 0.3s ease;
            border: 1px solid rgba(67, 97, 238, 0.1);
        }

        .user-profile:hover {
            background: rgba(67, 97, 238, 0.12);
            transform: translateY(-2px);
        }

        .user-profile img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-light);
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.2);
        }

        .user-info h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--dark);
        }

        .user-info p {
            font-size: 0.85rem;
            color: var(--gray);
            margin: 0;
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 1.5rem;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(67, 97, 238, 0.1);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 120px;
            height: 120px;
            background: linear-gradient(45deg, transparent, rgba(67, 97, 238, 0.03));
            border-radius: 0 0 0 120px;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .card-icon.print {
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.1) 0%, rgba(67, 97, 238, 0.2) 100%);
            color: var(--primary);
        }

        .card-icon.pending {
            background: linear-gradient(135deg, rgba(248, 150, 30, 0.1) 0%, rgba(248, 150, 30, 0.2) 100%);
            color: var(--warning);
        }

        .card-icon.completed {
            background: linear-gradient(135deg, rgba(76, 201, 240, 0.1) 0%, rgba(76, 201, 240, 0.2) 100%);
            color: var(--success);
        }

        .card-title {
            font-size: 1rem;
            font-weight: 500;
            color: var(--gray);
            margin-bottom: 0.75rem;
        }

        .card-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1.5rem;
            line-height: 1;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid rgba(0, 0, 0, 0.06);
            padding-top: 1.25rem;
            margin-top: 1.25rem;
        }

        .card-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }

        .card-link i {
            margin-left: 0.5rem;
            transition: transform 0.3s ease;
        }

        .card-link:hover {
            color: var(--secondary);
        }

        .card-link:hover i {
            transform: translateX(5px);
        }

        .card-badge {
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
        }

        .badge-success {
            background: linear-gradient(135deg, rgba(76, 201, 240, 0.15) 0%, rgba(76, 201, 240, 0.25) 100%);
            color: var(--success);
        }

        .badge-warning {
            background: linear-gradient(135deg, rgba(248, 150, 30, 0.15) 0%, rgba(248, 150, 30, 0.25) 100%);
            color: var(--warning);
        }

        .badge-info {
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.15) 0%, rgba(67, 97, 238, 0.25) 100%);
            color: var(--primary);
        }

        /* Recent Activity */
        .recent-activity {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 1.5rem;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            margin-top: 2rem;
            border: 1px solid rgba(67, 97, 238, 0.1);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin: 0;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .view-all {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            background: rgba(67, 97, 238, 0.08);
            transition: all 0.3s ease;
        }

        .view-all i {
            margin-left: 0.5rem;
            transition: transform 0.3s ease;
        }

        .view-all:hover {
            background: rgba(67, 97, 238, 0.12);
        }

        .view-all:hover i {
            transform: translateX(5px);
        }

        .activity-list {
            list-style: none;
        }

        .activity-item {
            display: flex;
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            border-radius: 12px;
        }

        .activity-item:hover {
            background-color: rgba(67, 97, 238, 0.04);
            transform: translateX(5px);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.25rem;
            flex-shrink: 0;
            font-size: 1.25rem;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .activity-icon.print {
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.1) 0%, rgba(67, 97, 238, 0.2) 100%);
            color: var(--primary);
        }

        .activity-icon.processing {
            background: linear-gradient(135deg, rgba(76, 201, 240, 0.1) 0%, rgba(76, 201, 240, 0.2) 100%);
            color: var(--success);
        }

        .activity-icon.completed {
            background: linear-gradient(135deg, rgba(67, 160, 71, 0.1) 0%, rgba(67, 160, 71, 0.2) 100%);
            color: #43a047;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 1rem;
            color: var(--dark);
        }

        .activity-time {
            font-size: 0.85rem;
            color: var(--gray);
            display: flex;
            align-items: center;
        }

        .activity-time::before {
            content: '';
            display: inline-block;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background-color: var(--gray);
            margin-right: 0.75rem;
        }

        /* Mobile toggle button */
        .toggle-sidebar {
            display: none;
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 1.25rem;
            cursor: pointer;
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 999;
            box-shadow: 0 5px 20px rgba(67, 97, 238, 0.3);
            transition: all 0.3s ease;
        }
        .toggle-sidebar:hover {
            transform: scale(1.1) rotate(90deg);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
        }

        /* Overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .dashboard-cards {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: 5px 0 30px rgba(0, 0, 0, 0.15);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1.5rem;
            }
            .toggle-sidebar {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1.5rem;
            }
            .user-profile {
                align-self: stretch;
                justify-content: center;
            }
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            .sidebar-overlay.active {
                display: block;
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 1.5rem;
            }
            .header-title h1 {
                font-size: 1.75rem;
            }
            .card {
                padding: 1.75rem;
            }
            .card-value {
                font-size: 2.25rem;
            }
            .recent-activity {
                padding: 1.75rem;
            }
            .section-title {
                font-size: 1.35rem;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 100%;
                padding: 1rem 0;
            }
            .sidebar-header {
                padding: 0 1rem 1rem;
            }
            .sidebar-header img {
                width: 40px;
                height: 40px;
            }
            .sidebar-header h3 {
                font-size: 1.2rem;
            }
            .sidebar-menu {
                padding: 0 0.5rem;
            }
            .menu-item {
                padding: 0.85rem 1rem;
                border-radius: 0.5rem;
            }
            .menu-title {
                padding: 0 1rem;
            }
            .main-content {
                padding: 1.25rem;
            }
            .header {
                padding: 1.25rem;
            }
            .card-header {
                flex-direction: column;
                gap: 1.25rem;
            }
            .activity-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            .activity-icon {
                margin-right: 0;
            }
            .card-footer {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
            .toggle-sidebar {
                width: 45px;
                height: 45px;
                bottom: 20px;
                right: 20px;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            animation: fadeIn 0.6s ease-out;
        }

        .card:nth-child(2) {
            animation-delay: 0.15s;
        }

        .card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .activity-item {
            animation: fadeIn 0.5s ease-out;
        }

        .activity-item:nth-child(1) { animation-delay: 0.1s; }
        .activity-item:nth-child(2) { animation-delay: 0.2s; }
        .activity-item:nth-child(3) { animation-delay: 0.3s; }
        .activity-item:nth-child(4) { animation-delay: 0.4s; }
        .activity-item:nth-child(5) { animation-delay: 0.5s; }
        .activity-item:nth-child(6) { animation-delay: 0.6s; }
        
        /* Loading animation */
        .loading {
            display: inline-block;
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Animation for sidebar items */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .menu-item {
            animation: slideIn 0.4s ease forwards;
        }

        .menu-item:nth-child(1) { animation-delay: 0.1s; }
        .menu-item:nth-child(2) { animation-delay: 0.15s; }
        .menu-item:nth-child(3) { animation-delay: 0.2s; }
        .menu-item:nth-child(4) { animation-delay: 0.25s; }
        .menu-item:nth-child(5) { animation-delay: 0.3s; }
        .menu-item:nth-child(6) { animation-delay: 0.35s; }
        .menu-item:nth-child(7) { animation-delay: 0.4s; }
        .menu-item:nth-child(8) { animation-delay: 0.45s; }

        /* Scrollbar styling for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>
    <!-- Overlay for mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="https://cdn-icons-png.flaticon.com/512/3069/3069172.png" alt="Stationary Logo">
            <h3>StationaryPro</h3>
        </div>
        <div class="sidebar-menu">
            <p class="menu-title">Main</p>
            <a href="stationary_dashboard.php" class="menu-item<?= basename($_SERVER['PHP_SELF']) == 'stationary_dashboard.php' ? ' active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <p class="menu-title">Operations</p>
            <a href="print_requests.php" class="menu-item<?= basename($_SERVER['PHP_SELF']) == 'print_requests.php' ? ' active' : '' ?>">
                <i class="fas fa-print"></i>
                <span>Print Requests</span>
                <?php if (isset($print_count) && $print_count > 0): ?>
                    <span class="menu-badge"><?= $print_count ?></span>
                <?php endif; ?>
            </a>
            <a href="edit_stationery.php?id=<?= htmlspecialchars($_SESSION['stationary_id'] ?? '') ?>" class="menu-item<?= basename($_SERVER['PHP_SELF']) == 'edit_stationery.php' ? ' active' : '' ?>">
                <i class="fas fa-store"></i>
                <span>Shop Settings</span>
            </a>
            
            <p class="menu-title">Account</p>
            <a href="../admin/logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <div class="header-title">
                <h1>Dashboard Overview</h1>
                <p>Welcome back, <?= htmlspecialchars($_SESSION['stationary_admin_name'] ?? 'Admin') ?></p>
            </div>
            
            <div class="user-profile">
                <img src="<?= htmlspecialchars($shop_data['logo']) ?>" alt="Shop Logo" style="border-radius: 50%; width: 50px; height: 50px;">
                <div class="user-info">
                    <h4><?= htmlspecialchars($_SESSION['stationary_admin_name'] ?? 'Admin') ?></h4>
                    <p>Stationary Admin</p>
                </div>
            </div>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <div class="card-header">
                    <div>
                        <p class="card-title">Print Requests</p>
                        <h3 class="card-value"><?= $print_count ?></h3>
                    </div>
                    <div class="card-icon print">
                        <i class="fas fa-print"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="print_requests.php" class="card-link">
                        View All Requests
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <?php if ($print_count > 0): ?>
                        <span class="card-badge badge-success">+<?= $print_count ?> New</span>
                    <?php else: ?>
                        <span class="card-badge badge-info">No new requests</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <div>
                        <p class="card-title">Pending Orders</p>
                        <h3 class="card-value"><?= $orders_count ?></h3>
                    </div>
                    <div class="card-icon pending">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="print_requests.php?status=pending" class="card-link">
                        Manage Orders
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <?php if ($orders_count > 0): ?>
                        <span class="card-badge badge-warning">Attention Needed</span>
                    <?php else: ?>
                        <span class="card-badge badge-info">All clear</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <div>
                        <p class="card-title">Completed Jobs</p>
                        <h3 class="card-value"><?= $completed_count ?></h3>
                    </div>
                    <div class="card-icon completed">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="print_requests.php?status=completed" class="card-link">
                        View Completed
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <?php if ($completed_count > 0): ?>
                        <span class="card-badge badge-success"><?= $completed_count ?> Done</span>
                    <?php else: ?>
                        <span class="card-badge badge-info">No completions yet</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="recent-activity">
            <div class="section-header">
                <h3 class="section-title">Recent Activities</h3>
                <a href="print_requests.php" class="view-all">
                    View All Activities
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <ul class="activity-list">
                <?php if (!empty($recent_activities)): ?>
                    <?php foreach ($recent_activities as $activity): ?>
                        <li class="activity-item">
                            <div class="activity-icon <?= $activity['status'] ?>">
                                <i class="fas fa-<?= 
                                    $activity['status'] == 'pending' ? 'clock' : 
                                    ($activity['status'] == 'processing' ? 'cog' : 
                                    ($activity['status'] == 'completed' ? 'check-circle' : 'print')) 
                                ?>"></i>
                            </div>
                            <div class="activity-content">
                                <h4 class="activity-title">
                                    <?= htmlspecialchars($activity['description']) ?> 
                                    from <?= htmlspecialchars($activity['user_name']) ?>
                                </h4>
                                <p class="activity-time"><?= timeAgo($activity['created_at']) ?> • Ref: #<?= htmlspecialchars($activity['reference_id']) ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="activity-item">
                        <div class="activity-content">
                            <h4 class="activity-title">No recent activities found</h4>
                            <p class="activity-time">Activities will appear here as they occur</p>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <button class="toggle-sidebar" id="toggleSidebar">
        <i class="fas fa-bars"></i>
    </button>

    <script>
        // Toggle sidebar on mobile
        const sidebarEl = document.querySelector('.sidebar');
        const toggleButton = document.getElementById('toggleSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        toggleButton.addEventListener('click', function() {
            sidebarEl.classList.toggle('active');
            overlay.classList.toggle('active');
            
            // Update icon based on state
            const icon = this.querySelector('i');
            if (icon.classList.contains('fa-bars')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
        
        // Close sidebar when clicking on overlay
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            this.classList.remove('active');
            const icon = toggleButton.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 992 && 
                !sidebar.contains(event.target) && 
                !toggleButton.contains(event.target) && 
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                const icon = toggleButton.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
        
        // Add resize event to handle sidebar on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                const icon = toggleButton.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
        
        // Add subtle hover effects to cards
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-10px)';
                card.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.15)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
                card.style.boxShadow = 'var(--card-shadow)';
            });
        });
        
        // Auto-refresh dashboard data every 60 seconds
        setInterval(function() {
            // Show loading state
            const cards = document.querySelectorAll('.card-value');
            cards.forEach(card => {
                card.innerHTML = '<span class="loading"></span>';
            });
            
            // Reload the page to get updated data
            setTimeout(() => {
                location.reload();
            }, 2000);
        }, 60000);
    </script>
</body>
</html>
<?php
session_start();
// Change this check to match what you set in admin_login.php
if (!isset($_SESSION['stationary_admin_id'])) {
    header('Location: ../admin/admin_login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stationary Management Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.1);
            --sidebar-width: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fb;
            color: var(--dark);
            display: flex;
            min-height: 100vh;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
            transition: all 0.3s;
        }

        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .header-title h1 {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--dark);
        }

        .header-title p {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 0.75rem;
            object-fit: cover;
        }

        .user-info h4 {
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.1rem;
        }

        .user-info p {
            font-size: 0.75rem;
            color: var(--gray);
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .card {
            background: var(--white);
            border-radius: 0.75rem;
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .card-icon.print {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary);
        }

        .card-icon.manage {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success);
        }

        .card-icon.logout {
            background-color: rgba(247, 37, 133, 0.1);
            color: var(--danger);
        }

        .card-title {
            font-size: 1rem;
            font-weight: 500;
            color: var(--gray);
            margin-bottom: 0.5rem;
        }

        .card-value {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 1rem;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }

        .card-link i {
            margin-left: 0.5rem;
            transition: transform 0.2s;
        }

        .card-link:hover {
            text-decoration: underline;
        }

        .card-link:hover i {
            transform: translateX(3px);
        }

        /* Recent Activity */
        .recent-activity {
            background: var(--white);
            border-radius: 0.75rem;
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }

        .activity-list {
            list-style: none;
        }

        .activity-item {
            display: flex;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .activity-time {
            font-size: 0.75rem;
            color: var(--gray);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                margin-left: -280px;
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar.active {
                margin-left: 0;
            }
        }

        /* Toggle Button */
        .toggle-sidebar {
            display: none;
            background: var(--primary);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 1.25rem;
            cursor: pointer;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 999;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        @media (max-width: 992px) {
            .toggle-sidebar {
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="header">
            <div class="header-title">
                <h1>Dashboard Overview</h1>
                <p>Welcome back, <?= htmlspecialchars($_SESSION['stationary_admin_name'] ?? 'Admin') ?></p>
            </div>
            
            <div class="user-profile">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['stationary_admin_name'] ?? 'Admin') ?>&background=4361ee&color=fff" alt="User">
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
                        <h3 class="card-value">24 New</h3>
                    </div>
                    <div class="card-icon print">
                        <i class="fas fa-print"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="print_requests.php" class="card-link">
                        View All
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <div>
                        <p class="card-title">Pending Orders</p>
                        <h3 class="card-value">8 Items</h3>
                    </div>
                    <div class="card-icon manage">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="orders.php" class="card-link">
                        Manage Orders
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <div>
                        <p class="card-title">Low Stock Items</p>
                        <h3 class="card-value">5 Products</h3>
                    </div>
                    <div class="card-icon logout">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="inventory.php" class="card-link">
                        Check Inventory
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="recent-activity">
            <h3 class="section-title">Recent Activities</h3>
            <ul class="activity-list">
                <li class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-print"></i>
                    </div>
                    <div class="activity-content">
                        <h4 class="activity-title">New print request from John Doe</h4>
                        <p class="activity-time">10 minutes ago</p>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="activity-content">
                        <h4 class="activity-title">New order #ORD-2023-056</h4>
                        <p class="activity-time">1 hour ago</p>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="activity-content">
                        <h4 class="activity-title">Print request #PR-2023-124 completed</h4>
                        <p class="activity-time">2 hours ago</p>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="activity-content">
                        <h4 class="activity-title">Order #ORD-2023-055 shipped</h4>
                        <p class="activity-time">5 hours ago</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <button class="toggle-sidebar" id="toggleSidebar">
        <i class="fas fa-bars"></i>
    </button>

    <script>
        // Toggle sidebar on mobile
        document.getElementById('toggleSidebar').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>
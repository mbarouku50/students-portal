<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session before any output
session_start();

include("../connection.php");

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get admin details
$admin_id = $_SESSION['admin_id'];
$query = "SELECT * FROM admin WHERE admin_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin_result = $stmt->get_result();
$admin_data = $admin_result->fetch_assoc();

// Get statistics
$users_count = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$courses_count = $conn->query("SELECT COUNT(*) as total FROM courses")->fetch_assoc()['total'];
$stationery_count = $conn->query("SELECT COUNT(*) as total FROM stationery")->fetch_assoc()['total'];

// Count all documents from all tables
$document_tables = [
    'first_year_sem1_documents', 
    'first_year_sem2_documents',
    'second_year_sem1_documents', 
    'second_year_sem2_documents',
    'third_year_sem1_documents', 
    'third_year_sem2_documents',
    'fourth_year_sem1_documents', 
    'fourth_year_sem2_documents'
];

$documents_count = 0;
foreach ($document_tables as $table) {
    $result = $conn->query("SELECT COUNT(*) as total FROM $table");
    if ($result) {
        $documents_count += $result->fetch_assoc()['total'];
    }
}

// Count PDF files
$pdf_count = 0;
foreach ($document_tables as $table) {
    $result = $conn->query("SELECT COUNT(*) as total FROM $table WHERE file_type = 'pdf'");
    if ($result) {
        $pdf_count += $result->fetch_assoc()['total'];
    }
}

// Count Word files
$word_count = 0;
foreach ($document_tables as $table) {
    $result = $conn->query("SELECT COUNT(*) as total FROM $table WHERE file_type = 'doc' OR file_type = 'docx'");
    if ($result) {
        $word_count += $result->fetch_assoc()['total'];
    }
}

// Get recent activities - using a more generic approach to avoid column name issues
$activities = [];

// First get recent user registrations
$user_activities = $conn->query("SELECT fullname as title, created_at as date FROM users ORDER BY created_at DESC LIMIT 3");
if ($user_activities) {
    while ($row = $user_activities->fetch_assoc()) {
        $row['type'] = 'user';
        $activities[] = $row;
    }
}

// Then get recent document uploads from the first table as example
$doc_activities = $conn->query("SELECT file_name as title, uploaded_at as date FROM first_year_sem1_documents ORDER BY uploaded_at DESC LIMIT 2");
if ($doc_activities) {
    while ($row = $doc_activities->fetch_assoc()) {
        $row['type'] = 'document';
        $activities[] = $row;
    }
}

// Sort activities by date
usort($activities, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

// Take only the 5 most recent
$activities = array_slice($activities, 0, 5);
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
            gap: 15px;
        }
        
        .user-menu .user-info {
            text-align: right;
        }
        
        .user-menu .user-name {
            font-weight: 500;
        }
        
        .user-menu .user-role {
            font-size: 0.8rem;
            color: #666;
        }
        
        .user-menu .avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid var(--secondary-color);
        }
        
        .user-menu .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }
        
        @media (max-width: 768px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            
            .quick-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .top-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .user-menu {
                flex-direction: row-reverse;
                align-self: flex-end;
            }
        }
        
        @media (max-width: 576px) {
            .quick-stats {
                grid-template-columns: 1fr;
            }
            
            .user-menu {
                flex-direction: column;
                align-items: flex-end;
            }
            
            .user-info {
                text-align: right;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
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
                    <div class="user-name"><?php echo htmlspecialchars($admin_data['fullname'] ?? 'Administrator'); ?></div>
                    <div class="user-role">Administrator</div>
                </div>
                <div class="avatar">
                    <?php if (!empty($admin_data['profile_picture'])): ?>
                        <img src="../admin/uploads/profiles/<?php echo htmlspecialchars($record['profile_picture']); ?>" alt="Profile Picture">
                    <?php else: ?>
                        <div style="width:100%; height:100%; background:#3498db; color:white; display:flex; align-items:center; justify-content:center;">
                            <?php echo strtoupper(substr($admin_data['fullname'] ?? 'A', 0, 1)); ?>
                        </div>
                    <?php endif; ?>
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
                <div class="card-value"><?php echo $users_count; ?></div>
                <div class="card-title">Registered Users</div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Documents</h3>
                    <div class="card-icon documents">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
                <div class="card-value"><?php echo $documents_count; ?></div>
                <div class="card-title">Uploaded Documents</div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Courses</h3>
                    <div class="card-icon courses">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
                <div class="card-value"><?php echo $courses_count; ?></div>
                <div class="card-title">Active Courses</div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Stationery</h3>
                    <div class="card-icon storage">
                        <i class="fas fa-pencil-alt"></i>
                    </div>
                </div>
                <div class="card-value"><?php echo $stationery_count; ?></div>
                <div class="card-title">Stationery Items</div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="section-title">
            <h2>Quick Statistics</h2>
        </div>
        <div class="quick-stats">
            <div class="stat-card">
                <i class="fas fa-user-graduate" style="color: #3498db; font-size: 1.5rem;"></i>
                <div class="stat-value"><?php echo $users_count; ?></div>
                <div class="stat-label">Students</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-pencil-alt" style="color: #e74c3c; font-size: 1.5rem;"></i>
                <div class="stat-value"><?php echo $stationery_count; ?></div>
                <div class="stat-label">Stationery Items</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-file-pdf" style="color: #2ecc71; font-size: 1.5rem;"></i>
                <div class="stat-value"><?php echo $pdf_count; ?></div>
                <div class="stat-label">PDF Files</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-file-word" style="color: #9b59b6; font-size: 1.5rem;"></i>
                <div class="stat-value"><?php echo $word_count; ?></div>
                <div class="stat-label">Word Docs</div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="section-title">
            <h2>Recent Activity</h2>
            <a href="activities.php" style="font-size: 0.9rem; color: var(--secondary-color);">View All</a>
        </div>
        <div class="recent-activity">
            <ul class="activity-list">
                <?php if (!empty($activities)): ?>
                    <?php foreach ($activities as $activity): ?>
                        <li class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-<?php 
                                    echo $activity['type'] == 'user' ? 'user-plus' : 
                                         ($activity['type'] == 'document' ? 'file-upload' : 'bell');
                                ?>"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-text">
                                    <?php 
                                    if ($activity['type'] == 'user') {
                                        echo "New user registered: " . htmlspecialchars($activity['title']);
                                    } else if ($activity['type'] == 'document') {
                                        echo "New document uploaded: " . htmlspecialchars($activity['title']);
                                    } else {
                                        echo htmlspecialchars($activity['title']);
                                    }
                                    ?>
                                </div>
                                <div class="activity-time">
                                    <?php 
                                    $date = new DateTime($activity['date']);
                                    echo $date->format('M j, Y \a\t g:i A');
                                    ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="activity-item">
                        <div class="activity-content">
                            <div class="activity-text">No recent activities found.</div>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </main>

    <?php $conn->close(); ?>
</body>
</html>
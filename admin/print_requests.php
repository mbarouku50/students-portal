<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Start session before any output
session_name('admin_session');
session_start();

include("../connection.php");

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

// Get counts for stats cards
$total_requests = $conn->query("SELECT COUNT(*) as count FROM print_jobs")->fetch_assoc()['count'];
$pending_requests = $conn->query("SELECT COUNT(*) as count FROM print_jobs WHERE status = 'pending'")->fetch_assoc()['count'];
$completed_requests = $conn->query("SELECT COUNT(*) as count FROM print_jobs WHERE status = 'completed'")->fetch_assoc()['count'];

// Get today's requests
$today = date('Y-m-d');
$todays_requests = $conn->query("SELECT COUNT(*) as count FROM print_jobs WHERE DATE(created_at) = '$today'")->fetch_assoc()['count'];

// Get stationery performance data
$stationery_performance = $conn->query("
    SELECT s.name, s.location, 
           COUNT(pj.job_id) as total_jobs,
           SUM(CASE WHEN pj.status = 'pending' THEN 1 ELSE 0 END) as pending,
           SUM(CASE WHEN pj.status = 'completed' THEN 1 ELSE 0 END) as completed,
           ROUND((SUM(CASE WHEN pj.status = 'completed' THEN 1 ELSE 0 END) / COUNT(pj.job_id)) * 100) as completion_rate
    FROM stationery s
    LEFT JOIN print_jobs pj ON s.stationery_id = pj.stationery_id
    GROUP BY s.stationery_id
");

// Get pending print requests
$pending_print_requests = $conn->query("
    SELECT pj.*, s.name as stationery_name 
    FROM print_jobs pj 
    JOIN stationery s ON pj.stationery_id = s.stationery_id 
    WHERE pj.status IN ('pending', 'processing')
    ORDER BY pj.created_at DESC
");

// Get completed print requests
$completed_print_requests = $conn->query("
    SELECT pj.*, s.name as stationery_name 
    FROM print_jobs pj 
    JOIN stationery s ON pj.stationery_id = s.stationery_id 
    WHERE pj.status = 'completed'
    ORDER BY pj.created_at DESC
    LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Requests Management | CBE Doc's Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Your CSS styles remain the same as provided */
        :root {
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --success: #10b981;
            --success-hover: #059669;
            --warning: #f59e0b;
            --danger: #ef4444;
            --text-main: #1e293b;
            --text-light: #64748b;
            --bg-light: #f8fafc;
            --border-color: #e2e8f0;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            --card-shadow-hover: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-main);
            line-height: 1.5;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
            transition: all 0.3s ease;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-main);
        }
        
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-light);
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        
        .breadcrumb a {
            color: var(--primary);
            text-decoration: none;
        }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            box-shadow: var(--card-shadow-hover);
            transform: translateY(-2px);
        }
        
        .stat-card .title {
            font-size: 0.875rem;
            color: var(--text-light);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .stat-card .value {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .stat-card .trend {
            display: flex;
            align-items: center;
            font-size: 0.75rem;
            color: var(--success);
        }
        
        .trend.down {
            color: var(--danger);
        }
        
        .data-table-container {
            background: white;
            border-radius: 0.75rem;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .table-title {
            font-weight: 600;
            color: var(--text-main);
            font-size: 1.125rem;
        }
        
        .table-actions {
            display: flex;
            gap: 0.75rem;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box input {
            padding: 0.625rem 1rem 0.625rem 2.5rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 0.875rem;
            width: 240px;
            transition: all 0.2s ease;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        
        .search-box i {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 0.875rem;
        }
        
        .filter-btn, .export-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1rem;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-main);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .filter-btn:hover, .export-btn:hover {
            background: var(--bg-light);
            border-color: var(--primary);
            color: var(--primary);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background-color: #f1f5f9;
        }
        
        th {
            padding: 1rem 1.5rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        td {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.875rem;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .status-pending {
            background-color: #fffbeb;
            color: #f59e0b;
            border: 1px solid #fcd34d;
        }
        
        .status-completed {
            background-color: #ecfdf5;
            color: #10b981;
            border: 1px solid #6ee7b7;
        }
        
        .status-processing {
            background-color: #eff6ff;
            color: #3b82f6;
            border: 1px solid #93c5fd;
        }
        
        .status-cancelled {
            background-color: #fef2f2;
            color: #ef4444;
            border: 1px solid #fca5a5;
        }
        
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .btn-success {
            background-color: var(--success);
            color: white;
        }
        
        .btn-success:hover {
            background-color: var(--success-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .btn-outline {
            background: white;
            border: 1px solid var(--border-color);
            color: var(--text-main);
        }
        
        .btn-outline:hover {
            background: var(--bg-light);
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .btn-group {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            border-top: 1px solid var(--border-color);
        }
        
        .pagination-info {
            font-size: 0.875rem;
            color: var(--text-light);
        }
        
        .pagination-controls {
            display: flex;
            gap: 0.5rem;
        }
        
        .page-btn {
            padding: 0.5rem 0.875rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }
        
        .page-btn:hover {
            background: var(--bg-light);
            border-color: var(--primary);
            color: var(--primary);
        }
        
        .page-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .page-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .document-preview {
            width: 100%;
            height: 70vh;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: white;
            border-radius: 0.75rem;
            width: 80%;
            max-width: 900px;
            max-height: 90vh;
            overflow: auto;
            padding: 2rem;
            position: relative;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .modal-title {
            font-size: 1.375rem;
            font-weight: 600;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-light);
            transition: color 0.2s ease;
        }
        
        .close-modal:hover {
            color: var(--text-main);
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--border-color);
        }
        
        .modal.show {
            display: flex;
        }
        
        .stationery-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .stationery-logo {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            font-size: 1.25rem;
        }
        
        .stationery-details {
            line-height: 1.4;
        }
        
        .stationery-name {
            font-weight: 500;
        }
        
        .stationery-meta {
            font-size: 0.75rem;
            color: var(--text-light);
        }
        
        .progress-container {
            margin-top: 1rem;
        }
        
        .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .progress-title {
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .progress-value {
            font-size: 0.875rem;
            color: var(--text-light);
        }
        
        .progress-bar {
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 4px;
        }
        
        .progress-pending {
            background: #f59e0b;
            width: 30%;
        }
        
        .progress-completed {
            background: #10b981;
            width: 70%;
        }
        
        /* Responsive styles */
        @media (max-width: 1200px) {
            .stats-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
                padding: 1rem 0.5rem;
            }
            
            .logo-text, .nav-text {
                display: none;
            }
            
            .logo {
                justify-content: center;
                padding: 1rem 0;
            }
            
            .nav-item {
                justify-content: center;
                padding: 0.875rem;
            }
            
            .main-content {
                margin-left: 80px;
                padding: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .stats-cards {
                grid-template-columns: 1fr;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .table-actions {
                width: 100%;
                justify-content: space-between;
            }
            
            .search-box input {
                width: 100%;
            }
            
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
        
        @media (max-width: 576px) {
            .container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding: 1rem;
            }
            
            .logo {
                justify-content: flex-start;
            }
            
            .logo-text, .nav-text {
                display: block;
            }
            
            .nav-items {
                display: flex;
                overflow-x: auto;
                gap: 0.5rem;
                padding-bottom: 0.5rem;
            }
            
            .nav-item {
                padding: 0.5rem 0.75rem;
                white-space: nowrap;
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .pagination {
                flex-direction: column;
                gap: 1rem;
            }
            
            .modal-content {
                width: 95%;
                padding: 1.5rem;
            }
        }
        
        .user-info {
            line-height: 1.4;
        }
        
        .user-name {
            font-weight: 500;
        }
        
        .user-meta {
            font-size: 0.75rem;
            color: var(--text-light);
        }
        
        .request-details {
            font-size: 0.875rem;
        }
        
        .request-details div {
            margin-bottom: 0.25rem;
        }
        
        .document-preview {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc;
        }
        
        .document-preview iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        
        .no-preview {
            text-align: center;
            color: var(--text-light);
        }
        
        .no-preview i {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <?php include('sidebar.php'); ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Print Requests Management</h1>
                    <div class="breadcrumb">
                        <a href="#">Dashboard</a>
                        <span>/</span>
                        <span>Print Requests</span>
                    </div>
                </div>
                <div class="actions">
                    <button class="action-btn btn-primary" onclick="location.href='new_request.php'">
                        <i class="fas fa-plus"></i> New Request
                    </button>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="title">
                        <i class="fas fa-print"></i>
                        Total Requests
                    </div>
                    <div class="value"><?php echo $total_requests; ?></div>
                    <div class="trend">
                        <i class="fas fa-arrow-up"></i> All time requests
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="title">
                        <i class="fas fa-clock"></i>
                        Pending
                    </div>
                    <div class="value"><?php echo $pending_requests; ?></div>
                    <div class="trend">
                        <i class="fas fa-info-circle"></i> Awaiting processing
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="title">
                        <i class="fas fa-check-circle"></i>
                        Completed
                    </div>
                    <div class="value"><?php echo $completed_requests; ?></div>
                    <div class="trend">
                        <i class="fas fa-check"></i> Successfully processed
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="title">
                        <i class="fas fa-calendar-day"></i>
                        Today's Requests
                    </div>
                    <div class="value"><?php echo $todays_requests; ?></div>
                    <div class="trend">
                        <i class="fas fa-calendar"></i> Requests today
                    </div>
                </div>
            </div>
            
            <!-- Stationery Performance -->
            <div class="data-table-container">
                <div class="table-header">
                    <h3 class="table-title">Stationery Performance Overview</h3>
                    <div class="table-actions">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search stationeries...">
                        </div>
                        <button class="filter-btn">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Stationery</th>
                            <th>Total Jobs</th>
                            <th>Pending</th>
                            <th>Completed</th>
                            <th>Completion Rate</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $stationery_performance->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div class="stationery-info">
                                    <div class="stationery-logo">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div class="stationery-details">
                                        <div class="stationery-name"><?php echo $row['name']; ?></div>
                                        <div class="stationery-meta"><?php echo $row['location']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo $row['total_jobs']; ?></td>
                            <td><?php echo $row['pending']; ?></td>
                            <td><?php echo $row['completed']; ?></td>
                            <td>
                                <div class="progress-container">
                                    <div class="progress-header">
                                        <div class="progress-title">Progress</div>
                                        <div class="progress-value"><?php echo $row['completion_rate']; ?>%</div>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill progress-completed" style="width: <?php echo $row['completion_rate']; ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if ($row['completion_rate'] > 80): ?>
                                <span class="status-badge status-completed"><i class="fas fa-check-circle"></i> Active</span>
                                <?php elseif ($row['completion_rate'] > 50): ?>
                                <span class="status-badge status-processing"><i class="fas fa-sync-alt"></i> Busy</span>
                                <?php else: ?>
                                <span class="status-badge status-pending"><i class="fas fa-exclamation-circle"></i> Maintenance</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pending Print Requests -->
            <div class="data-table-container">
                <div class="table-header">
                    <h3 class="table-title">Pending Print Requests</h3>
                    <div class="table-actions">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="pending-search" placeholder="Search pending requests...">
                        </div>
                        <button class="filter-btn">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Customer</th>
                            <th>Stationery</th>
                            <th>Document</th>
                            <th>Details</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $pending_print_requests->fetch_assoc()): ?>
                        <tr>
                            <td>#PR-<?php echo $row['job_id']; ?></td>
                            <td>
                                <div class="user-info">
                                    <div class="user-name"><?php echo $row['user_name']; ?></div>
                                    <div class="user-meta">Phone: <?php echo $row['phone_number']; ?></div>
                                </div>
                            </td>
                            <td><?php echo $row['stationery_name']; ?></td>
                            <td>
                                <?php 
                                $file_path = $row['file_path'];
                                $file_name = $file_path ? basename($file_path) : 'No file uploaded';
                                echo $file_name;
                                ?>
                            </td>
                            <td>
                                <div class="request-details">
                                    <div><strong>Copies:</strong> <?php echo $row['copies']; ?></div>
                                    <div><strong>Type:</strong> <?php echo ucfirst($row['print_type']); ?></div>
                                    <?php if (!empty($row['special_instructions'])): ?>
                                    <div><strong>Notes:</strong> <?php echo $row['special_instructions']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?php echo date('M j, Y g:i A', strtotime($row['created_at'])); ?></td>
                            <td>
                                <?php 
                                $status_class = '';
                                if ($row['status'] == 'pending') $status_class = 'status-pending';
                                if ($row['status'] == 'processing') $status_class = 'status-processing';
                                if ($row['status'] == 'completed') $status_class = 'status-completed';
                                if ($row['status'] == 'cancelled') $status_class = 'status-cancelled';
                                ?>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <i class="fas 
                                        <?php 
                                        if ($row['status'] == 'pending') echo 'fa-clock';
                                        if ($row['status'] == 'processing') echo 'fa-sync-alt';
                                        if ($row['status'] == 'completed') echo 'fa-check-circle';
                                        if ($row['status'] == 'cancelled') echo 'fa-times-circle';
                                        ?>
                                    "></i> 
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="action-btn btn-primary view-document" 
                                        data-file="<?php echo $row['file_path']; ?>" 
                                        data-filename="<?php echo $file_name; ?>">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <?php if ($row['status'] != 'completed'): ?>
                                    <form action="update_status.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="job_id" value="<?php echo $row['job_id']; ?>">
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="action-btn btn-success">
                                            <i class="fas fa-check"></i> Complete
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <div class="pagination">
                    <div class="pagination-info">Showing <?php echo $pending_print_requests->num_rows; ?> entries</div>
                    <div class="pagination-controls">
                        <button class="page-btn disabled">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="page-btn active">1</button>
                        <button class="page-btn">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Completed Print Requests -->
            <div class="data-table-container">
                <div class="table-header">
                    <h3 class="table-title">Completed Print Requests</h3>
                    <div class="table-actions">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="completed-search" placeholder="Search completed requests...">
                        </div>
                        <button class="filter-btn">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <button class="export-btn">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Customer</th>
                            <th>Stationery</th>
                            <th>Document</th>
                            <th>Details</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $completed_print_requests->fetch_assoc()): ?>
                        <tr>
                            <td>#PR-<?php echo $row['job_id']; ?></td>
                            <td>
                                <div class="user-info">
                                    <div class="user-name"><?php echo $row['user_name']; ?></div>
                                    <div class="user-meta">Phone: <?php echo $row['phone_number']; ?></div>
                                </div>
                            </td>
                            <td><?php echo $row['stationery_name']; ?></td>
                            <td>
                                <?php 
                                $file_path = $row['file_path'];
                                $file_name = $file_path ? basename($file_path) : 'No file uploaded';
                                echo $file_name;
                                ?>
                            </td>
                            <td>
                                <div class="request-details">
                                    <div><strong>Copies:</strong> <?php echo $row['copies']; ?></div>
                                    <div><strong>Type:</strong> <?php echo ucfirst($row['print_type']); ?></div>
                                </div>
                            </td>
                            <td><?php echo date('M j, Y g:i A', strtotime($row['created_at'])); ?></td>
                            <td><span class="status-badge status-completed"><i class="fas fa-check-circle"></i> Completed</span></td>
                            <td>
                                <div class="btn-group">
                                    <button class="action-btn btn-outline view-document" 
                                        data-file="<?php echo $row['file_path']; ?>" 
                                        data-filename="<?php echo $file_name; ?>">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <form action="reprint.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="job_id" value="<?php echo $row['job_id']; ?>">
                                        <button type="submit" class="action-btn btn-outline">
                                            <i class="fas fa-redo"></i> Re-print
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <div class="pagination">
                    <div class="pagination-info">Showing <?php echo $completed_print_requests->num_rows; ?> of <?php echo $completed_requests; ?> entries</div>
                    <div class="pagination-controls">
                        <button class="page-btn disabled">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="page-btn active">1</button>
                        <button class="page-btn">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Preview Modal -->
    <div id="documentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Document Preview</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="document-preview">
                <div class="no-preview">
                    <i class="fas fa-file-alt"></i>
                    <p>Preview not available for this document</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="action-btn btn-outline close-modal-btn">Close</button>
                <button class="action-btn btn-success">
                    <i class="fas fa-check"></i> Mark as Completed
                </button>
                <button class="action-btn btn-primary">
                    <i class="fas fa-print"></i> Print Document
                </button>
            </div>
        </div>
    </div>

    <script>
        // Document preview modal functionality
        const modal = document.getElementById('documentModal');
        const viewButtons = document.querySelectorAll('.action-btn.btn-primary');
        const closeModalButtons = document.querySelectorAll('.close-modal');
        const modalDocName = document.getElementById('modalDocName');
        const documentPreview = document.getElementById('documentPreview');

        // View document button click handler
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                // In a real application, this would get the actual document name and path
                const row = this.closest('tr');
                const docName = row.querySelector('td:nth-child(4)').textContent;
                
                modalDocName.textContent = docName;
                // For demo purposes, we're showing a PDF placeholder
                documentPreview.src = 'https://docs.google.com/gview?url=https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf&embedded=true';
                modal.classList.add('show');
            });
        });

        // Close modal button click handler
        closeModalButtons.forEach(button => {
            button.addEventListener('click', function() {
                modal.classList.remove('show');
                documentPreview.src = '';
            });
        });

        // Close modal when clicking outside the content
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('show');
                documentPreview.src = '';
            }
        });

        // Simple client-side search functionality
        document.getElementById('pending-search').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.data-table-container:nth-child(4) tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        document.getElementById('completed-search').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.data-table-container:nth-child(5) tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Complete button functionality
        const completeButtons = document.querySelectorAll('.action-btn.btn-success');
        completeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const requestId = row.querySelector('td:first-child').textContent;
                
                // Show confirmation (in a real app, this would submit a form)
                if (confirm(`Mark ${requestId} as completed?`)) {
                    // Change status to completed
                    const statusCell = row.querySelector('.status-badge');
                    statusCell.className = 'status-badge status-completed';
                    statusCell.innerHTML = '<i class="fas fa-check-circle"></i> Completed';
                    
                    // Change button to outline
                    this.className = 'action-btn btn-outline';
                    this.innerHTML = '<i class="fas fa-redo"></i> Re-print';
                    
                    // Show notification
                    alert(`${requestId} has been marked as completed.`);
                }
            });
        });
    </script>
</body>
</html>
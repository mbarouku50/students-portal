<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include("../connection.php");
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
        :root {
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --success: #10b981;
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
        
        .main-content {
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
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            box-shadow: var(--card-shadow-hover);
        }
        
        .stat-card .title {
            font-size: 0.875rem;
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }
        
        .stat-card .value {
            font-size: 1.5rem;
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
            border-radius: 0.5rem;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .table-title {
            font-weight: 600;
            color: var(--text-main);
        }
        
        .table-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box input {
            padding: 0.5rem 1rem 0.5rem 2rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            font-size: 0.875rem;
            width: 200px;
        }
        
        .search-box i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 0.875rem;
        }
        
        .filter-btn, .export-btn {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.5rem 0.75rem;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            font-size: 0.875rem;
            color: var(--text-main);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .filter-btn:hover, .export-btn:hover {
            background: var(--bg-light);
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
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.875rem;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-completed {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-processing {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
        }
        
        .btn-outline {
            background: white;
            border: 1px solid var(--border-color);
            color: var(--text-main);
        }
        
        .btn-outline:hover {
            background: var(--bg-light);
        }
        
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
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
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .page-btn:hover {
            background: var(--bg-light);
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
        
        /* Responsive styles */
        @media (max-width: 1200px) {
            .stats-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 992px) {
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
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .pagination {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">Print Requests Management</h1>
            <div class="actions">
                <button class="action-btn btn-primary">
                    <i class="fas fa-plus"></i> New Request
                </button>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="title">Total Requests</div>
                <div class="value">
                    <?php
                    $total_query = "SELECT COUNT(*) as total FROM print_submissions";
                    $total_result = $conn->query($total_query);
                    echo $total_result->fetch_assoc()['total'];
                    ?>
                </div>
                <div class="trend">
                    <i class="fas fa-arrow-up"></i> 12% from last week
                </div>
            </div>
            
            <div class="stat-card">
                <div class="title">Pending</div>
                <div class="value">
                    <?php
                    $pending_query = "SELECT COUNT(*) as total FROM print_submissions WHERE status = 'pending'";
                    $pending_result = $conn->query($pending_query);
                    echo $pending_result->fetch_assoc()['total'];
                    ?>
                </div>
                <div class="trend">
                    <i class="fas fa-arrow-up"></i> 8% from last week
                </div>
            </div>
            
            <div class="stat-card">
                <div class="title">Completed</div>
                <div class="value">
                    <?php
                    $completed_query = "SELECT COUNT(*) as total FROM print_submissions WHERE status = 'completed'";
                    $completed_result = $conn->query($completed_query);
                    echo $completed_result->fetch_assoc()['total'];
                    ?>
                </div>
                <div class="trend down">
                    <i class="fas fa-arrow-down"></i> 3% from last week
                </div>
            </div>
            
            <div class="stat-card">
                <div class="title">This Month</div>
                <div class="value">
                    <?php
                    $month_query = "SELECT COUNT(*) as total FROM print_submissions 
                                  WHERE MONTH(submission_date) = MONTH(CURRENT_DATE()) 
                                  AND YEAR(submission_date) = YEAR(CURRENT_DATE())";
                    $month_result = $conn->query($month_query);
                    echo $month_result->fetch_assoc()['total'];
                    ?>
                </div>
                <div class="trend">
                    <i class="fas fa-arrow-up"></i> 15% from last month
                </div>
            </div>
        </div>
        
        <!-- Data Table -->
        <div class="data-table-container">
            <div class="table-header">
                <h3 class="table-title">Recent Print Requests</h3>
                <div class="table-actions">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search requests...">
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
                        <th>User</th>
                        <th>Document</th>
                        <th>Details</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM print_submissions ORDER BY submission_date DESC LIMIT 10";
                    $result = $conn->query($query);
                    
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>#' . htmlspecialchars($row['id']) . '</td>';
                            echo '<td>
                                    <div class="user-info">
                                        <div class="user-name">' . htmlspecialchars($row['name']) . '</div>
                                        <div class="user-meta">ID: ' . htmlspecialchars($row['user_id']) . '</div>
                                    </div>
                                  </td>';
                            echo '<td>' . htmlspecialchars($row['document_name']) . '</td>';
                            echo '<td>
                                    <div class="request-details">
                                        <div><strong>Copies:</strong> ' . htmlspecialchars($row['copies']) . '</div>
                                        <div><strong>Type:</strong> ' . htmlspecialchars($row['color']) . '</div>
                                    </div>
                                  </td>';
                            echo '<td>' . date('M d, Y', strtotime($row['submission_date'])) . '</td>';
                            
                            // Status badge with different colors
                            $status_class = '';
                            switch(strtolower($row['status'])) {
                                case 'pending':
                                    $status_class = 'status-pending';
                                    break;
                                case 'completed':
                                    $status_class = 'status-completed';
                                    break;
                                case 'processing':
                                    $status_class = 'status-processing';
                                    break;
                                case 'cancelled':
                                    $status_class = 'status-cancelled';
                                    break;
                                default:
                                    $status_class = 'status-pending';
                            }
                            echo '<td><span class="status-badge ' . $status_class . '">' . htmlspecialchars($row['status']) . '</span></td>';
                            
                            echo '<td>
                                    <div class="action-buttons">
                                        <a href="' . htmlspecialchars($row['file_path']) . '" target="_blank" class="action-btn btn-primary">
                                            <i class="fas fa-print"></i> Print
                                        </a>
                                        <button class="action-btn btn-outline">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                    </div>
                                  </td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-light);">No print requests found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
            
            <div class="pagination">
                <div class="pagination-info">Showing 1 to 10 of 124 entries</div>
                <div class="pagination-controls">
                    <button class="page-btn disabled">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="page-btn active">1</button>
                    <button class="page-btn">2</button>
                    <button class="page-btn">3</button>
                    <button class="page-btn">4</button>
                    <button class="page-btn">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>
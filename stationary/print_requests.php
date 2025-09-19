<?php
session_name('admin_session');
session_start();

// Check if stationary admin is logged in
if (!isset($_SESSION['stationary_admin_id'])) {
    header('Location: ../admin_login.php');
    exit();
}
include("../connection.php");

$stationary_id = $_SESSION['stationary_admin_id'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $job_id = $_POST['job_id'];
    
    if ($_POST['action'] === 'update_status') {
        $status = $_POST['status'];
        
        $update_query = "UPDATE print_jobs SET status = ?, updated_at = NOW() WHERE job_id = ? AND stationery_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("sii", $status, $job_id, $stationary_id);
        
        if ($update_stmt->execute()) {
            $_SESSION['success_message'] = "Job status updated successfully!";
        } else {
            $_SESSION['error_message'] = "Error updating job status: " . $conn->error;
        }
        
        header("Location: print_requests.php");
        exit();
    }
    
    // Handle job deletion
    if ($_POST['action'] === 'delete_job') {
        $delete_query = "DELETE FROM print_jobs WHERE job_id = ? AND stationery_id = ? AND status = 'cancelled'";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("ii", $job_id, $stationary_id);
        
        if ($delete_stmt->execute()) {
            $_SESSION['success_message'] = "Job deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Error deleting job: " . $conn->error;
        }
        
        header("Location: print_requests.php");
        exit();
    }
}

// Fetch print jobs for this stationary
$query = "SELECT * FROM print_jobs WHERE stationery_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $stationary_id);
$stmt->execute();
$result = $stmt->get_result();
$print_jobs = $result->fetch_all(MYSQLI_ASSOC);

// Separate jobs by status
$active_jobs = array_filter($print_jobs, function($job) {
    return $job['status'] !== 'completed' && $job['status'] !== 'cancelled';
});

$completed_jobs = array_filter($print_jobs, function($job) {
    return $job['status'] === 'completed';
});

$cancelled_jobs = array_filter($print_jobs, function($job) {
    return $job['status'] === 'cancelled';
});

// Status colors for badges
$status_colors = [
    'pending' => 'warning',
    'processing' => 'info',
    'completed' => 'success',
    'cancelled' => 'danger'
];

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
    <title>Print Requests - Stationary Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .main-content {
            margin-left: 280px;
            padding: 10px;
        }

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
            color: var(--dark)
        }

        .header-title p {
            color: var(--gray);
            margin-bottom: 0;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-profile img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-info h4 {
            margin-bottom: 2px;
            font-size: 16px;
        }

        .user-info p {
            margin-bottom: 0;
            font-size: 13px;
            color: var(--gray);
        }

        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
            border-radius: 0.75rem 0.75rem 0 0 !important;
        }

        .card-title {
            font-weight: 600;
            margin-bottom: 0;
        }

        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
            font-size: 0.85em;
        }

        .badge-warning {
            background-color: rgba(248, 150, 30, 0.1);
            color: #f8961e;
        }

        .badge-info {
            background-color: rgba(76, 201, 240, 0.1);
            color: #4cc9f0;
        }

        .badge-success {
            background-color: rgba(67, 160, 71, 0.1);
            color: #43a047;
        }

        .badge-danger {
            background-color: rgba(247, 37, 133, 0.1);
            color: #f72585;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: none;
            font-weight: 500;
            color: var(--gray);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .table td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
        }

        .job-details {
            background-color: var(--white);
            padding: 1.5rem;
            border-radius: 0 0 0.75rem 0.75rem;
        }

        .job-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .meta-item {
            flex: 1 0 200px;
        }

        .meta-label {
            font-size: 0.75rem;
            color: var(--gray);
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }

        .meta-value {
            font-weight: 500;
        }

        .file-preview {
            margin-top: 1.5rem;
            border: 1px dashed #ddd;
            border-radius: 0.5rem;
            padding: 1rem;
            text-align: center;
        }

        .file-preview img {
            max-width: 100%;
            max-height: 300px;
            margin-bottom: 1rem;
        }

        .action-buttons {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .btn-sm {
            padding: 0.35rem 0.75rem;
            font-size: 0.85rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--gray);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #dee2e6;
        }

        .empty-state h4 {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            margin-bottom: 1.5rem;
        }

        .status-select {
            width: auto;
            display: inline-block;
            margin-left: 0.5rem;
        }

        .nav-tabs {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .nav-tabs .nav-link {
            border: none;
            padding: 0.75rem 1.25rem;
            color: var(--gray);
            font-weight: 500;
            border-bottom: 3px solid transparent;
        }

        .nav-tabs .nav-link.active {
            color: var(--primary);
            background: transparent;
            border-color: var(--primary);
        }

        .stats-card {
            text-align: center;
            padding: 20px;
            border-radius: 12px;
            background: white;
            box-shadow: var(--card-shadow);
        }

        .stats-card i {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .stats-card h3 {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stats-card p {
            color: var(--gray);
            margin-bottom: 0;
        }

        .stats-pending { border-top: 4px solid #f8961e; }
        .stats-processing { border-top: 4px solid #4cc9f0; }
        .stats-completed { border-top: 4px solid #43a047; }
        .stats-cancelled { border-top: 4px solid #f72585; }

        .stats-pending i { color: #f8961e; }
        .stats-processing i { color: #4cc9f0; }
        .stats-completed i { color: #43a047; }
        .stats-cancelled i { color: #f72585; }

        .job-table-container {
            max-height: 500px;
            overflow-y: auto;
        }
        
        .search-container {
            position: relative;
            margin-bottom: 20px;
        }
        
        .search-input {
            padding-left: 40px;
            border-radius: 25px;
        }
        
        .search-icon {
            position: absolute;
            left: 15px;
            top: 10px;
            color: var(--gray);
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .highlight {
            background-color: rgba(67, 97, 238, 0.1);
        }
        
        .action-cell {
            min-width: 200px;
        }
        
        /* Toast styling */
        .toast-container {
            z-index: 9999;
        }
        
        /* Enhanced document viewer styles */
        .document-viewer {
            width: 100%;
            min-height: 500px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .document-actions {
            display: flex;
            gap: 10px;
            margin-bottom: 1rem;
        }
        
        .document-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .file-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #6c757d;
        }
        
        .pdf-container, .office-container {
            width: 100%;
            height: 500px;
            border: none;
        }
        
        .image-preview {
            max-width: 100%;
            max-height: 500px;
            display: block;
            margin: 0 auto;
        }
        
        .text-preview {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            white-space: pre-wrap;
            font-family: monospace;
            max-height: 500px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <div class="header-title">
                <h1>Print Requests</h1>
                <p>Manage all print jobs for your stationary</p>
            </div>
            
            <div class="user-profile">
                <img src="<?= htmlspecialchars($shop_data['logo']) ?>" alt="Shop Logo" style="border-radius: 50%; width: 50px; height: 50px;">
                <div class="user-info">
                    <h4><?= htmlspecialchars($_SESSION['stationary_admin_name'] ?? 'Admin') ?></h4>
                    <p>Stationary Admin</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stats-card stats-pending">
                    <i class="fas fa-print"></i>
                    <h3><?= count($active_jobs) ?></h3>
                    <p>Active Print Jobs</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card stats-completed">
                    <i class="fas fa-check-circle"></i>
                    <h3><?= count($completed_jobs) ?></h3>
                    <p>Completed Jobs</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card stats-cancelled">
                    <i class="fas fa-times-circle"></i>
                    <h3><?= count($cancelled_jobs) ?></h3>
                    <p>Cancelled Jobs</p>
                </div>
            </div>
        </div>

        <!-- Tabs for different job statuses -->
        <ul class="nav nav-tabs" id="jobTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab">
                    Active Jobs <span class="badge bg-primary"><?= count($active_jobs) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab">
                    Completed Jobs <span class="badge bg-success"><?= count($completed_jobs) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled" type="button" role="tab">
                    Cancelled Jobs <span class="badge bg-danger"><?= count($cancelled_jobs) ?></span>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="jobTabsContent">
            <!-- Active Jobs Tab -->
            <div class="tab-pane fade show active" id="active" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Active Print Jobs</h5>
                        <div>
                            <span class="badge bg-primary">
                                <i class="fas fa-print me-1"></i>
                                <?= count($active_jobs) ?> Jobs
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="activeSearch" class="form-control search-input" placeholder="Search active jobs...">
                        </div>

                        <?php if (count($active_jobs) > 0): ?>
                            <div class="table-responsive job-table-container">
                                <table class="table table-hover" id="activeTable">
                                    <thead>
                                        <tr>
                                            <th>Job ID</th>
                                            <th>Customer</th>
                                            <th>Phone</th>
                                            <th>Type</th>
                                            <th>Copies</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th class="action-cell">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($active_jobs as $job): ?>
                                            <tr>
                                                <td class="searchable">#<?= htmlspecialchars($job['job_id']) ?></td>
                                                <td class="searchable"><?= htmlspecialchars($job['user_name']) ?></td>
                                                <td class="searchable"><?= htmlspecialchars($job['phone_number']) ?></td>
                                                <td class="searchable">
                                                    <?= ucfirst(htmlspecialchars($job['print_type'])) ?>
                                                    <?php if ($job['copies'] > 1): ?>
                                                        <span class="badge bg-light text-dark"><?= $job['copies'] ?> copies</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="searchable"><?= htmlspecialchars($job['copies']) ?></td>
                                                <td class="searchable"><?= date('M d, Y h:i A', strtotime($job['created_at'])) ?></td>
                                                <td class="searchable">
                                                    <span class="badge badge-<?= $status_colors[$job['status']] ?>">
                                                        <?= ucfirst(htmlspecialchars($job['status'])) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary view-details" data-jobid="<?= $job['job_id'] ?>">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success update-status" data-jobid="<?= $job['job_id'] ?>" data-status="completed">
                                                        <i class="fas fa-check"></i> Complete
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger update-status" data-jobid="<?= $job['job_id'] ?>" data-status="cancelled">
                                                        <i class="fas fa-times"></i> Cancel
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-print"></i>
                                <h4>No Active Print Jobs</h4>
                                <p>There are currently no active print requests for your stationary.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Completed Jobs Tab -->
            <div class="tab-pane fade" id="completed" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Completed Jobs</h5>
                        <span class="badge bg-success"><?= count($completed_jobs) ?> Jobs</span>
                    </div>
                    
                    <div class="card-body">
                        <div class="search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="completedSearch" class="form-control search-input" placeholder="Search completed jobs...">
                        </div>

                        <?php if (count($completed_jobs) > 0): ?>
                            <div class="table-responsive job-table-container">
                                <table class="table table-hover" id="completedTable">
                                    <thead>
                                        <tr>
                                            <th>Job ID</th>
                                            <th>Customer</th>
                                            <th>Phone</th>
                                            <th>Type</th>
                                            <th>Copies</th>
                                            <th>Completed Date</th>
                                            <th class="action-cell">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($completed_jobs as $job): ?>
                                            <tr>
                                                <td class="searchable">#<?= htmlspecialchars($job['job_id']) ?></td>
                                                <td class="searchable"><?= htmlspecialchars($job['user_name']) ?></td>
                                                <td class="searchable"><?= htmlspecialchars($job['phone_number']) ?></td>
                                                <td class="searchable">
                                                    <?= ucfirst(htmlspecialchars($job['print_type'])) ?>
                                                    <?php if ($job['copies'] > 1): ?>
                                                        <span class="badge bg-light text-dark"><?= $job['copies'] ?> copies</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="searchable"><?= htmlspecialchars($job['copies']) ?></td>
                                                <td class="searchable"><?= date('M d, Y h:i A', strtotime($job['updated_at'] ?? $job['created_at'])) ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary view-details" data-jobid="<?= $job['job_id'] ?>">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-check-circle"></i>
                                <h4>No Completed Jobs</h4>
                                <p>No print jobs have been completed yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Cancelled Jobs Tab -->
            <div class="tab-pane fade" id="cancelled" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Cancelled Jobs</h5>
                        <span class="badge bg-danger"><?= count($cancelled_jobs) ?> Jobs</span>
                    </div>
                    
                    <div class="card-body">
                        <div class="search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="cancelledSearch" class="form-control search-input" placeholder="Search cancelled jobs...">
                        </div>

                        <?php if (count($cancelled_jobs) > 0): ?>
                            <div class="table-responsive job-table-container">
                                <table class="table table-hover" id="cancelledTable">
                                    <thead>
                                        <tr>
                                            <th>Job ID</th>
                                            <th>Customer</th>
                                            <th>Phone</th>
                                            <th>Type</th>
                                            <th>Copies</th>
                                            <th>Cancelled Date</th>
                                            <th class="action-cell">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cancelled_jobs as $job): ?>
                                            <tr>
                                                <td class="searchable">#<?= htmlspecialchars($job['job_id']) ?></td>
                                                <td class="searchable"><?= htmlspecialchars($job['user_name']) ?></td>
                                                <td class="searchable"><?= htmlspecialchars($job['phone_number']) ?></td>
                                                <td class="searchable">
                                                    <?= ucfirst(htmlspecialchars($job['print_type'])) ?>
                                                    <?php if ($job['copies'] > 1): ?>
                                                        <span class="badge bg-light text-dark"><?= $job['copies'] ?> copies</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="searchable"><?= htmlspecialchars($job['copies']) ?></td>
                                                <td class="searchable"><?= date('M d, Y h:i A', strtotime($job['updated_at'] ?? $job['created_at'])) ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary view-details" data-jobid="<?= $job['job_id'] ?>">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-times-circle"></i>
                                <h4>No Cancelled Jobs</h4>
                                <p>No print jobs have been cancelled.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Job Details Modal -->
    <div class="modal fade" id="jobDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Print Job Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="jobDetailsContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Store original content for each table
        const activeTableContent = $('#activeTable tbody').html();
        const completedTableContent = $('#completedTable tbody').html();
        const cancelledTableContent = $('#cancelledTable tbody').html();
        
        // Search functionality for each table
        $('#activeSearch').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            
            if (value.length === 0) {
                // Restore original content if search is empty
                $('#activeTable tbody').html(activeTableContent);
                return;
            }
            
            $('#activeTable tbody tr').each(function() {
                const $row = $(this);
                let found = false;
                
                // Check only searchable cells (excluding action cells)
                $row.find('.searchable').each(function() {
                    const text = $(this).text().toLowerCase();
                    if (text.indexOf(value) > -1) {
                        found = true;
                        
                        // Highlight the matching text
                        const regex = new RegExp(value, 'gi');
                        const originalText = $(this).text();
                        $(this).html(originalText.replace(regex, function(match) {
                            return '<span class="highlight">' + match + '</span>';
                        }));
                    }
                });
                
                // Show or hide the row based on search results
                $row.toggle(found);
            });
        });
        
        $('#completedSearch').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            
            if (value.length === 0) {
                // Restore original content if search is empty
                $('#completedTable tbody').html(completedTableContent);
                return;
            }
            
            $('#completedTable tbody tr').each(function() {
                const $row = $(this);
                let found = false;
                
                // Check only searchable cells (excluding action cells)
                $row.find('.searchable').each(function() {
                    const text = $(this).text().toLowerCase();
                    if (text.indexOf(value) > -1) {
                        found = true;
                        
                        // Highlight the matching text
                        const regex = new RegExp(value, 'gi');
                        const originalText = $(this).text();
                        $(this).html(originalText.replace(regex, function(match) {
                            return '<span class="highlight">' + match + '</span>';
                        }));
                    }
                });
                
                // Show or hide the row based on search results
                $row.toggle(found);
            });
        });
        
        $('#cancelledSearch').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            
            if (value.length === 0) {
                // Restore original content if search is empty
                $('#cancelledTable tbody').html(cancelledTableContent);
                return;
            }
            
            $('#cancelledTable tbody tr').each(function() {
                const $row = $(this);
                let found = false;
                
                // Check only searchable cells (excluding action cells)
                $row.find('.searchable').each(function() {
                    const text = $(this).text().toLowerCase();
                    if (text.indexOf(value) > -1) {
                        found = true;
                        
                        // Highlight the matching text
                        const regex = new RegExp(value, 'gi');
                        const originalText = $(this).text();
                        $(this).html(originalText.replace(regex, function(match) {
                            return '<span class="highlight">' + match + '</span>';
                        }));
                    }
                });
                
                // Show or hide the row based on search results
                $row.toggle(found);
            });
        });
        
        // View job details
        $(document).on('click', '.view-details', function() {
            const jobId = $(this).data('jobid');
            
            // Show loading state
            $('#jobDetailsContent').html(`
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading job details...</p>
                </div>
            `);
            
            const modal = new bootstrap.Modal(document.getElementById('jobDetailsModal'));
            modal.show();
            
            $.ajax({
                url: 'get_job_details.php',
                type: 'GET',
                data: { job_id: jobId },
                success: function(response) {
                    $('#jobDetailsContent').html(response);
                    
                    // Initialize print functionality
                    $('.print-job').click(function() {
                        const fileUrl = $(this).data('file-url');
                        const fileType = $(this).data('file-type');
                        const jobId = $(this).data('job-id');
                        
                        if (fileType === 'pdf') {
                            // For PDFs
                            const pdfWindow = window.open(fileUrl, '_blank');
                            pdfWindow.onload = function() {
                                pdfWindow.print();
                            };
                        }
                        else if (['doc', 'docx', 'xls', 'xlsx'].includes(fileType)) {
                            // For Office docs - use Google Docs viewer
                            const printWindow = window.open(`https://docs.google.com/viewer?url=${encodeURIComponent(window.location.origin + '/' + fileUrl)}&embedded=true&rm=minimal`, '_blank');
                            setTimeout(() => {
                                printWindow.print();
                            }, 3000);
                        }
                        else {
                            // For images and text
                            const printWindow = window.open(fileUrl, '_blank');
                            printWindow.onload = function() {
                                printWindow.print();
                            };
                        }
                        
                        // Update status to completed
                        $.post('update_job_status.php', {
                            job_id: jobId,
                            status: 'completed'
                        }, function() {
                            // Reload page after status update
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        });
                    });
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    $('#jobDetailsContent').html(`
                        <div class="alert alert-danger">
                            <h5>Error Loading Details</h5>
                            <p>Could not load job details. Please try again.</p>
                            <p class="small text-muted">Error: ${xhr.status} ${xhr.statusText}</p>
                        </div>
                    `);
                }
            });
        });
        
        // Update job status
        $(document).on('click', '.update-status', function() {
            const jobId = $(this).data('jobid');
            const status = $(this).data('status');
            const button = $(this);
            
            // Show loading state on button
            button.html('<i class="fas fa-spinner fa-spin"></i> Updating...');
            button.prop('disabled', true);
            
            $.post('update_job_status.php', {
                job_id: jobId,
                status: status
            }, function(response) {
                if (response.success) {
                    // Show success message
                    const toast = $(`
                        <div class="toast align-items-center text-white bg-success" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="d-flex">
                                <div class="toast-body">
                                    Job status updated successfully
                                </div>
                                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                        </div>
                    `);
                    
                    $('.toast-container').append(toast);
                    const bsToast = new bootstrap.Toast(toast);
                    bsToast.show();
                    
                    // Reload page after a short delay
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    alert('Error updating job status: ' + response.message);
                    button.html('<i class="fas fa-check"></i> Complete');
                    button.prop('disabled', false);
                }
            }).fail(function() {
                alert('Error updating job status. Please try again.');
                button.html('<i class="fas fa-check"></i> Complete');
                button.prop('disabled', false);
            });
        });
        
        // Create toast container if it doesn't exist
        if ($('.toast-container').length === 0) {
            $('body').append('<div class="toast-container position-fixed bottom-0 end-0 p-3"></div>');
        }
    });
    </script>
</body>
</html>
<?php
session_start();
include("../connection.php");

// Check if stationary admin is logged in
if (!isset($_SESSION['stationary_admin_id'])) {
    header('Location: ../admin_login.php');
    exit();
}

$stationary_id = $_SESSION['stationary_admin_id'];

// Fetch print jobs for this stationary
$query = "SELECT * FROM print_jobs WHERE stationery_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $stationary_id);
$stmt->execute();
$result = $stmt->get_result();
$print_jobs = $result->fetch_all(MYSQLI_ASSOC);

// Status colors for badges
$status_colors = [
    'pending' => 'warning',
    'processing' => 'info',
    'completed' => 'success',
    'cancelled' => 'danger'
];
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

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fb;
            color: var(--dark);
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
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['stationary_admin_name'] ?? 'Admin') ?>&background=4361ee&color=fff" alt="User">
                <div class="user-info">
                    <h4><?= htmlspecialchars($_SESSION['stationary_admin_name'] ?? 'Admin') ?></h4>
                    <p>Stationary Admin</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">All Print Jobs</h5>
                <div>
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-print me-1"></i>
                        <?= count($print_jobs) ?> Jobs
                    </span>
                </div>
            </div>

            <?php if (count($print_jobs) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Job ID</th>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Type</th>
                                <th>Copies</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($print_jobs as $job): ?>
                                <tr>
                                    <td>#<?= htmlspecialchars($job['job_id']) ?></td>
                                    <td><?= htmlspecialchars($job['user_name']) ?></td>
                                    <td><?= htmlspecialchars($job['phone_number']) ?></td>
                                    <td>
                                        <?= ucfirst(htmlspecialchars($job['print_type'])) ?>
                                        <?php if ($job['copies'] > 1): ?>
                                            <span class="badge bg-light text-dark"><?= $job['copies'] ?> copies</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($job['copies']) ?></td>
                                    <td><?= date('M d, Y h:i A', strtotime($job['created_at'])) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $status_colors[$job['status']] ?>">
                                            <?= ucfirst(htmlspecialchars($job['status'])) ?>
                                        </span>
                                    </td>
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
                    <i class="fas fa-print"></i>
                    <h4>No Print Jobs Found</h4>
                    <p>There are currently no print requests for your stationary.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Job Details Modal -->
    <div class="modal fade" id="jobDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
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
    // View job details
    $('.view-details').click(function() {
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
});
</script>
</body>
</html>
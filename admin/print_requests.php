<?php

include("../connection.php");

// Handle print status update
if (isset($_POST['mark_printed'])) {
    $request_id = $_POST['request_id'];
    $update_query = "UPDATE print_submissions SET status = 'completed', submission_date = NOW() WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $stmt->close();
    
    // Redirect to avoid form resubmission
    header("Location: print_requests.php");
    exit();
}
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
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            min-width: 120px;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .btn-success {
            background-color: var(--success);
            color: white;
        }
        
        .btn-success:hover {
            background-color: var(--success-hover);
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .btn-outline {
            background: white;
            border: 1px solid var(--border-color);
            color: var(--text-main);
        }
        
        .btn-outline:hover {
            background: var(--bg-light);
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .btn-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
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
            border-radius: 0.5rem;
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
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-light);
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }
        .modal.show {
            display: flex;
        }
        
        .document-preview {
            width: 100%;
            height: 70vh;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
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
    <?php include 'sidebar.php'; ?>
     <div class="main-content">
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
            <div class="title">Today's Requests</div>
            <div class="value">
                <?php
                $today_query = "SELECT COUNT(*) as total FROM print_submissions 
                              WHERE DATE(submission_date) = CURDATE()";
                $today_result = $conn->query($today_query);
                echo $today_result->fetch_assoc()['total'];
                ?>
            </div>
            <div class="trend">
                <i class="fas fa-arrow-up"></i> 15% from yesterday
            </div>
        </div>
    </div>
    
    <!-- Pending Print Requests -->
    <div class="data-table-container" style="margin-bottom: 2rem;">
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
        
        <table id="pending-requests-table">
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>User</th>
                    <th>Document</th>
                    <th>Details</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
            $pending_query = "SELECT * FROM print_submissions WHERE status = 'pending' ORDER BY submission_date DESC";
            $pending_result = $conn->query($pending_query);
            
            if ($pending_result && $pending_result->num_rows > 0) {
                while ($row = $pending_result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>#' . htmlspecialchars($row['id'] ?? '') . '</td>';
                    echo '<td>
                            <div class="user-info">
                                <div class="user-name">' . htmlspecialchars($row['name'] ?? 'Unknown') . '</div>
                                <div class="user-meta">ID: ' . htmlspecialchars($row['user_id'] ?? 'N/A') . '</div>
                                <div class="user-meta">Phone: ' . htmlspecialchars($row['phone'] ?? 'N/A') . '</div>
                            </div>
                          </td>';
                    echo '<td>' . htmlspecialchars($row['document_name'] ?? 'Untitled') . '</td>';
                    echo '<td>
                            <div class="request-details">
                                <div><strong>Copies:</strong> ' . htmlspecialchars($row['copies'] ?? '1') . '</div>
                                <div><strong>Type:</strong> ' . htmlspecialchars($row['color'] ?? 'Black & White') . '</div>
                                <div><strong>Station:</strong> ' . htmlspecialchars($row['station'] ?? 'N/A') . '</div>
                                ' . (!empty($row['notes']) ? '<div><strong>Notes:</strong> ' . htmlspecialchars($row['notes']) . '</div>' : '') . '
                            </div>
                          </td>';
                    echo '<td>' . date('M d, Y h:i A', strtotime($row['submission_date'] ?? 'now')) . '</td>';
                    echo '<td><span class="status-badge status-pending">Pending</span></td>';
                    
                    echo '<td>
                            <div class="btn-group">
                                <button class="action-btn btn-primary view-document" 
                                        data-id="' . ($row['id'] ?? '') . '" 
                                        data-document="' . htmlspecialchars($row['document_name'] ?? '') . '"
                                        data-path="' . htmlspecialchars($row['file_path'] ?? '') . '">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="request_id" value="' . ($row['id'] ?? '') . '">
                                    <button type="submit" name="mark_printed" class="action-btn btn-success">
                                        <i class="fas fa-check"></i> Complete
                                    </button>
                                </form>
                            </div>
                          </td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-light);">No pending print requests found.</td></tr>';
            }
            ?>
            </tbody>
        </table>
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
        
        <table id="completed-requests-table">
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>User</th>
                    <th>Document</th>
                    <th>Details</th>
                    <th>Submitted</th>
                    <th>Completed</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
            $completed_query = "SELECT * FROM print_submissions WHERE status = 'completed' ORDER BY submission_date DESC LIMIT 10";
            $completed_result = $conn->query($completed_query);
            
            if ($completed_result && $completed_result->num_rows > 0) {
                while ($row = $completed_result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>#' . htmlspecialchars($row['id'] ?? '') . '</td>';
                    echo '<td>
                            <div class="user-info">
                                <div class="user-name">' . htmlspecialchars($row['name'] ?? 'Unknown') . '</div>
                                <div class="user-meta">ID: ' . htmlspecialchars($row['user_id'] ?? 'N/A') . '</div>
                            </div>
                          </td>';
                    echo '<td>' . htmlspecialchars($row['document_name'] ?? 'Untitled') . '</td>';
                    echo '<td>
                            <div class="request-details">
                                <div><strong>Copies:</strong> ' . htmlspecialchars($row['copies'] ?? '1') . '</div>
                                <div><strong>Type:</strong> ' . htmlspecialchars($row['color'] ?? 'Black & White') . '</div>
                                <div><strong>Station:</strong> ' . htmlspecialchars($row['station'] ?? 'N/A') . '</div>
                            </div>
                          </td>';
                    echo '<td>' . date('M d, Y h:i A', strtotime($row['submission_date'] ?? 'now')) . '</td>';
                    echo '<td><span class="status-badge status-completed">Completed</span></td>';
                    
                    echo '<td>
                            <div class="action-buttons">
                                <a href="' . htmlspecialchars($row['file_path'] ?? '#') . '" target="_blank" class="action-btn btn-outline">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="#" class="action-btn btn-outline">
                                    <i class="fas fa-redo"></i> Re-print
                                </a>
                            </div>
                          </td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-light);">No completed print requests found.</td></tr>';
            }
            ?>
            </tbody>
        </table>

        <!-- Document Preview Modal -->
    <div id="documentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Document Preview - <span id="modalDocName"></span></h3>
                <button class="close-modal">&times;</button>
            </div>
            <iframe id="documentPreview" class="document-preview" frameborder="0"></iframe>
            <div class="modal-footer">
                <button class="action-btn btn-outline close-modal">Close</button>
                <form method="POST" id="completeForm">
                    <input type="hidden" name="request_id" id="modalRequestId">
                    <button type="submit" name="mark_printed" class="action-btn btn-success">
                        <i class="fas fa-check"></i> Mark as Completed
                    </button>
                </form>
                <button class="action-btn btn-primary" id="printDocument">
                    <i class="fas fa-print"></i> Print Document
                </button>
            </div>
        </div>
    </div>
</div>
        
        <div class="pagination">
            <div class="pagination-info">Showing 1 to 10 of <?php 
                $count_query = "SELECT COUNT(*) as total FROM print_submissions WHERE status = 'completed'";
                $count_result = $conn->query($count_query);
                echo $count_result->fetch_assoc()['total'];
            ?> entries</div>
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
</div>

<script>
        // Simple client-side search functionality
        document.getElementById('pending-search').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#pending-requests-table tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        document.getElementById('completed-search').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#completed-requests-table tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Document preview modal functionality
        const modal = document.getElementById('documentModal');
        const viewButtons = document.querySelectorAll('.view-document');
        const closeModalButtons = document.querySelectorAll('.close-modal');
        const modalDocName = document.getElementById('modalDocName');
        const documentPreview = document.getElementById('documentPreview');
        const modalRequestId = document.getElementById('modalRequestId');
        const printButton = document.getElementById('printDocument');
        const completeForm = document.getElementById('completeForm');

        // View document button click handler
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const docName = this.getAttribute('data-document');
                const docPath = this.getAttribute('data-path');
                const requestId = this.getAttribute('data-id');
                
                modalDocName.textContent = docName;
                documentPreview.src = docPath;
                modalRequestId.value = requestId;
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

        // Print button click handler
        printButton.addEventListener('click', function() {
            if (documentPreview.src && documentPreview.src !== 'about:blank') {
                documentPreview.contentWindow.print();
            }
        });

        // Close modal when clicking outside the content
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('show');
                documentPreview.src = '';
            }
        });

        // Handle form submission for the modal's complete button
        completeForm.addEventListener('submit', function(e) {
            // The form will submit normally as it's a POST form
        });

        // Ensure regular complete buttons work
        document.querySelectorAll('form[method="POST"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                // The form will submit normally
            });
        });
        </script>

<?php include 'footer.php'; ?>
<?php $conn->close(); ?>
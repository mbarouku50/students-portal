<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Requests Management | CBE Doc's Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Your existing CSS styles here */
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
        
        .sidebar {
            width: 280px;
            background: white;
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .logo-icon {
            width: 36px;
            height: 36px;
            background: var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
        }
        
        .logo-text {
            font-weight: 700;
            font-size: 1.25rem;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .nav-item:hover, .nav-item.active {
            background-color: #eff6ff;
            color: var(--primary);
        }
        
        .nav-item.active {
            font-weight: 500;
        }
        
        .nav-item i {
            width: 20px;
            text-align: center;
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
                    <button class="action-btn btn-primary">
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
                    <div class="value">247</div>
                    <div class="trend">
                        <i class="fas fa-arrow-up"></i> 12% from last week
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="title">
                        <i class="fas fa-clock"></i>
                        Pending
                    </div>
                    <div class="value">42</div>
                    <div class="trend">
                        <i class="fas fa-arrow-up"></i> 8% from last week
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="title">
                        <i class="fas fa-check-circle"></i>
                        Completed
                    </div>
                    <div class="value">189</div>
                    <div class="trend down">
                        <i class="fas fa-arrow-down"></i> 3% from last week
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="title">
                        <i class="fas fa-calendar-day"></i>
                        Today's Requests
                    </div>
                    <div class="value">16</div>
                    <div class="trend">
                        <i class="fas fa-arrow-up"></i> 15% from yesterday
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
                        <tr>
                            <td>
                                <div class="stationery-info">
                                    <div class="stationery-logo">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div class="stationery-details">
                                        <div class="stationery-name">Alpha Printers</div>
                                        <div class="stationery-meta">Main Branch</div>
                                    </div>
                                </div>
                            </td>
                            <td>87</td>
                            <td>12</td>
                            <td>75</td>
                            <td>
                                <div class="progress-container">
                                    <div class="progress-header">
                                        <div class="progress-title">Progress</div>
                                        <div class="progress-value">86%</div>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill progress-completed" style="width: 86%"></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="status-badge status-completed"><i class="fas fa-check-circle"></i> Active</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="stationery-info">
                                    <div class="stationery-logo">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div class="stationery-details">
                                        <div class="stationery-name">Beta Copies</div>
                                        <div class="stationery-meta">City Center</div>
                                    </div>
                                </div>
                            </td>
                            <td>64</td>
                            <td>18</td>
                            <td>46</td>
                            <td>
                                <div class="progress-container">
                                    <div class="progress-header">
                                        <div class="progress-title">Progress</div>
                                        <div class="progress-value">72%</div>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill progress-completed" style="width: 72%"></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="status-badge status-processing"><i class="fas fa-sync-alt"></i> Busy</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="stationery-info">
                                    <div class="stationery-logo">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div class="stationery-details">
                                        <div class="stationery-name">Gamma Press</div>
                                        <div class="stationery-meta">West Branch</div>
                                    </div>
                                </div>
                            </td>
                            <td>52</td>
                            <td>8</td>
                            <td>44</td>
                            <td>
                                <div class="progress-container">
                                    <div class="progress-header">
                                        <div class="progress-title">Progress</div>
                                        <div class="progress-value">85%</div>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill progress-completed" style="width: 85%"></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="status-badge status-completed"><i class="fas fa-check-circle"></i> Active</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="stationery-info">
                                    <div class="stationery-logo">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div class="stationery-details">
                                        <div class="stationery-name">Delta Documents</div>
                                        <div class="stationery-meta">North Branch</div>
                                    </div>
                                </div>
                            </td>
                            <td>44</td>
                            <td>4</td>
                            <td>40</td>
                            <td>
                                <div class="progress-container">
                                    <div class="progress-header">
                                        <div class="progress-title">Progress</div>
                                        <div class="progress-value">91%</div>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill progress-completed" style="width: 91%"></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="status-badge status-pending"><i class="fas fa-exclamation-circle"></i> Maintenance</span></td>
                        </tr>
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
                        <tr>
                            <td>#PR-1028</td>
                            <td>
                                <div class="user-info">
                                    <div class="user-name">Michael Johnson</div>
                                    <div class="user-meta">ID: CBE2874</div>
                                    <div class="user-meta">Phone: +251 912 345 678</div>
                                </div>
                            </td>
                            <td>Alpha Printers</td>
                            <td>Business Proposal.pdf</td>
                            <td>
                                <div class="request-details">
                                    <div><strong>Copies:</strong> 3</div>
                                    <div><strong>Type:</strong> Color</div>
                                    <div><strong>Pages:</strong> 24</div>
                                    <div><strong>Notes:</strong> Spiral binding</div>
                                </div>
                            </td>
                            <td>Oct 15, 2023 10:30 AM</td>
                            <td><span class="status-badge status-pending"><i class="fas fa-clock"></i> Pending</span></td>
                            <td>
                                <div class="btn-group">
                                    <button class="action-btn btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="action-btn btn-success">
                                        <i class="fas fa-check"></i> Complete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#PR-1027</td>
                            <td>
                                <div class="user-info">
                                    <div class="user-name">Sarah Williams</div>
                                    <div class="user-meta">ID: CBE1983</div>
                                    <div class="user-meta">Phone: +251 911 987 654</div>
                                </div>
                            </td>
                            <td>Beta Copies</td>
                            <td>Research Paper.docx</td>
                            <td>
                                <div class="request-details">
                                    <div><strong>Copies:</strong> 2</div>
                                    <div><strong>Type:</strong> B&W</div>
                                    <div><strong>Pages:</strong> 32</div>
                                    <div><strong>Notes:</strong> Double-sided</div>
                                </div>
                            </td>
                            <td>Oct 15, 2023 9:15 AM</td>
                            <td><span class="status-badge status-processing"><i class="fas fa-sync-alt"></i> Processing</span></td>
                            <td>
                                <div class="btn-group">
                                    <button class="action-btn btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="action-btn btn-success">
                                        <i class="fas fa-check"></i> Complete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#PR-1026</td>
                            <td>
                                <div class="user-info">
                                    <div class="user-name">Robert Kim</div>
                                    <div class="user-meta">ID: CBE3462</div>
                                    <div class="user-meta">Phone: +251 913 456 789</div>
                                </div>
                            </td>
                            <td>Gamma Press</td>
                            <td>Marketing Materials.pptx</td>
                            <td>
                                <div class="request-details">
                                    <div><strong>Copies:</strong> 50</div>
                                    <div><strong>Type:</strong> Color</div>
                                    <div><strong>Pages:</strong> 12</div>
                                    <div><strong>Notes:</strong> High gloss finish</div>
                                </div>
                            </td>
                            <td>Oct 14, 2023 3:45 PM</td>
                            <td><span class="status-badge status-pending"><i class="fas fa-clock"></i> Pending</span></td>
                            <td>
                                <div class="btn-group">
                                    <button class="action-btn btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="action-btn btn-success">
                                        <i class="fas fa-check"></i> Complete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="pagination">
                    <div class="pagination-info">Showing 1 to 3 of 42 entries</div>
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
                            <th>Completed</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#PR-1025</td>
                            <td>
                                <div class="user-info">
                                    <div class="user-name">Emily Chen</div>
                                    <div class="user-meta">ID: CBE5632</div>
                                </div>
                            </td>
                            <td>Delta Documents</td>
                            <td>Training Manual.pdf</td>
                            <td>
                                <div class="request-details">
                                    <div><strong>Copies:</strong> 10</div>
                                    <div><strong>Type:</strong> B&W</div>
                                    <div><strong>Pages:</strong> 56</div>
                                </div>
                            </td>
                            <td>Oct 14, 2023 11:20 AM</td>
                            <td>Oct 14, 2023 2:45 PM</td>
                            <td><span class="status-badge status-completed"><i class="fas fa-check-circle"></i> Completed</span></td>
                            <td>
                                <div class="btn-group">
                                    <button class="action-btn btn-outline">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="action-btn btn-outline">
                                        <i class="fas fa-redo"></i> Re-print
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#PR-1024</td>
                            <td>
                                <div class="user-info">
                                    <div class="user-name">David Miller</div>
                                    <div class="user-meta">ID: CBE4287</div>
                                </div>
                            </td>
                            <td>Alpha Printers</td>
                            <td>Financial Report.xlsx</td>
                            <td>
                                <div class="request-details">
                                    <div><strong>Copies:</strong> 5</div>
                                    <div><strong>Type:</strong> Color</div>
                                    <div><strong>Pages:</strong> 18</div>
                                </div>
                            </td>
                            <td>Oct 13, 2023 4:30 PM</td>
                            <td>Oct 14, 2023 9:15 AM</td>
                            <td><span class="status-badge status-completed"><i class="fas fa-check-circle"></i> Completed</span></td>
                            <td>
                                <div class="btn-group">
                                    <button class="action-btn btn-outline">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="action-btn btn-outline">
                                        <i class="fas fa-redo"></i> Re-print
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#PR-1023</td>
                            <td>
                                <div class="user-info">
                                    <div class="user-name">Lisa Anderson</div>
                                    <div class="user-meta">ID: CBE8741</div>
                                </div>
                            </td>
                            <td>Beta Copies</td>
                            <td>Event Flyer.psd</td>
                            <td>
                                <div class="request-details">
                                    <div><strong>Copies:</strong> 200</div>
                                    <div><strong>Type:</strong> Color</div>
                                    <div><strong>Pages:</strong> 1</div>
                                </div>
                            </td>
                            <td>Oct 13, 2023 2:15 PM</td>
                            <td>Oct 13, 2023 5:30 PM</td>
                            <td><span class="status-badge status-completed"><i class="fas fa-check-circle"></i> Completed</span></td>
                            <td>
                                <div class="btn-group">
                                    <button class="action-btn btn-outline">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="action-btn btn-outline">
                                        <i class="fas fa-redo"></i> Re-print
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="pagination">
                    <div class="pagination-info">Showing 1 to 3 of 189 entries</div>
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
    </div>

    <!-- Document Preview Modal -->
    <div id="documentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Document Preview - <span id="modalDocName">Business Proposal.pdf</span></h3>
                <button class="close-modal">&times;</button>
            </div>
            <iframe id="documentPreview" class="document-preview" frameborder="0"></iframe>
            <div class="modal-footer">
                <button class="action-btn btn-outline close-modal">Close</button>
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
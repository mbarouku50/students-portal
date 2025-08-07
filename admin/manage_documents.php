
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cover Page Documents | CBE Doc's Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --error-color: #e74c3c;
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
        
        /* Form Styles */
        .form-container {
            background-color: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        
        .form-title {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            outline: none;
        }
        
        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            font-size: 1rem;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background-color: var(--error-color);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-success {
            background-color: var(--success-color);
            color: white;
        }
        
        .btn-success:hover {
            background-color: #27ae60;
        }
        
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        /* Alert Messages */
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.1);
            border-left: 4px solid var(--success-color);
            color: var(--success-color);
        }
        
        .alert-error {
            background-color: rgba(231, 76, 60, 0.1);
            border-left: 4px solid var(--error-color);
            color: var(--error-color);
        }
        
        /* Table Styles */
        .table-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            background-color: var(--light-color);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .table td {
            padding: 1rem;
            border-top: 1px solid #eee;
            vertical-align: middle;
        }
        
        .table tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        .badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-primary {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--secondary-color);
        }
        
        .badge-success {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success-color);
        }
        
        .badge-warning {
            background-color: rgba(243, 156, 18, 0.1);
            color: var(--warning-color);
        }
        
        .file-icon {
            font-size: 1.5rem;
            margin-right: 0.75rem;
            color: var(--secondary-color);
        }
        
        .file-info {
            display: flex;
            align-items: center;
        }
        
        .file-name {
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        
        .file-meta {
            font-size: 0.875rem;
            color: #666;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        /* Responsive Styles */
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
            }
            
            .main-content {
                margin-left: 80px;
            }
        }
        
        @media (max-width: 768px) {
            .table {
                display: block;
                overflow-x: auto;
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
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <?php include('sidebar.php'); ?>

    <!-- Main Content Area -->
    <main class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="page-title">
                <h1><i class="fas fa-file-alt"></i> Manage Cover Page Documents</h1>
            </div>
            <div class="user-menu">
                <div class="user-info">
                    <div class="user-name"></div>
                    <div class="user-role">Administrator</div>
                </div>
                <div class="avatar">
                    
                </div>
            </div>
        </div>

        <!-- Upload Form -->
        <div class="form-container">
            <h2 class="form-title"><i class="fas fa-upload"></i> Upload New Cover Page</h2>
            
            <form action="manage_documents.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="course" class="form-label">Course</label>
                    <select name="course" id="course" class="form-control form-select" required>
                        <option value="">-- Select Course --</option>
                        <option value="Information Technology">Information Technology</option>
                        <option value="Accounting">Accounting</option>
                        <option value="Business Administration">Business Administration</option>
                        <option value="Metrology">Metrology</option>
                        <option value="Human Resource">Human Resource</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="document_type" class="form-label">Document Type</label>
                    <select name="document_type" id="document_type" class="form-control form-select" required>
                        <option value="">-- Select Type --</option>
                        <option value="Individual Assignment">Individual Assignment</option>
                        <option value="Group Project">Group Project</option>
                        <option value="Thesis">Thesis/Dissertation</option>
                        <option value="Research Report">Research Report</option>
                        <option value="General">General Template</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="year" class="form-label">Year</label>
                    <input type="number" name="year" id="year" class="form-control" min="2000" max="<?php echo date('Y') + 1; ?>" required>
                </div>
                
                <div cl ass="form-group">
                    <label for="description" class="form-label">Description (Optional)</label>
                    <textarea name="description" id="description" class="form-control" rows="3" placeholder="Brief description of this cover page template"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="document_file" class="form-label">Document File</label>
                    <input type="file" name="document_file" id="document_file" class="form-control" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg" required>
                    <small class="text-muted">Accepted formats: PDF, DOC, DOCX, PNG, JPG (Max 5MB)</small>
                </div>
                
                <button type="submit" name="upload_document" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload Document
                </button>
            </form>
        </div>

        <!-- Documents Table -->
        <div class="table-container">
            <h2 class="form-title"><i class="fas fa-file-alt"></i> Existing Cover Pages</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Document</th>
                            <th>Course</th>
                            <th>Type</th>
                            <th>Year</th>
                            <th>Uploaded By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                       
                    </tbody>
                </table>
        </div>
    </main>
</body>
</html>
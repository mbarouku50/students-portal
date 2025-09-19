<?php
// Start session before any output
session_name('admin_session');
session_start();

include("../connection.php");

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get document type from URL if specified
$doc_type = isset($_GET['type']) ? $_GET['type'] : '';
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
$semester = isset($_GET['semester']) ? $_GET['semester'] : '';
$level = isset($_GET['level']) ? $_GET['level'] : '';

// Valid document types
$valid_types = [
    'lecture_notes' => ['icon' => 'fa-file-alt', 'name' => 'Lecture Notes'],
    'study_guides' => ['icon' => 'fa-book', 'name' => 'Study Guides'],
    'assignments' => ['icon' => 'fa-tasks', 'name' => 'Assignments'],
    'past_exams' => ['icon' => 'fa-question-circle', 'name' => 'Past Exams'],
    'case_studies' => ['icon' => 'fa-chart-bar', 'name' => 'Case Studies'],
    'projects' => ['icon' => 'fa-project-diagram', 'name' => 'Projects'],
    'field' => ['icon' => 'fa-map-marker-alt', 'name' => 'Field Reports'],
    'cover_pages' => ['icon' => 'fa-file-image', 'name' => 'Cover Pages']
];

// Get all courses for filter
$courses = [];
$courses_result = $conn->query("SELECT course_id, course_code, course_name FROM courses ORDER BY course_code");
if ($courses_result && $courses_result->num_rows > 0) {
    while ($row = $courses_result->fetch_assoc()) {
        $courses[$row['course_id']] = $row;
    }
}

// Build the query to fetch documents
$where_conditions = [];
$query_params = [];

if (!empty($doc_type) && array_key_exists($doc_type, $valid_types)) {
    $where_conditions[] = "doc_type = ?";
    $query_params[] = $doc_type;
}

if ($course_id > 0) {
    $where_conditions[] = "course_id = ?";
    $query_params[] = $course_id;
}

// Get documents from all semester/level tables
$all_documents = [];
$semester_tables = [];
$levels = ['certificate', 'diploma1', 'diploma2', 'bachelor1', 'bachelor2', 'bachelor3'];
$semesters = ['1', '2'];
foreach ($semesters as $semester_val) {
    foreach ($levels as $level_val) {
        $table_name = 'sem' . $semester_val . '_' . $level_val . '_documents';
        $semester_tables[] = $table_name;
    }
}

// Check each table and fetch documents
foreach ($semester_tables as $table) {
    // Check if table exists
    $table_check = $conn->query("SHOW TABLES LIKE '$table'");
    if ($table_check->num_rows > 0) {
        // Build query for this table
        $query = "SELECT d.*, '$table' as source_table, c.course_code, c.course_name FROM $table d LEFT JOIN courses c ON d.course_id = c.course_id";
        $table_where = $where_conditions;
        $table_params = $query_params;
        if (!empty($semester)) {
            $table_where[] = "d.semester = ?";
            $table_params[] = $semester;
        }
        if (!empty($level)) {
            $table_where[] = "d.level = ?";
            $table_params[] = $level;
        }
        if (!empty($table_where)) {
            $query .= " WHERE " . implode(" AND ", $table_where);
        }
        $query .= " ORDER BY d.uploaded_at DESC";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            if (!empty($table_params)) {
                $types = str_repeat('s', count($table_params));
                $stmt->bind_param($types, ...$table_params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Extract semester and level from table name
                    preg_match('/sem(\d)_(\w+)_documents/', $table, $matches);
                    if (count($matches) === 3) {
                        $row['semester'] = $matches[1];
                        $row['level'] = $matches[2];
                    }
                    $all_documents[] = $row;
                }
            }
            $stmt->close();
        }
    }
}

// Handle document deletion
if (isset($_POST['delete_document'])) {
    $doc_id = intval($_POST['doc_id']);
    $source_table = $_POST['source_table'];
    $file_path = $_POST['file_path'];
    
    // Delete from database
    $delete_sql = "DELETE FROM $source_table WHERE doc_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $doc_id);
    
    if ($stmt->execute()) {
        // Delete the actual file - add admin/ prefix to file path
        $full_file_path = "../admin/" . $file_path;
        if (file_exists($full_file_path)) {
            unlink($full_file_path);
        }
        $success_message = "Document deleted successfully!";
    } else {
        $error_message = "Error deleting document: " . $conn->error;
    }
    $stmt->close();
    
    // Refresh the page to show updated list
    header("Location: manage_documents.php?" . $_SERVER['QUERY_STRING']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Documents - CBE Doc's Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #4338ca;
            --secondary: #10b981;
            --secondary-dark: #0d9669;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --gray-light: #e2e8f0;
            --border: #e2e8f0;
            --shadow: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-lg: 0 4px 6px -1px rgba(0,0,0,0.1);
            --shadow-xl: 0 10px 15px -3px rgba(0,0,0,0.1);
            --radius: 0.5rem;
            --radius-lg: 0.75rem;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: #f1f5f9;
            color: var(--dark);
            line-height: 1.6;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar styling */
        .sidebar {
            width: 280px;
            background: var(--dark);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
            transition: all 0.3s ease;
        }
        
        .page-header {
            margin-bottom: 2.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .page-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .page-subtitle {
            color: var(--gray);
            font-size: 1.1rem;
        }
        
        /* Filters section */
        .filters-container {
            background: white;
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .filter-label {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.95rem;
        }
        
        .filter-select, .filter-input {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border);
            border-radius: var(--radius);
            font-size: 1rem;
            background: white;
            transition: all 0.3s ease;
        }
        
        .filter-select:focus, .filter-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        
        .filter-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            flex-wrap: wrap;
        }
        
        /* Buttons */
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--radius);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-secondary {
            background: var(--light);
            color: var(--dark);
            border: 2px solid var(--border);
        }
        
        .btn-secondary:hover {
            background: var(--gray-light);
        }
        
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        
        .btn-danger:hover {
            background: #dc2626;
        }
        
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        /* Documents grid */
        .documents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        
        .document-card {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
        }
        
        .document-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }
        
        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }
        
        .doc-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }
        
        .card-subtitle {
            color: var(--gray);
            font-size: 0.95rem;
        }
        
        .card-body {
            padding: 1.5rem;
            flex: 1;
        }
        
        .doc-meta {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .meta-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .meta-label {
            font-size: 0.875rem;
            color: var(--gray);
            font-weight: 500;
        }
        
        .meta-value {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.95rem;
        }
        
        .card-description {
            color: var(--gray);
            margin-bottom: 1.5rem;
            line-height: 1.6;
            font-size: 0.95rem;
        }
        
        .card-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border);
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            flex-wrap: wrap;
        }
        
        .file-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray);
            font-size: 0.875rem;
            margin-top: 1rem;
            padding: 0.75rem;
            background: #f8fafc;
            border-radius: var(--radius);
        }
        
        .no-documents {
            grid-column: 1 / -1;
            text-align: center;
            padding: 3rem;
            color: var(--gray);
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            margin-top: 2rem;
        }
        
        .no-documents i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #cbd5e1;
        }
        
        /* Alerts */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--secondary);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        /* Stats bar */
        .stats-bar {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            flex: 1;
            min-width: 200px;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--gray);
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        /* Document types grid */
        .doc-types-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .doc-type-card {
            background: white;
            padding: 2rem;
            border-radius: var(--radius-lg);
            text-align: center;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        
        .doc-type-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary);
        }
        
        .doc-type-card.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
        }
        
        .doc-type-card.active i,
        .doc-type-card.active h3,
        .doc-type-card.active p {
            color: white;
        }
        
        .doc-type-card i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .doc-type-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .doc-type-card p {
            color: var(--gray);
            line-height: 1.6;
        }
        
        /* Mobile menu toggle */
        .menu-toggle {
            display: none;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius);
            padding: 0.75rem;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            z-index: 1001;
            position: fixed;
            top: 1rem;
            left: 1rem;
        }
        
        /* Mobile overlay when sidebar is active */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        
        .overlay.active {
            display: block;
        }
        
        /* Responsive design for tablets */
        @media (max-width: 1199px) and (min-width: 768px) {
            .sidebar {
                width: 240px;
            }
            
            .main-content {
                margin-left: 240px;
                padding: 1.5rem;
            }
            
            .documents-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
            
            .doc-types-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            
            .filter-row {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .stat-card {
                min-width: calc(50% - 0.75rem);
            }
            
            .card-footer {
                flex-direction: column;
            }
            
            .card-footer .btn {
                width: 100%;
                justify-content: center;
            }
        }
        
        /* Responsive design for mobile */
        @media (max-width: 767px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .menu-toggle {
                display: flex;
            }
            
            .documents-grid {
                grid-template-columns: 1fr;
            }
            
            .doc-types-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
            
            .doc-type-card {
                padding: 1.5rem;
            }
            
            .doc-type-card i {
                font-size: 2rem;
            }
            
            .filter-row {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .filters-container {
                padding: 1rem;
            }
            
            .filter-actions {
                flex-direction: column;
            }
            
            .filter-actions .btn {
                width: 100%;
                justify-content: center;
            }
            
            .stats-bar {
                flex-direction: column;
                gap: 1rem;
            }
            
            .stat-card {
                min-width: 100%;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                margin-top: 3rem;
            }
            
            .page-title {
                font-size: 1.75rem;
            }
            
            .doc-meta {
                grid-template-columns: 1fr;
            }
            
            .card-footer {
                flex-direction: column;
            }
            
            .card-footer .btn {
                width: 100%;
                justify-content: center;
            }
        }
        
        /* Small mobile devices */
        @media (max-width: 480px) {
            .doc-types-grid {
                grid-template-columns: 1fr;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .stat-card {
                padding: 1rem;
            }
            
            .stat-value {
                font-size: 1.75rem;
            }
            
            .document-card {
                margin-bottom: 1rem;
            }
            
            .card-header, .card-body, .card-footer {
                padding: 1rem;
            }
        }
        
        /* Extra small devices */
        @media (max-width: 360px) {
            .main-content {
                padding: 0.75rem;
            }
            
            .doc-type-card {
                padding: 1rem;
            }
            
            .doc-type-card h3 {
                font-size: 1.1rem;
            }
            
            .filter-select, .filter-input {
                padding: 0.6rem 0.8rem;
            }
            
            .btn {
                padding: 0.6rem 1.2rem;
                font-size: 0.9rem;
            }
        }
        /* ...existing code... */

/* ...existing code... */

/* Responsive design for tablets */
@media (max-width: 1199px) and (min-width: 768px) {
    .sidebar {
        width: 200px;
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        z-index: 1000;
        transform: none;
        display: block;
    }

    .main-content {
        margin-left: 200px;
        padding: 1rem;
    }

    .documents-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .doc-types-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .filter-row {
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .stat-card {
        min-width: 150px;
        padding: 1rem;
    }

    .card-footer {
        flex-direction: column;
        gap: 0.5rem;
    }

    .card-footer .btn {
        width: 100%;
        justify-content: center;
    }
}
/* ...existing code... */
    </style>
</head>
<body>
    
    
    <div class="container">
        <?php include('sidebar.php'); ?>
        
        <div class="main-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Manage Documents</h1>
                    <p class="page-subtitle">View, manage, and organize all uploaded documents</p>
                </div>
            </div>
            
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <!-- Document Types Grid -->
            <div class="doc-types-grid">
                <?php foreach ($valid_types as $type_key => $type_info): ?>
                    <a href="manage_documents.php?type=<?php echo $type_key; ?>" class="doc-type-card <?php echo $doc_type === $type_key ? 'active' : ''; ?>">
                        <i class="fas <?php echo $type_info['icon']; ?>"></i>
                        <h3><?php echo $type_info['name']; ?></h3>
                        <p>
                            <?php 
                            $count = 0;
                            foreach ($all_documents as $doc) {
                                if ($doc['doc_type'] === $type_key) $count++;
                            }
                            echo $count . ' document' . ($count !== 1 ? 's' : '');
                            ?>
                        </p>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Statistics Bar -->
            <div class="stats-bar">
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($all_documents); ?></div>
                    <div class="stat-label">Total Documents</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($courses); ?></div>
                    <div class="stat-label">Available Courses</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-value">8</div>
                    <div class="stat-label">Document Types</div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="filters-container">
                <form method="GET" action="manage_documents.php">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label class="filter-label">Document Type</label>
                            <select name="type" class="filter-select">
                                <option value="">All Types</option>
                                <?php foreach ($valid_types as $type_key => $type_info): ?>
                                    <option value="<?php echo $type_key; ?>" <?php echo $doc_type === $type_key ? 'selected' : ''; ?>>
                                        <?php echo $type_info['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">Course</label>
                            <select name="course_id" class="filter-select">
                                <option value="">All Courses</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo $course['course_id']; ?>" <?php echo $course_id == $course['course_id'] ? 'selected' : ''; ?>>
                                        <?php echo $course['course_code'] . ' - ' . $course['course_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">Semester</label>
                            <select name="semester" class="filter-select">
                                <option value="">All Semesters</option>
                                <option value="1" <?php echo $semester === '1' ? 'selected' : ''; ?>>Semester 1</option>
                                <option value="2" <?php echo $semester === '2' ? 'selected' : ''; ?>>Semester 2</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">Level</label>
                            <select name="level" class="filter-select">
                                <option value="">All Levels</option>
                                <option value="certificate" <?php echo $level === 'certificate' ? 'selected' : ''; ?>>Certificate</option>
                                <option value="diploma1" <?php echo $level === 'diploma1' ? 'selected' : ''; ?>>Diploma 1</option>
                                <option value="diploma2" <?php echo $level === 'diploma2' ? 'selected' : ''; ?>>Diploma 2</option>
                                <option value="bachelor1" <?php echo $level === 'bachelor1' ? 'selected' : ''; ?>>Bachelor 1</option>
                                <option value="bachelor2" <?php echo $level === 'bachelor2' ? 'selected' : ''; ?>>Bachelor 2</option>
                                <option value="bachelor3" <?php echo $level === 'bachelor3' ? 'selected' : ''; ?>>Bachelor 3</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <a href="manage_documents.php" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Clear Filters
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Documents Grid -->
            <div class="documents-grid">
                <?php if (empty($all_documents)): ?>
                    <div class="no-documents">
                        <i class="fas fa-file-alt"></i>
                        <h3>No documents found</h3>
                        <p>Try adjusting your filters or upload new documents.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($all_documents as $document): ?>
                        <div class="document-card">
                            <div class="card-header">
                                <div class="doc-type-badge">
                                    <i class="fas <?php echo $valid_types[$document['doc_type']]['icon']; ?>"></i>
                                    <?php echo $valid_types[$document['doc_type']]['name']; ?>
                                </div>
                                <h3 class="card-title"><?php echo htmlspecialchars($document['title']); ?></h3>
                                <p class="card-subtitle">
                                    <?php echo htmlspecialchars($document['course_code'] . ' - ' . $document['course_name']); ?>
                                </p>
                            </div>
                            
                            <div class="card-body">
                                <div class="doc-meta">
                                    <div class="meta-item">
                                        <span class="meta-label">Semester</span>
                                        <span class="meta-value">Semester <?php echo $document['semester']; ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <span class="meta-label">Level</span>
                                        <span class="meta-value"><?php echo ucfirst($document['level']); ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <span class="meta-label">Uploaded</span>
                                        <span class="meta-value"><?php echo date('M j, Y', strtotime($document['uploaded_at'])); ?></span>
                                    </div>
                                </div>
                                
                                <?php if (!empty($document['description'])): ?>
                                    <p class="card-description"><?php echo htmlspecialchars($document['description']); ?></p>
                                <?php endif; ?>
                                
                                <div class="file-info">
                                    <i class="fas fa-file"></i>
                                    <?php echo htmlspecialchars($document['file_name']); ?>
                                    (<?php echo round($document['file_size'] / 1024 / 1024, 2); ?> MB)
                                </div>
                            </div>
                            
                            <div class="card-footer">
                                <!-- Fixed file paths - added admin/ prefix -->
                                <a href="../admin/<?php echo htmlspecialchars($document['file_path']); ?>" 
                                   class="btn btn-primary btn-sm" download>
                                    <i class="fas fa-download"></i> Download
                                </a>
                                <a href="../admin/<?php echo htmlspecialchars($document['file_path']); ?>" 
                                   class="btn btn-secondary btn-sm" target="_blank">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="doc_id" value="<?php echo $document['doc_id']; ?>">
                                    <input type="hidden" name="source_table" value="<?php echo $document['source_table']; ?>">
                                    <input type="hidden" name="file_path" value="<?php echo htmlspecialchars($document['file_path']); ?>">
                                    <button type="submit" name="delete_document" class="btn btn-danger btn-sm" 
                                            onclick="return confirm('Are you sure you want to delete this document?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="overlay" id="overlay"></div>

    <script>
        // Mobile menu toggle functionality
        const menuToggle = document.getElementById('menuToggle');
        const overlay = document.getElementById('overlay');
        const sidebar = document.querySelector('.sidebar');
        
        // Toggle sidebar on menu button click
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });
        
        // Close sidebar when clicking on overlay
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
        
        // Add active class to clicked doc type cards
        document.querySelectorAll('.doc-type-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.doc-type-card').forEach(c => {
                    c.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
        
        // Auto-submit form when filters change
        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
        
        // Handle window resize to show/hide menu button
        function handleResize() {
            if (window.innerWidth < 768) {
                menuToggle.style.display = 'flex';
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            } else {
                menuToggle.style.display = 'none';
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }
        }
        
        // Initial call and event listener for resize
        handleResize();
        window.addEventListener('resize', handleResize);
    </script>
</body>
</html>
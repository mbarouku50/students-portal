<?php
include("../connection.php");
include("sidebar.php");

// Get document type from filename
$current_file = basename(__FILE__, '.php');
$doc_type = str_replace('category_', '', $current_file);

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

// Check if the document type is valid
if (!array_key_exists($doc_type, $valid_types)) {
    header("Location: manage_documents.php");
    exit();
}

$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
$year = isset($_GET['year']) ? $_GET['year'] : '';
$semester = isset($_GET['semester']) ? $_GET['semester'] : '';

// Get all courses for filter
$courses = [];
$courses_result = $conn->query("SELECT course_id, course_code, course_name FROM courses ORDER BY course_code");
if ($courses_result && $courses_result->num_rows > 0) {
    while ($row = $courses_result->fetch_assoc()) {
        $courses[$row['course_id']] = $row;
    }
}

// Build the query to fetch documents
$where_conditions = ["doc_type = ?"];
$query_params = [$doc_type];

if ($course_id > 0) {
    $where_conditions[] = "course_id = ?";
    $query_params[] = $course_id;
}

// Get documents from all semester tables
$all_documents = [];
$semester_tables = [];

// Generate all possible semester table names
$years = ['first', 'second', 'third', 'fourth'];
$semesters = ['1', '2'];

foreach ($years as $year_val) {
    foreach ($semesters as $semester_val) {
        $table_name = $year_val . '_year_sem' . $semester_val . '_documents';
        $semester_tables[] = $table_name;
    }
}

// Check each table and fetch documents
foreach ($semester_tables as $table) {
    // Check if table exists
    $table_check = $conn->query("SHOW TABLES LIKE '$table'");
    if ($table_check->num_rows > 0) {
        // Build query for this table
        $query = "SELECT d.*, '$table' as source_table, 
                 c.course_code, c.course_name 
                 FROM $table d 
                 LEFT JOIN courses c ON d.course_id = c.course_id";
        
        if (!empty($where_conditions)) {
            $query .= " WHERE " . implode(" AND ", $where_conditions);
        }
        
        $query .= " ORDER BY d.uploaded_at DESC";
        
        // Prepare and execute query
        $stmt = $conn->prepare($query);
        if ($stmt) {
            if (!empty($query_params)) {
                $types = str_repeat('s', count($query_params));
                $stmt->bind_param($types, ...$query_params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Extract year and semester from table name
                    preg_match('/(.+)_year_sem(\d)/', $table, $matches);
                    if (count($matches) === 3) {
                        $row['year'] = $matches[1];
                        $row['semester'] = $matches[2];
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
        // Delete the actual file
        if (file_exists("../" . $file_path)) {
            unlink("../" . $file_path);
        }
        $success_message = "Document deleted successfully!";
    } else {
        $error_message = "Error deleting document: " . $conn->error;
    }
    $stmt->close();
    
    // Refresh the page to show updated list
    header("Location: " . basename(__FILE__) . "?" . $_SERVER['QUERY_STRING']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $valid_types[$doc_type]['name']; ?> - CBE Doc's Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Same CSS as in manage_documents.php */
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --secondary: #10b981;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --border: #e2e8f0;
            --shadow: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-lg: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 2rem;
            transition: all 0.3s ease;
        }
        
        .page-header {
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .page-title {
            font-size: 2.25rem;
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
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border: 2px solid var(--border);
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .filters-container {
            background: white;
            border-radius: 0.75rem;
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
            border-radius: 0.5rem;
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
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-secondary {
            background: var(--light);
            color: var(--dark);
            border: 2px solid var(--border);
        }
        
        .btn-secondary:hover {
            background: #e2e8f0;
        }
        
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        
        .btn-danger:hover {
            background: #dc2626;
        }
        
        .documents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        
        .document-card {
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            border: 1px solid var(--border);
        }
        
        .document-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
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
        }
        
        .card-subtitle {
            color: var(--gray);
            font-size: 0.95rem;
        }
        
        .card-body {
            padding: 1.5rem;
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
        }
        
        .card-description {
            color: var(--gray);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .card-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border);
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
        
        .file-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray);
            font-size: 0.875rem;
        }
        
        .no-documents {
            grid-column: 1 / -1;
            text-align: center;
            padding: 3rem;
            color: var(--gray);
        }
        
        .no-documents i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #cbd5e1;
        }
        
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
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
        
        .stats-bar {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            flex: 1;
            min-width: 200px;
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
        }
        
        @media (max-width: 1200px) {
            .main-content {
                margin-left: 0;
                padding: 1.5rem;
            }
            
            .documents-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .filter-row {
                grid-template-columns: 1fr;
            }
            
            .filter-actions {
                flex-direction: column;
            }
            
            .page-title {
                font-size: 1.75rem;
            }
            
            .documents-grid {
                grid-template-columns: 1fr;
            }
            
            .card-footer {
                flex-direction: column;
            }
            
            .stats-bar {
                flex-direction: column;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>
    
    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title"><?php echo $valid_types[$doc_type]['name']; ?></h1>
                <p class="page-subtitle">View and manage all <?php echo strtolower($valid_types[$doc_type]['name']); ?> documents</p>
            </div>
            <a href="manage_documents.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to All Documents
            </a>
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
        
        <!-- Statistics Bar -->
        <div class="stats-bar">
            <div class="stat-card">
                <div class="stat-value"><?php echo count($all_documents); ?></div>
                <div class="stat-label">Total <?php echo $valid_types[$doc_type]['name']; ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value"><?php 
                    $unique_courses = [];
                    foreach ($all_documents as $doc) {
                        if (!in_array($doc['course_id'], $unique_courses)) {
                            $unique_courses[] = $doc['course_id'];
                        }
                    }
                    echo count($unique_courses);
                ?></div>
                <div class="stat-label">Courses with <?php echo strtolower($valid_types[$doc_type]['name']); ?></div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filters-container">
            <form method="GET" action="<?php echo basename(__FILE__); ?>">
                <input type="hidden" name="type" value="<?php echo $doc_type; ?>">
                <div class="filter-row">
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
                        <label class="filter-label">Year</label>
                        <select name="year" class="filter-select">
                            <option value="">All Years</option>
                            <option value="first" <?php echo $year === 'first' ? 'selected' : ''; ?>>First Year</option>
                            <option value="second" <?php echo $year === 'second' ? 'selected' : ''; ?>>Second Year</option>
                            <option value="third" <?php echo $year === 'third' ? 'selected' : ''; ?>>Third Year</option>
                            <option value="fourth" <?php echo $year === 'fourth' ? 'selected' : ''; ?>>Fourth Year</option>
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
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="<?php echo basename(__FILE__); ?>" class="btn btn-secondary">
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
                    <h3>No <?php echo strtolower($valid_types[$doc_type]['name']); ?> found</h3>
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
                                    <span class="meta-label">Year</span>
                                    <span class="meta-value"><?php echo ucfirst($document['year']); ?> Year</span>
                                </div>
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
                            <a href="../<?php echo htmlspecialchars($document['file_path']); ?>" 
                               class="btn btn-primary" download>
                                <i class="fas fa-download"></i> Download
                            </a>
                            <a href="../<?php echo htmlspecialchars($document['file_path']); ?>" 
                               class="btn btn-secondary" target="_blank">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="doc_id" value="<?php echo $document['doc_id']; ?>">
                                <input type="hidden" name="source_table" value="<?php echo $document['source_table']; ?>">
                                <input type="hidden" name="file_path" value="<?php echo htmlspecialchars($document['file_path']); ?>">
                                <button type="submit" name="delete_document" class="btn btn-danger" 
                                        onclick="return confirm('Are you sure you want to delete this document?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <script>
        // Auto-submit form when filters change
        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
</body>
</html>
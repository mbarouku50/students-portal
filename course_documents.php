<?php
session_name('user_session');
session_start();

// Restrict access: redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include("temperate/header.php");
include("connection.php");

// Get course ID from URL
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

// Fetch course details
$course = null;
if ($course_id > 0) {
    $sql = "SELECT * FROM courses WHERE course_id = $course_id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $course = $result->fetch_assoc();
    }
}

// If course not found, redirect or show error
if (!$course) {
    echo "<div class='container'><div class='alert alert-error'>Course not found.</div></div>";
    include("temperate/footer.php");
    exit;
}

// Define valid document types
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

// Get the active tab from URL or set default
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'certificate';

// Define all available tabs
$tabs = [
    'certificate' => ['name' => 'Certificate', 'levels' => ['certificate']],
    'diploma1' => ['name' => 'Diploma I', 'levels' => ['diploma1']],
    'diploma2' => ['name' => 'Diploma II', 'levels' => ['diploma2']],
    'bachelor1' => ['name' => 'Bachelor I', 'levels' => ['bachelor1']],
    'bachelor2' => ['name' => 'Bachelor II', 'levels' => ['bachelor2']],
    'bachelor3' => ['name' => 'Bachelor III', 'levels' => ['bachelor3']]
];

// Get documents for this course from the active tab's levels
$course_documents = [];
$semester_tables = [];

// Generate semester table names for the active tab
$levels = $tabs[$active_tab]['levels'];
$semesters = ['1', '2'];
foreach ($semesters as $semester_val) {
    foreach ($levels as $level_val) {
        $table_name = 'sem' . $semester_val . '_' . $level_val . '_documents';
        $semester_tables[] = $table_name;
    }
}

// Check each table and fetch documents for this course
foreach ($semester_tables as $table) {
    // Check if table exists
    $table_check = $conn->query("SHOW TABLES LIKE '$table'");
    if ($table_check && $table_check->num_rows > 0) {
        // Build query for this table
        $query = "SELECT d.*, '$table' as source_table 
                 FROM $table d 
                 WHERE d.course_id = $course_id
                 ORDER BY d.uploaded_at DESC";
        
        // Prepare and execute query
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Extract semester and level from table name (new format)
                    preg_match('/sem(\d)_(\w+)_documents/', $table, $matches);
                    if (count($matches) === 3) {
                        $row['semester'] = $matches[1];
                        $row['level'] = $matches[2];
                    }
                    // Set default values for missing fields
                    if (!isset($row['download_count'])) {
                        $row['download_count'] = 0;
                    }
                    $course_documents[] = $row;
                }
            }
            $stmt->close();
        }
    }
}

// Group documents by type
$documents_by_type = [];
foreach ($course_documents as $doc) {
    $type = $doc['doc_type'];
    if (!isset($documents_by_type[$type])) {
        $documents_by_type[$type] = [];
    }
    $documents_by_type[$type][] = $doc;
}

// Handle download count increment
if (isset($_GET['download_id']) && isset($_GET['table'])) {
    $doc_id = intval($_GET['download_id']);
    $source_table = $_GET['table'];
    
    // Update download count in database
    $update_sql = "UPDATE $source_table SET download_count = download_count + 1 WHERE doc_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $doc_id);
    $stmt->execute();
    $stmt->close();
    
    // Exit after handling the download count to prevent full page rendering
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']); ?> - Documents</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #4338ca;
            --secondary: #10b981;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --gray-light: #e2e8f0;
            --border: #e2e8f0;
            --shadow: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-lg: 0 4px 6px -1px rgba(0,0,0,0.1);
            --radius: 0.5rem;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background-color: #f9fafb;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            margin: 1.5rem 0;
            padding: 0.5rem 0;
        }
        
        .back-link:hover {
            color: var(--primary-dark);
        }
        
        .course-header {
            background: linear-gradient(135deg, <?php echo $course['color_code'] ?: '#4f46e5'; ?> 0%, #6366f1 100%);
            color: white;
            padding: 1.5rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
        }
        
        .course-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .course-header p {
            opacity: 0.9;
            max-width: 800px;
        }
        
        /* Tabs styling */
        .tabs-container {
            margin-bottom: 1.5rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        
        .tabs-container::-webkit-scrollbar {
            display: none;
        }
        
        .tabs {
            display: inline-flex;
            background-color: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 0.25rem;
            margin-bottom: 1rem;
            min-width: 100%;
        }
        
        .tab {
            padding: 0.75rem 1.25rem;
            cursor: pointer;
            border-radius: var(--radius);
            font-weight: 600;
            white-space: nowrap;
            transition: all 0.3s ease;
            text-align: center;
            flex: 1;
            min-width: max-content;
        }
        
        .tab:hover {
            background-color: var(--gray-light);
        }
        
        .tab.active {
            background-color: var(--primary);
            color: white;
        }
        
        /* Documents section */
        .documents-section {
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .documents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.25rem;
        }
        
        .document-card {
            background: white;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            border: 1px solid var(--border);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .document-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }
        
        .card-header {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }
        
        .badge-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .doc-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 0.8rem;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
            border-radius: 1rem;
            font-weight: 600;
            font-size: 0.8rem;
        }
        
        .level-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 0.8rem;
            background: rgba(16, 185, 129, 0.1);
            color: var(--secondary);
            border-radius: 1rem;
            font-weight: 600;
            font-size: 0.8rem;
        }
        
        .card-body {
            padding: 1rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .card-body h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--dark);
            line-height: 1.4;
        }
        
        .card-body p {
            color: var(--gray);
            margin-bottom: 1rem;
            line-height: 1.5;
            font-size: 0.9rem;
            flex-grow: 1;
        }
        
        .doc-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
            font-size: 0.8rem;
            color: var(--gray);
        }
        
        .card-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1rem;
            background: var(--primary);
            color: white;
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: var(--radius);
            border: 2px dashed var(--border);
            margin: 1rem 0;
        }
        
        .empty-state i {
            font-size: 2.5rem;
            color: var(--gray);
            margin-bottom: 1rem;
        }
        
        .empty-state h3 {
            font-size: 1.3rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        .empty-state p {
            color: var(--gray);
            max-width: 500px;
            margin: 0 auto 1.5rem;
        }
        
        .badge {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            background: var(--primary-light);
            color: white;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .course-header h1 {
                font-size: 1.5rem;
            }
            
            .tabs {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
            }
            
            .tab {
                flex: 0 0 auto;
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
            
            .documents-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 1rem;
            }
            
            .section-title {
                font-size: 1.2rem;
            }
        }
        
        @media (max-width: 576px) {
            .documents-grid {
                grid-template-columns: 1fr;
            }
            
            .course-header {
                padding: 1.2rem;
            }
            
            .card-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php#courses" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Courses
        </a>
        
        <div class="course-header">
            <h1><?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']); ?></h1>
            <p><?php echo htmlspecialchars($course['description']); ?></p>
        </div>
        
        <!-- Tabs Navigation -->
        <div class="tabs-container">
            <div class="tabs">
                <?php foreach ($tabs as $tab_key => $tab_info): ?>
                    <div class="tab <?php echo $active_tab == $tab_key ? 'active' : ''; ?>" 
                         onclick="changeTab('<?php echo $tab_key; ?>')">
                        <?php echo $tab_info['name']; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php if (empty($course_documents)): ?>
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3>No Documents Available</h3>
                <p>There are currently no documents uploaded for <?php echo $tabs[$active_tab]['name']; ?> level. Please check another tab or come back later.</p>
            </div>
        <?php else: ?>
            <?php foreach ($valid_types as $type_key => $type_info): ?>
                <?php if (isset($documents_by_type[$type_key])): ?>
                    <div class="documents-section">
                        <h2 class="section-title">
                            <i class="fas <?php echo $type_info['icon']; ?>"></i>
                            <?php echo $type_info['name']; ?>
                            <span class="badge"><?php echo count($documents_by_type[$type_key]); ?></span>
                        </h2>
                        
                        <div class="documents-grid">
                            <?php foreach ($documents_by_type[$type_key] as $document): ?>
                                <div class="document-card">
                                    <div class="card-header">
                                        <div class="badge-container">
                                            <span class="doc-type-badge">
                                                <i class="fas <?php echo $type_info['icon']; ?>"></i>
                                                Semester <?php echo $document['semester']; ?>
                                            </span>
                                            <span class="level-badge">
                                                <i class="fas fa-graduation-cap"></i>
                                                <?php echo ucfirst(htmlspecialchars($document['level'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h3><?php echo htmlspecialchars($document['title']); ?></h3>
                                        <p><?php echo htmlspecialchars(substr($document['description'], 0, 100)); ?><?php echo strlen($document['description']) > 100 ? '...' : ''; ?></p>
                                        
                                        <div class="doc-meta">
                                            <span>
                                                <i class="fas fa-calendar"></i>
                                                <?php echo date('M j, Y', strtotime($document['uploaded_at'])); ?>
                                            </span>
                                            <span>
                                                <i class="fas fa-download"></i>
                                                <?php echo intval($document['download_count']); ?>
                                            </span>
                                        </div>
                                        
                                        <?php
                                        // Use the file_path from database instead of constructing from file_name
                                        // The file_path already contains the relative path like "uploads/documents/filename.ext"
                                        $file_path = "admin/" . htmlspecialchars($document['file_path']);
                                        ?>
                                        
                                        <div class="card-actions">
                                            <a href="<?php echo $file_path; ?>" class="btn" download 
                                               onclick="incrementDownloadCount(<?php echo $document['doc_id']; ?>, '<?php echo $document['source_table']; ?>')">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                            
                                            <?php 
                                            // Check if file is viewable in browser (PDF, images)
                                            $file_ext = strtolower(pathinfo($document['file_name'], PATHINFO_EXTENSION));
                                            $viewable_extensions = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt'];
                                            if (in_array($file_ext, $viewable_extensions)): 
                                            ?>
                                                <a href="<?php echo $file_path; ?>" target="_blank" class="btn btn-outline">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
    function changeTab(tabName) {
        // Update URL with the selected tab without reloading the page
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.pushState({}, '', url);
        
        // Reload the page to show documents for the selected tab
        window.location.reload();
    }
    
    function incrementDownloadCount(docId, tableName) {
        // Use AJAX to increment download count without interrupting the download
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '<?php echo basename(__FILE__); ?>?download_id=' + docId + '&table=' + encodeURIComponent(tableName), true);
        xhr.send();
        
        // Allow the download to proceed naturally
        return true;
    }
    
    // Set active tab based on URL parameter
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        if (tabParam) {
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.classList.remove('active');
                if (tab.textContent.trim().toLowerCase().includes(tabParam.toLowerCase())) {
                    tab.classList.add('active');
                }
            });
        }
    });
    </script>
</body>
</html>

<?php
include("temperate/footer.php");
?>
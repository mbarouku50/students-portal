<?php
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

// Get documents for this course from all semester tables
$course_documents = [];
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
                    // Extract year and semester from table name
                    preg_match('/(.+)_year_sem(\d)/', $table, $matches);
                    if (count($matches) === 3) {
                        $row['year'] = $matches[1];
                        $row['semester'] = $matches[2];
                    }
                    
                    // Set default values for missing fields
                    if (!isset($row['download_count'])) {
                        $row['download_count'] = 0;
                    }
                    
                    // Set level if not exists
                    if (!isset($row['level'])) {
                        $row['level'] = 'Undergraduate'; // Default value
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

<style>
    /* Your existing CSS styles remain unchanged */
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
    
    .course-header {
        background: linear-gradient(135deg, <?php echo $course['color_code'] ?: '#4f46e5'; ?> 0%, #6366f1 100%);
        color: white;
        padding: 2rem;
        border-radius: 0.75rem;
        margin-bottom: 2rem;
    }
    
    .course-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }
    
    .course-header p {
        font-size: 1.1rem;
        opacity: 0.9;
        max-width: 800px;
    }
    
    .documents-section {
        margin-bottom: 3rem;
    }
    
    .section-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--border);
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .documents-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
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
        padding: 1.25rem;
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
    
    .level-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: rgba(16, 185, 129, 0.1);
        color: var(--secondary);
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 1rem;
        margin-left: 0.5rem;
    }
    
    .card-body {
        padding: 1.25rem;
    }
    
    .card-body h3 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
        color: var(--dark);
    }
    
    .card-body p {
        color: var(--gray);
        margin-bottom: 1rem;
        line-height: 1.6;
    }
    
    .doc-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
        font-size: 0.875rem;
        color: var(--gray);
    }
    
    .btn {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        background: var(--primary);
        color: white;
        border-radius: 0.5rem;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .btn:hover {
        background: var(--primary-light);
        transform: translateY(-2px);
    }
    
    .btn-outline {
        background: transparent;
        border: 2px solid var(--primary);
        color: var(--primary);
    }
    
    .btn-outline:hover {
        background: var(--primary);
        color: white;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        background: white;
        border-radius: 0.75rem;
        border: 2px dashed var(--border);
    }
    
    .empty-state i {
        font-size: 3rem;
        color: var(--gray);
        margin-bottom: 1rem;
    }
    
    .empty-state h3 {
        font-size: 1.5rem;
        color: var(--dark);
        margin-bottom: 1rem;
    }
    
    .empty-state p {
        color: var(--gray);
        max-width: 500px;
        margin: 0 auto 1.5rem;
    }
    
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 2rem;
    }
    
    .back-link:hover {
        color: var(--primary-light);
    }
    
    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: var(--primary-light);
        color: white;
        border-radius: 1rem;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .alert {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid;
    }
    
    .alert-error {
        background-color: #fee2e2;
        border-color: #ef4444;
        color: #b91c1c;
    }
    
    .badge-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
</style>

<div class="container">
    <a href="index.php#courses" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Courses
    </a>
    
    <div class="course-header">
        <h1><?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']); ?></h1>
        <p><?php echo htmlspecialchars($course['description']); ?></p>
    </div>
    
    <?php if (empty($course_documents)): ?>
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h3>No Documents Available</h3>
            <p>There are currently no documents uploaded for this course. Please check back later.</p>
            <a href="index.php#courses" class="btn">Browse Other Courses</a>
        </div>
    <?php else: ?>
        <?php foreach ($valid_types as $type_key => $type_info): ?>
            <?php if (isset($documents_by_type[$type_key])): ?>
                <div class="documents-section">
                    <h2 class="section-title">
                        <i class="fas <?php echo $type_info['icon']; ?>"></i>
                        <?php echo $type_info['name']; ?>
                        <span class="badge"><?php echo count($documents_by_type[$type_key]); ?> documents</span>
                    </h2>
                    
                    <div class="documents-grid">
                        <?php foreach ($documents_by_type[$type_key] as $document): ?>
                            <div class="document-card">
                                <div class="card-header">
                                    <div class="badge-container">
                                        <span class="doc-type-badge">
                                            <i class="fas <?php echo $type_info['icon']; ?>"></i>
                                            <?php echo ucfirst(str_replace('_', ' ', $document['year'])) . ' Year - Semester ' . $document['semester']; ?>
                                        </span>
                                        <span class="level-badge">
                                            <i class="fas fa-graduation-cap"></i>
                                            <?php echo htmlspecialchars($document['level']); ?>
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
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
function incrementDownloadCount(docId, tableName) {
    // Use AJAX to increment download count without interrupting the download
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '<?php echo basename(__FILE__); ?>?download_id=' + docId + '&table=' + encodeURIComponent(tableName), true);
    xhr.send();
    
    // Allow the download to proceed naturally
    return true;
}
</script>

<?php
include("temperate/footer.php");
?>
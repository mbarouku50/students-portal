<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("temperate/header.php");
include("connection.php");

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

// Get document type from URL if specified
$doc_type = isset($_GET['type']) ? $_GET['type'] : '';

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
    if ($table_check && $table_check->num_rows > 0) {
        // Build query for this table
        $query = "SELECT d.*, '$table' as source_table, 
                 c.course_code, c.course_name 
                 FROM $table d 
                 LEFT JOIN courses c ON d.course_id = c.course_id
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
                    $all_documents[] = $row;
                }
            }
            $stmt->close();
        }
    }
}

// Fetch all courses
$courses = [];
$sql = "SELECT * FROM courses ORDER BY course_name";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $courses = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<style>
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
    
    .doc-types-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
    
    .doc-type-card {
        background: white;
        padding: 2rem;
        border-radius: 0.75rem;
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
        box-shadow: var(--shadow-lg);
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
    
    .section-title {
        font-size: 2rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 2rem;
        text-align: center;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .courses {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
    
    .course-card {
        background: white;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        border: 1px solid var(--border);
    }
    
    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }
    
    .course-img {
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.5rem;
    }
    
    .course-info {
        padding: 1.5rem;
    }
    
    .course-info h3 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
        color: var(--dark);
    }
    
    .course-info p {
        color: var(--gray);
        margin-bottom: 1.5rem;
        line-height: 1.6;
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
    }
    
    .btn:hover {
        background: var(--primary-light);
        transform: translateY(-2px);
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    
    .main-content {
        padding: 2rem 0;
    }
    
    .document-types {
        padding: 2rem 0;
        background: #f8fafc;
    }
</style>

<section class="main-content" id="courses">
    <div class="container">
        <h2 class="section-title">Browse by Course Program</h2>
        <div class="courses">
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $course): ?>
                <div class="course-card">
                    <div class="course-img" style="background-color: <?php echo $course['color_code'] ?: '#4f46e5'; ?>">
                        <?php echo $course['course_code']; ?>
                    </div>
                    <div class="course-info">
                        <h3><?php echo htmlspecialchars($course['course_name']); ?></h3>
                        <p><?php echo substr(htmlspecialchars($course['description']), 0, 100); ?><?php echo strlen($course['description']) > 100 ? '...' : ''; ?></p>
                        <a href="course_documents.php?course_id=<?php echo $course['course_id']; ?>" class="btn">View Documents</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No courses available.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="document-types" id="documents">
    <div class="container">
        <h2 class="section-title">Document Types Available</h2>
        <!-- Document Types Grid -->
        <div class="doc-types-grid">
            <?php foreach ($valid_types as $type_key => $type_info): ?>
                <a href="" class="doc-type-card <?php echo $doc_type === $type_key ? 'active' : ''; ?>">
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
    </div>
</section>

<?php
include("temperate/footer.php");
?>
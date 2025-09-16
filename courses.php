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
    <?php
include("temperate/footer.php")
?>
<?php
// Start the script with proper includes and variable definitions
include("connection.php");
include("temperate/header.php");

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
                    // Extract semester and level from table name (new format)
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
        display: block;
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
        margin: 0;
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
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    
    .document-types {
        padding: 3rem 0;
        background: #f8fafc;
    }
</style>

<section class="document-types" id="documents">
    <div class="container">
        <h2 class="section-title">Document Types Available</h2>
        <!-- Document Types Grid -->
        <div class="doc-types-grid">
            <?php foreach ($valid_types as $type_key => $type_info): ?>
                <div class="doc-type-card <?php echo $doc_type === $type_key ? 'active' : ''; ?>">
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
                    </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
include("temperate/footer.php");
?>
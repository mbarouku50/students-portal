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

// Check if search was submitted
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_results = [];
$has_searched = !empty($search_query);

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
                 LEFT JOIN courses c ON d.course_id = c.course_id";
        
        // Add search condition if a search query exists
        if ($has_searched) {
            $query .= " WHERE (d.title LIKE ? OR d.description LIKE ? OR c.course_code LIKE ? OR c.course_name LIKE ?)";
        }
        
        $query .= " ORDER BY d.uploaded_at DESC";
        
        // Prepare and execute query
        $stmt = $conn->prepare($query);
        if ($stmt) {
            if ($has_searched) {
                $search_param = "%" . $search_query . "%";
                $stmt->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
            }
            
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
                    
                    if ($has_searched) {
                        $search_results[] = $row;
                    } else {
                        $all_documents[] = $row;
                    }
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
        --primary: #2c3e50;
        --primary-light: #2c3e50;
        --secondary: #10b981;
        --dark: #1e293b;
        --light: #f8fafc;
        --gray: #64748b;
        --border: #e2e8f0;
        --shadow: 0 1px 3px rgba(0,0,0,0.1);
        --shadow-lg: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .hero {
        background: linear-gradient(rgba(44, 62, 80, 0.9), rgba(44, 62, 80, 0.9));
        background-image: url('pexels-markusspiske-96593.jpg');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 4rem 0;
        text-align: center;
    }
    
    .hero h1 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    
    .hero p {
        font-size: 1.2rem;
        max-width: 700px;
        margin: 0 auto 2rem;
    }
    
    .search-bar {
        max-width: 600px;
        margin: 0 auto;
        display: flex;
    }
    
    .search-bar input {
        flex: 1;
        padding: 0.8rem;
        border: none;
        border-radius: 4px 0 0 4px;
        font-size: 1rem;
    }
    
    .search-bar button {
        background-color: var(--secondary);
        color: white;
        border: none;
        padding: 0 1.5rem;
        border-radius: 0 4px 4px 0;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s;
    }
    
    .search-bar button:hover {
        background-color: #0d9669;
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
    
    .search-results {
        margin: 2rem 0;
    }
    
    .result-count {
        font-size: 1.2rem;
        margin-bottom: 1.5rem;
        color: var(--gray);
    }
    
    .no-results {
        text-align: center;
        padding: 3rem;
        color: var(--gray);
    }
    
    .document-info {
        padding: 1.5rem;
    }
    
    .document-info h3 {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
        color: var(--dark);
    }
    
    .document-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        color: var(--gray);
    }
    
    .document-meta span {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .document-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .document-actions .btn {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
</style>

<section class="hero">
    <div class="container">
        <h1>Your University Document Repository</h1>
        <p>Find and share assignments, exams, notes, and other academic resources for all CBE bachelor degree programs.</p>
        <form method="GET" action="" class="search-bar">
            <input type="text" name="search" placeholder="Search for documents, courses, or keywords..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>
    </div>
</section>

<?php if ($has_searched): ?>
<section class="search-results">
    <div class="container">
        <h2 class="section-title">Search Results</h2>
        <div class="result-count">
            Found <?php echo count($search_results); ?> result(s) for "<?php echo htmlspecialchars($search_query); ?>"
        </div>
        
        <?php if (count($search_results) > 0): ?>
        <div class="documents-grid">
            <?php foreach ($search_results as $document): ?>
            <div class="document-card">
                <div class="card-header">
                    <span class="doc-type-badge">
                        <i class="fas <?php echo $valid_types[$document['doc_type']]['icon'] ?? 'fa-file'; ?>"></i>
                        <?php echo $valid_types[$document['doc_type']]['name'] ?? 'Document'; ?>
                    </span>
                    <h3><?php echo htmlspecialchars($document['title']); ?></h3>
                </div>
                <div class="document-info">
                    <p><?php echo htmlspecialchars(substr($document['description'], 0, 100)); ?><?php echo strlen($document['description']) > 100 ? '...' : ''; ?></p>
                    <div class="document-meta">
                        <span><i class="fas fa-book"></i> <?php echo htmlspecialchars($document['course_code'] . ' - ' . $document['course_name']); ?></span>
                        <span><i class="fas fa-graduation-cap"></i> <?php echo ucfirst($document['level']); ?></span>
                        <span><i class="fas fa-calendar"></i> Semester <?php echo $document['semester']; ?></span>
                    </div>
                    <div class="document-actions">
                        <a href="download.php?table=<?php echo urlencode($document['source_table']); ?>&id=<?php echo $document['doc_id']; ?>" class="btn">Download</a>
                        <a href="view_document.php?table=<?php echo urlencode($document['source_table']); ?>&id=<?php echo $document['doc_id']; ?>" class="btn" style="background-color: var(--gray);">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="no-results">
            <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 1rem;"></i>
            <h3>No documents found</h3>
            <p>Try different keywords or browse by document type or course.</p>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

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
                <a href="?type=<?php echo $type_key; ?>" class="doc-type-card <?php echo $doc_type === $type_key ? 'active' : ''; ?>">
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
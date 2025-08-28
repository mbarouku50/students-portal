<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ...existing code...
include("temperate/header.php");

include("connection.php");

// Fetch all courses
$courses = [];
$sql = "SELECT * FROM courses ORDER BY course_name";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $courses = $result->fetch_all(MYSQLI_ASSOC);
}
?>
    <section class="main-content" id="courses">
        <div class="container">
            <h2 class="section-title">Browse by Course Program</h2>
            <div class="courses">
                <?php foreach ($courses as $course): ?>
                <div class="course-card">
                    <div class="course-img" style="background-color: <?php echo $course['color_code']; ?>">
                        <?php echo $course['course_code']; ?>
                    </div>
                    <div class="course-info">
                        <h3><?php echo $course['course_name']; ?></h3>
                        <p><?php echo substr($course['description'], 0, 100); ?><?php echo strlen($course['description']) > 100 ? '...' : ''; ?></p>
                        <a href="#" class="btn">View Documents</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <section class="document-types" id="documents">
        <div class="container">
            <h2 class="section-title">Document Types Available</h2>
            <div class="doc-types">
                <div class="doc-type">
                    <i class="fas fa-file-alt"></i>
                    <h3>Lecture Notes</h3>
                    <p>Comprehensive notes from lectures and tutorials</p>
                </div>
                
                <div class="doc-type">
                    <i class="fas fa-book"></i>
                    <h3>Study Guides</h3>
                    <p>Condensed materials for exam preparation</p>
                </div>
                
                <div class="doc-type">
                    <i class="fas fa-tasks"></i>
                    <h3>Assignments</h3>
                    <p>Sample assignments with solutions</p>
                </div>
                
                <div class="doc-type">
                    <i class="fas fa-question-circle"></i>
                    <h3>Past Exams</h3>
                    <p>Previous examination papers</p>
                </div>
                
                <div class="doc-type">
                    <i class="fas fa-chart-bar"></i>
                    <h3>Case Studies</h3>
                    <p>Real-world business case analyses</p>
                </div>
                
                <div class="doc-type">
                    <i class="fas fa-project-diagram"></i>
                    <h3>Projects</h3>
                    <p>Sample research projects and reports</p>
                </div>
                <div class="doc-type">
                    <i class="fas fa-project-diagram"></i>
                    <h3>Field Reports</h3>
                    <p>Sample field research reports</p>
                </div>
                <div class="doc-type">
                    <i class="fas fa-project-diagram"></i>
                    <h3>cover pages</h3>
                    <p>Sample cover pages for all caus</p>
                </div>
            </div>
        </div>
    </section>
<?php
include("temperate/footer.php")
?>
   
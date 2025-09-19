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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_course'])) {
        // Add new course
        $course_code = $conn->real_escape_string($_POST['course_code']);
        $course_name = $conn->real_escape_string($_POST['course_name']);
        $description = $conn->real_escape_string($_POST['description']);
        $color_code = $conn->real_escape_string($_POST['color_code']);

        $sql = "INSERT INTO courses (course_code, course_name, description, color_code) 
                VALUES ('$course_code', '$course_name', '$description', '$color_code')";
        
        if ($conn->query($sql)) {
            $success = "Course added successfully!";
        } else {
            $error = "Error adding course: " . $conn->error;
        }
    } elseif (isset($_POST['update_course'])) {
        // Update existing course
        $course_id = $conn->real_escape_string($_POST['course_id']);
        $course_code = $conn->real_escape_string($_POST['course_code']);
        $course_name = $conn->real_escape_string($_POST['course_name']);
        $description = $conn->real_escape_string($_POST['description']);
        $color_code = $conn->real_escape_string($_POST['color_code']);

        $sql = "UPDATE courses SET 
                course_code = '$course_code',
                course_name = '$course_name',
                description = '$description',
                color_code = '$color_code'
                WHERE course_id = '$course_id'";
        
        if ($conn->query($sql)) {
            $success = "Course updated successfully!";
        } else {
            $error = "Error updating course: " . $conn->error;
        }
    } elseif (isset($_GET['delete'])) {
        // Delete course
        $course_id = $conn->real_escape_string($_GET['delete']);
        
        $sql = "DELETE FROM courses WHERE course_id = '$course_id'";
        if ($conn->query($sql)) {
            $success = "Course deleted successfully!";
        } else {
            $error = "Error deleting course: " . $conn->error;
        }
    }
}

// Fetch all courses
$courses = [];
$sql = "SELECT * FROM courses ORDER BY course_name";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $courses = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses - Admin Panel</title>
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
            --sidebar-collapsed-width: 70px;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7f9;
            color: #333;
            line-height: 1.6;
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar styles */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--primary-color);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
            transition: all 0.3s ease;
        }
        
        /* Mobile menu toggle */
        .menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 12px;
            z-index: 1001;
            cursor: pointer;
        }
        
        /* Form styles */
        .form-container {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #444;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        /* Button styles */
        .btn {
            display: inline-block;
            padding: 10px 16px;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        .btn-success {
            background-color: var(--success-color);
        }
        
        .btn-danger {
            background-color: var(--error-color);
        }
        
        /* Courses grid */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            overflow: visible;
        }
        
        .course-card {
            background-color: white;
            border-radius: 8px;
            overflow: visible; /* ✅ Allow dropdown to escape */
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
            z-index: 1; 
        }
        .course-card.active {
            z-index: 9999; /* bring this card + dropdown on top */
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .course-img {
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        
        .course-info {
            padding: 15px;
        }
        
        .course-info h3 {
            margin-bottom: 8px;
            font-size: 16px;
            color: #2c3e50;
        }
        
        .course-info p {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
            line-height: 1.4;
        }
        
        .course-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .color-preview {
            width: 25px;
            height: 25px;
            border-radius: 4px;
            display: inline-block;
            margin-left: 8px;
            border: 1px solid #ddd;
            vertical-align: middle;
        }
        
        /* Alert styles */
        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.1);
            border-left: 3px solid var(--success-color);
            color: var(--success-color);
        }
        
        .alert-error {
            background-color: rgba(231, 76, 60, 0.1);
            border-left: 3px solid var(--error-color);
            color: var(--error-color);
        }
        
        /* Dropdown styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #2c3e50; /* Changed to dark background for better contrast */
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
            z-index: 2000;
            border-radius: 6px;
            margin-top: 10px;
            right: 0;
            max-height: 300px;
            overflow-y: auto;
        }

        .dropdown-content a {
            color: white; /* Changed to white for better contrast */
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 14px;
            border-bottom: 1px solid rgba(255,255,255,0.1); /* Lighter border for dark background */
            transition: background-color 0.2s;
        }

        .dropdown-content a:last-child {
            border-bottom: none;
        }

        .dropdown-content a:hover {
            background-color: rgba(255,255,255,0.1); /* Lighter hover effect for dark background */
        }

        .dropdown-content a i {
            margin-right: 8px;
            width: 18px;
            text-align: center;
        }

        .show {
            display: block;
        }
        
        /* Page header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .page-title {
            font-size: 24px;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .courses-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }
        
        @media (max-width: 992px) {
            :root {
                --sidebar-width: 70px;
            }
            
            .sidebar .menu-text {
                display: none;
            }
            
            .main-content {
                margin-left: var(--sidebar-width);
            }
            
            .menu-toggle {
                display: block;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                padding: 1.5rem;
                margin-left: 0;
            }
            
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .menu-toggle {
                display: block;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .courses-grid {
                grid-template-columns: 1fr;
            }
            
            .course-actions {
                flex-direction: column;
                align-items: flex-start;
            }
            
             .dropdown-content {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 90%;
                max-width: 300px;
                max-height: 80vh;
                z-index: 1002; /* Higher z-index for mobile */
            }
            
            /* Add overlay for mobile dropdowns */
            .dropdown-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0,0,0,0.5);
                z-index: 1001;
            }
            
            .dropdown-overlay.active {
                display: block;
            }
        }
        
        @media (max-width: 576px) {
            .main-content {
                padding: 1rem;
            }
            
            .form-container {
                padding: 15px;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 8px;
                text-align: center;
                justify-content: center;
            }
            
            .course-actions .btn {
                width: auto;
            }
            
            .color-preview {
                width: 20px;
                height: 20px;
            }
        }
        
        /* Print styles */
        @media print {
            .sidebar, .menu-toggle, .course-actions, .btn {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .course-card {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
   
     <div class="dropdown-overlay" id="dropdownOverlay"></div>
    
    <?php include('sidebar.php'); ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-book"></i> Manage Courses</h1>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <h2 style="margin-bottom: 15px; font-size: 18px; color: var(--dark-color);"><?php echo isset($_GET['edit']) ? 'Edit Course' : 'Add New Course'; ?></h2>
            <form method="POST">
                <?php
                $edit_course = null;
                if (isset($_GET['edit'])) {
                    $course_id = $conn->real_escape_string($_GET['edit']);
                    $sql = "SELECT * FROM courses WHERE course_id = '$course_id'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $edit_course = $result->fetch_assoc();
                    }
                }
                ?>
                
                <?php if ($edit_course): ?>
                    <input type="hidden" name="course_id" value="<?php echo $edit_course['course_id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="course_code">Course Code</label>
                    <input type="text" id="course_code" name="course_code" class="form-control" 
                           value="<?php echo $edit_course ? $edit_course['course_code'] : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="course_name">Course Name</label>
                    <input type="text" id="course_name" name="course_name" class="form-control" 
                           value="<?php echo $edit_course ? $edit_course['course_name'] : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3" required><?php echo $edit_course ? $edit_course['description'] : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="color_code">Color Code <span class="color-preview" id="colorPreview" style="background-color: <?php echo $edit_course ? $edit_course['color_code'] : '#e74c3c'; ?>"></span></label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="color" id="color_code" name="color_code" class="form-control" style="height: 40px; width: 60px; padding: 0;"
                               value="<?php echo $edit_course ? $edit_course['color_code'] : '#e74c3c'; ?>" required>
                        <span>Click to choose a color</span>
                    </div>
                </div>
                
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    <button type="submit" name="<?php echo $edit_course ? 'update_course' : 'add_course'; ?>" class="btn btn-success">
                        <i class="fas fa-save"></i> <?php echo $edit_course ? 'Update' : 'Add Course'; ?>
                    </button>
                    
                    <?php if ($edit_course): ?>
                        <a href="manage_courses.php" class="btn">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <h2 style="margin-bottom: 15px; font-size: 18px; color: var(--dark-color);">Existing Courses</h2>
        
        <?php if (count($courses) > 0): ?>
            <div class="courses-grid">
                <?php foreach ($courses as $course): ?>
                    <div class="course-card">
                        <div class="course-img" style="background-color: <?php echo $course['color_code']; ?>">
                            <?php echo $course['course_code']; ?>
                        </div>
                        <div class="course-info">
                            <h3><?php echo $course['course_name']; ?></h3>
                            <p><?php echo substr($course['description'], 0, 100); ?><?php echo strlen($course['description']) > 100 ? '...' : ''; ?></p>
                            <div class="course-actions">
                                <a href="manage_courses.php?edit=<?php echo $course['course_id']; ?>" class="btn">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="manage_courses.php?delete=<?php echo $course['course_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this course?');">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                                <div class="dropdown">
                                    <button onclick="toggleDropdown(this)" class="btn">
                                        <i class="fas fa-upload"></i> Upload <i class="fas fa-caret-down"></i>
                                    </button>
                                    <div id="dropdown-<?php echo $course['course_id']; ?>" class="dropdown-content">
                    
                                        <a href="upload_docs.php?course_id=<?php echo $course['course_id']; ?>&type=lecture_notes">
                                            <i class="fas fa-file-alt"></i> Lecture Notes
                                        </a>
                                        <a href="upload_docs.php?course_id=<?php echo $course['course_id']; ?>&type=study_guides">
                                            <i class="fas fa-book"></i> Study Guides
                                        </a>
                                        <a href="upload_docs.php?course_id=<?php echo $course['course_id']; ?>&type=assignments">
                                            <i class="fas fa-tasks"></i> Assignments
                                        </a>
                                        <a href="upload_docs.php?course_id=<?php echo $course['course_id']; ?>&type=past_exams">
                                            <i class="fas fa-file-contract"></i> Past Exams
                                        </a>
                                        <a href="upload_docs.php?course_id=<?php echo $course['course_id']; ?>&type=case_studies">
                                            <i class="fas fa-briefcase"></i> Case Studies
                                        </a>
                                        <a href="upload_docs.php?course_id=<?php echo $course['course_id']; ?>&type=projects">
                                            <i class="fas fa-project-diagram"></i> Projects
                                        </a>
                                        <a href="upload_docs.php?course_id=<?php echo $course['course_id']; ?>&type=field">
                                            <i class="fas fa-map-marked-alt"></i> Field Reports
                                        </a>
                                        <a href="upload_docs.php?course_id=<?php echo $course['course_id']; ?>&type=cover_pages">
                                            <i class="fas fa-file-image"></i> Cover Pages
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="form-container" style="text-align: center; padding: 30px;">
                <i class="fas fa-book-open" style="font-size: 48px; color: #ddd; margin-bottom: 15px;"></i>
                <h3 style="color: #888;">No Courses Found</h3>
                <p style="color: #aaa;">Get started by adding your first course using the form above.</p>
            </div>
        <?php endif; ?>
    </main>

    <script>
        // Update color preview when color picker changes
        document.getElementById('color_code').addEventListener('input', function() {
            document.getElementById('colorPreview').style.backgroundColor = this.value;
        });

        // Toggle dropdown function
                function toggleDropdown(button) {
            const dropdown = button.nextElementSibling;
            const card = button.closest('.course-card');
            const isShowing = dropdown.classList.contains('show');

            // Close all dropdowns & reset active cards
            document.querySelectorAll('.dropdown-content').forEach(dd => dd.classList.remove('show'));
            document.querySelectorAll('.course-card').forEach(c => c.classList.remove('active'));
            document.getElementById('dropdownOverlay').classList.remove('active');

            // If this one was not showing, open it
            if (!isShowing) {
                dropdown.classList.add("show");
                card.classList.add("active");

                // Show overlay on mobile
                if (window.innerWidth <= 768) {
                    document.getElementById('dropdownOverlay').classList.add('active');
                }

                // Position dropdown correctly (flip up if no space)
                positionDropdown(button, dropdown);
            }
        }


        // Position dropdown to ensure full visibility
        function positionDropdown(button, dropdown) {
            const buttonRect = button.getBoundingClientRect();
            const dropdownHeight = dropdown.offsetHeight;
            const dropdownWidth = dropdown.offsetWidth;
            const windowHeight = window.innerHeight;
            const windowWidth = window.innerWidth;
            
            // For mobile, center the dropdown
            if (window.innerWidth <= 768) {
                dropdown.style.top = '50%';
                dropdown.style.left = '50%';
                dropdown.style.transform = 'translate(-50%, -50%)';
                dropdown.style.bottom = 'auto';
                dropdown.style.right = 'auto';
                return;
            }
            
            // Check if dropdown would go off-screen at bottom
            if (buttonRect.bottom + dropdownHeight > windowHeight) {
                // Position above button if not enough space below
                dropdown.style.bottom = '100%';
                dropdown.style.top = 'auto';
                dropdown.style.marginTop = '0';
                dropdown.style.marginBottom = '5px';
            } else {
                // Default position below button
                dropdown.style.top = '100%';
                dropdown.style.bottom = 'auto';
                dropdown.style.marginTop = '5px';
                dropdown.style.marginBottom = '0';
            }
            
            // Check if dropdown would go off-screen at right
            if (buttonRect.right + dropdownWidth > windowWidth) {
                dropdown.style.right = '0';
                dropdown.style.left = 'auto';
            } else {
                dropdown.style.left = '0';
                dropdown.style.right = 'auto';
            }
            
            // Reset transform for desktop
            dropdown.style.transform = 'none';
        }

        // Close dropdowns when clicking outside
        window.addEventListener('click', function(event) {
            if (!event.target.matches('.dropdown button') && 
                !event.target.closest('.dropdown-content') &&
                !event.target.matches('.dropdown-content a')) {
                
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    dropdowns[i].classList.remove('show');
                }
                
                // Hide overlay
                document.getElementById('dropdownOverlay').classList.remove('active');
            }
        });
        
        // Close dropdowns when clicking on overlay
        document.getElementById('dropdownOverlay').addEventListener('click', function() {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                dropdowns[i].classList.remove('show');
            }
            this.classList.remove('active');
        });
        
        // Mobile menu toggle
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
        
        // Adjust layout on window resize
        window.addEventListener('resize', function() {
            // Close all dropdowns on resize
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                dropdowns[i].classList.remove('show');
            }
            
            // Hide overlay
            document.getElementById('dropdownOverlay').classList.remove('active');
        });
    </script>
</body>
</html>
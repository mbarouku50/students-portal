<?php

include("../connection.php");

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
        }
        
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }
        
        .form-container {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
        }
        
        .btn-success {
            background-color: var(--success-color);
        }
        
        .btn-danger {
            background-color: var(--error-color);
        }
        
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 15px;
        }
        
        .course-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: relative;
            overflow: visible !important; /* Override any overflow settings */
        }
        
        .course-img {
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        
        .course-info {
            padding: 15px;
            position: relative;
            z-index: 1;
        }
        
        .course-info h3 {
            margin-bottom: 5px;
            font-size: 16px;
        }
        
        .course-info p {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .course-actions {
            display: flex;
            gap: 8px;
        }
        
        .color-preview {
            width: 25px;
            height: 25px;
            border-radius: 4px;
            display: inline-block;
            margin-left: 8px;
            border: 1px solid #ddd;
        }
        
        .alert {
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 14px;
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
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 180px; /* Increased width */
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1000;
            border-radius: 4px;
            margin-top: 5px;
            right: 0;
            max-height: 300px; /* Limit height with scroll if needed */
            overflow-y: auto; /* Add scroll if content is too long */
        }

        .dropdown-content a {
            color: #333;
            padding: 10px 15px; /* Increased padding */
            text-decoration: none;
            display: block;
            font-size: 14px;
            white-space: nowrap; /* Prevent text wrapping */
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .show {
            display: block;
        }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>
    
    <main class="main-content">
        <h1 style="margin-bottom: 20px;"><i class="fas fa-book"></i> Manage Courses</h1>
        
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
            <h2 style="margin-bottom: 15px; font-size: 18px;"><?php echo isset($_GET['edit']) ? 'Edit Course' : 'Add New Course'; ?></h2>
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
                    <input type="color" id="color_code" name="color_code" class="form-control" style="height: 40px; width: 60px; padding: 0;"
                           value="<?php echo $edit_course ? $edit_course['color_code'] : '#e74c3c'; ?>" required>
                </div>
                
                <button type="submit" name="<?php echo $edit_course ? 'update_course' : 'add_course'; ?>" class="btn btn-success">
                    <i class="fas fa-save"></i> <?php echo $edit_course ? 'Update' : 'Add Course'; ?>
                </button>
                
                <?php if ($edit_course): ?>
                    <a href="manage_courses.php" class="btn">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                <?php endif; ?>
            </form>
        </div>
        
        <h2 style="margin-bottom: 15px; font-size: 18px;">Existing Courses</h2>
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
                            <a href="manage_courses.php?edit=<?php echo $course['course_id']; ?>" class="btn" style="padding: 5px 10px;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="manage_courses.php?delete=<?php echo $course['course_id']; ?>" class="btn btn-danger" style="padding: 5px 10px;" onclick="return confirm('Are you sure you want to delete this course?');">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                              <!-- Updated dropdown with better spacing -->
                                <div class="dropdown" style="position: relative;">
                                    <button onclick="toggleDropdown(this)" class="btn" style="padding: 5px 10px;">
                                        <i class="fas fa-upload"></i> Upload Docs <i class="fas fa-caret-down"></i>
                                    </button>
                                    <div id="dropdown-<?php echo $course['course_id']; ?>" class="dropdown-content" style="position: absolute;">
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
                                            <i class="fas fa-map-marked-alt"></i> Cover Pages
                                        </a>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script>
        // Update color preview when color picker changes
        document.getElementById('color_code').addEventListener('input', function() {
            document.getElementById('colorPreview').style.backgroundColor = this.value;
        });

        // Toggle dropdown function with improved visibility
        function toggleDropdown(button) {
            // Close all other dropdowns first
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown !== button.nextElementSibling && openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
            
            // Toggle the clicked dropdown
            var dropdown = button.nextElementSibling;
            dropdown.classList.toggle("show");
            
            // Ensure dropdown is fully visible
            positionDropdown(button, dropdown);
        }

        // Position dropdown to ensure full visibility
        function positionDropdown(button, dropdown) {
            const buttonRect = button.getBoundingClientRect();
            const dropdownHeight = dropdown.offsetHeight;
            const windowHeight = window.innerHeight;
            
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
            if (buttonRect.right + dropdown.offsetWidth > window.innerWidth) {
                dropdown.style.right = '0';
                dropdown.style.left = 'auto';
            } else {
                dropdown.style.left = '0';
                dropdown.style.right = 'auto';
            }
        }

        // Close dropdowns when clicking outside
        window.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    dropdowns[i].classList.remove('show');
                }
            }
        });
</script>
</body>
</html>
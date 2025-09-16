<?php
include("../connection.php");
include("sidebar.php");

// Get course and document type
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
$doc_type = isset($_GET['type']) ? $_GET['type'] : '';

// Validate document type
$valid_types = [
    'lecture_notes' => 'Lecture Notes',
    'study_guides' => 'Study Guides',
    'assignments' => 'Assignments',
    'past_exams' => 'Past Exams',
    'case_studies' => 'Case Studies',
    'projects' => 'Projects',
    'field' => 'Field Reports',
    'cover_pages' => 'Cover Pages'
];

if (!array_key_exists($doc_type, $valid_types)) {
    die("Invalid document type");
}

// Get course info
$course = [];
$sql = "SELECT * FROM courses WHERE course_id = $course_id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $course = $result->fetch_assoc();
} else {
    die("Course not found");
}

// Handle file upload
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['document'])) {
    $title = $conn->real_escape_string($_POST['document_title']);
    $description = $conn->real_escape_string($_POST['description']);
    $semester = $conn->real_escape_string($_POST['semester']);
    $level = $conn->real_escape_string($_POST['level']);
    $uploaded_by = $_SESSION['user_id'] ?? 0;

    // File upload configuration
    $target_dir = __DIR__ . "/uploads/documents/";
    
    // Create directory if it doesn't exist with proper permissions
    if (!file_exists($target_dir)) {
        if (!mkdir($target_dir, 0755, true)) {
            $error = "Failed to create upload directory. Please check permissions.";
        }
    }
    
    // Check if directory is writable
    if (file_exists($target_dir) && is_writable($target_dir)) {
    $file_name = basename($_FILES["document"]["name"]);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $new_file_name = uniqid() . '_' . time() . '.' . $file_ext;
    $target_file = $target_dir . $new_file_name;
        
        // Validate file
        $allowed_types = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt'];
        $max_file_size = 10 * 1024 * 1024; // 10MB
        
        if (!in_array($file_ext, $allowed_types)) {
            $error = "Sorry, only PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, and TXT files are allowed.";
        } elseif ($_FILES["document"]["size"] > $max_file_size) {
            $error = "Sorry, your file is too large. Maximum 10MB allowed.";
        } else {
            // Check for upload errors
            if ($_FILES["document"]["error"] !== UPLOAD_ERR_OK) {
                $error = "Upload error: " . getUploadError($_FILES["document"]["error"]);
            } elseif (move_uploaded_file($_FILES["document"]["tmp_name"], $target_file)) {
                // Determine the correct table based on semester and level
                $table_name = 'sem' . $semester . '_' . strtolower($level) . '_documents';
                // Check if table exists
                $table_check = $conn->query("SHOW TABLES LIKE '$table_name'");
                if ($table_check->num_rows == 0) {
                    $error = "Invalid semester/level combination. Table '$table_name' does not exist.";
                    // Clean up uploaded file
                    if (file_exists($target_file)) {
                        unlink($target_file);
                    }
                } else {
                    // Insert into the correct table
                    $relative_file_path = "uploads/documents/" . $new_file_name;
                    $sql = "INSERT INTO $table_name (
                        course_id, 
                        doc_type, 
                        title, 
                        description, 
                        file_path, 
                        file_name, 
                        file_size, 
                        file_type, 
                        level,
                        uploaded_by
                    ) VALUES (
                        '$course_id',
                        '$doc_type',
                        '$title',
                        '$description',
                        '$relative_file_path',
                        '$file_name',
                        '{$_FILES["document"]["size"]}',
                        '$file_ext',
                        '$level',
                        '$uploaded_by'
                    )";
                    if ($conn->query($sql)) {
                        $success = "Document uploaded successfully to Semester $semester, Level $level!";
                        // Clear form
                        $_POST['document_title'] = $_POST['description'] = '';
                    } else {
                        $error = "Error saving to database: " . $conn->error;
                        // Delete the uploaded file if DB insert failed
                        if (file_exists($target_file)) {
                            unlink($target_file);
                        }
                    }
                }
            } else {
                $error = "Sorry, there was an error uploading your file. ";
                $error .= "Please check file permissions or try again.";
                
                // Debug info (remove in production)
                $error .= " Debug: Target file: " . $target_file;
                $error .= " | Temp file: " . $_FILES["document"]["tmp_name"];
                $error .= " | Writable: " . (is_writable($target_dir) ? 'Yes' : 'No');
            }
        }
    } else {
        $error = "Upload directory is not writable. Please check permissions for: " . $target_dir;
    }
}

// Helper function to get upload error messages
function getUploadError($error_code) {
    $errors = [
        UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
        UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
        UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
    ];
    
    return $errors[$error_code] ?? 'Unknown upload error';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload <?php echo $valid_types[$doc_type]; ?> - <?php echo $course['course_name']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #2ecc71;
            --error-color: #e74c3c;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 2rem;
        }
        
        .upload-container {
            background-color: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            max-width: 700px;
            margin: 2rem auto;
        }
        
        .upload-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .upload-header h2 {
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 1.25rem;
        }
        
        .form-col {
            flex: 1;
        }
        
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 1em;
        }
        
        .upload-target {
            background-color: #f0f8ff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: bold;
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        
        .file-input-wrapper input[type="file"] {
            position: absolute;
            font-size: 100px;
            opacity: 0;
            right: 0;
            top: 0;
            cursor: pointer;
        }
        
        .file-input-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem;
            background-color: #f8f9fa;
            border: 1px dashed #ccc;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .file-input-label:hover {
            background-color: #e9ecef;
        }
        
        .file-info {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            gap: 8px;
        }
        
        .btn-success {
            background-color: var(--success-color);
            color: white;
        }
        
        .btn-success:hover {
            background-color: #27ae60;
        }
        
        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
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
        
        .file-preview {
            margin-top: 1rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #eee;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .upload-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>
    
    <main class="main-content">
        <div class="upload-container">
            <div class="upload-header">
                <h2>
                    <i class="fas fa-upload"></i> 
                    Upload <?php echo $valid_types[$doc_type]; ?> for
                    <?php echo $course['course_name']; ?> (<?php echo $course['course_code']; ?>)
                </h2>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form class="upload-form" method="POST" enctype="multipart/form-data">
                <div class="upload-target" id="uploadTarget">
                    <i class="fas fa-folder-open"></i> 
                    Selected Target: 
                    <span id="targetText">Please select semester and level</span>
                </div>
                
                <div class="form-group">
                    <label for="document_title">Document Title *</label>
                    <input type="text" id="document_title" name="document_title" class="form-control" 
                           value="<?php echo $_POST['document_title'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4"><?php echo $_POST['description'] ?? ''; ?></textarea>
                </div>
                
                <div class="form-row">
                    
                    <div class="form-col">
                        <label for="semester">Semester *</label>
                        <select id="semester" name="semester" class="form-control" required onchange="updateTarget()">
                            <option value="">Select Semester</option>
                            <option value="1" <?php echo ($_POST['semester'] ?? '') == '1' ? 'selected' : ''; ?>>Semester 1</option>
                            <option value="2" <?php echo ($_POST['semester'] ?? '') == '2' ? 'selected' : ''; ?>>Semester 2</option>
                        </select>
                    </div>
                    <div class="form-col">
                        <label for="level">Level *</label>
                        <select id="level" name="level" class="form-control" required onchange="updateTarget()">
                            <option value="">Select Level</option>
                            <option value="certificate" <?php echo ($_POST['level'] ?? '') == 'certificate' ? 'selected' : ''; ?>>Certificate</option>
                            <option value="diploma1" <?php echo ($_POST['level'] ?? '') == 'diploma1' ? 'selected' : ''; ?>>Diploma 1</option>
                            <option value="diploma2" <?php echo ($_POST['level'] ?? '') == 'diploma2' ? 'selected' : ''; ?>>Diploma 2</option>
                            <option value="bachelor1" <?php echo ($_POST['level'] ?? '') == 'bachelor1' ? 'selected' : ''; ?>>Bachelor 1</option>
                            <option value="bachelor2" <?php echo ($_POST['level'] ?? '') == 'bachelor2' ? 'selected' : ''; ?>>Bachelor 2</option>
                            <option value="bachelor3" <?php echo ($_POST['level'] ?? '') == 'bachelor3' ? 'selected' : ''; ?>>Bachelor 3</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="document">Select File *</label>
                    <div class="file-input-wrapper">
                        <label class="file-input-label">
                            <span>
                                <i class="fas fa-cloud-upload-alt"></i>
                                Choose a file...
                            </span>
                            <span class="file-info">Max 10MB (PDF, DOC, PPT, XLS, TXT)</span>
                        </label>
                        <input type="file" id="document" name="document" class="form-control" required>
                    </div>
                    
                    <?php if (isset($_FILES['document'])): ?>
                        <div class="file-preview">
                            <i class="fas fa-file"></i> Selected: <?php echo $_FILES['document']['name']; ?>
                            (<?php echo round($_FILES['document']['size'] / 1024 / 1024, 2); ?> MB)
                        </div>
                    <?php endif; ?>
                </div>
                
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <input type="hidden" name="doc_type" value="<?php echo $doc_type; ?>">
                
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-upload"></i> Upload Document
                </button>
            </form>
        </div>
    </main>

    <script>
        // Show selected file name
        document.getElementById('document').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'No file selected';
            const fileSize = e.target.files[0]?.size ? 
                Math.round(e.target.files[0].size / 1024 / 1024 * 100) / 100 + ' MB' : '';
            
            const previewHTML = `
                <div class="file-preview">
                    <i class="fas fa-file"></i> Selected: ${fileName} (${fileSize})
                </div>
            `;
            
            const existingPreview = document.querySelector('.file-preview');
            if (existingPreview) {
                existingPreview.outerHTML = previewHTML;
            } else {
                document.querySelector('.file-input-wrapper').insertAdjacentHTML('afterend', previewHTML);
            }
        });


        // Update upload target display
        function updateTarget() {
            const semesterSelect = document.getElementById('semester');
            const levelSelect = document.getElementById('level');
            const targetText = document.getElementById('targetText');
            if (semesterSelect.value && levelSelect.value) {
                targetText.textContent = `Semester ${semesterSelect.value} - Level ${levelSelect.value}`;
            } else {
                targetText.textContent = 'Please select semester and level';
            }
        }
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateTarget();
        });
    </script>
</body>
</html>
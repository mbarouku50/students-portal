<?php
include("temperate/header.php");

// Start output buffering to capture content
ob_start();

// Process form data if submitted
$cover_type = $_POST['cover-type'] ?? '';
$course = $_POST['course'] ?? '';
$lecturer = $_POST['lecturer'] ?? '';
$subject = $_POST['subject'] ?? '';
$code = $_POST['code'] ?? '';
$student_name = $_POST['student-name'] ?? '';
$reg_no = $_POST['reg-no'] ?? '';
$program = $_POST['program'] ?? '';
$module_name = $_POST['module-name'] ?? '';
$instructor = $_POST['instructor'] ?? '';
$module_code = $_POST['module-code'] ?? '';
$year = $_POST['year'] ?? date('Y');

// Fix for group members data processing
$members = [];
if (isset($_POST['members']) && is_array($_POST['members'])) {
    foreach ($_POST['members'] as $member) {
        if (!empty($member['name']) || !empty($member['reg'])) {
            $members[] = [
                'name' => $member['name'] ?? '',
                'reg' => $member['reg'] ?? ''
            ];
        }
    }
}

// Check if we need to print
$print_mode = isset($_POST['print']) && $_POST['print'] == 'true';

// If in print mode, generate the cover page and exit
if ($print_mode) {
    // Generate appropriate cover based on type
    if ($cover_type === 'individual') {
        generateIndividualCover($course, $lecturer, $subject, $code, $student_name, $reg_no, $year);
    } else if ($cover_type === 'group') {
        generateGroupCover($program, $module_name, $instructor, $module_code, $year, $members);
    }
    exit;
}

// Function to generate individual cover
function generateIndividualCover($course, $lecturer, $subject, $code, $student_name, $reg_no, $year) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Cover Page</title>
        <style>
            body {
                font-family: 'Times New Roman', serif;
                margin: 0;
                padding: 0;
            }
            .cover-template {
                width: 21cm;
                min-height: 29.7cm;
                padding: 2cm;
                margin: 0 auto;
                background: white;
            }
            .institution-name {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 1.5rem;
                text-transform: uppercase;
                text-align: center;
            }
            .logo-container {
                text-align: center;
                margin-bottom: 1.5rem;
            }
            .logo {
                max-width: 150px;
                height: auto;
            }
            .document-type {
                font-size: 20px;
                font-weight: bold;
                margin: 1.5rem 0;
                text-decoration: underline;
                text-align: center;
            }
            .course-info {
                margin: 1rem 0;
                text-align: left;
            }
            .course-info div {
                margin-bottom: 0.5rem;
            }
            .signature-area {
                margin-top: 3rem;
                display: flex;
                justify-content: space-between;
            }
            .signature-line {
                border-top: 1px solid #000;
                width: 200px;
                text-align: center;
                padding-top: 5px;
            }
            @media print {
                body {
                    margin: 0;
                    padding: 0;
                }
                .cover-template {
                    width: 100%;
                    height: 100%;
                    padding: 0;
                    margin: 0;
                    box-shadow: none;
                }
                .no-print {
                    display: none !important;
                }
            }
        </style>
    </head>
    <body>
        <div class="cover-template individual">
            <div class="institution-name">COLLEGE OF BUSINESS EDUCATION (CBE)<br>DAR ES SALAAM CAMPUS</div>
            <div class="logo-container">
                <img src="CBE_Logo2.png" alt="CBE Logo" class="logo">
            </div>
            
            <div class="document-type">INDIVIDUAL ASSIGNMENT:</div>
            
            <div class="course-info">
                <div><strong>COURSE:</strong> <?php echo htmlspecialchars($course); ?></div>
                <div><strong>LECTURER NAME:</strong> <?php echo htmlspecialchars($lecturer); ?></div>
                <div><strong>SUBJECT:</strong> <?php echo htmlspecialchars($subject); ?></div>
                <div><strong>CODE:</strong> <?php echo htmlspecialchars($code); ?></div>
                <div><strong>NAME:</strong> <?php echo htmlspecialchars($student_name); ?></div>
                <div><strong>REG NO:</strong> <?php echo htmlspecialchars($reg_no); ?></div>
                <div><strong>YEAR:</strong> <?php echo htmlspecialchars($year); ?></div>
            </div>
        
            <div class="signature-area">
                <div class="signature-line">Student's Signature</div>
                <div class="signature-line">Lecturer's Signature</div>
            </div>
        </div>
        
        <div class="no-print" style="text-align: center; margin-top: 20px;">
            <button onclick="window.close()">Close</button>
        </div>
        
        <script>
            // Auto-print when page loads
            window.onload = function() {
                window.print();
            };
        </script>
    </body>
    </html>
    <?php
}

// Function to generate group cover
function generateGroupCover($program, $module_name, $instructor, $module_code, $year, $members) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Cover Page</title>
        <style>
            body {
                font-family: 'Times New Roman', serif;
                margin: 0;
                padding: 0;
            }
            .cover-template {
                width: 21cm;
                min-height: 29.7cm;
                padding: 2cm;
                margin: 0 auto;
                background: white;
            }
            .institution-name {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 1.5rem;
                text-transform: uppercase;
                text-align: center;
            }
            .logo-container {
                text-align: center;
                margin-bottom: 1.5rem;
            }
            .logo {
                max-width: 150px;
                height: auto;
            }
            .course-info {
                margin: 1rem 0;
                text-align: left;
            }
            .course-info div {
                margin-bottom: 0.5rem;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 1.5rem 0;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }
            .signature-area {
                margin-top: 3rem;
                display: flex;
                justify-content: space-between;
            }
            .signature-line {
                border-top: 1px solid #000;
                width: 200px;
                text-align: center;
                padding-top: 5px;
            }
            @media print {
                body {
                    margin: 0;
                    padding: 0;
                }
                .cover-template {
                    width: 100%;
                    height: 100%;
                    padding: 0;
                    margin: 0;
                    box-shadow: none;
                }
                .no-print {
                    display: none !important;
                }
            }
        </style>
    </head>
    <body>
  
        <div class="cover-template group">
            <div class="institution-name">COLLEGE OF BUSINESS EDUCATION<br>DAR ES SALAAM</div>
            <div class="logo-container">
                <img src="CBE_Logo2.png" alt="CBE Logo" class="logo">
            </div>
            
            <div class="course-info">
                <div><strong>PROGRAM:</strong> <?php echo htmlspecialchars($program); ?></div>
                <div><strong>MODULE NAME:</strong> <?php echo htmlspecialchars($module_name); ?></div>
                <div><strong>INSTRUCTOR:</strong> <?php echo htmlspecialchars($instructor); ?></div>
                <div><strong>MODULE CODE:</strong> <?php echo htmlspecialchars($module_code); ?></div>
                <div><strong>YEAR:</strong> <?php echo htmlspecialchars($year); ?></div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>NAME</th>
                        <th>REG NO.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = 1;
                    foreach ($members as $member) {
                        if (!empty($member['name']) || !empty($member['reg'])) {
                            echo "<tr>
                                <td>{$counter}</td>
                                <td>" . htmlspecialchars($member['name'] ?? '') . "</td>
                                <td>" . htmlspecialchars($member['reg'] ?? '') . "</td>
                            </tr>";
                            $counter++;
                        }
                    }
                    ?>
                </tbody>
            </table>
            
            <div class="signature-area">
                <div class="signature-line">Group Representative</div>
                <div class="signature-line">Instructor's Signature</div>
            </div>
        </div>
        
        <div class="no-print" style="text-align: center; margin-top: 20px;">
            <button onclick="window.close()">Close</button>
        </div>
        
        <script>
            // Auto-print when page loads
            window.onload = function() {
                window.print();
            };
        </script>
    </body>
    </html>
    <?php
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Cover Page | University Document System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:  #2c3e50;
            --primary-dark: #3498db;
            --secondary: #f9fafb;
            --dark: #1f2937;
            --light: #f3f4f6;
            --gray: #6b7280;
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
            font-size: 1rem;
        }
        
        .coverpage-hero {
            position: relative;
            padding: 4rem 1.5rem;
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(243, 244, 246, 1) 100%);
            overflow: hidden;
        }
        
        .coverpage-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://source.unsplash.com/random/1920x1080/?university,library') no-repeat center center/cover;
            opacity: 0.03;
            z-index: 1;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
            position: relative;
            z-index: 2;
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
        
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        
        .page-header p {
            color: var(--gray);
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        }
        
        .card-header {
            padding: 1.5rem;
            background: var(--primary);
            color: white;
        }
        
        .card-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            outline: none;
        }
        
        .form-control.error {
            border-color: var(--error);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }
        
        .error-message {
            color: var(--error);
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: none;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.85rem 1.75rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            font-size: 1rem;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(90deg, var(--primary-dark), var(--primary));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-outline:hover {
            background: rgba(79, 70, 229, 0.1);
        }
        
        .btn-success {
            background: linear-gradient(90deg, var(--success), #0da271);
            color: white;
        }
        
        .btn-success:hover {
            background: linear-gradient(90deg, #0da271, var(--success));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        .preview-section {
            margin-top: 3rem;
            <?php if (isset($_POST['generate'])) echo 'display: block;'; else echo 'display: none;'; ?>
        }
        
        .preview-card {
            text-align: center;
        }
        
        .preview-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin: 1.5rem 0;
            border: 1px solid #e5e7eb;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 3rem;
        }
        
        .feature-card {
            padding: 1.5rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .feature-card h3 {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            color: var(--dark);
        }
        
        .feature-card p {
            color: var(--gray);
            font-size: 0.95rem;
        }
        
        /* Additional styles for form fields */
        .dynamic-fields {
            margin-top: 1.5rem;
            padding: 1.5rem;
            background-color: #f9fafb;
            border-radius: 8px;
            border-left: 4px solid var(--primary);
        }
        
        .member-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            align-items: center;
        }
        
        .member-row input {
            flex: 1;
        }
        
        .add-member-btn, .remove-member-btn {
            padding: 0.5rem 1rem;
        }
        
        .remove-member-btn {
            background-color: var(--error);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        /* Preview modal styles */
        .preview-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            overflow-y: auto;
            padding: 20px;
        }
        
        .preview-content {
            background-color: white;
            margin: 2rem auto;
            padding: 2rem;
            border-radius: 12px;
            width: 90%;
            max-width: 800px;
            position: relative;
        }
        
        .preview-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }
        
        .close-preview {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--gray);
        }
        
        /* Cover page template styles */
        .cover-template {
            width: 21cm;
            min-height: 29.7cm;
            padding: 2cm;
            margin: 1rem auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background: white;
            font-family: 'Times New Roman', serif;
        }
        
        .cover-template.individual {
            text-align: center;
        }
        
        .cover-template.group table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
        }
        
        .cover-template.group th, .cover-template.group td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .cover-template.group th {
            background-color: #f2f2f2;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .logo {
            max-width: 150px;
            height: auto;
        }
        
        .institution-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
        }
        
        .document-type {
            font-size: 20px;
            font-weight: bold;
            margin: 1.5rem 0;
            text-decoration: underline;
        }
        
        .course-info {
            margin: 1rem 0;
            text-align: left;
            width: 100%;
        }
        
        .course-info div {
            margin-bottom: 0.5rem;
        }
        
        .signature-area {
            margin-top: 3rem;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            text-align: center;
            padding-top: 5px;
        }
        
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2rem;
            }
            
            .card-body {
                padding: 1.5rem;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .btn {
                width: 100%;
            }
            
            .cover-template {
                width: 100%;
                padding: 1rem;
            }
            
            .member-row {
                flex-direction: column;
                align-items: stretch;
            }
            
            .preview-content {
                padding: 1rem;
                width: 95%;
            }
            
            .preview-actions {
                flex-direction: column;
            }
        }
        
        @media (max-width: 480px) {
            .coverpage-hero {
                padding: 3rem 1rem;
            }
            
            .page-header h1 {
                font-size: 1.75rem;
            }
            
            .page-header p {
                font-size: 1rem;
            }
            
            .signature-area {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>

<body>
    <section class="hero">
        <div class="container">
            <h1>Professional Cover Page Generator</h1>
            <p>Create standardized cover pages for your academic documents in just a few clicks. Select your course and customize the template to match your requirements.</p>
        </div>
    </section>

    <section class="coverpage-hero">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-file-alt"></i> Cover Page Details</h2>
                </div>
                <div class="card-body">
                    <form id="coverpage-form" class="coverpage-form" method="POST">
                        <div class="form-group">
                            <label for="cover-type" class="form-label">Cover Page Type</label>
                            <select name="cover-type" id="cover-type" class="form-control" required onchange="toggleFormFields()">
                                <option value="">-- Select type --</option>
                                <option value="individual" <?php if ($cover_type === 'individual') echo 'selected'; ?>>Individual Assignment</option>
                                <option value="group" <?php if ($cover_type === 'group') echo 'selected'; ?>>Group Project</option>
                            </select>
                            <div class="error-message" id="cover-type-error">Please select a cover page type</div>
                        </div>
                        
                        <div id="individual-fields" style="<?php if ($cover_type !== 'individual') echo 'display: none;'; ?>">
                            <div class="form-group">
                                <label for="course" class="form-label">Course Name</label>
                                <input type="text" name="course" id="course" class="form-control" placeholder="Enter course name" value="<?php echo htmlspecialchars($course); ?>">
                                <div class="error-message" id="course-error">Please enter a course name</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="lecturer" class="form-label">Lecturer Name</label>
                                <input type="text" name="lecturer" id="lecturer" class="form-control" placeholder="Enter lecturer's name" value="<?php echo htmlspecialchars($lecturer); ?>">
                                <div class="error-message" id="lecturer-error">Please enter a lecturer name</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" name="subject" id="subject" class="form-control" placeholder="Enter subject" value="<?php echo htmlspecialchars($subject); ?>">
                                <div class="error-message" id="subject-error">Please enter a subject</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="code" class="form-label">Course Code</label>
                                <input type="text" name="code" id="code" class="form-control" placeholder="Enter course code" value="<?php echo htmlspecialchars($code); ?>">
                                <div class="error-message" id="code-error">Please enter a course code</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="student-name" class="form-label">Student Name</label>
                                <input type="text" name="student-name" id="student-name" class="form-control" placeholder="Enter your name" value="<?php echo htmlspecialchars($student_name); ?>">
                                <div class="error-message" id="student-name-error">Please enter your name</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="reg-no" class="form-label">Registration Number</label>
                                <input type="text" name="reg-no" id="reg-no" class="form-control" placeholder="Enter registration number" value="<?php echo htmlspecialchars($reg_no); ?>">
                                <div class="error-message" id="reg-no-error">Please enter your registration number</div>
                            </div>
                        </div>
                        
                        <div id="group-fields" style="<?php if ($cover_type !== 'group') echo 'display: none;'; ?>">
                            <div class="form-group">
                                <label for="program" class="form-label">Program</label>
                                <input type="text" name="program" id="program" class="form-control" placeholder="e.g., BACHELOR IN INFORMATION TECHNOLOGY" value="<?php echo htmlspecialchars($program); ?>">
                                <div class="error-message" id="program-error">Please enter a program name</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="module-name" class="form-label">Module Name</label>
                                <input type="text" name="module-name" id="module-name" class="form-control" placeholder="e.g., PROGRAMMING IN JAVA" value="<?php echo htmlspecialchars($module_name); ?>">
                                <div class="error-message" id="module-name-error">Please enter a module name</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="instructor" class="form-label">Instructor Name</label>
                                <input type="text" name="instructor" id="instructor" class="form-control" placeholder="e.g., Eng. Dr. Ahmed Kijazi" value="<?php echo htmlspecialchars($instructor); ?>">
                                <div class="error-message" id="instructor-error">Please enter an instructor name</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="module-code" class="form-label">Module Code</label>
                                <input type="text" name="module-code" id="module-code" class="form-control" placeholder="e.g., ITU07312" value="<?php echo htmlspecialchars($module_code); ?>">
                                <div class="error-message" id="module-code-error">Please enter a module code</div>
                            </div>
                            
                            <div class="dynamic-fields">
                                <label class="form-label">Group Members</label>
                                <div id="members-container">
                                    <?php
                                    if (!empty($members)) {
                                        foreach ($members as $index => $member) {
                                            echo '<div class="member-row">
                                                <input type="text" class="form-control member-name" name="members['.$index.'][name]" placeholder="Member Name" value="'.htmlspecialchars($member['name'] ?? '').'">
                                                <input type="text" class="form-control member-reg" name="members['.$index.'][reg]" placeholder="Registration Number" value="'.htmlspecialchars($member['reg'] ?? '').'">
                                                <button type="button" class="remove-member-btn"><i class="fas fa-times"></i></button>
                                            </div>';
                                        }
                                    } else {
                                        echo '<div class="member-row">
                                            <input type="text" class="form-control member-name" name="members[0][name]" placeholder="Member Name">
                                            <input type="text" class="form-control member-reg" name="members[0][reg]" placeholder="Registration Number">
                                            <button type="button" class="remove-member-btn"><i class="fas fa-times"></i></button>
                                        </div>';
                                    }
                                    ?>
                                </div>
                                <button type="button" id="add-member" class="btn btn-outline add-member-btn" style="margin-top: 1rem;">
                                    <i class="fas fa-plus"></i> Add Member
                                </button>
                                <div class="error-message" id="members-error" style="margin-top: 0.5rem;">Please fill all member details</div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="year" class="form-label">Academic Year</label>
                            <input type="number" name="year" id="year" class="form-control" min="2000" max="2025" placeholder="Enter academic year" value="<?php echo htmlspecialchars($year); ?>">
                            <div class="error-message" id="year-error">Please enter a valid academic year</div>
                        </div>
                        
                        <button type="submit" name="generate" value="true" class="btn btn-primary btn-block">
                            <i class="fas fa-magic"></i> Generate Cover Page
                        </button>
                    </form>
                </div>
            </div>
            
            <?php if (isset($_POST['generate'])): ?>
            <div id="preview-modal" class="preview-modal" style="display:block;">
                <div class="preview-content">
                    <button class="close-preview" onclick="closePreview()">&times;</button>
                    <h2>Cover Page Preview</h2>
                    <div id="preview-content">
                        <?php
                        ob_start();
                        if ($cover_type === 'individual') {
                            generateIndividualCover($course, $lecturer, $subject, $code, $student_name, $reg_no, $year);
                        } else if ($cover_type === 'group') {
                            generateGroupCover($program, $module_name, $instructor, $module_code, $year, $members);
                        }
                        $coverHtml = ob_get_clean();
                        echo $coverHtml;
                        ?>
                    </div>
                    <div class="preview-actions">
                        <button class="btn btn-primary" onclick="printCoverOnly()">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <button class="btn btn-success" onclick="downloadCoverPDF()">
                            <i class="fas fa-download"></i> Download PDF
                        </button>
                        <button class="btn btn-outline" onclick="window.location.href=window.location.href.split('?')[0]">
                            <i class="fas fa-plus"></i> Create New
                        </button>
                        <button class="btn btn-outline" onclick="closePreview()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>
            <script>
            // Automatically open print dialog when modal is shown
            window.onload = function() {
                var previewContent = document.getElementById('preview-content');
                if (previewContent) {
                    setTimeout(function() {
                        printCoverOnly();
                    }, 500);
                }
            };
            function printCoverOnly() {
                var printContents = document.getElementById('preview-content').innerHTML;
                var printWindow = window.open('', '', 'height=900,width=800');
                if (!printWindow) {
                    alert('Unable to open print window. Please allow pop-ups for this site.');
                    return;
                }
                printWindow.document.write('<html><head><title></title>');
                printWindow.document.write('<style>body{font-family:Times New Roman,serif;} .cover-template{width:21cm;min-height:29.7cm;padding:2cm;margin:0 auto;background:white;} .logo{max-width:150px;height:auto;} .signature-area{margin-top:3rem;display:flex;justify-content:space-between;} .signature-line{border-top:1px solid #000;width:200px;text-align:center;padding-top:5px;} @media print{body{margin:0;padding:0;} .cover-template{width:100%;height:100%;padding:0;margin:0;box-shadow:none;} .no-print{display:none!important;}}</style>');
                printWindow.document.write('</head><body>');
                printWindow.document.write(printContents);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.focus();
                setTimeout(function(){printWindow.print();}, 500);
            }
            // Download cover page as PDF using html2pdf.js
            function downloadCoverPDF() {
                var element = document.getElementById('preview-content');
                if (!element) {
                    alert('Preview not found!');
                    return;
                }
                // Load html2pdf.js dynamically if not loaded
                if (typeof html2pdf === 'undefined') {
                    var script = document.createElement('script');
                    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
                    script.onload = function() {
                        html2pdf().set({
                            margin: 0.5,
                            filename: 'cover_page.pdf',
                            image: { type: 'jpeg', quality: 0.98 },
                            html2canvas: { scale: 2 },
                            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
                        }).from(element).save();
                    };
                    document.body.appendChild(script);
                } else {
                    html2pdf().set({
                        margin: 0.5,
                        filename: 'cover_page.pdf',
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: { scale: 2 },
                        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
                    }).from(element).save();
                }
            }

            function closePreview() {
                document.getElementById('preview-modal').style.display = 'none';
            }
            </script>
            <?php endif; ?>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>University Standards</h3>
                    <p>All templates follow official university formatting guidelines for academic documents.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Save Time</h3>
                    <p>Generate professional cover pages in seconds instead of manually formatting them.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <h3>PDF Export</h3>
                    <p>Download your cover page as a PDF document ready for submission.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Preview Modal -->
    <div id="preview-modal" class="preview-modal">
        <div class="preview-content">
            <button class="close-preview" onclick="closePreview()">&times;</button>
            <h2>Cover Page Preview</h2>
            <div id="preview-content">
                <!-- Preview content will be inserted here -->
            </div>
            <div class="preview-actions">
                <button class="btn btn-primary" onclick="printCover()">
                    <i class="fas fa-print"></i> Print
                </button>
                <button class="btn btn-outline" onclick="closePreview()">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addMemberBtn = document.getElementById('add-member');
            const membersContainer = document.getElementById('members-container');
            const form = document.getElementById('coverpage-form');
            
            // Add member functionality
            addMemberBtn.addEventListener('click', function() {
                const memberCount = membersContainer.children.length;
                const memberRow = document.createElement('div');
                memberRow.className = 'member-row';
                memberRow.innerHTML = `
                    <input type="text" class="form-control member-name" name="members[${memberCount}][name]" placeholder="Member Name">
                    <input type="text" class="form-control member-reg" name="members[${memberCount}][reg]" placeholder="Registration Number">
                    <button type="button" class="remove-member-btn"><i class="fas fa-times"></i></button>
                `;
                membersContainer.appendChild(memberRow);
                
                // Add event listener to remove button
                memberRow.querySelector('.remove-member-btn').addEventListener('click', function() {
                    if (membersContainer.children.length > 1) {
                        membersContainer.removeChild(memberRow);
                    }
                });
            });
            
            // Add event listeners to existing remove buttons
            document.querySelectorAll('.remove-member-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (membersContainer.children.length > 1) {
                        membersContainer.removeChild(btn.parentElement);
                    }
                });
            });
            
            // Add input validation
            const yearInput = document.getElementById('year');
            if (yearInput) {
                yearInput.addEventListener('input', function() {
                    if (yearInput.value.length > 4) {
                        yearInput.value = yearInput.value.slice(0, 4);
                    }
                });
            }
            
            // Form submission for preview
            form.addEventListener('submit', function(e) {
                // Basic validation
                let isValid = true;
                const coverType = document.getElementById('cover-type').value;
                
                if (!coverType) {
                    document.getElementById('cover-type-error').style.display = 'block';
                    isValid = false;
                } else {
                    document.getElementById('cover-type-error').style.display = 'none';
                }
                
                if (coverType === 'individual') {
                    // Validate individual fields
                    const requiredFields = ['course', 'lecturer', 'subject', 'code', 'student-name', 'reg-no'];
                    requiredFields.forEach(field => {
                        const element = document.getElementById(field);
                        if (!element.value.trim()) {
                            document.getElementById(`${field}-error`).style.display = 'block';
                            isValid = false;
                        } else {
                            document.getElementById(`${field}-error`).style.display = 'none';
                        }
                    });
                } else if (coverType === 'group') {
                    // Validate group fields
                    const requiredFields = ['program', 'module-name', 'instructor', 'module-code'];
                    requiredFields.forEach(field => {
                        const element = document.getElementById(field);
                        if (!element.value.trim()) {
                            document.getElementById(`${field}-error`).display = 'block';
                            isValid = false;
                        } else {
                            document.getElementById(`${field}-error`).style.display = 'none';
                        }
                    });
                    
                    // Validate at least one member
                    const memberNames = document.querySelectorAll('.member-name');
                    let hasMember = false;
                    memberNames.forEach(input => {
                        if (input.value.trim()) hasMember = true;
                    });
                    
                    if (!hasMember) {
                        document.getElementById('members-error').style.display = 'block';
                        isValid = false;
                    } else {
                        document.getElementById('members-error').style.display = 'none';
                    }
                }
                
                // Validate year
                if (!yearInput.value || yearInput.value < 2000 || yearInput.value > 2025) {
                    document.getElementById('year-error').style.display = 'block';
                    isValid = false;
                } else {
                    document.getElementById('year-error').style.display = 'none';
                }
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
        
        function toggleFormFields() {
            const coverType = document.getElementById('cover-type').value;
            const individualFields = document.getElementById('individual-fields');
            const groupFields = document.getElementById('group-fields');
            
            if (coverType === 'individual') {
                individualFields.style.display = 'block';
                groupFields.style.display = 'none';
            } else if (coverType === 'group') {
                individualFields.style.display = 'none';
                groupFields.style.display = 'block';
            } else {
                individualFields.style.display = 'none';
                groupFields.style.display = 'none';
            }
        }
        
        function showPreview() {
            // Get form data
            const formData = new FormData(document.getElementById('coverpage-form'));
            
            // Create a preview of the cover page
            const coverType = formData.get('cover-type');
            let previewHTML = `
                <div class="cover-template ${coverType}">
                    <div class="institution-name">${coverType === 'individual' ? 'COLLEGE OF BUSINESS EDUCATION (CBE)<br>DAR ES SALAAM CAMPUS' : 'COLLEGE OF BUSINESS EDUCATION<br>DAR ES SALAAM'}</div>
                    <div class="logo-container">
                        <img src="CBE_Logo2.png" alt="CBE Logo" class="logo">
                    </div>
            `;
            
            if (coverType === 'individual') {
                previewHTML += `
                    <div class="document-type">INDIVIDUAL ASSIGNMENT:</div>
                    <div class="course-info">
                        <div><strong>COURSE:</strong> ${formData.get('course')}</div>
                        <div><strong>LECTURER NAME:</strong> ${formData.get('lecturer')}</div>
                        <div><strong>SUBJECT:</strong> ${formData.get('subject')}</div>
                        <div><strong>CODE:</strong> ${formData.get('code')}</div>
                        <div><strong>NAME:</strong> ${formData.get('student-name')}</div>
                        <div><strong>REG NO:</strong> ${formData.get('reg-no')}</div>
                        <div><strong>YEAR:</strong> ${formData.get('year')}</div>
                    </div>
                    <div class="signature-area">
                        <div class="signature-line">Student's Signature</div>
                        <div class="signature-line">Lecturer's Signature</div>
                    </div>
                `;
            } else {
                previewHTML += `
                    <div class="course-info">
                        <div><strong>PROGRAM:</strong> ${formData.get('program')}</div>
                        <div><strong>MODULE NAME:</strong> ${formData.get('module-name')}</div>
                        <div><strong>INSTRUCTOR:</strong> ${formData.get('instructor')}</div>
                        <div><strong>MODULE CODE:</strong> ${formData.get('module-code')}</div>
                        <div><strong>YEAR:</strong> ${formData.get('year')}</div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>NAME</th>
                                <th>REG NO.</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                
                // Add group members
                let counter = 1;
                for (let i = 0; i < formData.getAll('members[]').length; i++) {
                    const name = formData.get(`members[${i}][name]`);
                    const reg = formData.get(`members[${i}][reg]`);
                    if (name || reg) {
                        previewHTML += `
                            <tr>
                                <td>${counter}</td>
                                <td>${name || ''}</td>
                                <td>${reg || ''}</td>
                            </tr>
                        `;
                        counter++;
                    }
                }
                
                previewHTML += `
                        </tbody>
                    </table>
                    <div class="signature-area">
                        <div class="signature-line">Group Representative</div>
                        <div class="signature-line">Instructor's Signature</div>
                    </div>
                `;
            }
            
            previewHTML += `</div>`;
            
            // Display preview in modal
            document.getElementById('preview-content').innerHTML = previewHTML;
            document.getElementById('preview-modal').style.display = 'block';
        }
        
        function closePreview() {
            document.getElementById('preview-modal').style.display = 'none';
        }
        
        function printCover() {
            // Submit the form to the print version
            const form = document.getElementById('coverpage-form');
            const printInput = document.createElement('input');
            printInput.type = 'hidden';
            printInput.name = 'print';
            printInput.value = 'true';
            form.appendChild(printInput);
            
            // Open in new tab for printing
            form.target = '_blank';
            form.submit();
            
            // Close the preview
            closePreview();
        }
    </script>
</body>
</html>
<?php
// End output buffering and flush content
ob_end_flush();
?>
<?php
include("temperate/footer.php")
?>
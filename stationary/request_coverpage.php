<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Cover Page | University Document System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
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
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        .preview-section {
            margin-top: 3rem;
            display: none;
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
    <section class="coverpage-hero">
        <div class="container">
            <div class="page-header">
                <h1>Professional Cover Page Generator</h1>
                <p>Create standardized cover pages for your academic documents in just a few clicks. Select your course and customize the template to match your requirements.</p>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-file-alt"></i> Cover Page Details</h2>
                </div>
                <div class="card-body">
                    <form id="coverpage-form" class="coverpage-form">
                        <div class="form-group">
                            <label for="cover-type" class="form-label">Cover Page Type</label>
                            <select name="cover-type" id="cover-type" class="form-control" required onchange="toggleFormFields()">
                                <option value="">-- Select type --</option>
                                <option value="individual">Individual Assignment</option>
                                <option value="group">Group Project</option>
                            </select>
                            <div class="error-message" id="cover-type-error">Please select a cover page type</div>
                        </div>
                        
                        <div id="individual-fields">
                            <div class="form-group">
                                <label for="course" class="form-label">Course Name</label>
                                <input type="text" name="course" id="course" class="form-control" placeholder="Enter course name">
                                <div class="error-message" id="course-error">Please enter a course name</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="lecturer" class="form-label">Lecturer Name</label>
                                <input type="text" name="lecturer" id="lecturer" class="form-control" placeholder="Enter lecturer's name">
                                <div class="error-message" id="lecturer-error">Please enter a lecturer name</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" name="subject" id="subject" class="form-control" placeholder="Enter subject">
                                <div class="error-message" id="subject-error">Please enter a subject</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="code" class="form-label">Course Code</label>
                                <input type="text" name="code" id="code" class="form-control" placeholder="Enter course code">
                                <div class="error-message" id="code-error">Please enter a course code</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="student-name" class="form-label">Student Name</label>
                                <input type="text" name="student-name" id="student-name" class="form-control" placeholder="Enter your name">
                                <div class="error-message" id="student-name-error">Please enter your name</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="reg-no" class="form-label">Registration Number</label>
                                <input type="text" name="reg-no" id="reg-no" class="form-control" placeholder="Enter registration number">
                                <div class="error-message" id="reg-no-error">Please enter your registration number</div>
                            </div>
                        </div>
                        
                        <div id="group-fields" style="display: none;">
                            <div class="form-group">
                                <label for="program" class="form-label">Program</label>
                                <input type="text" name="program" id="program" class="form-control" placeholder="e.g., BACHELOR IN INFORMATION TECHNOLOGY">
                                <div class="error-message" id="program-error">Please enter a program name</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="module-name" class="form-label">Module Name</label>
                                <input type="text" name="module-name" id="module-name" class="form-control" placeholder="e.g., PROGRAMMING IN JAVA">
                                <div class="error-message" id="module-name-error">Please enter a module name</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="instructor" class="form-label">Instructor Name</label>
                                <input type="text" name="instructor" id="instructor" class="form-control" placeholder="e.g., Eng. Dr. Ahmed Kijazi">
                                <div class="error-message" id="instructor-error">Please enter an instructor name</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="module-code" class="form-label">Module Code</label>
                                <input type="text" name="module-code" id="module-code" class="form-control" placeholder="e.g., ITU07312">
                                <div class="error-message" id="module-code-error">Please enter a module code</div>
                            </div>
                            
                            <div class="dynamic-fields">
                                <label class="form-label">Group Members</label>
                                <div id="members-container">
                                    <div class="member-row">
                                        <input type="text" class="form-control member-name" placeholder="Member Name">
                                        <input type="text" class="form-control member-reg" placeholder="Registration Number">
                                        <button type="button" class="remove-member-btn"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                                <button type="button" id="add-member" class="btn btn-outline add-member-btn" style="margin-top: 1rem;">
                                    <i class="fas fa-plus"></i> Add Member
                                </button>
                                <div class="error-message" id="members-error" style="margin-top: 0.5rem;">Please fill all member details</div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="year" class="form-label">Academic Year</label>
                            <input type="number" name="year" id="year" class="form-control" min="2000" max="2025" placeholder="Enter academic year">
                            <div class="error-message" id="year-error">Please enter a valid academic year</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-magic"></i> Generate Cover Page
                        </button>
                    </form>
                </div>
            </div>
            
            <div id="preview-section" class="preview-section">
                <div class="card preview-card">
                    <div class="card-header">
                        <h2><i class="fas fa-eye"></i> Cover Page Preview</h2>
                    </div>
                    <div class="card-body">
                        <div id="cover-preview" class="cover-template"></div>
                        
                        <div class="action-buttons">
                            <button id="download-pdf" class="btn btn-primary">
                                <i class="fas fa-download"></i> Download PDF
                            </button>
                            <button id="print-btn" class="btn btn-outline">
                                <i class="fas fa-print"></i> Print Document
                            </button>
                            <button id="new-cover" class="btn btn-outline">
                                <i class="fas fa-plus"></i> Create New
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('coverpage-form');
            const previewSection = document.getElementById('preview-section');
            const coverPreview = document.getElementById('cover-preview');
            const downloadBtn = document.getElementById('download-pdf');
            const printBtn = document.getElementById('print-btn');
            const newCoverBtn = document.getElementById('new-cover');
            const addMemberBtn = document.getElementById('add-member');
            const membersContainer = document.getElementById('members-container');
            
            // Initialize with first member row
            addInitialMemberRow();
            
            // Add member functionality
            addMemberBtn.addEventListener('click', function() {
                const memberRow = document.createElement('div');
                memberRow.className = 'member-row';
                memberRow.innerHTML = `
                    <input type="text" class="form-control member-name" placeholder="Member Name">
                    <input type="text" class="form-control member-reg" placeholder="Registration Number">
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
            
            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Clear previous errors
                clearErrors();
                
                const coverType = document.getElementById('cover-type').value;
                
                // Validate form
                if (!coverType) {
                    showError('cover-type', 'Please select a cover page type');
                    return;
                }
                
                if (coverType === 'individual') {
                    if (!validateIndividualForm()) {
                        return;
                    }
                    generateIndividualCover();
                } else if (coverType === 'group') {
                    if (!validateGroupForm()) {
                        return;
                    }
                    generateGroupCover();
                }
                
                previewSection.style.display = 'block';
                previewSection.scrollIntoView({ behavior: 'smooth' });
            });
            
            // Download PDF functionality
            downloadBtn.addEventListener('click', function() {
                const element = document.getElementById('cover-preview');
                const opt = {
                    margin: 10,
                    filename: 'cover_page.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { 
                        scale: 2,
                        useCORS: true,
                        logging: true
                    },
                    jsPDF: { 
                        unit: 'mm', 
                        format: 'a4', 
                        orientation: 'portrait' 
                    }
                };
                
                // Clone the element to avoid issues with the original
                const clone = element.cloneNode(true);
                
                // Make sure the clone is visible for rendering
                clone.style.display = 'block';
                clone.style.width = '21cm';
                clone.style.minHeight = '29.7cm';
                clone.style.padding = '2cm';
                clone.style.margin = '0 auto';
                clone.style.boxShadow = '0 0 10px rgba(0, 0, 0, 0.1)';
                clone.style.background = 'white';
                clone.style.fontFamily = "'Times New Roman', serif";
                
                document.body.appendChild(clone);
                
                // Generate PDF from the clone
                html2pdf().set(opt).from(clone).save().then(() => {
                    // Remove the clone after generating the PDF
                    document.body.removeChild(clone);
                });
            });
            
            // Print functionality
            printBtn.addEventListener('click', function() {
                const printContents = document.getElementById('cover-preview').innerHTML;
                const originalContents = document.body.innerHTML;
                
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
                window.location.reload();
            });
            
            // New cover button
            newCoverBtn.addEventListener('click', function() {
                previewSection.style.display = 'none';
                form.reset();
                // Reset member rows
                membersContainer.innerHTML = '';
                addInitialMemberRow();
                clearErrors();
                window.scrollTo(0, 0);
            });
            
            // Add input validation
            const yearInput = document.getElementById('year');
            yearInput.addEventListener('input', function() {
                if (yearInput.value.length > 4) {
                    yearInput.value = yearInput.value.slice(0, 4);
                }
            });
            
            function addInitialMemberRow() {
                const memberRow = document.createElement('div');
                memberRow.className = 'member-row';
                memberRow.innerHTML = `
                    <input type="text" class="form-control member-name" placeholder="Member Name">
                    <input type="text" class="form-control member-reg" placeholder="Registration Number">
                    <button type="button" class="remove-member-btn"><i class="fas fa-times"></i></button>
                `;
                membersContainer.appendChild(memberRow);
                
                // Add event listener to remove button
                memberRow.querySelector('.remove-member-btn').addEventListener('click', function() {
                    if (membersContainer.children.length > 1) {
                        membersContainer.removeChild(memberRow);
                    }
                });
            }
            
            function validateIndividualForm() {
                let isValid = true;
                
                const fields = [
                    {id: 'course', message: 'Please enter a course name'},
                    {id: 'lecturer', message: 'Please enter a lecturer name'},
                    {id: 'subject', message: 'Please enter a subject'},
                    {id: 'code', message: 'Please enter a course code'},
                    {id: 'student-name', message: 'Please enter your name'},
                    {id: 'reg-no', message: 'Please enter your registration number'},
                    {id: 'year', message: 'Please enter a valid academic year'}
                ];
                
                fields.forEach(field => {
                    const element = document.getElementById(field.id);
                    if (!element.value.trim()) {
                        showError(field.id, field.message);
                        isValid = false;
                    }
                });
                
                return isValid;
            }
            
            function validateGroupForm() {
                let isValid = true;
                
                const fields = [
                    {id: 'program', message: 'Please enter a program name'},
                    {id: 'module-name', message: 'Please enter a module name'},
                    {id: 'instructor', message: 'Please enter an instructor name'},
                    {id: 'module-code', message: 'Please enter a module code'},
                    {id: 'year', message: 'Please enter a valid academic year'}
                ];
                
                fields.forEach(field => {
                    const element = document.getElementById(field.id);
                    if (!element.value.trim()) {
                        showError(field.id, field.message);
                        isValid = false;
                    }
                });
                
                // Validate member fields
                const memberNames = document.querySelectorAll('.member-name');
                const memberRegs = document.querySelectorAll('.member-reg');
                let membersValid = true;
                
                for (let i = 0; i < memberNames.length; i++) {
                    if (!memberNames[i].value.trim() || !memberRegs[i].value.trim()) {
                        membersValid = false;
                    }
                }
                
                if (!membersValid) {
                    document.getElementById('members-error').style.display = 'block';
                    isValid = false;
                }
                
                return isValid;
            }
            
            function showError(fieldId, message) {
                const field = document.getElementById(fieldId);
                const errorElement = document.getElementById(fieldId + '-error');
                
                field.classList.add('error');
                errorElement.textContent = message;
                errorElement.style.display = 'block';
            }
            
            function clearErrors() {
                // Remove error classes
                const errorFields = document.querySelectorAll('.form-control.error');
                errorFields.forEach(field => {
                    field.classList.remove('error');
                });
                
                // Hide error messages
                const errorMessages = document.querySelectorAll('.error-message');
                errorMessages.forEach(message => {
                    message.style.display = 'none';
                });
            }
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
        
        function generateIndividualCover() {
            const course = document.getElementById('course').value;
            const lecturer = document.getElementById('lecturer').value;
            const subject = document.getElementById('subject').value;
            const code = document.getElementById('code').value;
            const studentName = document.getElementById('student-name').value;
            const regNo = document.getElementById('reg-no').value;
            const year = document.getElementById('year').value;
            
            const coverHTML = `
                <div class="cover-template individual">
                    <div class="institution-name">COLLEGE OF BUSINESS EDUCATION (CBE)<br>DAR ES SALAAM CAMPUS</div>
                    <div class="logo-container">
                        <!-- Replace with your actual logo path -->
                        <img src="CBE_Logo2.png" alt="CBE Logo" class="logo">
                    </div>
                    
                    <div class="document-type">INDIVIDUAL ASSIGNMENT:</div>
                    
                    <div class="course-info">
                        <div><strong>COURSE:</strong> ${course}</div>
                        <div><strong>LECTURER NAME:</strong> ${lecturer}</div>
                        <div><strong>SUBJECT:</strong> ${subject}</div>
                        <div><strong>CODE:</strong> ${code}</div>
                        <div><strong>NAME:</strong> ${studentName}</div>
                        <div><strong>REG NO:</strong> ${regNo}</div>
                        <div><strong>YEAR:</strong> ${year}</div>
                    </div>
                
                    <div class="signature-area">
                        
                    </div>
                </div>
            `;
            
            document.getElementById('cover-preview').innerHTML = coverHTML;
        }
        
        function generateGroupCover() {
            const program = document.getElementById('program').value;
            const moduleName = document.getElementById('module-name').value;
            const instructor = document.getElementById('instructor').value;
            const moduleCode = document.getElementById('module-code').value;
            const year = document.getElementById('year').value;
            
            // Get group members
            const memberRows = document.querySelectorAll('.member-row');
            let membersHTML = '';
            
            memberRows.forEach((row, index) => {
                const name = row.querySelector('.member-name').value || '__________';
                const reg = row.querySelector('.member-reg').value || '__________';
                membersHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${name}</td>
                        <td>${reg}</td>
                    </tr>
                `;
            });
            
            const coverHTML = `
                <div class="cover-template group">
                    <div class="institution-name">COLLEGE OF BUSINESS EDUCATION<br>DAR ES SALAAM</div>
                    <div class="logo-container">
                        <img src="CBE_Logo2.png" alt="CBE Logo" class="logo">
                    </div>
                    
                    <div class="course-info">
                        <div><strong>PROGRAM:</strong> ${program}</div>
                        <div><strong>MODULE NAME:</strong> ${moduleName}</div>
                        <div><strong>INSTRUCTOR:</strong> ${instructor}</div>
                        <div><strong>MODULE CODE:</strong> ${moduleCode}</div>
                        <div><strong>YEAR:</strong> ${year}</div>
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
                            ${membersHTML}
                        </tbody>
                    </table>
                    
                    <div class="signature-area">
                       
                    </div>
                </div>
            `;
            
            document.getElementById('cover-preview').innerHTML = coverHTML;
        }
    </script>
</body>
</html>
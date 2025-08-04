<?php
include("temperate/header.php")
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
                            <label for="course" class="form-label">Select Course</label>
                            <select name="course" id="course" class="form-control" required>
                                <option value="">-- Select your course --</option>
                                <option value="it">Information Technology</option>
                                <option value="accounting">Accounting</option>
                                <option value="business">Business Administration</option>
                                <option value="metrology">Metrology</option>
                                <option value="hr">Human Resource</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="cover-type" class="form-label">Cover Page Type</label>
                            <select name="cover-type" id="cover-type" class="form-control" required>
                                <option value="">-- Select type --</option>
                                <option value="individual">Individual Assignment</option>
                                <option value="group">Group Project</option>
                                <option value="thesis">Thesis/Dissertation</option>
                                <option value="report">Research Report</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="year" class="form-label">Academic Year</label>
                            <input type="number" name="year" id="year" class="form-control" min="2000" max="2025" placeholder="Enter academic year" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="title" class="form-label">Document Title (Optional)</label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Enter your document title">
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
                        <img id="preview-image" src="" alt="Cover Page Preview" class="preview-image">
                        
                        <div class="action-buttons">
                            <a href="#" id="view-btn" class="btn btn-primary">
                                <i class="fas fa-expand"></i> View Fullscreen
                            </a>
                            <a href="#" id="download-btn" class="btn btn-outline">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                            <a href="print_documents.php" class="btn btn-outline">
                                <i class="fas fa-print"></i> Print Document
                            </a>
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
                    <h3>Multiple Formats</h3>
                    <p>Download in PDF, Word, or PNG formats to suit your needs.</p>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('coverpage-form');
            const previewSection = document.getElementById('preview-section');
            const previewImage = document.getElementById('preview-image');
            const downloadBtn = document.getElementById('download-btn');
            const viewBtn = document.getElementById('view-btn');
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const course = document.getElementById('course').value;
                const coverType = document.getElementById('cover-type').value;
                const year = document.getElementById('year').value;
                const title = document.getElementById('title').value || 'Document Title';
                
                // In a real implementation, this would call your backend to generate the cover page
                // For demo purposes, we're using a placeholder
                const mockImage = `https://via.placeholder.com/600x800/4f46e5/ffffff?text=${encodeURIComponent(title)}\n${course.toUpperCase()}\n${year}\n${coverType}`;
                
                previewImage.src = mockImage;
                downloadBtn.href = mockImage;
                viewBtn.href = mockImage;
                previewSection.style.display = 'block';
                
                // Scroll to preview section
                previewSection.scrollIntoView({ behavior: 'smooth' });
            });
            
            // Add input validation
            const yearInput = document.getElementById('year');
            yearInput.addEventListener('input', function() {
                if (yearInput.value.length > 4) {
                    yearInput.value = yearInput.value.slice(0, 4);
                }
            });
        });
    </script>

<?php
include("temperate/footer.php")
?>
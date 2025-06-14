<?php include("template/header.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Coverpage</title>
    <!-- Responsive viewport for mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


<style>
    body {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        background-color: #f3f4f6;
        margin: 0;
        color: #1f2937;
        font-size: 1rem;
        line-height: 1.6;
    }

    .coverpage-section {
        padding: 5rem 1.5rem;
        background: linear-gradient(135deg, #e0e7ff 0%, #f1f5f9 100%);
        position: relative;
        overflow: hidden;
    }

    .coverpage-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('https://source.unsplash.com/random/1920x1080/?pattern') no-repeat center center/cover;
        opacity: 0.05;
        z-index: 1;
    }

    .container {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 1.5rem;
        position: relative;
        z-index: 2;
    }

    h2 {
        font-size: 2.5rem;
        font-weight: 800;
        color: #1f2937;
        text-align: center;
        margin-bottom: 2.5rem;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .coverpage-form-container {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        padding: 2.5rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(8px);
    }

    .coverpage-form label {
        display: block;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }

    .coverpage-form select,
    .coverpage-form input {
        width: 100%;
        padding: 0.85rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 1.1rem;
        box-sizing: border-box;
        transition: all 0.3s ease;
        background: #fff;
    }

    .coverpage-form select:focus,
    .coverpage-form input:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    .coverpage-preview {
        margin-top: 2.5rem;
        padding: 2rem;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        text-align: center;
        backdrop-filter: blur(8px);
    }

    .coverpage-preview img {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .coverpage-preview .btn {
        display: inline-block;
        padding: 0.85rem 1.75rem;
        background: linear-gradient(90deg, #4f46e5, #7c3aed);
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1.1rem;
        margin: 0.5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .coverpage-preview .btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
    }

    @media (max-width: 768px) {
        h2 {
            font-size: 2rem;
        }

        .coverpage-form-container,
        .coverpage-preview {
            padding: 2rem;
        }

        body {
            font-size: 1.1rem;
        }
    }

    @media (max-width: 480px) {
        .coverpage-section {
            padding: 3rem 1rem;
        }

        .container {
            padding: 0 1rem;
        }

        h2 {
            font-size: 1.75rem;
        }

        .coverpage-form-container,
        .coverpage-preview {
            padding: 1.5rem;
        }

        body {
            font-size: 1.15rem;
        }

        .coverpage-form label {
            font-size: 1rem;
        }

        .coverpage-form select,
        .coverpage-form input {
            font-size: 1rem;
        }

        .coverpage-preview .btn {
            font-size: 1rem;
            padding: 0.75rem 1.5rem;
        }
    }
</style>
</head>

<section class="coverpage-section">
    <div class="container">
        <h2>Request Coverpage</h2>
        <div class="coverpage-form-container">
            <form id="coverpage-form" class="coverpage-form">
                <label for="course">Select Course:</label>
                <select name="course" id="course" required>
                    <option value="">-- Select Course --</option>
                    <option value="cs">Information Technology</option>
                    <option value="eng">Accounting</option>
                    <option value="biz">Business Administration</option>
                    <option value="biz">Metrology</option>
                    <option value="biz">Human Resource</option>
                </select>

                <label for="cover-type">Coverpage Type:</label>
                <select name="cover-type" id="cover-type" required>
                    <option value="">-- Select Type --</option>
                    <option value="individual">Individual</option>
                    <option value="group">Group</option>
                </select>

                <label for="year">Year:</label>
                <input type="number" name="year" id="year" min="2000" max="2025" placeholder="Enter year" required>

                <button type="submit" class="btn">Generate Coverpage</button>
            </form>
        </div>

        <div class="coverpage-preview" id="coverpage-preview" style="display: none;">
            <h3>Preview Coverpage</h3>
            <img id="preview-image" src="" alt="Coverpage Preview">
            <div>
                <a href="#" id="view-btn" class="btn">View</a>
                <a href="#" id="download-btn" class="btn">Download</a>
                <a href="print_documents.php" class="btn">Print</a>
            </div>
        </div>
    </div>
</section>

<script>
    const form = document.getElementById('coverpage-form');
    const previewSection = document.getElementById('coverpage-preview');
    const previewImage = document.getElementById('preview-image');
    const downloadBtn = document.getElementById('download-btn');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const course = document.getElementById('course').value;
        const coverType = document.getElementById('cover-type').value;
        const year = document.getElementById('year').value;

        const mockImage = `https://via.placeholder.com/300x400?text=${course}+${coverType}+${year}`;
        previewImage.src = mockImage;
        downloadBtn.href = mockImage;
        previewSection.style.display = 'block';
    });
</script>

<?php include("template/footer.php"); ?>
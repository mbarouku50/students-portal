<?php include("template/header.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Documents</title>
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

    .print-section {
        padding: 5rem 1.5rem;
        background: linear-gradient(135deg, #e0e7ff 0%, #f1f5f9 100%);
        position: relative;
        overflow: hidden;
    }

    .print-section::before {
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

    .print-form-container {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        padding: 2.5rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(8px);
    }

    .print-form label {
        display: block;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }

    .print-form input,
    .print-form select,
    .print-form textarea {
        width: 100%;
        padding: 0.85rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 1.1rem;
        box-sizing: border-box;
        transition: all 0.3s ease;
        background: #fff;
    }

    .print-form input:focus,
    .print-form textarea:focus,
    .print-form select:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    .print-form textarea {
        resize: vertical;
        min-height: 120px;
    }

    .print-form .btn {
        display: block;
        width: 100%;
        padding: 1rem;
        background: linear-gradient(90deg, #4f46e5, #7c3aed);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.2rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        margin-top: 2rem;
    }

    .print-form .btn:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
    }

    @media (max-width: 768px) {
        h2 {
            font-size: 2rem;
        }

        .print-form-container {
            padding: 2rem;
        }

        body {
            font-size: 1.1rem;
        }
    }

    @media (max-width: 480px) {
        .print-section {
            padding: 3rem 1rem;
        }

        .container {
            padding: 0 1rem;
        }

        h2 {
            font-size: 1.75rem;
        }

        .print-form-container {
            padding: 1.5rem;
        }

        body {
            font-size: 1.15rem;
        }

        .print-form label {
            font-size: 1rem;
        }

        .print-form input,
        .print-form select,
        .print-form textarea {
            font-size: 1rem;
        }

        .print-form .btn {
            font-size: 1.1rem;
        }
    }
</style>
</head>

<section class="print-section">
    <div class="container">
        <h2>Print Documents</h2>
        <div class="print-form-container">
            <form action="upload_print.php" method="POST" enctype="multipart/form-data" class="print-form">
                <label for="file">Upload Document:</label>
                <input type="file" name="file" id="file" accept=".pdf,.doc,.docx,.ppt,.pptx" required>

                <label for="name">Full Name:</label>
                <input type="text" name="name" id="name" placeholder="Enter your full name" required>

                <label for="phone">Phone Number:</label>
                <input type="tel" name="phone" id="phone" placeholder="Enter your phone number" required>

                <label for="station">Select Printing Station:</label>
                <select name="station" id="station" required>
                    <option value="">-- Select Station --</option>
                    <option value="station1">Mbuya Stationary</option>
                    <option value="station2">Ubaya Stationary</option>
                    <option value="station3">Hatuchezi Stationary</option>
                    <option value="station4">Chapa Chap</option>
                    <option value="station5">Fast Print</option>
                    <option value="station6">Quality Prints</option>
                </select>

                <label for="copies">Number of Copies:</label>
                <input type="number" name="copies" id="copies" min="1" value="1" required>

                <label for="color">Print Type:</label>
                <select name="color" id="color" required>
                    <option value="blackwhite">Black & White</option>
                    <option value="color">Color</option>
                </select>

                <label for="notes">Special Instructions:</label>
                <textarea name="notes" id="notes" rows="4" placeholder="e.g., double-sided, staple"></textarea>

                <button type="submit" class="btn">Submit Print Request</button>
            </form>
        </div>
    </div>
</section>

<?php include("template/footer.php"); ?>
<?php
include("temperate/header.php");
include("../connection.php");

// Handle form submission for typed content
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['typed-content'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $stationery_id = intval($_POST['station']);
    $content = $_POST['typed-content']; // HTML content from editor
    $copies = intval($_POST['copies']);
    $color = $_POST['color'] == 'color' ? 'color' : 'black';
    $notes = $conn->real_escape_string($_POST['notes']);

    // Convert HTML content to .doc file
    $doc_filename = 'typed_' . time() . '_' . rand(1000,9999) . '.doc';
    $upload_dir = __DIR__ . '/uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $doc_path = $upload_dir . $doc_filename;
    $doc_content = "<html><body>" . $content . "</body></html>";
    file_put_contents($doc_path, $doc_content);

    // Save relative path for file in DB
    $sql = "INSERT INTO print_jobs (user_name, phone_number, stationery_id, content, copies, print_type, special_instructions, file_path)
            VALUES ('$name', '$phone', $stationery_id, '', $copies, '$color', '$notes', 'uploads/$doc_filename')";

    if ($conn->query($sql)) {
        echo "<script>alert('Typed document converted to .doc and submitted for printing!');</script>";
    } else {
        echo "<script>alert('Error submitting print job: " . $conn->error . "');</script>";
    }
}

// Fetch all stationery items
$stationeryItems = [];
$result = $conn->query("SELECT * FROM stationery ORDER BY stationery_id DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $stationeryItems[] = $row;
    }
}
// Get selected stationery_id from URL if present
$selected_stationery_id = isset($_GET['stationery_id']) ? intval($_GET['stationery_id']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Options</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color:#2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --success-color: #27ae60;
            --text-color: #333;
            --border-radius: 8px;
            --box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            --transition: all 0.3s ease;
        }
        
        body { 
            background: #f5f5f5; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
            line-height: 1.6;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .print-options-section { 
            padding: 2rem 0; 
        }
        
        .section-title {
            text-align: center;
            color: var(--secondary-color);
            margin-bottom: 2.5rem;
            font-size: 2.2rem;
            position: relative;
            padding-bottom: 15px;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }
        
        .option-tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .tab-button {
            background: #fff;
            border: none;
            border-radius: var(--border-radius);
            padding: 1rem 1.8rem;
            font-size: 1.1rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: var(--box-shadow);
            margin: 0 5px;
        }
        
        .tab-button:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .tab-button.active {
            background: var(--primary-color);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.3);
        }
        
        .tab-content {
            display: none;
            background: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 2.5rem;
            margin: 0 auto;
            max-width: 800px;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .tab-content.active {
            display: block;
        }
        
        .tab-header {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--secondary-color);
            font-size: 1.5rem;
        }
        
        .editor-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 1.2rem;
            padding: 12px;
            background: #f8f9fa;
            border-radius: var(--border-radius);
        }
        
        .editor-toolbar button, .editor-toolbar select {
            background: white;
            border: 1px solid #ddd;
            padding: 0.5rem 0.8rem;
            border-radius: 4px;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .editor-toolbar button:hover, .editor-toolbar select:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .editor-area {
            min-height: 250px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            padding: 1rem;
            font-size: 1.1rem;
            background: #fafbfc;
            margin-bottom: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--secondary-color);
        }
        
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .btn-main { 
            background: var(--success-color); 
            color: #fff; 
            border: none; 
            border-radius: var(--border-radius); 
            padding: 0.9rem 2rem; 
            font-size: 1.1rem; 
            cursor: pointer; 
            transition: var(--transition);
            display: block;
            width: 100%;
            font-weight: 600;
        }
        
        .btn-main:hover { 
            background: #219150; 
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
        }
        
        .file-upload-wrapper {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .file-upload-wrapper input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        
        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2.5rem;
            border: 2px dashed #ddd;
            border-radius: var(--border-radius);
            background: #f8f9fa;
            text-align: center;
            transition: var(--transition);
        }
        
        .file-upload-label:hover {
            border-color: var(--primary-color);
            background: #e8f4fd;
        }
        
        .file-upload-label i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .file-upload-label h4 {
            margin-bottom: 0.5rem;
            color: var(--secondary-color);
        }
        
        .file-upload-label p {
            color: #777;
            margin-bottom: 0;
        }
        
        .error-message {
            color: var(--accent-color);
            margin-bottom: 1rem;
            padding: 0.8rem;
            background: #fdeded;
            border-radius: var(--border-radius);
            display: none;
        }
        
        @media (max-width: 768px) {
            .option-tabs {
                flex-direction: column;
            }
            
            .tab-button {
                width: 100%;
                margin-bottom: 10px;
                justify-content: center;
            }
            
            .tab-content {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
<section class="print-options-section">
    <div class="container">
        <h2 class="section-title">Choose How You Want to Print</h2>
        
        <div class="option-tabs">
            <button class="tab-button active" data-tab="upload">
                <i class="fas fa-upload"></i>
                Upload Document
            </button>
            <button class="tab-button" data-tab="editor">
                <i class="fas fa-keyboard"></i>
                Type Document
            </button>
            <button class="tab-button" data-tab="edit">
                <i class="fas fa-edit"></i>
                Edit & Print
            </button>
        </div>
        
        <!-- Upload Document Section -->
        <div class="tab-content active" id="upload-tab">
            <h3 class="tab-header">Upload Your Document</h3>
            <form action="upload_print.php" method="POST" enctype="multipart/form-data">
                <div class="file-upload-wrapper">
                    <div class="file-upload-label">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <h4>Click to Upload Document</h4>
                        <p>Supported formats: PDF, DOC, DOCX, PPT, PPTX</p>
                    </div>
                    <input type="file" name="file" id="file" accept=".pdf,.doc,.docx,.ppt,.pptx" required>
                </div>
                <div class="form-group">
                    <label for="name-upload">Full Name</label>
                    <input type="text" name="name" id="name-upload" required>
                </div>
                <div class="form-group">
                    <label for="phone-upload">Phone Number</label>
                    <input type="tel" name="phone" id="phone-upload" required>
                </div>
                <div class="form-group">
                    <label for="station-upload">Select Printing Station</label>
                    <select name="station" id="station-upload" required>
                        <option value="">-- Select Station --</option>
                        <?php foreach ($stationeryItems as $item): ?>
                            <option value="<?= htmlspecialchars($item['stationery_id']) ?>" <?= ($selected_stationery_id == $item['stationery_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($item['name']) ?> (<?= htmlspecialchars($item['location']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="copies-upload">Number of Copies</label>
                    <input type="number" name="copies" id="copies-upload" min="1" value="1" required>
                </div>
                <div class="form-group">
                    <label for="color-upload">Print Type</label>
                    <select name="color" id="color-upload" required>
                        <option value="black">Black & White</option>
                        <option value="color">Color</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="notes-upload">Special Instructions</label>
                    <textarea name="notes" id="notes-upload" rows="3" placeholder="e.g., double-sided, staple"></textarea>
                </div>
                <button type="submit" class="btn-main">Submit for Printing</button>
            </form>
        </div>
        
        <!-- Type Document Section -->
        <div class="tab-content" id="editor-tab">
            <h3 class="tab-header">Type Your Document</h3>
            <form action="print_option.php" method="POST" onsubmit="return saveAndSubmitEditorContent(this)">
                <div class="editor-toolbar">
                    <button type="button" onclick="format('bold')" title="Bold"><b>B</b></button>
                    <button type="button" onclick="format('italic')" title="Italic"><i>I</i></button>
                    <button type="button" onclick="format('underline')" title="Underline"><u>U</u></button>
                    <button type="button" onclick="format('insertUnorderedList')" title="Bullet List"><i class='fas fa-list-ul'></i></button>
                    <button type="button" onclick="format('insertOrderedList')" title="Numbered List"><i class='fas fa-list-ol'></i></button>
                    <select id="font-family" onchange="setFontFamily(this.value)" title="Font Family">
                        <option value="">Font</option>
                        <option value="Arial">Arial</option>
                        <option value="Times New Roman">Times New Roman</option>
                        <option value="Courier New">Courier New</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Verdana">Verdana</option>
                    </select>
                    <select id="font-size" onchange="setFontSize(this.value)" title="Font Size">
                        <option value="">Size</option>
                        <option value="1">8pt</option>
                        <option value="2">10pt</option>
                        <option value="3">12pt</option>
                        <option value="4">14pt</option>
                        <option value="5">18pt</option>
                        <option value="6">24pt</option>
                        <option value="7">36pt</option>
                    </select>
                    <button type="button" onclick="format('justifyLeft')" title="Align Left"><i class="fas fa-align-left"></i></button>
                    <button type="button" onclick="format('justifyCenter')" title="Align Center"><i class="fas fa-align-center"></i></button>
                    <button type="button" onclick="format('justifyRight')" title="Align Right"><i class="fas fa-align-right"></i></button>
                </div>
                <div class="form-group">
                    <label for="editor-area">Document Content</label>
                    <div id="editor-area" class="editor-area" contenteditable="true"></div>
                    <input type="hidden" name="typed-content" id="typed-content-hidden">
                </div>
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" name="phone" id="phone" required>
                </div>
                <div class="form-group">
                    <label for="station">Select Printing Station</label>
                    <select name="station" id="station" required>
                        <option value="">-- Select Station --</option>
                        <?php foreach ($stationeryItems as $item): ?>
                            <option value="<?= htmlspecialchars($item['stationery_id']) ?>" <?= ($selected_stationery_id == $item['stationery_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($item['name']) ?> (<?= htmlspecialchars($item['location']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="copies">Number of Copies</label>
                    <input type="number" name="copies" id="copies" min="1" value="1" required>
                </div>
                <div class="form-group">
                    <label for="color">Print Type</label>
                    <select name="color" id="color" required>
                        <option value="black">Black & White</option>
                        <option value="color">Color</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="notes">Special Instructions</label>
                    <textarea name="notes" id="notes" rows="3" placeholder="e.g., double-sided, staple"></textarea>
                </div>
                <button type="submit" class="btn-main">Submit for Printing</button>
            </form>
        </div>
        
        <!-- Edit & Print Section -->
        <div class="tab-content" id="edit-tab">
            <h3 class="tab-header">Upload and Edit Your Document</h3>
            <form action="print_option.php" id="edit-upload-form" enctype="multipart/form-data" onsubmit="return handleEditUpload(event)">
                <div class="file-upload-wrapper">
                    <div class="file-upload-label">
                        <i class="fas fa-file-edit"></i>
                        <h4>Click to Upload Document for Editing</h4>
                        <p>Supported formats: TXT, DOC, DOCX</p>
                    </div>
                    <input type="file" name="edit-file" id="edit-file" accept=".txt,.doc,.docx" required>
                </div>
                <div id="edit-upload-error" class="error-message"></div>
                <button type="submit" class="btn-main">Load for Editing</button>
            </form>
            <form action="upload_print.php" method="POST" id="edit-form" style="display:none;">
                <div class="editor-toolbar">
                    <button type="button" onclick="formatEdit('bold')" title="Bold"><b>B</b></button>
                    <button type="button" onclick="formatEdit('italic')" title="Italic"><i>I</i></button>
                    <button type="button" onclick="formatEdit('underline')" title="Underline"><u>U</u></button>
                    <button type="button" onclick="formatEdit('insertUnorderedList')" title="Bullet List"><i class='fas fa-list-ul'></i></button>
                    <button type="button" onclick="formatEdit('insertOrderedList')" title="Numbered List"><i class='fas fa-list-ol'></i></button>
                    <select id="edit-font-family" onchange="setEditFontFamily(this.value)" title="Font Family">
                        <option value="">Font</option>
                        <option value="Arial">Arial</option>
                        <option value="Times New Roman">Times New Roman</option>
                        <option value="Courier New">Courier New</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Verdana">Verdana</option>
                    </select>
                    <select id="edit-font-size" onchange="setEditFontSize(this.value)" title="Font Size">
                        <option value="">Size</option>
                        <option value="1">8pt</option>
                        <option value="2">10pt</option>
                        <option value="3">12pt</option>
                        <option value="4">14pt</option>
                        <option value="5">18pt</option>
                        <option value="6">24pt</option>
                        <option value="7">36pt</option>
                    </select>
                    <button type="button" onclick="formatEdit('justifyLeft')" title="Align Left"><i class="fas fa-align-left"></i></button>
                    <button type="button" onclick="formatEdit('justifyCenter')" title="Align Center"><i class="fas fa-align-center"></i></button>
                    <button type="button" onclick="formatEdit('justifyRight')" title="Align Right"><i class="fas fa-align-right"></i></button>
                </div>
                <div class="form-group">
                    <label for="edit-area">Edit Document</label>
                    <div id="edit-area" class="editor-area" contenteditable="true"></div>
                    <input type="hidden" name="edit-content" id="edit-content-hidden">
                </div>
                <div class="form-group">
                    <label for="name-edit">Full Name</label>
                    <input type="text" name="name" id="name-edit" required>
                </div>
                <div class="form-group">
                    <label for="phone-edit">Phone Number</label>
                    <input type="tel" name="phone" id="phone-edit" required>
                </div>
                <div class="form-group">
                    <label for="station-edit">Select Printing Station</label>
                    <select name="station" id="station-edit" required>
                        <option value="">-- Select Station --</option>
                        <?php foreach ($stationeryItems as $item): ?>
                            <option value="<?= htmlspecialchars($item['stationery_id']) ?>" <?= ($selected_stationery_id == $item['stationery_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($item['name']) ?> (<?= htmlspecialchars($item['location']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="copies-edit">Number of Copies</label>
                    <input type="number" name="copies" id="copies-edit" min="1" value="1" required>
                </div>
                <div class="form-group">
                    <label for="color-edit">Print Type</label>
                    <select name="color" id="color-edit" required>
                        <option value="black">Black & White</option>
                        <option value="color">Color</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="notes-edit">Special Instructions</label>
                    <textarea name="notes" id="notes-edit" rows="3" placeholder="e.g., double-sided, staple"></textarea>
                </div>
                <button type="submit" class="btn-main">Submit for Printing</button>
            </form>
        </div>
    </div>
</section>

<script>
// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabId = button.getAttribute('data-tab');
            
            // Update active button
            tabButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            // Show active tab content
            tabContents.forEach(content => content.classList.remove('active'));
            document.getElementById(`${tabId}-tab`).classList.add('active');
        });
    });
    
    // Initialize the editor area with some basic styling
    document.getElementById('editor-area').innerHTML = '<p>Start typing your document here...</p>';
});

// Text formatting functions for the editor
function format(cmd) {
    document.execCommand(cmd, false, null);
    document.getElementById('editor-area').focus();
}

function formatEdit(cmd) {
    document.execCommand(cmd, false, null);
    document.getElementById('edit-area').focus();
}

function setFontFamily(font) {
    if(font) document.execCommand('fontName', false, font);
}

function setEditFontFamily(font) {
    if(font) document.execCommand('fontName', false, font);
}

function setFontSize(size) {
    if(size) document.execCommand('fontSize', false, size);
}

function setEditFontSize(size) {
    if(size) document.execCommand('fontSize', false, size);
}

function saveEditorContent() {
    document.getElementById('typed-content-hidden').value = document.getElementById('editor-area').innerHTML;
}

function saveEditContent() {
    document.getElementById('edit-content-hidden').value = document.getElementById('edit-area').innerHTML;
}

function handleEditUpload(event) {
    event.preventDefault();
    const fileInput = document.getElementById('edit-file');
    const file = fileInput.files[0];
    const errorDiv = document.getElementById('edit-upload-error');
    errorDiv.style.display = 'none';
    
    if (!file) {
        errorDiv.textContent = 'Please select a file to upload.';
        errorDiv.style.display = 'block';
        return false;
    }
    
    const ext = file.name.split('.').pop().toLowerCase();
    if (ext !== 'txt' && ext !== 'doc' && ext !== 'docx') {
        errorDiv.textContent = 'Only .txt, .doc, or .docx files are supported for editing. Please upload a valid file.';
        errorDiv.style.display = 'block';
        return false;
    }

    if (ext === 'txt') {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('edit-area').innerText = e.target.result;
            document.getElementById('edit-form').style.display = 'block';
            document.getElementById('edit-form').scrollIntoView({ behavior: 'smooth' });
        };
        reader.readAsText(file);
    } else if (ext === 'doc') {
        // For .doc, allow user to download, edit offline, and re-upload
        errorDiv.innerHTML = 'DOC editing is not supported in-browser.<br>You can <a id="downloadDoc" href="#" style="color:blue;text-decoration:underline;">download</a> and edit offline, then re-upload.<br>Or convert to TXT for editing.';
        errorDiv.style.display = 'block';
        // Optionally, provide download link for uploaded file
        const reader = new FileReader();
        reader.onload = function(e) {
            const blob = new Blob([e.target.result], { type: 'application/msword' });
            const url = URL.createObjectURL(blob);
            document.getElementById('downloadDoc').href = url;
            document.getElementById('downloadDoc').download = file.name;
        };
        reader.readAsArrayBuffer(file);
    } else if (ext === 'docx') {
        errorDiv.textContent = 'DOCX editing is not supported in-browser. You can upload and print, or convert to TXT for editing.';
        errorDiv.style.display = 'block';
    }
    return false;
}

function saveAndSubmitEditorContent(form) {
    // Save editor content to hidden input
    document.getElementById('typed-content-hidden').value = document.getElementById('editor-area').innerHTML;
    return true;
}

// Enhance file input UX
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function() {
        const label = this.previousElementSibling;
        if (this.files.length > 0) {
            label.querySelector('h4').textContent = this.files[0].name;
            label.querySelector('p').textContent = 'Click to change file';
        } else {
            label.querySelector('h4').textContent = 'Click to Upload Document';
            label.querySelector('p').textContent = 'Supported formats: PDF, DOC, DOCX, PPT, PPTX';
        }
    });
});
</script>

<?php include("temperate/footer.php"); ?>
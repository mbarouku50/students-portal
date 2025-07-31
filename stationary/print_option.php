<?php
include("temperate/header.php");
include("../connection.php");
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
        body { background: #f5f5f5; }
        .print-options-section { padding: 3rem 0; }
        .option-cards { display: flex; flex-wrap: wrap; gap: 2rem; justify-content: center; }
        .option-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); padding: 2rem 2.5rem; text-align: center; width: 320px; transition: box-shadow 0.3s; }
        .option-card:hover { box-shadow: 0 8px 32px rgba(52,152,219,0.15); }
        .option-card i { font-size: 2.5rem; color: #3498db; margin-bottom: 1rem; }
        .option-card h3 { margin-bottom: 0.7rem; color: #2c3e50; }
        .option-card p { color: #555; margin-bottom: 1.5rem; }
        .option-card button { background: #3498db; color: #fff; border: none; border-radius: 6px; padding: 0.7rem 1.5rem; font-size: 1.1rem; cursor: pointer; transition: background 0.2s; }
        .option-card button:hover { background: #217dbb; }
        .editor-section, .upload-section, .edit-section { display: none; background: #fff; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); padding: 2rem; margin: 2rem auto; max-width: 700px; }
        .editor-toolbar { margin-bottom: 1rem; }
        .editor-toolbar button { background: #ecf0f1; border: none; margin-right: 0.5rem; padding: 0.5rem 0.8rem; border-radius: 4px; cursor: pointer; }
        .editor-toolbar button.active, .editor-toolbar button:hover { background: #3498db; color: #fff; }
        .editor-area { min-height: 250px; border: 1px solid #ddd; border-radius: 6px; padding: 1rem; font-size: 1.1rem; background: #fafbfc; }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; margin-bottom: 0.4rem; font-weight: 500; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.7rem; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem; }
        .form-group textarea { min-height: 100px; }
        .btn-main { background: #27ae60; color: #fff; border: none; border-radius: 6px; padding: 0.8rem 2rem; font-size: 1.1rem; cursor: pointer; transition: background 0.2s; }
        .btn-main:hover { background: #219150; }
        @media (max-width: 900px) { .option-cards { flex-direction: column; align-items: center; } .option-card { width: 95%; } }
    </style>
</head>
<body>
<section class="print-options-section">
    <div class="container">
        <h2 style="text-align:center; color:#2c3e50; margin-bottom:2.5rem;">Choose How You Want to Print</h2>
        <div class="option-cards">
            <div class="option-card">
                <i class="fas fa-keyboard"></i>
                <h3>Type Document</h3>
                <p>Type your document directly here with basic formatting options.</p>
                <button onclick="showSection('editor')">Type Now</button>
            </div>
            <div class="option-card">
                <i class="fas fa-upload"></i>
                <h3>Upload Document</h3>
                <p>Upload your ready-made document for printing (PDF, DOC, DOCX, PPT).</p>
                <button onclick="showSection('upload')">Upload</button>
            </div>
            <div class="option-card">
                <i class="fas fa-edit"></i>
                <h3>Edit & Print</h3>
                <p>Upload a document, edit it online, and then send for printing.</p>
                <button onclick="showSection('edit')">Edit & Print</button>
            </div>
        </div>
        <!-- Type Document Section -->
        <div class="editor-section" id="editor-section">
            <h3>Type Your Document</h3>
            <form action="upload_print.php" method="POST" onsubmit="return saveAndSubmitEditorContent(this)">
                <div class="editor-toolbar">
                    <button type="button" onclick="format('bold')"><b>B</b></button>
                    <button type="button" onclick="format('italic')"><i>I</i></button>
                    <button type="button" onclick="format('underline')"><u>U</u></button>
                    <button type="button" onclick="format('insertUnorderedList')"><i class='fas fa-list-ul'></i></button>
                    <button type="button" onclick="format('insertOrderedList')"><i class='fas fa-list-ol'></i></button>
                    <select id="font-family" onchange="setFontFamily(this.value)">
                        <option value="">Font</option>
                        <option value="Arial">Arial</option>
                        <option value="Times New Roman">Times New Roman</option>
                        <option value="Courier New">Courier New</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Verdana">Verdana</option>
                    </select>
                    <select id="font-size" onchange="setFontSize(this.value)">
                        <option value="">Size</option>
                        <option value="1">8pt</option>
                        <option value="2">10pt</option>
                        <option value="3">12pt</option>
                        <option value="4">14pt</option>
                        <option value="5">18pt</option>
                        <option value="6">24pt</option>
                        <option value="7">36pt</option>
                    </select>
                    <button type="button" onclick="format('justifyLeft')"><i class="fas fa-align-left"></i></button>
                    <button type="button" onclick="format('justifyCenter')"><i class="fas fa-align-center"></i></button>
                    <button type="button" onclick="format('justifyRight')"><i class="fas fa-align-right"></i></button>
                </div>
                <div class="form-group">
                    <label for="typed-content">Document Content</label>
                    <div id="editor-area" class="editor-area" contenteditable="true" name="typed-content"></div>
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
                            <option value="<?= htmlspecialchars($item['stationery_id']) ?>" <?= ($selected_stationery_id == $item['stationery_id']) ? 'selected' : '' ?>><?= htmlspecialchars($item['name']) ?> (<?= htmlspecialchars($item['location']) ?>)</option>
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
                <button type="submit" class="btn-main" onclick="saveEditorContent()">Submit for Printing</button>
            </form>
        </div>
        <!-- Upload Document Section -->
        <div class="upload-section" id="upload-section">
            <h3>Upload Your Document</h3>
            <form action="upload_print.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="file">Upload Document</label>
                    <input type="file" name="file" id="file" accept=".pdf,.doc,.docx,.ppt,.pptx" required>
                </div>
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name-upload" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" name="phone" id="phone-upload" required>
                </div>
                <div class="form-group">
                    <label for="station">Select Printing Station</label>
                    <select name="station" id="station-upload" required>
                        <option value="">-- Select Station --</option>
                        <?php foreach ($stationeryItems as $item): ?>
                            <option value="<?= htmlspecialchars($item['stationery_id']) ?>" <?= ($selected_stationery_id == $item['stationery_id']) ? 'selected' : '' ?>><?= htmlspecialchars($item['name']) ?> (<?= htmlspecialchars($item['location']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="copies">Number of Copies</label>
                    <input type="number" name="copies" id="copies-upload" min="1" value="1" required>
                </div>
                <div class="form-group">
                    <label for="color">Print Type</label>
                    <select name="color" id="color-upload" required>
                        <option value="black">Black & White</option>
                        <option value="color">Color</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="notes">Special Instructions</label>
                    <textarea name="notes" id="notes-upload" rows="3" placeholder="e.g., double-sided, staple"></textarea>
                </div>
                <button type="submit" class="btn-main">Submit for Printing</button>
            </form>
        </div>
        <!-- Edit & Print Section -->
        <div class="edit-section" id="edit-section">
            <h3>Upload and Edit Your Document</h3>
            <form id="edit-upload-form" enctype="multipart/form-data" onsubmit="return handleEditUpload(event)">
                <div class="form-group">
                    <label for="edit-file">Upload Document (<b>TXT or DOCX only</b>)</label>
                    <input type="file" name="edit-file" id="edit-file" accept=".txt,.docx" required>
                </div>
                <div id="edit-upload-error" style="color:#e74c3c; margin-bottom:1rem; display:none;"></div>
                <button type="submit" class="btn-main">Load for Editing</button>
            </form>
            <form action="upload_print.php" method="POST" id="edit-form" style="display:none;">
                <div class="form-group">
                    <label for="edit-content">Edit Document</label>
                    <div id="edit-area" class="editor-area" contenteditable="true" name="edit-content"></div>
                    <input type="hidden" name="edit-content" id="edit-content-hidden">
                </div>
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name-edit" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" name="phone" id="phone-edit" required>
                </div>
                <div class="form-group">
                    <label for="station">Select Printing Station</label>
                    <select name="station" id="station-edit" required>
                        <option value="">-- Select Station --</option>
                        <?php foreach ($stationeryItems as $item): ?>
                            <option value="<?= htmlspecialchars($item['stationery_id']) ?>" <?= ($selected_stationery_id == $item['stationery_id']) ? 'selected' : '' ?>><?= htmlspecialchars($item['name']) ?> (<?= htmlspecialchars($item['location']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="copies">Number of Copies</label>
                    <input type="number" name="copies" id="copies-edit" min="1" value="1" required>
                </div>
                <div class="form-group">
                    <label for="color">Print Type</label>
                    <select name="color" id="color-edit" required>
                        <option value="black">Black & White</option>
                        <option value="color">Color</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="notes">Special Instructions</label>
                    <textarea name="notes" id="notes-edit" rows="3" placeholder="e.g., double-sided, staple"></textarea>
                </div>
                <button type="submit" class="btn-main" onclick="saveEditContent()">Submit for Printing</button>
            </form>
        </div>
    </div>
</section>
<script>
function showSection(section) {
    document.getElementById('editor-section').style.display = 'none';
    document.getElementById('upload-section').style.display = 'none';
    document.getElementById('edit-section').style.display = 'none';
    if(section === 'editor') document.getElementById('editor-section').style.display = 'block';
    if(section === 'upload') document.getElementById('upload-section').style.display = 'block';
    if(section === 'edit') document.getElementById('edit-section').style.display = 'block';
}
function format(cmd) {
    document.execCommand(cmd, false, null);
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
    if (!file) return false;
    const ext = file.name.split('.').pop().toLowerCase();
    if (ext !== 'txt' && ext !== 'docx') {
        errorDiv.textContent = 'Only .txt or .docx files are supported for editing. Please upload a valid file.';
        errorDiv.style.display = 'block';
        return false;
    }
    if (ext === 'txt') {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('edit-area').innerText = e.target.result;
            document.getElementById('edit-form').style.display = 'block';
        };
        reader.readAsText(file);
    } else if (ext === 'docx') {
        errorDiv.textContent = 'DOCX editing is not supported in-browser yet. Please convert your DOCX to TXT for editing, or use the upload option for DOCX.';
        errorDiv.style.display = 'block';
        // For real DOCX editing, you would need a library like docx.js or a server-side conversion.
    }
    return false;
}
function setFontFamily(font) {
    if(font) document.execCommand('fontName', false, font);
}
function setFontSize(size) {
    if(size) document.execCommand('fontSize', false, size);
}
function saveAndSubmitEditorContent(form) {
    // Save editor content to hidden input
    document.getElementById('typed-content-hidden').value = document.getElementById('editor-area').innerHTML;
    // Save as .html file automatically before upload
    const content = document.getElementById('editor-area').innerHTML;
    const blob = new Blob([content], {type: 'text/html'});
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'typed_document.html';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    // Continue with form submission
    return true;
}
</script>
<?php include("temperate/footer.php"); ?>

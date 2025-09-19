<?php
session_name('admin_session');
session_start();

// Check if stationary admin is logged in
if (!isset($_SESSION['stationary_admin_id'])) {
    die('<div class="alert alert-danger">Session expired. Please login again.</div>');
}

include("../connection.php");

if (!isset($_GET['job_id']) || !is_numeric($_GET['job_id'])) {
    die('<div class="alert alert-danger">Invalid job ID specified.</div>');
}

$job_id = intval($_GET['job_id']);
$stationary_id = $_SESSION['stationary_admin_id'];

// Fetch the specific job
$query = "SELECT * FROM print_jobs WHERE job_id = ? AND stationery_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die('<div class="alert alert-danger">Database error: ' . htmlspecialchars($conn->error) . '</div>');
}

$stmt->bind_param("ii", $job_id, $stationary_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('<div class="alert alert-danger">Job not found or you don\'t have permission to view it.</div>');
}

$job = $result->fetch_assoc();

// Status colors
$status_colors = [
    'pending' => 'warning',
    'processing' => 'info',
    'completed' => 'success',
    'cancelled' => 'danger'
];

// INFINITYFREE PATH FIX
$base_dir = $_SERVER['DOCUMENT_ROOT']; // This will give the correct document root for InfinityFree

// File path handling - ensure correct paths
$file_relative_path = !empty($job['file_path']) ? $job['file_path'] : '';

// Remove any potential duplicate path segments
$clean_path = str_replace('students-portal/stationary/', '', $file_relative_path);
$clean_path = str_replace('stationary/', '', $clean_path);

// Build the correct absolute path
$file_absolute_path = $base_dir . '/students-portal/stationary/uploads/' . basename($clean_path);

// Build the correct URL path
$file_url = '/students-portal/stationary/uploads/' . basename($clean_path);

$file_name = !empty($clean_path) ? basename($clean_path) : 'No file attached';
$file_ext = !empty($clean_path) ? strtolower(pathinfo($clean_path, PATHINFO_EXTENSION)) : '';

// Check if file actually exists
$file_exists = !empty($file_absolute_path) && file_exists($file_absolute_path);

// For Office Online Viewer - needs full URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$full_file_url = $protocol . $_SERVER['HTTP_HOST'] . $file_url;

// Debug information (you can remove this after fixing)
echo '<!-- DEBUG INFO: ';
echo 'Base Dir: ' . $base_dir . ' | ';
echo 'Original Path: ' . $file_relative_path . ' | ';
echo 'Clean Path: ' . $clean_path . ' | ';
echo 'Absolute Path: ' . $file_absolute_path . ' | ';
echo 'File URL: ' . $file_url . ' | ';
echo 'File Exists: ' . ($file_exists ? 'Yes' : 'No');
echo ' -->';
?>

<div class="document-info bg-light p-3 rounded mb-3">
    <h5>Print Job Details</h5>
    <div class="row">
        <div class="col-md-6">
            <p><strong>Customer:</strong> <?= htmlspecialchars($job['user_name']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($job['phone_number']) ?></p>
        </div>
        <div class="col-md-6">
            <p><strong>Print Type:</strong> <?= ucfirst(htmlspecialchars($job['print_type'])) ?></p>
            <p><strong>Copies:</strong> <?= htmlspecialchars($job['copies']) ?></p>
        </div>
    </div>
</div>

<?php if (!empty($clean_path)): ?>
    <div class="document-viewer mb-4">
        <div class="document-actions mb-3">
            <a href="<?= htmlspecialchars($file_url) ?>" class="btn btn-primary" download>
                <i class="fas fa-download me-1"></i> Download
            </a>
            
            <?php if ($file_exists && in_array($file_ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt'])): ?>
                <button class="btn btn-success print-job" 
                        data-file-url="<?= htmlspecialchars($file_url) ?>"
                        data-file-type="<?= $file_ext ?>"
                        data-job-id="<?= $job['job_id'] ?>">
                    <i class="fas fa-print me-1"></i> Print
                </button>
            <?php endif; ?>
            
            <span class="badge bg-secondary ms-2"><?= strtoupper($file_ext) ?> file</span>
            <span class="badge bg-info ms-2">
                <i class="fas fa-copy"></i> <?= $job['copies'] ?> copies
            </span>
        </div>

        <h6>Document: <?= htmlspecialchars($file_name) ?></h6>
        
        <?php if (!$file_exists): ?>
            <div class="alert alert-danger">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>File Not Found!</h6>
                <p class="mb-1">Expected path: <?= htmlspecialchars($file_absolute_path) ?></p>
                <p class="mb-1">URL path: <?= htmlspecialchars($file_url) ?></p>
                <small class="text-muted">Please check if the file was uploaded correctly.</small>
            </div>
        <?php else: ?>
            <div class="document-content mt-3" style="min-height: 500px;">
                <?php if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                    <img src="<?= htmlspecialchars($file_url) ?>" class="image-preview" alt="Document to print">
                
                <?php elseif ($file_ext === 'pdf'): ?>
                    <iframe src="<?= htmlspecialchars($file_url) ?>#toolbar=0" class="pdf-container" width="100%" height="500px"></iframe>
                
                <?php elseif (in_array($file_ext, ['doc', 'docx', 'xls', 'xlsx'])): ?>
                    <div class="office-container">
                        <?php if ($protocol === 'https://'): ?>
                            <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=<?= urlencode($full_file_url) ?>" width="100%" height="500px" frameborder="0"></iframe>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Office documents require HTTPS for preview. Please download the file.
                                <div class="mt-2">
                                    <a href="<?= htmlspecialchars($file_url) ?>" class="btn btn-sm btn-primary" download>
                                        <i class="fas fa-download me-1"></i> Download
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                
                <?php elseif ($file_ext === 'txt'): ?>
                    <div class="text-preview"><?= htmlspecialchars(file_get_contents($file_absolute_path)) ?></div>
                
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        This file format cannot be previewed. Please download the file.
                        <div class="mt-2">
                            <a href="<?= htmlspecialchars($file_url) ?>" class="btn btn-sm btn-primary" download>
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle me-2"></i>
        No document attached to this print job
    </div>
<?php endif; ?>

<div class="action-buttons mt-3">
    <form method="POST" action="update_job_status.php" class="d-inline">
        <input type="hidden" name="job_id" value="<?= $job['job_id'] ?>">
        <div class="input-group">
            <select name="status" class="form-select form-select-sm">
                <option value="pending" <?= $job['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="processing" <?= $job['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                <option value="completed" <?= $job['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="cancelled" <?= $job['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="fas fa-save me-1"></i> Update Status
            </button>
        </div>
    </form>
</div>
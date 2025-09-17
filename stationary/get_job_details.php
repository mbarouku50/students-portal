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

// File path handling - ensure correct paths
$base_dir = dirname(dirname(__FILE__)); // Gets /var/www/html/students-portal
$file_relative_path = !empty($job['file_path']) ? $job['file_path'] : '';
$file_absolute_path = !empty($file_relative_path) ? $base_dir . '/stationary/' . $file_relative_path : '';
$file_url = !empty($file_relative_path) ? '/students-portal/stationary/' . $file_relative_path : '';
$file_name = !empty($file_absolute_path) ? basename($file_absolute_path) : 'No file attached';
$file_ext = !empty($file_absolute_path) ? strtolower(pathinfo($file_absolute_path, PATHINFO_EXTENSION)) : '';

// Check if file actually exists
$file_exists = !empty($file_absolute_path) && file_exists($file_absolute_path);

// For Office Online Viewer - needs full URL
$full_file_url = 'http://' . $_SERVER['HTTP_HOST'] . $file_url;
?>

<?php if (!empty($file_absolute_path)): ?>
    <div class="file-preview border rounded p-3 mb-3">
        <h6>Document for Printing: <?= htmlspecialchars($file_name) ?></h6>
        
        <?php if (!$file_exists): ?>
            <div class="alert alert-danger">
                File not found at: <?= htmlspecialchars($file_absolute_path) ?>
            </div>
        <?php else: ?>
            <div class="mt-3 mb-3" style="min-height: 500px;">
                <?php if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                    <img src="<?= htmlspecialchars($file_url) ?>" class="img-fluid" alt="Document to print">
                
                <?php elseif ($file_ext === 'pdf'): ?>
                    <embed src="<?= htmlspecialchars($file_url) ?>#toolbar=0" type="application/pdf" width="100%" height="500px">
                
                <?php elseif (in_array($file_ext, ['doc', 'docx', 'xls', 'xlsx'])): ?>
                    <div class="office-preview-container" style="height: 500px;">
                        <?php if (isset($_SERVER['HTTPS']) || $_SERVER['SERVER_PORT'] == 443): ?>
                            <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=<?= urlencode($full_file_url) ?>" width="100%" height="100%" frameborder="0"></iframe>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                Office documents require HTTPS for preview. Please download the file.
                                <a href="<?= htmlspecialchars($file_url) ?>" class="btn btn-sm btn-primary mt-2" download>
                                    <i class="fas fa-download me-1"></i> Download
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                
                <?php elseif ($file_ext === 'txt'): ?>
                    <pre style="white-space: pre-wrap; background: #f8f9fa; padding: 15px; border-radius: 5px;"><?= 
                        htmlspecialchars(file_get_contents($file_absolute_path)) 
                    ?></pre>
                
                <?php else: ?>
                    <div class="alert alert-warning">
                        This file format cannot be previewed. Please download the file.
                        <a href="<?= htmlspecialchars($file_url) ?>" class="btn btn-sm btn-primary mt-2" download>
                            <i class="fas fa-download me-1"></i> Download
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="badge bg-secondary"><?= strtoupper($file_ext) ?> file</span>
                <span class="badge bg-light text-dark ms-2">
                    <i class="fas fa-copy"></i> <?= $job['copies'] ?> copies
                </span>
                <span class="badge bg-light text-dark ms-2">
                    <i class="fas fa-print"></i> <?= ucfirst($job['print_type']) ?>
                </span>
            </div>
            
            <?php if ($file_exists): ?>
                <div class="btn-group">
                    <a href="<?= htmlspecialchars($file_url) ?>" class="btn btn-primary" download>
                        <i class="fas fa-download me-1"></i> Download
                    </a>
                    
                    <?php if (in_array($file_ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt'])): ?>
                        <button class="btn btn-success print-job" 
                                data-file-url="<?= htmlspecialchars($file_url) ?>"
                                data-file-type="<?= $file_ext ?>"
                                data-job-id="<?= $job['job_id'] ?>">
                            <i class="fas fa-print me-1"></i> Print
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-danger">No document attached to this print job</div>
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
</div>
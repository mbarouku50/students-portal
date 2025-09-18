<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Start session before any output
session_name('admin_session');
session_start();

include("../connection.php");

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

// Initialize variables
$success = '';
$error = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Begin transaction
        $conn->begin_transaction();

        // Handle logo upload
        if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
            $logoTmpPath = $_FILES['site_logo']['tmp_name'];
            $logoName = uniqid('logo_') . '.' . pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION);
            $logoDest = '../uploads/' . $logoName;
            if (move_uploaded_file($logoTmpPath, $logoDest)) {
                $_POST['site_logo_path'] = $logoDest;
            }
        }

        // Handle favicon upload
        if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
            $faviconTmpPath = $_FILES['favicon']['tmp_name'];
            $faviconName = uniqid('favicon_') . '.' . pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION);
            $faviconDest = '../uploads/' . $faviconName;
            if (move_uploaded_file($faviconTmpPath, $faviconDest)) {
                $_POST['favicon_path'] = $faviconDest;
            }
        }

        // Loop through all POST values and save them
        foreach ($_POST as $key => $value) {
            // Skip the submit button and other non-setting fields
            if ($key === 'submit' || substr($key, 0, 2) === '__') continue;

            // Determine category based on field name patterns
            $category = 'general';
            if (strpos($key, 'theme_') === 0 || strpos($key, 'enable_') === 0) $category = 'appearance';
            if (strpos($key, 'notify_') === 0 || strpos($key, 'alert_') === 0) $category = 'notifications';
            if (strpos($key, '2fa_') === 0 || strpos($key, 'password_') === 0 ||
                strpos($key, 'login_') === 0 || strpos($key, 'max_') === 0 ||
                strpos($key, 'lockout_') === 0) $category = 'security';
            if (strpos($key, 'backup_') === 0 || strpos($key, 'api_') === 0 ||
                strpos($key, 'cache_') === 0 || strpos($key, 'log_') === 0 ||
                strpos($key, 'enable_') === 0) $category = 'advanced';

            // Check if setting exists
            $stmt = $conn->prepare("SELECT id FROM system_settings WHERE setting_key = ?");
            $stmt->bind_param("s", $key);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Update existing setting
                $stmt = $conn->prepare("UPDATE system_settings SET setting_value = ?, setting_category = ? WHERE setting_key = ?");
                $stmt->bind_param("sss", $value, $category, $key);
            } else {
                // Insert new setting
                $stmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value, setting_category) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $key, $value, $category);
            }

            $stmt->execute();
            $stmt->close();
        }

        // Commit transaction
        $conn->commit();
        $success = "Settings saved successfully!";
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $error = "Error saving settings: " . $e->getMessage();
    }
}

// Load all settings from database
$settings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM system_settings");
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Function to get setting value with default fallback
function getSetting($key, $default = '') {
    global $settings;
    return isset($settings[$key]) ? $settings[$key] : $default;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - CBE Doc's Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --secondary: #10b981;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --border: #e2e8f0;
            --shadow: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-lg: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 2rem;
            transition: all 0.3s ease;
        }
        
        .page-header {
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .page-title {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .page-subtitle {
            color: var(--gray);
            font-size: 1.1rem;
        }
        
        .settings-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .settings-card {
            background: white;
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            height: fit-content;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .settings-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }
        
        .card-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }
        
        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }
        
        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-label .info {
            color: var(--gray);
            font-size: 0.8rem;
            font-weight: normal;
        }
        
        .form-input, .form-select, .form-textarea {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border);
            border-radius: 0.5rem;
            font-size: 1rem;
            background: white;
            transition: all 0.3s ease;
        }
        
        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        
        .error {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .success {
            color: var(--secondary);
            text-align: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: rgba(16, 185, 129, 0.1);
            border-radius: 0.5rem;
            border: 1px solid rgba(16, 185, 129, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-secondary {
            background: var(--light);
            color: var(--dark);
            border: 2px solid var(--border);
        }
        
        .btn-secondary:hover {
            background: #e2e8f0;
        }
        
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        
        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }
        
        .checkbox-group label {
            font-weight: 500;
            color: var(--dark);
        }
        
        .color-picker {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .color-preview {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            border: 2px solid var(--border);
        }
        
        .system-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.875rem;
            width: fit-content;
            margin-bottom: 1rem;
        }
        
        .status-active {
            background: rgba(16, 185, 129, 0.1);
            color: var(--secondary);
        }
        
        .status-inactive {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        
        .status-maintenance {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }
        
        .tab-container {
            margin-bottom: 2rem;
        }
        
        .tabs {
            display: flex;
            gap: 0;
            border-bottom: 2px solid var(--border);
            margin-bottom: 2rem;
        }
        
        .tab {
            padding: 1rem 1.5rem;
            cursor: pointer;
            font-weight: 600;
            color: var(--gray);
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .tab.active {
            color: var(--primary);
            border-bottom: 3px solid var(--primary);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .file-upload {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .file-upload-input {
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            position: absolute;
            z-index: -1;
        }
        
        .file-upload-label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            border: 2px dashed var(--border);
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .file-upload-label:hover {
            border-color: var(--primary);
        }
        
        .file-preview {
            margin-top: 1rem;
            max-width: 200px;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        
        .advanced-toggle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            color: var(--primary);
            cursor: pointer;
            font-weight: 600;
            width: fit-content;
        }
        
        .advanced-settings {
            display: none;
            padding: 1.5rem;
            background: #f8fafc;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary);
        }
        
        .advanced-settings.active {
            display: block;
            animation: slideDown 0.5s ease;
        }
        
        @keyframes slideDown {
            from { opacity: 0; max-height: 0; }
            to { opacity: 1; max-height: 500px; }
        }
        
        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .radio-option input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }
        
        .radio-option label {
            font-weight: 500;
        }
        
        @media (max-width: 1200px) {
            .main-content {
                margin-left: 0;
                padding: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .settings-container {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .page-title {
                font-size: 1.75rem;
            }
            
            .color-picker {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .tabs {
                flex-wrap: wrap;
            }
            
            .tab {
                flex: 1;
                text-align: center;
                padding: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>
    
    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">System Settings</h1>
                <p class="page-subtitle">Configure and manage system preferences</p>
            </div>
            <a href="admin_dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <?php if ($success): ?>
            <form method="POST" enctype="multipart/form-data">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="tab-container">
            <div class="tabs">
                <div class="tab active" data-tab="general">General</div>
                <div class="tab" data-tab="appearance">Appearance</div>
                <div class="tab" data-tab="notifications">Notifications</div>
                <div class="tab" data-tab="security">Security</div>
                <div class="tab" data-tab="advanced">Advanced</div>
            </div>
            
            <form method="POST">
                <!-- General Settings Tab -->
                <div class="tab-content active" id="general-tab">
                    <div class="settings-container">
                        <!-- General Settings -->
                        <div class="settings-card">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <h2 class="card-title">General Settings</h2>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="site_name">
                                    Site Name
                                    <span class="info">(Displayed in browser tab)</span>
                                </label>
                                <input type="text" class="form-input" id="site_name" name="site_name" value="<?php echo htmlspecialchars(getSetting('site_name', 'CBE Doc\'s Store')); ?>" placeholder="Enter your site name">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="timezone">
                                    Timezone
                                </label>
                                <select class="form-select" id="timezone" name="timezone">
                                    <option value="UTC" <?php echo getSetting('timezone', 'EST') === 'UTC' ? 'selected' : ''; ?>>UTC</option>
                                    <option value="EST" <?php echo getSetting('timezone', 'EST') === 'EST' ? 'selected' : ''; ?>>Eastern Time (EST)</option>
                                    <option value="PST" <?php echo getSetting('timezone', 'EST') === 'PST' ? 'selected' : ''; ?>>Pacific Time (PST)</option>
                                    <option value="CST" <?php echo getSetting('timezone', 'EST') === 'CST' ? 'selected' : ''; ?>>Central Time (CST)</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="date_format">
                                    Date Format
                                </label>
                                <select class="form-select" id="date_format" name="date_format">
                                    <option value="Y-m-d" <?php echo getSetting('date_format', 'm/d/Y') === 'Y-m-d' ? 'selected' : ''; ?>>YYYY-MM-DD</option>
                                    <option value="m/d/Y" <?php echo getSetting('date_format', 'm/d/Y') === 'm/d/Y' ? 'selected' : ''; ?>>MM/DD/YYYY</option>
                                    <option value="d/m/Y" <?php echo getSetting('date_format', 'm/d/Y') === 'd/m/Y' ? 'selected' : ''; ?>>DD/MM/YYYY</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- System Status -->
                        <div class="settings-card">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-server"></i>
                                </div>
                                <h2 class="card-title">System Status</h2>
                            </div>
                            
                            <?php
                            $systemMode = getSetting('system_mode', 'live');
                            $statusClass = $systemMode === 'live' ? 'status-active' : ($systemMode === 'maintenance' ? 'status-maintenance' : 'status-inactive');
                            $statusText = $systemMode === 'live' ? 'System is running normally' : ($systemMode === 'maintenance' ? 'System is in maintenance mode' : 'System is in testing mode');
                            ?>
                            <div class="system-status <?php echo $statusClass; ?>">
                                <i class="fas fa-circle-check"></i>
                                <span><?php echo $statusText; ?></span>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    System Mode
                                </label>
                                <div class="radio-group">
                                    <div class="radio-option">
                                        <input type="radio" id="mode_live" name="system_mode" value="live" <?php echo $systemMode === 'live' ? 'checked' : ''; ?>>
                                        <label for="mode_live">Live Mode</label>
                                    </div>
                                    <div class="radio-option">
                                        <input type="radio" id="mode_maintenance" name="system_mode" value="maintenance" <?php echo $systemMode === 'maintenance' ? 'checked' : ''; ?>>
                                        <label for="mode_maintenance">Maintenance Mode</label>
                                    </div>
                                    <div class="radio-option">
                                        <input type="radio" id="mode_testing" name="system_mode" value="testing" <?php echo $systemMode === 'testing' ? 'checked' : ''; ?>>
                                        <label for="mode_testing">Testing Mode</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="maintenance_message">
                                    Maintenance Message
                                    <span class="info">(Shown when in maintenance mode)</span>
                                </label>
                                <textarea class="form-textarea" id="maintenance_message" name="maintenance_message" placeholder="We'll be back soon!"><?php echo htmlspecialchars(getSetting('maintenance_message', 'Our site is currently under maintenance. Please check back later.')); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Appearance Settings Tab -->
                <div class="tab-content" id="appearance-tab">
                    <div class="settings-container">
                        <!-- Theme Settings -->
                        <div class="settings-card">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-paint-brush"></i>
                                </div>
                                <h2 class="card-title">Theme Settings</h2>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    Theme Color
                                </label>
                                <div class="color-picker">
                                    <div class="color-preview" style="background-color: <?php echo getSetting('theme_color', '#4f46e5'); ?>;"></div>
                                    <input type="color" class="form-input" id="theme_color" name="theme_color" value="<?php echo getSetting('theme_color', '#4f46e5'); ?>" style="width: 100px; height: 40px; padding: 0;">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="theme_mode">
                                    Default Theme Mode
                                </label>
                                <select class="form-select" id="theme_mode" name="theme_mode">
                                    <option value="light" <?php echo getSetting('theme_mode', 'auto') === 'light' ? 'selected' : ''; ?>>Light Mode</option>
                                    <option value="dark" <?php echo getSetting('theme_mode', 'auto') === 'dark' ? 'selected' : ''; ?>>Dark Mode</option>
                                    <option value="auto" <?php echo getSetting('theme_mode', 'auto') === 'auto' ? 'selected' : ''; ?>>Auto (System Preference)</option>
                                </select>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="enable_animations" name="enable_animations" value="1" <?php echo getSetting('enable_animations', '1') ? 'checked' : ''; ?>>
                                <label for="enable_animations">Enable Animations</label>
                            </div>
                            
                                    <input type="file" id="site_logo" name="site_logo" class="file-upload-input" accept="image/*">
                                <input type="checkbox" id="enable_shadows" name="enable_shadows" value="1" <?php echo getSetting('enable_shadows', '1') ? 'checked' : ''; ?>>
                                <label for="enable_shadows">Enable Shadow Effects</label>
                            </div>
                        </div>
                        
                                <div class="file-preview">
                                    <img src="<?php echo isset($settings['site_logo_path']) ? $settings['site_logo_path'] : 'https://via.placeholder.com/200x80/4f46e5/ffffff?text=CBE+Doc\'s+Store'; ?>" alt="Current logo" style="width: 100%;">
                                </div>
                                <div class="card-icon">
                                    <i class="fas fa-image"></i>
                                </div>
                                <h2 class="card-title">Logo & Favicon</h2>
                            </div>
                            
                            <div class="form-group">
                                    <input type="file" id="favicon" name="favicon" class="file-upload-input" accept="image/*">
                                    Site Logo
                                </label>
                                <div class="file-upload">
                                    <input type="file" id="site_logo" class="file-upload-input" accept="image/*">
                                    <label for="site_logo" class="file-upload-label">
                                <div class="file-preview">
                                    <img src="<?php echo isset($settings['favicon_path']) ? $settings['favicon_path'] : 'https://via.placeholder.com/32/4f46e5/ffffff?text=C'; ?>" alt="Current favicon" style="width: 32px; height: 32px;">
                                </div>
                                </div>
                                <div class="file-preview">
                                    <img src="https://via.placeholder.com/200x80/4f46e5/ffffff?text=CBE+Doc's+Store" alt="Current logo" style="width: 100%;">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    Favicon
                                </label>
                                <div class="file-upload">
                                    <input type="file" id="favicon" class="file-upload-input" accept="image/*">
                                    <label for="favicon" class="file-upload-label">
                                        <i class="fas fa-upload"></i>
                                        <span>Choose favicon file...</span>
                                    </label>
                                </div>
                                <div class="file-preview">
                                    <img src="https://via.placeholder.com/32/4f46e5/ffffff?text=C" alt="Current favicon" style="width: 32px; height: 32px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Notifications Tab -->
                <div class="tab-content" id="notifications-tab">
                    <div class="settings-container">
                        <!-- Email Notifications -->
                        <div class="settings-card">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <h2 class="card-title">Email Notifications</h2>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="notify_new_user" name="notify_new_user" value="1" <?php echo getSetting('notify_new_user', '1') ? 'checked' : ''; ?>>
                                <label for="notify_new_user">Notify on new user registration</label>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="notify_order" name="notify_order" value="1" <?php echo getSetting('notify_order', '1') ? 'checked' : ''; ?>>
                                <label for="notify_order">Notify on new orders</label>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="notify_error" name="notify_error" value="1" <?php echo getSetting('notify_error', '1') ? 'checked' : ''; ?>>
                                <label for="notify_error">Notify on system errors</label>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="notify_update" name="notify_update" value="1" <?php echo getSetting('notify_update', '0') ? 'checked' : ''; ?>>
                                <label for="notify_update">Notify on available updates</label>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="notification_email">
                                    Notification Email Address
                                </label>
                                <input type="email" class="form-input" id="notification_email" name="notification_email" value="<?php echo htmlspecialchars(getSetting('notification_email', 'notifications@example.com')); ?>" placeholder="Enter notification email">
                            </div>
                        </div>
                        
                        <!-- Alert Settings -->
                        <div class="settings-card">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <h2 class="card-title">Alert Settings</h2>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    Alert Sound
                                </label>
                                <div class="radio-group">
                                    <?php $alertSound = getSetting('alert_sound', 'none'); ?>
                                    <div class="radio-option">
                                        <input type="radio" id="sound_none" name="alert_sound" value="none" <?php echo $alertSound === 'none' ? 'checked' : ''; ?>>
                                        <label for="sound_none">None</label>
                                    </div>
                                    <div class="radio-option">
                                        <input type="radio" id="sound_chime" name="alert_sound" value="chime" <?php echo $alertSound === 'chime' ? 'checked' : ''; ?>>
                                        <label for="sound_chime">Chime</label>
                                    </div>
                                    <div class="radio-option">
                                        <input type="radio" id="sound_beep" name="alert_sound" value="beep" <?php echo $alertSound === 'beep' ? 'checked' : ''; ?>>
                                        <label for="sound_beep">Beep</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="alert_volume">
                                    Alert Volume
                                </label>
                                <input type="range" class="form-input" id="alert_volume" name="alert_volume" min="0" max="100" value="<?php echo getSetting('alert_volume', '70'); ?>" style="padding: 0;">
                                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--gray);">
                                    <span>0%</span>
                                    <span><?php echo getSetting('alert_volume', '70'); ?>%</span>
                                    <span>100%</span>
                                </div>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="desktop_notifications" name="desktop_notifications" value="1" <?php echo getSetting('desktop_notifications', '1') ? 'checked' : ''; ?>>
                                <label for="desktop_notifications">Enable desktop notifications</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Security Tab -->
                <div class="tab-content" id="security-tab">
                    <div class="settings-container">
                        <!-- Login Security -->
                        <div class="settings-card">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <h2 class="card-title">Login Security</h2>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="2fa_enabled" name="2fa_enabled" value="1" <?php echo getSetting('2fa_enabled', '1') ? 'checked' : ''; ?>>
                                <label for="2fa_enabled">Enable Two-Factor Authentication</label>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="login_attempts" name="login_attempts" value="1" <?php echo getSetting('login_attempts', '1') ? 'checked' : ''; ?>>
                                <label for="login_attempts">Limit login attempts</label>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="max_login_attempts">
                                    Maximum Login Attempts
                                </label>
                                <input type="number" class="form-input" id="max_login_attempts" name="max_login_attempts" value="<?php echo getSetting('max_login_attempts', '5'); ?>" min="3" max="10">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="lockout_time">
                                    Lockout Time (minutes)
                                </label>
                                <input type="number" class="form-input" id="lockout_time" name="lockout_time" value="<?php echo getSetting('lockout_time', '30'); ?>" min="5" max="1440">
                            </div>
                        </div>
                        
                        <!-- Password Policy -->
                        <div class="settings-card">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-key"></i>
                                </div>
                                <h2 class="card-title">Password Policy</h2>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="password_complexity" name="password_complexity" value="1" <?php echo getSetting('password_complexity', '1') ? 'checked' : ''; ?>>
                                <label for="password_complexity">Require complex passwords</label>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="min_password_length">
                                    Minimum Password Length
                                </label>
                                <input type="number" class="form-input" id="min_password_length" name="min_password_length" value="<?php echo getSetting('min_password_length', '8'); ?>" min="6" max="20">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="password_expiry">
                                    Password Expiry (days)
                                </label>
                                <input type="number" class="form-input" id="password_expiry" name="password_expiry" value="<?php echo getSetting('password_expiry', '90'); ?>" min="30" max="365">
                                <span class="info">Set to 0 to disable password expiry</span>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="prevent_reuse" name="prevent_reuse" value="1" <?php echo getSetting('prevent_reuse', '1') ? 'checked' : ''; ?>>
                                <label for="prevent_reuse">Prevent password reuse</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Advanced Tab -->
                <div class="tab-content" id="advanced-tab">
                    <div class="settings-container">
                        <!-- System Maintenance -->
                        <div class="settings-card">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <h2 class="card-title">System Maintenance</h2>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="backup_schedule">
                                    Automatic Backup Schedule
                                </label>
                                <select class="form-select" id="backup_schedule" name="backup_schedule">
                                    <?php $backupSchedule = getSetting('backup_schedule', 'daily'); ?>
                                    <option value="none" <?php echo $backupSchedule === 'none' ? 'selected' : ''; ?>>No automatic backups</option>
                                    <option value="daily" <?php echo $backupSchedule === 'daily' ? 'selected' : ''; ?>>Daily</option>
                                    <option value="weekly" <?php echo $backupSchedule === 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                                    <option value="monthly" <?php echo $backupSchedule === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="backup_retention">
                                    Backup Retention (days)
                                </label>
                                <input type="number" class="form-input" id="backup_retention" name="backup_retention" value="<?php echo getSetting('backup_retention', '30'); ?>" min="7" max="365">
                            </div>
                            
                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary" id="backup-now">
                                    <i class="fas fa-download"></i> Backup Now
                                </button>
                            </div>
                            
                            <div class="advanced-toggle" id="advanced-maintenance-toggle">
                                <i class="fas fa-cog"></i>
                                <span>Advanced Maintenance Options</span>
                            </div>
                            
                            <div class="advanced-settings" id="advanced-maintenance">
                                <div class="form-group">
                                    <label class="form-label" for="cache_duration">
                                        Cache Duration (minutes)
                                    </label>
                                    <input type="number" class="form-input" id="cache_duration" name="cache_duration" value="<?php echo getSetting('cache_duration', '60'); ?>" min="0" max="1440">
                                    <span class="info">Set to 0 to disable caching</span>
                                </div>
                                
                                <div class="checkbox-group">
                                    <input type="checkbox" id="enable_gzip" name="enable_gzip" value="1" <?php echo getSetting('enable_gzip', '1') ? 'checked' : ''; ?>>
                                    <label for="enable_gzip">Enable GZIP Compression</label>
                                </div>
                                
                                <div class="checkbox-group">
                                    <input type="checkbox" id="enable_logging" name="enable_logging" value="1" <?php echo getSetting('enable_logging', '1') ? 'checked' : ''; ?>>
                                    <label for="enable_logging">Enable System Logging</label>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="log_retention">
                                        Log Retention (days)
                                    </label>
                                    <input type="number" class="form-input" id="log_retention" name="log_retention" value="<?php echo getSetting('log_retention', '30'); ?>" min="1" max="365">
                                </div>
                            </div>
                        </div>
                        
                        <!-- API Settings -->
                        <div class="settings-card">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-code"></i>
                                </div>
                                <h2 class="card-title">API Settings</h2>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="api_enabled" name="api_enabled" value="1" <?php echo getSetting('api_enabled', '1') ? 'checked' : ''; ?>>
                                <label for="api_enabled">Enable REST API</label>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="api_rate_limit">
                                    API Rate Limit (requests per minute)
                                </label>
                                <input type="number" class="form-input" id="api_rate_limit" name="api_rate_limit" value="<?php echo getSetting('api_rate_limit', '100'); ?>" min="10" max="1000">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    API Key
                                </label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <input type="text" class="form-input" value="<?php echo getSetting('api_key', 'sk_5c2b7e3d8f9a1b6d4e7f8c9a'); ?>" readonly style="flex: 1;">
                                    <button type="button" class="btn btn-secondary" id="copy-api-key">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="regenerate-api-key">
                                        <i class="fas fa-refresh"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="advanced-toggle" id="advanced-api-toggle">
                                <i class="fas fa-cog"></i>
                                <span>Advanced API Options</span>
                            </div>
                            
                            <div class="advanced-settings" id="advanced-api">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="cors_enabled" name="cors_enabled" value="1" <?php echo getSetting('cors_enabled', '1') ? 'checked' : ''; ?>>
                                    <label for="cors_enabled">Enable CORS</label>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="cors_origins">
                                        Allowed Origins
                                    </label>
                                    <textarea class="form-textarea" id="cors_origins" name="cors_origins" placeholder="Enter allowed origins (one per line)"><?php echo htmlspecialchars(getSetting('cors_origins', "https://yourdomain.com\nhttp://localhost:3000")); ?></textarea>
                                </div>
                                
                                <div class="checkbox-group">
                                    <input type="checkbox" id="api_docs" name="api_docs" value="1" <?php echo getSetting('api_docs', '0') ? 'checked' : ''; ?>>
                                    <label for="api_docs">Enable API Documentation</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset Changes
                    </button>
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active class from all tabs and contents
                    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked tab
                    tab.classList.add('active');
                    
                    // Show corresponding content
                    const tabId = tab.getAttribute('data-tab');
                    document.getElementById(`${tabId}-tab`).classList.add('active');
                });
            });
            
            // Advanced settings toggles
            const advancedToggles = document.querySelectorAll('.advanced-toggle');
            advancedToggles.forEach(toggle => {
                toggle.addEventListener('click', () => {
                    const targetId = toggle.id.replace('-toggle', '');
                    const target = document.getElementById(targetId);
                    target.classList.toggle('active');
                    
                    const icon = toggle.querySelector('i');
                    if (target.classList.contains('active')) {
                        icon.classList.remove('fa-cog');
                        icon.classList.add('fa-chevron-up');
                    } else {
                        icon.classList.remove('fa-chevron-up');
                        icon.classList.add('fa-cog');
                    }
                });
            });
            
            // Copy API key functionality
            document.getElementById('copy-api-key').addEventListener('click', () => {
                const apiKeyInput = document.querySelector('input[value="<?php echo getSetting('api_key', 'sk_5c2b7e3d8f9a1b6d4e7f8c9a'); ?>"]');
                apiKeyInput.select();
                document.execCommand('copy');
                
                // Show feedback
                const originalText = document.getElementById('copy-api-key').innerHTML;
                document.getElementById('copy-api-key').innerHTML = '<i class="fas fa-check"></i> Copied!';
                
                setTimeout(() => {
                    document.getElementById('copy-api-key').innerHTML = originalText;
                }, 2000);
            });
            
            // Backup now button
            document.getElementById('backup-now').addEventListener('click', () => {
                const button = document.getElementById('backup-now');
                const originalText = button.innerHTML;
                
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Backing up...';
                button.disabled = true;
                
                // Simulate backup process
                setTimeout(() => {
                    button.innerHTML = '<i class="fas fa-check"></i> Backup Complete!';
                    button.classList.remove('btn-secondary');
                    button.classList.add('btn-primary');
                    
                    setTimeout(() => {
                        button.innerHTML = originalText;
                        button.disabled = false;
                        button.classList.remove('btn-primary');
                        button.classList.add('btn-secondary');
                    }, 3000);
                }, 2000);
            });
            
            // Theme color preview update
            const colorPicker = document.getElementById('theme_color');
            const colorPreview = document.querySelector('.color-preview');
            
            colorPicker.addEventListener('input', () => {
                colorPreview.style.backgroundColor = colorPicker.value;
            });
        });`
    </script>
</body>
</html>
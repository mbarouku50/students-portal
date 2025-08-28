<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Check if stationary admin is logged in
if (!isset($_SESSION['stationary_admin_id'])) {
    header('Location: ../admin_login.php');
    exit();
}

// Database connection
include("../connection.php");
$stationary_id = $_SESSION['stationary_admin_id'];

// Fetch shop details
$shop_query = "SELECT * FROM stationery WHERE stationery_id = ?";
$shop_stmt = $conn->prepare($shop_query);
$shop_stmt->bind_param("i", $stationary_id);
$shop_stmt->execute();
$shop_result = $shop_stmt->get_result();
$shop_data = $shop_result->fetch_assoc();

// Handle form submission
$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $description = $_POST['description'] ?? '';
    $opening_hours = $_POST['opening_hours'] ?? '';
    $price = $_POST['price'] ?? '';
    
    // Handle file upload
    $logo_path = $shop_data['logo'] ?? '';
    if (!empty($_FILES['logo']['name'])) {
        // Use absolute path inside stationary/uploads/stationery_logos/
        $target_dir = __DIR__ . "/uploads/stationery_logos/";
        $public_path = "uploads/stationery_logos/"; // relative for DB & HTML use

        // Create directory if it doesn't exist
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0755, true)) {
                $error_message = "Failed to create upload directory. Please check permissions.";
            }
        }

        if (is_dir($target_dir) && is_writable($target_dir)) {
            $file_extension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            $filename = "stationery_" . $stationary_id . "_" . time() . "." . $file_extension;
            $target_file = $target_dir . $filename;

            $check = getimagesize($_FILES['logo']['tmp_name']);
            if ($check !== false) {
                if ($_FILES['logo']['size'] > 5000000) {
                    $error_message = "Sorry, your file is too large. Max size 5MB.";
                } else {
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                    if (in_array($file_extension, $allowed_extensions)) {
                        if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_file)) {
                            $logo_path = $public_path . $filename;

                            // Delete old logo if exists
                            if (!empty($shop_data['logo']) && file_exists(__DIR__ . "/" . $shop_data['logo'])) {
                                unlink(__DIR__ . "/" . $shop_data['logo']);
                            }
                        } else {
                            $error_message = "Error uploading your file. Check permissions.";
                        }
                    } else {
                        $error_message = "Only JPG, JPEG, PNG & GIF are allowed.";
                    }
                }
            } else {
                $error_message = "File is not an image.";
            }
        } else {
            $error_message = "Upload directory is not writable. Check permissions.";
        }
    }
    
    $update_query = "UPDATE stationery SET name=?, email=?, phone=?, address=?, description=?, opening_hours=?, price=?, logo=? WHERE stationery_id=?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ssssssssi", $name, $email, $phone, $address, $description, $opening_hours, $price, $logo_path, $stationary_id);


    
    if ($update_stmt->execute()) {
        $success_message = "Shop settings updated successfully!";
        // Refresh shop data
        $shop_stmt->execute();
        $shop_result = $shop_stmt->get_result();
        $shop_data = $shop_result->fetch_assoc();
    } else {
        $error_message = "Error updating shop settings: " . $conn->error;
    }
}

// Fetch stats
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM print_jobs WHERE stationery_id=? AND status IN ('pending','processing')) as pending_prints,
    (SELECT COUNT(*) FROM print_jobs WHERE stationery_id=? AND status='completed') as completed_prints";
$stats_stmt = $conn->prepare($stats_query);
$stats_stmt->bind_param("ii", $stationary_id, $stationary_id);
$stats_stmt->execute();
$stats_result = $stats_stmt->get_result();
$stats_data = $stats_result->fetch_assoc();

$pending_prints = $stats_data['pending_prints'] ?? 0;
$completed_prints = $stats_data['completed_prints'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Settings - StationaryPro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --secondary: #3f37c9;
            --dark: #1a1a2e;
            --light: #f8f9fa;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #f72585;
            --gray: #6c757d;
            --white: #ffffff;
            --card-shadow: 0 10px 20px rgba(0, 0, 0, 0.05), 0 6px 6px rgba(0, 0, 0, 0.1);
            --sidebar-width: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fb 0%, #e8ecf1 100%);
            color: var(--dark);
            display: flex;
            min-height: 100vh;
            line-height: 1.6;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #4361ee 0%, #3a0ca3 100%);
            color: #fff;
            padding: 1.5rem 0;
            position: fixed;
            height: 100vh;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.15);
            overflow-y: auto;
            overflow-x: hidden;
        }
        .sidebar-header {
            display: flex;
            align-items: center;
            padding: 0 1.5rem 1.5rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar-header img {
            width: 45px;
            height: 45px;
            margin-right: 12px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .sidebar-header h3 {
            font-weight: 700;
            font-size: 1.3rem;
            letter-spacing: 0.5px;
            background: linear-gradient(45deg, #fff, #f72585);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .sidebar-menu {
            padding: 0 1rem;
        }
        .menu-title {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255, 255, 255, 0.6);
            padding: 0 1rem;
            margin: 1.5rem 0 0.75rem;
            font-weight: 600;
        }
        .menu-item {
            display: flex;
            align-items: center;
            padding: 0.85rem 1.25rem;
            border-radius: 0.75rem;
            margin-bottom: 0.35rem;
            cursor: pointer;
            transition: all 0.3s ease;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }
        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s ease;
        }
        .menu-item:hover::before {
            left: 100%;
        }
        .menu-item:hover {
            background-color: rgba(255, 255, 255, 0.12);
            color: #fff;
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .menu-item.active {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.1));
            color: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .menu-item.active::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: linear-gradient(180deg, #f72585, #4361ee);
            border-radius: 3px 0 0 3px;
        }
        .menu-item i {
            margin-right: 0.85rem;
            font-size: 1.15rem;
            width: 24px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .menu-item:hover i {
            transform: scale(1.1);
        }
        .menu-item span {
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .menu-item.active span {
            font-weight: 600;
        }
        .menu-badge {
            position: absolute;
            right: 1rem;
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
            transition: all 0.3s ease;
            width: calc(100% - var(--sidebar-width));
        }

        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            padding: 1.75rem 2rem;
            background: var(--white);
            border-radius: 1.25rem;
            box-shadow: var(--card-shadow);
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: 1px solid rgba(67, 97, 238, 0.1);
        }

        .header-title h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-title p {
            color: var(--gray);
            font-size: 1rem;
            margin: 0;
            font-weight: 400;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1.5rem;
            background: rgba(67, 97, 238, 0.08);
            border-radius: 50px;
            transition: all 0.3s ease;
            border: 1px solid rgba(67, 97, 238, 0.1);
        }

        .user-profile:hover {
            background: rgba(67, 97, 238, 0.12);
            transform: translateY(-2px);
        }

        .user-profile img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-light);
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.2);
        }

        .user-info h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--dark);
        }

        .user-info p {
            font-size: 0.85rem;
            color: var(--gray);
            margin: 0;
        }

        /* Settings Content */
        .settings-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 992px) {
            .settings-container {
                grid-template-columns: 1fr;
            }
        }

        .settings-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 1.5rem;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(67, 97, 238, 0.1);
            height: fit-content;
        }

        .settings-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(67, 97, 238, 0.1);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-title i {
            color: var(--primary);
            font-size: 1.25rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #ffffff;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .file-upload {
            position: relative;
            border: 2px dashed #e2e8f0;
            border-radius: 0.75rem;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload:hover {
            border-color: var(--primary);
        }

        .file-upload i {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .file-upload p {
            color: var(--gray);
            margin-bottom: 1rem;
        }

        .file-upload input {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
        }

        .logo-preview {
            margin-top: 1rem;
            text-align: center;
        }

        .logo-preview img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 0.5rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: var(--dark);
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(76, 201, 240, 0.1);
            color: var(--success);
            border: 1px solid rgba(76, 201, 240, 0.2);
        }

        .alert-error {
            background: rgba(247, 37, 133, 0.1);
            color: var(--danger);
            border: 1px solid rgba(247, 37, 133, 0.2);
        }

        /* Quick Stats */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(67, 97, 238, 0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
        }

        .stat-icon.print {
            background: rgba(67, 97, 238, 0.1);
            color: var(--primary);
        }

        .stat-icon.completed {
            background: rgba(76, 201, 240, 0.1);
            color: var(--success);
        }

        .stat-icon.warning {
            background: rgba(248, 150, 30, 0.1);
            color: var(--warning);
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--gray);
            font-size: 0.9rem;
        }

        /* Preview Items */
        .preview-item {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .preview-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .preview-item strong {
            display: block;
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .preview-item p {
            color: var(--dark);
            line-height: 1.5;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .settings-container {
                gap: 1.5rem;
            }
        }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1.5rem;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1.5rem;
            }
            
            .user-profile {
                align-self: stretch;
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 1.5rem;
            }
            
            .header-title h1 {
                font-size: 1.75rem;
            }
            
            .settings-card {
                padding: 1.5rem;
            }
            
            .card-title {
                font-size: 1.35rem;
            }
        }

        @media (max-width: 576px) {
            .main-content {
                padding: 1.25rem;
            }
            
            .header {
                padding: 1.25rem;
            }
            
            .quick-stats {
                grid-template-columns: 1fr;
            }
        }
/* Mobile toggle button */
        .toggle-sidebar {
            display: none;
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 1.25rem;
            cursor: pointer;
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 999;
            box-shadow: 0 5px 20px rgba(67, 97, 238, 0.3);
            transition: all 0.3s ease;
        }
        .toggle-sidebar:hover {
            transform: scale(1.1) rotate(90deg);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
        }

        /* Overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
         @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: 5px 0 30px rgba(0, 0, 0, 0.15);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1.5rem;
            }
            .toggle-sidebar {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1.5rem;
            }
            .user-profile {
                align-self: stretch;
                justify-content: center;
            }
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            .sidebar-overlay.active {
                display: block;
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 1.5rem;
            }
            .header-title h1 {
                font-size: 1.75rem;
            }
            .card {
                padding: 1.75rem;
            }
            .card-value {
                font-size: 2.25rem;
            }
            .recent-activity {
                padding: 1.75rem;
            }
            .section-title {
                font-size: 1.35rem;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 100%;
                padding: 1rem 0;
            }
            .sidebar-header {
                padding: 0 1rem 1rem;
            }
            .sidebar-header img {
                width: 40px;
                height: 40px;
            }
            .sidebar-header h3 {
                font-size: 1.2rem;
            }
            .sidebar-menu {
                padding: 0 0.5rem;
            }
            .menu-item {
                padding: 0.85rem 1rem;
                border-radius: 0.5rem;
            }
            .menu-title {
                padding: 0 1rem;
            }
            .main-content {
                padding: 1.25rem;
            }
            .header {
                padding: 1.25rem;
            }
            .card-header {
                flex-direction: column;
                gap: 1.25rem;
            }
            .activity-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            .activity-icon {
                margin-right: 0;
            }
            .card-footer {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
            .toggle-sidebar {
                width: 45px;
                height: 45px;
                bottom: 20px;
                right: 20px;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }


        .settings-card {
            animation: fadeIn 0.6s ease-out;
        }

        .stat-card {
            animation: fadeIn 0.6s ease-out;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
    </style>
</head>
<body>
    <!-- Overlay for mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="https://cdn-icons-png.flaticon.com/512/3069/3069172.png" alt="Stationary Logo">
            <h3>StationaryPro</h3>
        </div>
        <div class="sidebar-menu">
            <p class="menu-title">Main</p>
            <a href="stationary_dashboard.php" class="menu-item<?= basename($_SERVER['PHP_SELF']) == 'stationary_dashboard.php' ? ' active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <p class="menu-title">Operations</p>
            <a href="print_requests.php" class="menu-item<?= basename($_SERVER['PHP_SELF']) == 'print_requests.php' ? ' active' : '' ?>">
                <i class="fas fa-print"></i>
                <span>Print Requests</span>
                <?php if (isset($print_count) && $print_count > 0): ?>
                    <span class="menu-badge"><?= $print_count ?></span>
                <?php endif; ?>
            </a>
            <a href="edit_stationery.php?id=<?= htmlspecialchars($_SESSION['stationary_id'] ?? '') ?>" class="menu-item<?= basename($_SERVER['PHP_SELF']) == 'edit_stationery.php' ? ' active' : '' ?>">
                <i class="fas fa-store"></i>
                <span>Shop Settings</span>
            </a>
            
            <p class="menu-title">Account</p>
            <a href="../admin/logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <div class="header-title">
                <h1>Shop Settings</h1>
                <p>Manage your stationary shop information and preferences</p>
            </div>
            
            <div class="user-profile">
                <img src="<?= htmlspecialchars($shop_data['logo']) ?>" alt="Shop Logo" style="border-radius: 50%; width: 50px; height: 50px;">
                <div class="user-info">
                    <h4><?= htmlspecialchars($_SESSION['stationary_admin_name'] ?? 'Admin') ?></h4>
                    <p>Stationary Admin</p>
                </div>
            </div>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $success_message ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= $error_message ?>
            </div>
        <?php endif; ?>

        <div class="settings-container">
            <div class="settings-card">
                <h2 class="card-title">
                    <i class="fas fa-store"></i>
                    Shop Information
                </h2>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label" for="name">Shop Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($shop_data['name'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($shop_data['email'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               value="<?= htmlspecialchars($shop_data['phone'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" required><?= htmlspecialchars($shop_data['address'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" name="description"><?= htmlspecialchars($shop_data['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="opening_hours">Opening Hours</label>
                        <input type="text" class="form-control" id="opening_hours" name="opening_hours" 
                               value="<?= htmlspecialchars($shop_data['opening_hours'] ?? '') ?>" 
                               placeholder="e.g., Mon-Fri: 9AM-6PM, Sat: 10AM-4PM">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="price">Price</label>
                        <input type="text" class="form-control" id="price" name="price" 
                               value="<?= htmlspecialchars($shop_data['price'] ?? '') ?>" 
                               placeholder="e.g., Tsh10.00">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Shop Logo</label>
                        <div class="file-upload">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to upload or drag and drop</p>
                            <span>PNG, JPG up to 5MB</span>
                            <input type="file" id="logo" name="logo" accept="image/*">
                        </div>
                        
                        <?php if (!empty($shop_data['logo'])): ?>
                            <div class="logo-preview">
                                <p>Current Logo:</p>
                                <img src="<?= htmlspecialchars($shop_data['logo']) ?>" alt="Shop Logo">
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="stationary_dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </form>
            </div>

            <div class="settings-card">
                <h2 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Shop Preview
                </h2>
                
                <div class="shop-preview">
                    <?php if (!empty($shop_data['logo'])): ?>
                        <div style="text-align: center; margin-bottom: 1.5rem;">
                            <img src="<?= htmlspecialchars($shop_data['logo']) ?>" alt="Shop Logo" 
                                 style="max-width: 150px; border-radius: 0.5rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                        </div>
                    <?php endif; ?>
                    
                    <div class="preview-item">
                        <strong>Shop Name:</strong>
                        <p><?= htmlspecialchars($shop_data['name'] ?? 'Not set') ?></p>
                    </div>
                    
                    <div class="preview-item">
                        <strong>Email:</strong>
                        <p><?= htmlspecialchars($shop_data['email'] ?? 'Not set') ?></p>
                    </div>
                    
                    <div class="preview-item">
                        <strong>Phone:</strong>
                        <p><?= htmlspecialchars($shop_data['phone'] ?? 'Not set') ?></p>
                    </div>
                    
                    <div class="preview-item">
                        <strong>Address:</strong>
                        <p><?= nl2br(htmlspecialchars($shop_data['address'] ?? 'Not set')) ?></p>
                    </div>
                    
                    <?php if (!empty($shop_data['description'])): ?>
                    <div class="preview-item">
                        <strong>Description:</strong>
                        <p><?= nl2br(htmlspecialchars($shop_data['description'])) ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($shop_data['opening_hours'])): ?>
                    <div class="preview-item">
                        <strong>Opening Hours:</strong>
                        <p><?= htmlspecialchars($shop_data['opening_hours']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($shop_data['price'])): ?>
                    <div class="preview-item">
                        <strong>Price:</strong>
                        <p><?= htmlspecialchars($shop_data['price']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="quick-stats">
                    <div class="stat-card">
                        <div class="stat-icon print">
                            <i class="fas fa-print"></i>
                        </div>
                        <div class="stat-value"><?= $pending_prints ?></div>
                        <div class="stat-label">Pending Prints</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon completed">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-value"><?= $completed_prints ?></div>
                        <div class="stat-label">Completed Jobs</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        
                        <div class="stat-label">Low Stock Items</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button class="toggle-sidebar" id="toggleSidebar">
        <i class="fas fa-bars"></i>
    </button>

    <script>
    // Sidebar toggle functionality
    const sidebar = document.querySelector('.sidebar'); // unified variable
    const toggleButton = document.getElementById('toggleSidebar');
    const overlay = document.getElementById('sidebarOverlay');

    toggleButton.addEventListener('click', function() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');

        // Update icon
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-bars');
        icon.classList.toggle('fa-times');
    });

    // Close sidebar when clicking on overlay
    overlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        this.classList.remove('active');
        const icon = toggleButton.querySelector('i');
        icon.classList.add('fa-bars');
        icon.classList.remove('fa-times');
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 992 &&
            !sidebar.contains(event.target) &&
            !toggleButton.contains(event.target) &&
            sidebar.classList.contains('active')
        ) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            const icon = toggleButton.querySelector('i');
            icon.classList.add('fa-bars');
            icon.classList.remove('fa-times');
        }
    });

    // Reset sidebar on window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            const icon = toggleButton.querySelector('i');
            icon.classList.add('fa-bars');
            icon.classList.remove('fa-times');
        }
    });

    // File upload functionality
    const fileUpload = document.querySelector('.file-upload');
    const fileInput = document.getElementById('logo');

    if (fileUpload && fileInput) {
        fileUpload.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = 'var(--primary)';
            this.style.backgroundColor = 'rgba(67, 97, 238, 0.05)';
        });

        fileUpload.addEventListener('dragleave', function() {
            this.style.borderColor = '#e2e8f0';
            this.style.backgroundColor = 'transparent';
        });

        fileUpload.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = '#e2e8f0';
            this.style.backgroundColor = 'transparent';

            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;

                // Show preview
                if (FileReader && e.dataTransfer.files[0]) {
                    const fr = new FileReader();
                    fr.onload = function() {
                        let preview = document.querySelector('.logo-preview');
                        if (!preview) {
                            preview = document.createElement('div');
                            preview.className = 'logo-preview';
                            fileUpload.parentNode.appendChild(preview);
                        }
                        preview.innerHTML = '<p>New Logo Preview:</p><img src="' + fr.result + '" alt="Logo Preview" style="max-width: 150px; max-height: 150px; border-radius: 0.5rem; margin-top: 10px;">';
                    }
                    fr.readAsDataURL(e.dataTransfer.files[0]);
                }
            }
        });

        // Handle file input change
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const fr = new FileReader();
                fr.onload = function() {
                    let preview = document.querySelector('.logo-preview');
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.className = 'logo-preview';
                        fileInput.parentNode.appendChild(preview);
                    }
                    preview.innerHTML = '<p>New Logo Preview:</p><img src="' + fr.result + '" alt="Logo Preview" style="max-width: 150px; max-height: 150px; border-radius: 0.5rem; margin-top: 10px;">';
                }
                fr.readAsDataURL(this.files[0]);
            }
        });
    }
</script>

</body>
</html>
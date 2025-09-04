
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("../connection.php");
include("sidebar.php");

// Initialize variables
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$program_filter = isset($_GET['program']) ? $conn->real_escape_string($_GET['program']) : '';
$year_filter = isset($_GET['year']) ? $conn->real_escape_string($_GET['year']) : '';

// Build the query
$where_conditions = [];
$query_params = [];

if (!empty($search)) {
    $where_conditions[] = "(fullname LIKE ? OR email LIKE ? OR reg LIKE ?)";
    $search_param = "%$search%";
    $query_params[] = $search_param;
    $query_params[] = $search_param;
    $query_params[] = $search_param;
}

if (!empty($program_filter)) {
    $where_conditions[] = "program = ?";
    $query_params[] = $program_filter;
}

if (!empty($year_filter)) {
    $where_conditions[] = "year = ?";
    $query_params[] = $year_filter;
}

// Get total number of users
$count_query = "SELECT COUNT(*) as total FROM users";
if (!empty($where_conditions)) {
    $count_query .= " WHERE " . implode(" AND ", $where_conditions);
}

$count_stmt = $conn->prepare($count_query);
if (!empty($query_params)) {
    $types = str_repeat('s', count($query_params));
    $count_stmt->bind_param($types, ...$query_params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_users = $count_result->fetch_assoc()['total'];
$count_stmt->close();

// Pagination
$per_page = 10;
$total_pages = ceil($total_users / $per_page);
$current_page = isset($_GET['page']) ? max(1, min($total_pages, intval($_GET['page']))) : 1;
$offset = ($current_page - 1) * $per_page;

// Get users with pagination
$query = "SELECT user_id, fullname, email, reg, program, year, created_at FROM users";
if (!empty($where_conditions)) {
    $query .= " WHERE " . implode(" AND ", $where_conditions);
}
$query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";

// Prepare and execute query
$stmt = $conn->prepare($query);
if (!empty($query_params)) {
    $types = str_repeat('s', count($query_params)) . 'ii';
    $params = array_merge($query_params, [$per_page, $offset]);
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param("ii", $per_page, $offset);
}

$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get unique programs and years for filters
$programs_result = $conn->query("SELECT DISTINCT program FROM users WHERE program IS NOT NULL ORDER BY program");
$programs = [];
while ($row = $programs_result->fetch_assoc()) {
    $programs[] = $row['program'];
}

$years_result = $conn->query("SELECT DISTINCT year FROM users WHERE year IS NOT NULL ORDER BY year");
$years = [];
while ($row = $years_result->fetch_assoc()) {
    $years[] = $row['year'];
}

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $user_id = intval($_POST['user_id']);
    
    $delete_sql = "DELETE FROM users WHERE user_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $user_id);
    
    if ($delete_stmt->execute()) {
        $success_message = "User deleted successfully!";
        // Refresh the page to show updated list
        header("Location: manage_users.php?" . $_SERVER['QUERY_STRING']);
        exit();
    } else {
        $error_message = "Error deleting user: " . $conn->error;
    }
    $delete_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - CBE Doc's Store</title>
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
        
        .filters-container {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .filter-label {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.95rem;
        }
        
        .filter-select, .filter-input {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border);
            border-radius: 0.5rem;
            font-size: 1rem;
            background: white;
            transition: all 0.3s ease;
        }
        
        .filter-select:focus, .filter-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        
        .filter-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            flex-wrap: wrap;
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
        }
        
        .btn-success {
            background: var(--secondary);
            color: white;
        }
        
        .btn-success:hover {
            background: #0da271;
        }
        
        .users-table-container {
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 2rem;
            overflow-x: auto;
        }
        
        .users-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .users-table th {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--dark);
            border-bottom: 1px solid var(--border);
        }
        
        .users-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
        }
        
        .users-table tr:last-child td {
            border-bottom: none;
        }
        
        .users-table tr:hover {
            background-color: #f8fafc;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .user-details {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-weight: 600;
            color: var(--dark);
        }
        
        .user-email {
            color: var(--gray);
            font-size: 0.875rem;
        }
        
        .badge {
            padding: 0.35rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .badge-primary {
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
        }
        
        .badge-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--secondary);
        }
        
        .badge-gray {
            background: rgba(100, 116, 139, 0.1);
            color: var(--gray);
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-icon {
            padding: 0.5rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .no-users {
            text-align: center;
            padding: 3rem;
            color: var(--gray);
        }
        
        .no-users i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #cbd5e1;
        }
        
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--secondary);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .stats-bar {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            flex: 1;
            min-width: 200px;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--gray);
            font-weight: 600;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }
        
        .pagination-item {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            border: 1px solid var(--border);
            color: var(--dark);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .pagination-item:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .pagination-item.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        @media (max-width: 1200px) {
            .main-content {
                margin-left: 0;
                padding: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .filter-row {
                grid-template-columns: 1fr;
            }
            
            .filter-actions {
                flex-direction: column;
            }
            
            .page-title {
                font-size: 1.75rem;
            }
            
            .stats-bar {
                flex-direction: column;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>
    
    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Manage Users</h1>
                <p class="page-subtitle">View and manage all registered users</p>
            </div>
            <a href="admin_register.php" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Add New User
            </a>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Statistics Bar -->
        <div class="stats-bar">
            <div class="stat-card">
                <div class="stat-value"><?php echo $total_users; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value"><?php echo count($programs); ?></div>
                <div class="stat-label">Programs</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value"><?php echo count($years); ?></div>
                <div class="stat-label">Academic Years</div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filters-container">
            <form method="GET" action="manage_users.php">
                <div class="filter-row">
                    <div class="filter-group">
                        <label class="filter-label">Search Users</label>
                        <input type="text" name="search" class="filter-input" placeholder="Search by name, email or registration..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Program</label>
                        <select name="program" class="filter-select">
                            <option value="">All Programs</option>
                            <?php foreach ($programs as $program): ?>
                                <option value="<?php echo htmlspecialchars($program); ?>" <?php echo $program_filter === $program ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($program); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Year</label>
                        <select name="year" class="filter-select">
                            <option value="">All Years</option>
                            <?php foreach ($years as $year): ?>
                                <option value="<?php echo htmlspecialchars($year); ?>" <?php echo $year_filter === $year ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($year); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="manage_users.php" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Clear Filters
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Users Table -->
        <div class="users-table-container">
            <?php if (empty($users)): ?>
                <div class="no-users">
                    <i class="fas fa-users"></i>
                    <h3>No users found</h3>
                    <p>Try adjusting your search or filters.</p>
                </div>
            <?php else: ?>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Registration Number</th>
                            <th>Program</th>
                            <th>Year</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <?php echo strtoupper(substr($user['fullname'], 0, 1)); ?>
                                        </div>
                                        <div class="user-details">
                                            <span class="user-name"><?php echo htmlspecialchars($user['fullname']); ?></span>
                                            <span class="user-email"><?php echo htmlspecialchars($user['email']); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-gray"><?php echo htmlspecialchars($user['reg']); ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-primary"><?php echo htmlspecialchars($user['program']); ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-success">Year <?php echo htmlspecialchars($user['year']); ?></span>
                                </td>
                                <td>
                                    <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-icon btn-secondary" title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                            <button type="submit" name="delete_user" class="btn btn-icon btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')" title="Delete User">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="pagination-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </main>

    <script>
        // Auto-submit form when filters change
        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
</body>
</html>
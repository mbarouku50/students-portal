<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ...existing code...
include '../connection.php';
$message = '';
$formData = [
    'name' => '', 'location' => '', 'phone' => '', 'email' => '', 
    'whatsapp' => '', 'description' => '', 'quantity' => '', 'price' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'name' => trim($_POST['name']),
        'location' => trim($_POST['location']),
        'phone' => trim($_POST['phone']),
        'email' => trim($_POST['email']),
        'whatsapp' => trim($_POST['whatsapp']),
        'description' => trim($_POST['description']),
        'quantity' => intval($_POST['quantity']),
        'price' => floatval($_POST['price']),
        'password' => $_POST['password']
    ];
    $salt = "CBE_DOCS_2023";
    $hashed_password = sha1($formData['password'] . $salt);
    if ($formData['name'] && $formData['location'] && $formData['phone'] && 
        $formData['email'] && $formData['whatsapp'] && 
        $formData['quantity'] >= 0 && $formData['price'] >= 0 && $formData['password']) {
        
        $stmt = $conn->prepare("INSERT INTO stationery (name, location, phone, email, whatsapp, description, quantity, price, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssssids', 
            $formData['name'], $formData['location'], $formData['phone'], 
            $formData['email'], $formData['whatsapp'], $formData['description'], 
            $formData['quantity'], $formData['price'], $hashed_password);
        
        if ($stmt->execute()) {
            $message = '<div class="notification success"><i class="fas fa-check-circle"></i> Stationery registered successfully!</div>';
            $formData = array_fill_keys(array_keys($formData), ''); // Clear form
        } else {
            $message = '<div class="notification error"><i class="fas fa-exclamation-circle"></i> Error: Could not register stationery.</div>';
        }
        $stmt->close();
    } else {
        $message = '<div class="notification warning"><i class="fas fa-exclamation-triangle"></i> Please fill all required fields correctly.</div>';
    }
}

// Delete functionality
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM stationery WHERE stationery_id = ?");
    $stmt->bind_param('i', $delete_id);
    if ($stmt->execute()) {
        $message = '<div class="notification success"><i class="fas fa-check-circle"></i> Stationery deleted successfully!</div>';
    } else {
        $message = '<div class="notification error"><i class="fas fa-exclamation-circle"></i> Error: Could not delete stationery.</div>';
    }
    $stmt->close();
}

// Fetch existing stationery items
$stationeryItems = [];
$result = $conn->query("SELECT * FROM stationery ORDER BY stationery_id DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $stationeryItems[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stationery Management | Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-hover: #3a56d4;
            --success: #06d6a0;
            --error: #ef476f;
            --warning: #ffd166;
            --text-main: #1a1a2e;
            --text-light: #64748b;
            --bg-light: #f8fafc;
            --border-color: #e2e8f0;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            --card-shadow-hover: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-main);
            line-height: 1.6;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 2rem;
            transition: all 0.3s ease;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .page-title i {
            color: var(--primary);
        }
        
        .tab-container {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 2rem;
        }
        
        .tab {
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-light);
            border-bottom: 3px solid transparent;
            transition: all 0.2s ease;
        }
        
        .tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* Form Styles */
        .form-container {
            background: white;
            border-radius: 0.75rem;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-main);
        }
        
        .form-label .required {
            color: var(--error);
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.2s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
        }
        
        .notification {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .notification i {
            font-size: 1.25rem;
        }
        
        .success {
            background-color: rgba(6, 214, 160, 0.1);
            color: #065f46;
            border-left: 4px solid var(--success);
        }
        
        .error {
            background-color: rgba(239, 71, 111, 0.1);
            color: #991b1b;
            border-left: 4px solid var(--error);
        }
        
        .warning {
            background-color: rgba(255, 209, 102, 0.1);
            color: #92400e;
            border-left: 4px solid var(--warning);
        }
        
        .form-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 2rem;
        }
        
        /* Stationery List Styles */
        .stationery-list {
            background: white;
            border-radius: 0.75rem;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .table-title {
            font-weight: 600;
            color: var(--text-main);
        }
        
        .table-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box input {
            padding: 0.5rem 1rem 0.5rem 2rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            font-size: 0.875rem;
            width: 200px;
        }
        
        .search-box i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 0.875rem;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background-color: #f1f5f9;
        }
        
        th {
            padding: 1rem 1.5rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.875rem;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .status-available {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-low {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-out {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .btn-view {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-view:hover {
            background-color: var(--primary-hover);
        }
        
        .btn-edit {
            background: white;
            border: 1px solid var(--border-color);
            color: var(--text-main);
        }
        
        .btn-edit:hover {
            background: var(--bg-light);
        }
        
        .btn-delete {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .btn-delete:hover {
            background: #fecaca;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
        }
        
        .pagination-info {
            font-size: 0.875rem;
            color: var(--text-light);
        }
        
        .pagination-controls {
            display: flex;
            gap: 0.5rem;
        }
        
        .page-btn {
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .page-btn:hover {
            background: var(--bg-light);
        }
        
        .page-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .page-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Responsive styles */
        @media (max-width: 992px) {
            .main-content {
                margin-left: 80px;
                padding: 1.5rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .form-container, .stationery-list {
                padding: 1.5rem;
            }
            
            .page-header, .table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .table-actions {
                width: 100%;
                justify-content: space-between;
            }
            
            .search-box input {
                width: 100%;
            }
            
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
        
        @media (max-width: 576px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .form-container, .stationery-list {
                padding: 1rem;
                box-shadow: none;
                border-radius: 0;
            }
            
            .pagination {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-pencil-alt"></i> Stationery Management</h1>
        </div>
        
        <div class="tab-container">
            <div class="tab active" onclick="switchTab('register')">Register New</div>
            <div class="tab" onclick="switchTab('view')">View Stationery</div>
        </div>
        
        <div id="register-tab" class="tab-content active">
            <div class="form-container">
                <?= $message ?>
                
                <form method="POST" autocomplete="off">
                    <div class="form-group">
                        <label for="name" class="form-label">
                            Stationery Name <span class="required">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="form-control" 
                               value="<?= htmlspecialchars($formData['name']) ?>" 
                               required
                               placeholder="e.g. A4 Paper, Blue Pens">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="location" class="form-label">
                                Location <span class="required">*</span>
                            </label>
                            <input type="text" 
                                   name="location" 
                                   id="location" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($formData['location']) ?>" 
                                   required
                                   placeholder="e.g. Room 12, Building A">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone" class="form-label">
                                Phone Number <span class="required">*</span>
                            </label>
                            <input type="tel" 
                                   name="phone" 
                                   id="phone" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($formData['phone']) ?>" 
                                   required
                                   placeholder="e.g. 0712345678">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email" class="form-label">
                                Email <span class="required">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($formData['email']) ?>" 
                                   required
                                   placeholder="e.g. contact@example.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="whatsapp" class="form-label">
                                WhatsApp <span class="required">*</span>
                            </label>
                            <input type="tel" 
                                   name="whatsapp" 
                                   id="whatsapp" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($formData['whatsapp']) ?>" 
                                   required
                                   placeholder="e.g. 0712345678">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">
                            Description
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  class="form-control"
                                  placeholder="Optional description or specifications"><?= htmlspecialchars($formData['description']) ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="quantity" class="form-label">
                                Quantity <span class="required">*</span>
                            </label>
                            <input type="number" 
                                   name="quantity" 
                                   id="quantity" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($formData['quantity']) ?>" 
                                   min="0" 
                                   required
                                   placeholder="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="price" class="form-label">
                                Price (Tsh per copy) <span class="required">*</span>
                            </label>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-light);">Tsh</span>
                                <input type="number" 
                                       name="price" 
                                       id="price" 
                                       class="form-control" 
                                       style="padding-left: 2.5rem;"
                                       value="<?= htmlspecialchars($formData['price']) ?>" 
                                       min="0" 
                                       step="0.01" 
                                       required
                                       placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Password <span class="required">*</span></label>
                        <input type="password" name="password" id="password" class="form-control" required placeholder="Set a password for this stationery">
                    </div>
                    
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Register Stationery
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div id="view-tab" class="tab-content">
            <div class="stationery-list">
                <div class="table-header">
                    <h3 class="table-title">Stationery Inventory</h3>
                    <div class="table-actions">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search stationery..." id="search-input">
                        </div>
                        <button class="filter-btn">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stationeryItems)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-light);">
                                    No stationery items found. Register a new item to get started.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($stationeryItems as $item): ?>
                                <?php
                                // Determine status
                                $statusClass = 'status-available';
                                $statusText = 'Available';
                                if ($item['quantity'] <= 0) {
                                    $statusClass = 'status-out';
                                    $statusText = 'Out of Stock';
                                } elseif ($item['quantity'] < 10) {
                                    $statusClass = 'status-low';
                                    $statusText = 'Low Stock';
                                }
                                ?>
                                <tr>
                                    <td>#<?= htmlspecialchars($item['stationery_id']) ?></td>
                                    <td><?= htmlspecialchars($item['name']) ?></td>
                                    <td><?= htmlspecialchars($item['location']) ?></td>
                                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                                    <td>Tsh <?= number_format($item['price'], 2) ?></td>
                                    <td><span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="edit_stationery.php?id=<?= htmlspecialchars($item['stationery_id']) ?>" class="action-btn btn-edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="?delete=<?= htmlspecialchars($item['stationery_id']) ?>" class="action-btn btn-delete" onclick="return confirm('Are you sure you want to delete this stationery?');">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <div class="pagination">
                    <div class="pagination-info">Showing 1 to <?= count($stationeryItems) ?> of <?= count($stationeryItems) ?> entries</div>
                    <div class="pagination-controls">
                        <button class="page-btn disabled">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="page-btn active">1</button>
                        <button class="page-btn disabled">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script>
        // Tab switching functionality
        function switchTab(tabName) {
            // Update tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelector(`.tab:nth-child(${tabName === 'register' ? 1 : 2})`).classList.add('active');
            
            // Update content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(`${tabName}-tab`).classList.add('active');
        }
        
        // Simple client-side validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const price = document.getElementById('price').value;
            const quantity = document.getElementById('quantity').value;
            
            if (price < 0 || quantity < 0) {
                e.preventDefault();
                alert('Please enter valid positive numbers for price and quantity');
            }
        });
        
        // Simple search functionality
        document.getElementById('search-input').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
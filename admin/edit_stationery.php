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
    header("Location: login.php");
    exit();
}

$message = '';
$stationery_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$formData = [
    'name' => '', 'location' => '', 'phone' => '', 'email' => '', 'whatsapp' => '', 'description' => '', 'quantity' => '', 'price' => '', 'password' => ''
];

if ($stationery_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM stationery WHERE stationery_id = ? LIMIT 1");
    $stmt->bind_param('i', $stationery_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $row = $result->fetch_assoc()) {
        $formData = $row;
        $formData['price'] = number_format($formData['price'], 2, '.', '');
    } else {
        $message = '<div class="notification error">Stationery not found.</div>';
    }
    $stmt->close();
} else {
    $message = '<div class="notification error">Invalid stationery ID.</div>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $stationery_id > 0) {
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
    $update_sql = "UPDATE stationery SET name=?, location=?, phone=?, email=?, whatsapp=?, description=?, quantity=?, price=?";
    $params = [$formData['name'], $formData['location'], $formData['phone'], $formData['email'], $formData['whatsapp'], $formData['description'], $formData['quantity'], $formData['price']];
    $types = 'ssssssdi';
    if (!empty($formData['password'])) {
        $salt = "CBE_DOCS_2023";
        $hashed_password = sha1($formData['password'] . $salt);
        $update_sql .= ", password=?";
        $params[] = $hashed_password;
        $types .= 's';
    }
    $update_sql .= " WHERE stationery_id=?";
    $params[] = $stationery_id;
    $types .= 'i';
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param($types, ...$params);
    if ($stmt->execute()) {
        $message = '<div class="notification success">Stationery updated successfully!</div>';
    } else {
        $message = '<div class="notification error">Error: Could not update stationery.</div>';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Stationery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background: #f4f6fb; margin: 0; }
        .main-content { margin-left: 240px; padding: 2rem 1rem; }
        .form-container { background: #fff; border-radius: 12px; box-shadow: 0 2px 16px rgba(44,62,80,0.08); max-width: 500px; margin: 2rem auto; padding: 2rem; }
        h2 { margin-bottom: 1.5rem; color: #232946; }
        label { font-weight: 600; color: #232946; }
        input, textarea { width: 100%; padding: 0.7rem; margin: 0.5rem 0 1rem 0; border-radius: 8px; border: 1px solid #bfc8e2; font-size: 1rem; }
        button { background: #232946; color: #fff; border: none; border-radius: 8px; padding: 0.8rem 1.5rem; font-size: 1.08rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        button:hover { background: #353a50; }
        .notification { margin-bottom: 1rem; padding: 0.7rem 1rem; border-radius: 8px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .warning { background: #fff3cd; color: #856404; }
        @media (max-width: 992px) { .main-content { margin-left: 60px; } }
        @media (max-width: 576px) { .main-content { margin-left: 0; padding: 1rem 0.2rem; } .form-container { padding: 1rem; } }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="form-container">
            <h2><i class="fas fa-edit"></i> Edit Stationery</h2>
            <?= $message ?>
            <form method="POST" autocomplete="off">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($formData['name']) ?>" required>
                <label for="location">Location</label>
                <input type="text" name="location" id="location" value="<?= htmlspecialchars($formData['location']) ?>" required>
                <label for="phone">Phone</label>
                <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($formData['phone']) ?>" required>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($formData['email']) ?>" required>
                <label for="whatsapp">WhatsApp</label>
                <input type="text" name="whatsapp" id="whatsapp" value="<?= htmlspecialchars($formData['whatsapp']) ?>" required>
                <label for="description">Description</label>
                <textarea name="description" id="description" rows="3"><?= htmlspecialchars($formData['description']) ?></textarea>
                <label for="quantity">Quantity</label>
                <input type="number" name="quantity" id="quantity" min="0" value="<?= htmlspecialchars($formData['quantity']) ?>" required>
                <label for="price">Price (Tsh per copy)</label>
                <input type="number" name="price" id="price" min="0" step="0.01" value="<?= htmlspecialchars($formData['price']) ?>" required>
                <label for="password">New Password (leave blank to keep current)</label>
                <input type="password" name="password" id="password" placeholder="Enter new password if you want to change">
                <button type="submit"><i class="fas fa-save"></i> Update Stationery</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['stationary_admin_id'])) {
    header('Location: ../admin/admin_login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stationary Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Inter', Arial, sans-serif; 
            background: #f4f6fb; 
            margin: 0; 
        }
        .main-content { margin-left: 0; padding: 2rem 1rem; }
        .dashboard-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; }
        .dashboard-header h2 { color: #232946; font-size: 2rem; font-weight: 700; }
        .dashboard-header .welcome { color: #3498db; font-weight: 600; }
        .dashboard-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 2rem; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 16px rgba(44,62,80,0.08); padding: 2rem 1.5rem; text-align: center; }
        .card i { font-size: 2.5rem; color: #3498db; margin-bottom: 1rem; }
        .card h3 { margin-bottom: 0.7rem; color: #232946; }
        .card p { color: #555; margin-bottom: 1.5rem; }
        .card a { background: #3498db; color: #fff; border: none; border-radius: 6px; padding: 0.7rem 1.5rem; font-size: 1.1rem; text-decoration: none; display: inline-block; transition: background 0.2s; }
        .card a:hover { background: #217dbb; }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="dashboard-header">
            <h2><i class="fas fa-store"></i> Stationary Dashboard</h2>
            <span class="welcome">Welcome, <?= htmlspecialchars($_SESSION['stationary_admin_name'] ?? 'Stationary Admin') ?></span>
        </div>
        <div class="dashboard-cards">
            <div class="card">
                <i class="fas fa-print"></i>
                <h3>View Print Requests</h3>
                <p>See all print requests submitted by students.</p>
                <a href="print_requests.php">View Requests</a>
            </div>
            <div class="card">
                <i class="fas fa-cogs"></i>
                <h3>Manage Stationary Info</h3>
                <p>Update your shop details and contact information.</p>
                <a href="edit_stationery.php?id=<?= htmlspecialchars($_SESSION['stationary_id'] ?? '') ?>">Edit Info</a>
            </div>
            <div class="card">
                <i class="fas fa-sign-out-alt"></i>
                <h3>Logout</h3>
                <p>Sign out of your dashboard.</p>
                <a href="../admin/logout.php">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>

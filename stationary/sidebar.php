<?php
// sidebar.php
?>
<style>
.sidebar {
    width: 280px;
    background: linear-gradient(180deg, #4361ee 0%, #3f37c9 100%);
    color: #fff;
    padding: 1.5rem 0;
    position: fixed;
    height: 100vh;
    transition: all 0.3s;
    z-index: 1000;
}
.sidebar-header {
    display: flex;
    align-items: center;
    padding: 0 1.5rem;
    margin-bottom: 2rem;
}
.sidebar-header img {
    width: 40px;
    height: 40px;
    margin-right: 10px;
}
.sidebar-header h3 {
    font-weight: 600;
    font-size: 1.2rem;
}
.sidebar-menu {
    padding: 0 1rem;
}
.menu-title {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgba(255, 255, 255, 0.6);
    padding: 0 1rem;
    margin: 1.5rem 0 0.5rem;
}
.menu-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    margin-bottom: 0.25rem;
    cursor: pointer;
    transition: all 0.2s;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
}
.menu-item:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: #fff;
}
.menu-item.active {
    background-color: rgba(255, 255, 255, 0.2);
    color: #fff;
}
.menu-item i {
    margin-right: 0.75rem;
    font-size: 1.1rem;
    width: 24px;
    text-align: center;
}
@media (max-width: 992px) {
    .sidebar {
        margin-left: -280px;
    }
    .sidebar.active {
        margin-left: 0;
    }
}
</style>
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
        </a>
        <a href="edit_stationery.php?id=<?= htmlspecialchars($_SESSION['stationary_id'] ?? '') ?>" class="menu-item<?= basename($_SERVER['PHP_SELF']) == 'edit_stationery.php' ? ' active' : '' ?>">
            <i class="fas fa-store"></i>
            <span>Shop Settings</span>
        </a>
        <a href="inventory.php" class="menu-item<?= basename($_SERVER['PHP_SELF']) == 'inventory.php' ? ' active' : '' ?>">
            <i class="fas fa-boxes"></i>
            <span>Inventory</span>
        </a>
        <a href="orders.php" class="menu-item<?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? ' active' : '' ?>">
            <i class="fas fa-clipboard-list"></i>
            <span>Orders</span>
        </a>
        <p class="menu-title">Account</p>
        <a href="../admin/logout.php" class="menu-item">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<button class="toggle-sidebar" id="toggleSidebar">
    <i class="fas fa-bars"></i>
</button>

<script>
    // Toggle sidebar on mobile
    document.getElementById('toggleSidebar').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('active');
    });
</script>
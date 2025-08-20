<?php
// sidebar.php
?>
<style>
.sidebar {
    width: 280px;
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

/* Responsive design */
@media (max-width: 1200px) {
    .sidebar {
        width: 250px;
    }
    .sidebar-header {
        padding: 0 1.25rem 1.25rem;
    }
    .menu-item {
        padding: 0.75rem 1rem;
    }
}

@media (max-width: 992px) {
    .sidebar {
        margin-left: -280px;
        box-shadow: 5px 0 30px rgba(0, 0, 0, 0.15);
        transform: translateX(-100%);
    }
    .sidebar.active {
        margin-left: 0;
        transform: translateX(0);
    }
    .toggle-sidebar {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .main-content {
        margin-left: 0 !important;
        width: 100% !important;
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
    .toggle-sidebar {
        width: 45px;
        height: 45px;
        bottom: 20px;
        right: 20px;
    }
}

/* Animation for sidebar items */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.menu-item {
    animation: slideIn 0.4s ease forwards;
}

.menu-item:nth-child(1) { animation-delay: 0.1s; }
.menu-item:nth-child(2) { animation-delay: 0.15s; }
.menu-item:nth-child(3) { animation-delay: 0.2s; }
.menu-item:nth-child(4) { animation-delay: 0.25s; }
.menu-item:nth-child(5) { animation-delay: 0.3s; }
.menu-item:nth-child(6) { animation-delay: 0.35s; }
.menu-item:nth-child(7) { animation-delay: 0.4s; }
.menu-item:nth-child(8) { animation-delay: 0.45s; }

/* Scrollbar styling for sidebar */
.sidebar::-webkit-scrollbar {
    width: 5px;
}
.sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}
.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
}
.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
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
    .sidebar-overlay.active {
        display: block;
        opacity: 1;
    }
}
</style>

<!-- Overlay for mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

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
            <?php
            // Show badge for pending print requests
            if (isset($print_count) && $print_count > 0): ?>
                <span class="menu-badge"><?= $print_count ?></span>
            <?php endif; ?>
        </a>
        <a href="edit_stationery.php?id=<?= htmlspecialchars($_SESSION['stationary_id'] ?? '') ?>" class="menu-item<?= basename($_SERVER['PHP_SELF']) == 'edit_stationery.php' ? ' active' : '' ?>">
            <i class="fas fa-store"></i>
            <span>Shop Settings</span>
        </a>
        <a href="inventory.php" class="menu-item<?= basename($_SERVER['PHP_SELF']) == 'inventory.php' ? ' active' : '' ?>">
            <i class="fas fa-boxes"></i>
            <span>Inventory</span>
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
    const sidebar = document.querySelector('.sidebar');
    const toggleButton = document.getElementById('toggleSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    toggleButton.addEventListener('click', function() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        
        // Update icon based on state
        const icon = this.querySelector('i');
        if (icon.classList.contains('fa-bars')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });
    
    // Close sidebar when clicking on overlay
    overlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        this.classList.remove('active');
        const icon = toggleButton.querySelector('i');
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
    });
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 992 && 
            !sidebar.contains(event.target) && 
            !toggleButton.contains(event.target) && 
            sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            const icon = toggleButton.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });
    
    // Add resize event to handle sidebar on window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            const icon = toggleButton.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });
</script>
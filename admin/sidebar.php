<?php
include("../connection.php");

// Get counts from database
$pending_print_count = 0;
$total_users_count = 0;

// Count pending print requests
$pending_query = "SELECT COUNT(*) as count FROM print_jobs WHERE status = 'pending'";
$pending_result = $conn->query($pending_query);
if ($pending_result) {
    $pending_print_count = $pending_result->fetch_assoc()['count'];
}

// Count total users (adjust table name as needed)
$users_query = "SELECT COUNT(*) as count FROM users";
$users_result = $conn->query($users_query);
if ($users_result) {
    $total_users_count = $users_result->fetch_assoc()['count'];
}
?>

<button id="sidebarMenuBtn" aria-label="Open menu" class="sidebar-menu-btn">
    <span></span>
    <span></span>
    <span></span>
</button>
<div class="sidebar-overlay" id="sidebarOverlay" tabindex="-1" aria-hidden="true"></div>

<style>
    :root {
        --sidebar-bg: #1e293b;
        --sidebar-active-bg: #334155;
        --sidebar-text: #e2e8f0;
        --sidebar-active-text: #ffffff;
        --sidebar-hover: #2c3e50;
        --sidebar-accent: #3b82f6;
        --sidebar-border: #334155;
        --sidebar-icon-size: 1.1rem;
        --sidebar-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --sidebar-width: 280px;
        --sidebar-collapsed-width: 80px;
    }

    /* Hamburger menu button */
    .sidebar-menu-btn {
        position: fixed;
        top: 18px;
        left: 18px;
        z-index: 1001;
        width: 44px;
        height: 44px;
        background: #334155;
        border: none;
        border-radius: 8px;
        display: none;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        cursor: pointer;
        transition: background 0.2s;
    }
    
    .sidebar-menu-btn:hover {
        background: #475569;
    }
    
    .sidebar-menu-btn span {
        display: block;
        width: 26px;
        height: 3px;
        background: #fff;
        margin: 4px 0;
        border-radius: 2px;
        transition: 0.3s;
    }
    
    /* Overlay for mobile */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 199;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s, visibility 0.3s;
    }
    
    .sidebar-overlay.open {
        opacity: 1;
        visibility: visible;
    }

    /* Sidebar container */
    .sidebar {
        width: var(--sidebar-width);
        background: var(--sidebar-bg);
        color: var(--sidebar-text);
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        display: flex;
        flex-direction: column;
        z-index: 200;
        border-right: 1px solid var(--sidebar-border);
        transition: transform 0.3s ease, width 0.3s ease;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--sidebar-accent) var(--sidebar-bg);
        box-shadow: 2px 0 16px rgba(0,0,0,0.08);
    }

    .sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: var(--sidebar-bg);
    }

    .sidebar::-webkit-scrollbar-thumb {
        background-color: var(--sidebar-accent);
        border-radius: 6px;
    }

    /* Logo section */
    .sidebar-logo {
        display: flex;
        align-items: center;
        padding: 1.5rem 1.5rem 1rem;
        border-bottom: 1px solid var(--sidebar-border);
        position: sticky;
        top: 0;
        background: var(--sidebar-bg);
        z-index: 10;
        min-height: 80px;
        box-sizing: border-box;
    }

    .sidebar-logo img {
        height: 32px;
        width: 32px;
        object-fit: contain;
        margin-right: 12px;
        filter: brightness(0) invert(1);
    }

    .sidebar-logo span {
        font-size: 1.25rem;
        font-weight: 700;
        color: white;
        letter-spacing: 0.5px;
        transition: opacity 0.3s;
    }

    /* Section headers */
    .sidebar-header {
        padding: 1.25rem 1.5rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #94a3b8;
        margin-top: 0.5rem;
        transition: opacity 0.3s;
        white-space: nowrap;
        overflow: hidden;
    }

    /* Menu items */
    .sidebar-menu {
        list-style: none;
        padding: 0.5rem 0.75rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        margin-top: 0.5rem;
        margin-bottom: 1rem;
    }

    .sidebar-menu li {
        width: 100%;
        position: relative;
    }

    .sidebar-menu a {
        color: var(--sidebar-text);
        text-decoration: none;
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        transition: var(--sidebar-transition);
        font-size: 0.95rem;
        border-radius: 6px;
        gap: 0.75rem;
        font-weight: 500;
        position: relative;
        white-space: nowrap;
        overflow: hidden;
    }

    .sidebar-menu a:hover {
        background: var(--sidebar-hover);
        color: var(--sidebar-active-text);
    }

    .sidebar-menu a.active {
        background: var(--sidebar-active-bg);
        color: var(--sidebar-active-text);
        font-weight: 600;
    }

    .sidebar-menu a.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: var(--sidebar-accent);
        border-radius: 0 4px 4px 0;
    }

    .sidebar-menu i {
        font-size: var(--sidebar-icon-size);
        width: 24px;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-shrink: 0;
    }

    .sidebar-menu .badge {
        margin-left: auto;
        background: var(--sidebar-accent);
        color: white;
        border-radius: 10px;
        padding: 0.2rem 0.5rem;
        font-size: 0.7rem;
        font-weight: 700;
        min-width: 24px;
        text-align: center;
        transition: opacity 0.3s;
    }

    /* Footer section */
    .sidebar-footer {
        padding: 1rem;
        border-top: 1px solid var(--sidebar-border);
        margin-top: auto;
        position: sticky;
        bottom: 0;
        background: var(--sidebar-bg);
        z-index: 10;
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem;
        border-radius: 6px;
        transition: var(--sidebar-transition);
        cursor: pointer;
    }

    .user-profile:hover {
        background: var(--sidebar-hover);
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--sidebar-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .user-info {
        flex: 1;
        overflow: hidden;
        transition: opacity 0.3s;
    }

    .user-name {
        font-weight: 600;
        font-size: 0.9rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-role {
        font-size: 0.75rem;
        color: #94a3b8;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .logout-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: #f87171;
        text-decoration: none;
        font-weight: 500;
        border-radius: 6px;
        transition: var(--sidebar-transition);
        margin-top: 0.5rem;
        white-space: nowrap;
        overflow: hidden;
    }

    .logout-link:hover {
        background: rgba(239, 68, 68, 0.1);
    }

    .logout-link i {
        font-size: var(--sidebar-icon-size);
        width: 24px;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-shrink: 0;
    }

    /* Collapsed state */
    .sidebar.collapsed {
        width: var(--sidebar-collapsed-width);
    }

    .sidebar.collapsed .sidebar-logo span,
    .sidebar.collapsed .sidebar-header,
    .sidebar.collapsed .sidebar-menu span,
    .sidebar.collapsed .sidebar-menu .badge,
    .sidebar.collapsed .user-info,
    .sidebar.collapsed .logout-link span {
        opacity: 0;
        visibility: hidden;
        width: 0;
        height: 0;
        overflow: hidden;
    }

    .sidebar.collapsed .sidebar-logo {
        justify-content: center;
        padding: 1.5rem 0.5rem;
    }

    .sidebar.collapsed .sidebar-logo img {
        margin-right: 0;
    }

    .sidebar.collapsed .sidebar-menu a {
        justify-content: center;
        padding: 0.75rem 0.5rem;
    }

    .sidebar.collapsed .user-profile {
        justify-content: center;
        padding: 0.5rem 0;
    }

    .sidebar.collapsed .logout-link {
        justify-content: center;
        padding: 0.75rem 0.5rem;
    }

    /* Tooltips for collapsed state */
    .sidebar.collapsed .sidebar-menu li {
        position: relative;
    }

    .sidebar.collapsed .sidebar-menu a:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
        background: var(--sidebar-bg);
        color: var(--sidebar-text);
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-size: 0.9rem;
        white-space: nowrap;
        margin-left: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 300;
        pointer-events: none;
    }

    /* Responsive styles */
    @media (max-width: 900px) {
        .sidebar {
            width: var(--sidebar-collapsed-width);
        }
        
        .sidebar-logo span,
        .sidebar-header,
        .sidebar-menu span,
        .sidebar-menu .badge,
        .user-info,
        .logout-link span {
            opacity: 0;
            visibility: hidden;
            width: 0;
            height: 0;
            overflow: hidden;
        }
        
        .sidebar-logo {
            justify-content: center;
            padding: 1.5rem 0.5rem;
        }
        
        .sidebar-logo img {
            margin-right: 0;
        }
        
        .sidebar-menu a {
            justify-content: center;
            padding: 0.75rem 0.5rem;
        }
        
        .user-profile {
            justify-content: center;
            padding: 0.5rem 0;
        }
        
        .logout-link {
            justify-content: center;
            padding: 0.75rem 0.5rem;
        }
        
        /* Show hamburger menu button */
        .sidebar-menu-btn {
            display: flex;
        }
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            width: var(--sidebar-width);
            box-shadow: 2px 0 16px rgba(0,0,0,0.18);
        }
        
        .sidebar.open {
            transform: translateX(0);
        }
        
        .sidebar-overlay.open {
            opacity: 1;
            visibility: visible;
        }
        
        .sidebar-logo span,
        .sidebar-header,
        .sidebar-menu span,
        .sidebar-menu .badge,
        .user-info,
        .logout-link span {
            opacity: 1;
            visibility: visible;
            width: auto;
            height: auto;
            overflow: visible;
        }
        
        .sidebar-logo {
            justify-content: flex-start;
            padding: 1.5rem 1.5rem 1rem;
        }
        
        .sidebar-logo img {
            margin-right: 12px;
        }
        
        .sidebar-menu a {
            justify-content: flex-start;
            padding: 0.75rem 1rem;
        }
        
        .user-profile {
            justify-content: flex-start;
            padding: 0.5rem;
        }
        
        .logout-link {
            justify-content: flex-start;
            padding: 0.75rem 1rem;
        }
        
        /* Show hamburger menu button */
        .sidebar-menu-btn {
            display: flex;
        }
        
        /* Adjust menu for mobile */
        .sidebar-menu a.active::before {
            width: 4px;
            height: 100%;
            border-radius: 0 4px 4px 0;
        }
    }

    @media (max-width: 480px) {
        .sidebar {
            width: 100%;
            max-width: 320px;
        }
    }
</style>

<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/Document_icon_%28white%29.svg" alt="Logo">
        <span>CBE Doc's Store</span>
    </div>
    
    <div class="sidebar-header">Navigation</div>
    <ul class="sidebar-menu">
        <li>
            <a href="admin_dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : '' ?>" data-tooltip="Dashboard">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="manage_users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'active' : '' ?>" data-tooltip="Manage Users">
                <i class="fas fa-users-cog"></i>
                <span>Manage Users</span>
                <span class="badge"><?php echo $total_users_count; ?></span>
            </a>
        </li>
        <li>
            <a href="manage_documents.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_documents.php' ? 'active' : '' ?>" data-tooltip="Documents">
                <i class="fas fa-file-alt"></i>
                <span>Documents</span>
            </a>
        </li>
        <li>
            <a href="manage_courses.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_courses.php' ? 'active' : '' ?>" data-tooltip="Courses">
                <i class="fas fa-book-open"></i>
                <span>Courses</span>
            </a>
        </li>
        <li>
            <a href="admin_register_stationery.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_register_stationery.php' ? 'active' : '' ?>" data-tooltip="Stationary">
                <i class="fas fa-book-open"></i>
                <span>Stationary</span>
            </a>
        </li>
    </ul>
    
    <div class="sidebar-header">Reports</div>
    <ul class="sidebar-menu">
        <li>
            <a href="reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>" data-tooltip="Analytics">
                <i class="fas fa-chart-bar"></i>
                <span>Analytics</span>
            </a>
        </li>
        <li>
            <a href="print_requests.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'print_requests.php' ? 'active' : '' ?>" data-tooltip="Print Requests">
                <i class="fas fa-print"></i>
                <span>Print Requests</span>
                <span class="badge"><?php echo $pending_print_count; ?></span>
            </a>
        </li>
    </ul>
    
    <div class="sidebar-header">System</div>
    <ul class="sidebar-menu">
        <li>
            <a href="system_settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'system_settings.php' ? 'active' : '' ?>" data-tooltip="Settings">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </li>
        <li>
            <a href="backup.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'backup.php' ? 'active' : '' ?>" data-tooltip="Backups">
                <i class="fas fa-database"></i>
                <span>Backups</span>
            </a>
        </li>
    </ul>
    
    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar"><?php echo substr($_SESSION['stationary_admin_name'] ?? 'AD', 0, 2); ?></div>
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($_SESSION['stationary_admin_name'] ?? 'Admin User'); ?></div>
                <div class="user-role"><?php echo htmlspecialchars($_SESSION['stationary_admin_role'] ?? 'Administrator'); ?></div>
            </div>
        </div>
        
        <a href="logout.php" class="logout-link" data-tooltip="Logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('sidebarMenuBtn');
        
        // Toggle sidebar function
        function toggleSidebar() {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('open');
            document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
        }
        
        // Close sidebar function
        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
            document.body.style.overflow = '';
        }
        
        // Event listeners
        menuBtn.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', closeSidebar);
        
        // Handle window resize
        function handleResize() {
            if (window.innerWidth <= 768) {
                menuBtn.style.display = 'flex';
                closeSidebar();
            } else {
                menuBtn.style.display = 'none';
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
                document.body.style.overflow = '';
            }
            
            // Handle automatic collapsing on medium screens (improved for tablets)
            if (window.innerWidth <= 900 && window.innerWidth > 768) {
                sidebar.classList.add('collapsed');
            } else {
                sidebar.classList.remove('collapsed');
            }
        }
        
        // Initialize and add event listener
        handleResize();
        window.addEventListener('resize', handleResize);
        
        // Highlight active menu item
        const currentPage = window.location.pathname.split('/').pop();
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active');
            }
        });
        
        // Add keyboard accessibility
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('open')) {
                closeSidebar();
            }
        });
    });
</script>
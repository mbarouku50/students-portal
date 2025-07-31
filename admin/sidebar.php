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
    }

    .sidebar {
        width: 280px;
        background: var(--sidebar-bg);
        color: var(--sidebar-text);
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        display: flex;
        flex-direction: column;
        z-index: 100;
        border-right: 1px solid var(--sidebar-border);
        transition: var(--sidebar-transition);
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--sidebar-accent) var(--sidebar-bg);
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

    .sidebar-logo {
        display: flex;
        align-items: center;
        padding: 1.5rem 1.5rem 1rem;
        border-bottom: 1px solid var(--sidebar-border);
        position: sticky;
        top: 0;
        background: var(--sidebar-bg);
        z-index: 10;
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
    }

    .sidebar-header {
        padding: 1.25rem 1.5rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #94a3b8;
        margin-top: 0.5rem;
    }

    .sidebar-menu {
        list-style: none;
        padding: 0.5rem 0.75rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        margin-top: 0.5rem;
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
    }

    .sidebar-menu .badge {
        margin-left: auto;
        background: var(--sidebar-accent);
        color: white;
        border-radius: 10px;
        padding: 0.2rem 0.5rem;
        font-size: 0.7rem;
        font-weight: 700;
    }

    .sidebar-footer {
        padding: 1rem;
        border-top: 1px solid var(--sidebar-border);
        margin-top: auto;
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
    }

    .user-info {
        flex: 1;
        overflow: hidden;
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
    }

    /* Collapsed state */
    .sidebar.collapsed {
        width: 80px;
    }

    .sidebar.collapsed .sidebar-logo span,
    .sidebar.collapsed .sidebar-header,
    .sidebar.collapsed .sidebar-menu span,
    .sidebar.collapsed .sidebar-menu .badge,
    .sidebar.collapsed .user-info,
    .sidebar.collapsed .logout-link span {
        display: none;
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

    /* Responsive */
    @media (max-width: 992px) {
        .sidebar {
            width: 80px;
        }
        
        .sidebar-logo span,
        .sidebar-header,
        .sidebar-menu span,
        .sidebar-menu .badge,
        .user-info,
        .logout-link span {
            display: none;
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
    }

    @media (max-width: 576px) {
        .sidebar {
            width: 100%;
            height: auto;
            position: relative;
            flex-direction: row;
            flex-wrap: wrap;
            padding: 0.5rem;
            border-right: none;
            border-bottom: 1px solid var(--sidebar-border);
        }
        
        .sidebar-logo {
            width: 100%;
            justify-content: center;
            padding: 0.5rem;
            border-bottom: none;
        }
        
        .sidebar-header {
            display: none;
        }
        
        .sidebar-menu {
            flex-direction: row;
            flex-wrap: wrap;
            gap: 0.25rem;
            padding: 0.25rem;
            margin-top: 0;
        }
        
        .sidebar-menu li {
            width: auto;
        }
        
        .sidebar-menu a {
            padding: 0.5rem;
            border-radius: 6px;
        }
        
        .sidebar-menu a.active::before {
            width: 100%;
            height: 3px;
            top: auto;
            bottom: 0;
            border-radius: 3px 3px 0 0;
        }
        
        .sidebar-footer {
            display: none;
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
            <a href="admin_dashboard.php" class="active">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="manage_users.php">
                <i class="fas fa-users-cog"></i>
                <span>Manage Users</span>
                <span class="badge">5</span>
            </a>
        </li>
        <li>
            <a href="manage_documents.php">
                <i class="fas fa-file-alt"></i>
                <span>Documents</span>
            </a>
        </li>
        <li>
            <a href="manage_courses.php">
                <i class="fas fa-book-open"></i>
                <span>Courses</span>
            </a>
        </li>
        <li>
            <a href="admin_register_stationery.php">
                <i class="fas fa-book-open"></i>
                <span>stationary</span>
            </a>
        </li>
    </ul>
    
    <div class="sidebar-header">Reports</div>
    <ul class="sidebar-menu">
        <li>
            <a href="reports.php">
                <i class="fas fa-chart-bar"></i>
                <span>Analytics</span>
            </a>
        </li>
        <li>
            <a href="print_requests.php">
                <i class="fas fa-print"></i>
                <span>Print Requests</span>
                <span class="badge">12</span>
            </a>
        </li>
    </ul>
    
    <div class="sidebar-header">System</div>
    <ul class="sidebar-menu">
        <li>
            <a href="system_settings.php">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </li>
        <li>
            <a href="backup.php">
                <i class="fas fa-database"></i>
                <span>Backups</span>
            </a>
        </li>
    </ul>
    
    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar">AD</div>
            <div class="user-info">
                <div class="user-name">Admin User</div>
                <div class="user-role">Super Administrator</div>
            </div>
        </div>
        
        <a href="logout.php" class="logout-link">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<script>
    // Optional: Add toggle functionality for sidebar
    document.addEventListener('DOMContentLoaded', function() {
        // This would be triggered by a button in your header
        // document.getElementById('toggle-sidebar').addEventListener('click', function() {
        //     document.getElementById('sidebar').classList.toggle('collapsed');
        // });
        
        // Highlight active menu item based on current page
        const currentPage = window.location.pathname.split('/').pop();
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    });
</script>
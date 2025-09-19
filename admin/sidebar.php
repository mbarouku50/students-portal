<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Sidebar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #0f172a;
            --sidebar-active-bg: rgba(96, 165, 250, 0.15);
            --sidebar-text: #cbd5e1;
            --sidebar-active-text: #ffffff;
            --sidebar-hover: #1e293b;
            --sidebar-accent: #60a5fa;
            --sidebar-border: #334155;
            --sidebar-icon-size: 1.1rem;
            --sidebar-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --sidebar-header-color: #94a3b8;
            --card-bg: rgba(255, 255, 255, 0.05);
            --card-border: rgba(255, 255, 255, 0.08);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Hamburger menu button */
        .sidebar-menu-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            width: 44px;
            height: 44px;
            background: var(--sidebar-accent);
            border: none;
            border-radius: 12px;
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .sidebar-menu-btn:hover {
            background: #3b82f6;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
        }
        
        .sidebar-menu-btn span {
            display: block;
            width: 22px;
            height: 2px;
            background: #fff;
            margin: 3px 0;
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
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
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
            padding: 1.75rem 1.5rem 1.25rem;
            border-bottom: 1px solid var(--sidebar-border);
            position: sticky;
            top: 0;
            background: var(--sidebar-bg);
            z-index: 10;
            min-height: 85px;
            box-sizing: border-box;
            background: linear-gradient(to right, rgba(15, 23, 42, 0.95), rgba(15, 23, 42, 0.98));
            backdrop-filter: blur(8px);
        }

        .sidebar-logo img {
            height: 36px;
            width: 36px;
            object-fit: contain;
            margin-right: 12px;
            filter: brightness(0) invert(1);
            transition: transform 0.3s ease;
        }

        .sidebar-logo:hover img {
            transform: scale(1.05);
        }

        .sidebar-logo span {
            font-size: 1.35rem;
            font-weight: 700;
            color: white;
            letter-spacing: 0.5px;
            transition: opacity 0.3s;
            background: linear-gradient(to right, #60a5fa, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Section headers */
        .sidebar-header {
            padding: 1.25rem 1.5rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--sidebar-header-color);
            margin-top: 0.5rem;
            transition: opacity 0.3s;
            white-space: nowrap;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        .sidebar-header::before {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--sidebar-border);
            margin-right: 10px;
        }

        .sidebar-header::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--sidebar-border);
            margin-left: 10px;
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
            padding: 0.85rem 1rem;
            transition: var(--sidebar-transition);
            font-size: 0.95rem;
            border-radius: 10px;
            gap: 0.85rem;
            font-weight: 500;
            position: relative;
            white-space: nowrap;
            overflow: hidden;
            background: transparent;
        }

        .sidebar-menu a:hover {
            background: var(--sidebar-hover);
            color: var(--sidebar-active-text);
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .sidebar-menu a.active {
            background: var(--sidebar-active-bg);
            color: var(--sidebar-active-text);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
            transition: transform 0.2s ease;
        }

        .sidebar-menu a:hover i {
            transform: scale(1.1);
            color: var(--sidebar-accent);
        }

        .sidebar-menu .badge {
            margin-left: auto;
            background: var(--sidebar-accent);
            color: white;
            border-radius: 12px;
            padding: 0.25rem 0.6rem;
            font-size: 0.7rem;
            font-weight: 700;
            min-width: 24px;
            text-align: center;
            transition: opacity 0.3s;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
        }

        /* Footer section */
        .sidebar-footer {
            padding: 1.25rem 1rem;
            border-top: 1px solid var(--sidebar-border);
            margin-top: auto;
            position: sticky;
            bottom: 0;
            background: var(--sidebar-bg);
            z-index: 10;
            min-height: 120px;
            background: linear-gradient(to top, rgba(15, 23, 42, 0.95), rgba(15, 23, 42, 0.98));
            backdrop-filter: blur(8px);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.75rem;
            border-radius: 10px;
            transition: var(--sidebar-transition);
            cursor: pointer;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
        }

        .user-profile:hover {
            background: var(--sidebar-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--sidebar-accent), #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
            flex-shrink: 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .user-info {
            flex: 1;
            overflow: hidden;
            transition: opacity 0.3s;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.95rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: white;
        }

        .user-role {
            font-size: 0.8rem;
            color: var(--sidebar-header-color);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .logout-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.85rem 1rem;
            color: #f87171;
            text-decoration: none;
            font-weight: 500;
            border-radius: 10px;
            transition: var(--sidebar-transition);
            margin-top: 0.75rem;
            white-space: nowrap;
            overflow: hidden;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
        }

        .logout-link:hover {
            background: rgba(239, 68, 68, 0.12);
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
            padding: 1.75rem 0.5rem;
        }

        .sidebar.collapsed .sidebar-logo img {
            margin-right: 0;
        }

        .sidebar.collapsed .sidebar-menu a {
            justify-content: center;
            padding: 0.85rem 0.5rem;
        }

        .sidebar.collapsed .user-profile {
            justify-content: center;
            padding: 0.75rem 0;
        }

        .sidebar.collapsed .logout-link {
            justify-content: center;
            padding: 0.85rem 0.5rem;
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
            padding: 0.6rem 1rem;
            border-radius: 6px;
            font-size: 0.9rem;
            white-space: nowrap;
            margin-left: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 300;
            pointer-events: none;
            border: 1px solid var(--sidebar-border);
        }

        /* Responsive styles */
        @media (max-width: 1024px) {
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
                padding: 1.75rem 0.5rem;
            }
            
            .sidebar-logo img {
                margin-right: 0;
            }
            
            .sidebar-menu a {
                justify-content: center;
                padding: 0.85rem 0.5rem;
            }
            
            .user-profile {
                justify-content: center;
                padding: 0.75rem 0;
            }
            
            .logout-link {
                justify-content: center;
                padding: 0.85rem 0.5rem;
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
                box-shadow: 4px 0 20px rgba(0,0,0,0.15);
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
                padding: 1.75rem 1.5rem 1.25rem;
            }
            
            .sidebar-logo img {
                margin-right: 12px;
            }
            
            .sidebar-menu a {
                justify-content: flex-start;
                padding: 0.85rem 1rem;
            }
            
            .user-profile {
                justify-content: flex-start;
                padding: 0.75rem;
            }
            
            .logout-link {
                justify-content: flex-start;
                padding: 0.85rem 1rem;
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
            
            /* Ensure footer content is visible */
            .sidebar-footer {
                position: relative;
                min-height: auto;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: 100%;
                max-width: 320px;
            }
            
            /* Mobile-specific adjustments for small screens */
            .sidebar-menu a {
                padding: 0.9rem 1rem;
            }
            
            .logout-link {
                padding: 0.9rem 1rem;
            }
            
            .sidebar-footer {
                padding: 1rem;
            }
            
            /* Reduce spacing for mobile */
            .sidebar-header {
                padding: 1.1rem 1.25rem 0.4rem;
                margin-top: 0;
            }
            
            .sidebar-menu {
                padding: 0.3rem 0.5rem;
                gap: 0.2rem;
                margin-top: 0.3rem;
                margin-bottom: 0.6rem;
            }
            
            .sidebar-logo {
                padding: 1.5rem 1.25rem 1rem;
                min-height: 75px;
            }
            
            /* Fix for small screens - ensure footer content is visible */
            .sidebar-footer {
                min-height: 115px;
            }
        }

        /* Extra small devices */
        @media (max-width: 360px) {
            .sidebar {
                width: 100%;
            }
            
            .sidebar-logo {
                padding: 1.25rem 1rem 0.9rem;
            }
            
            .sidebar-menu a {
                padding: 0.8rem;
            }
            
            .logout-link {
                padding: 0.8rem;
            }
            
            .sidebar-footer {
                padding: 0.9rem;
            }
            
            /* Reduce font sizes for very small screens */
            .sidebar-menu a {
                font-size: 0.9rem;
            }
            
            .user-name {
                font-size: 0.9rem;
            }
            
            .user-role {
                font-size: 0.75rem;
            }
            
            /* Ensure text doesn't overflow on very small screens */
            .user-name, .user-role {
                max-width: 160px;
            }
            
            /* Fix for very small screens - ensure footer content is visible */
            .sidebar-footer {
                min-height: 110px;
            }
        }

        /* Special fix for Samsung S10+ and similar devices */
        @media (max-height: 700px) and (max-width: 768px) {
            .sidebar {
                overflow-y: auto;
            }
            
            .sidebar-menu {
                flex: 0 1 auto;
            }
            
            .sidebar-footer {
                position: sticky;
                bottom: 0;
                background: var(--sidebar-bg);
            }
        }
        
        /* Fix for logout button visibility on small screens */
        @media (max-width: 768px) {
            .sidebar.open {
                display: flex;
                flex-direction: column;
            }
            
            .sidebar-footer {
                margin-top: auto;
                position: sticky;
                bottom: 0;
                background: var(--sidebar-bg);
                z-index: 10;
            }
        }
        
        /* Main content area */
        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease;
            padding: 24px;
            min-height: 100vh;
        }
        
        @media (min-width: 1025px) {
            .main-content {
                margin-left: var(--sidebar-width);
            }
        }
        
        @media (max-width: 1024px) and (min-width: 769px) {
            .main-content {
                margin-left: var(--sidebar-collapsed-width);
            }
        }
    </style>
</head>
<body>
    <button id="sidebarMenuBtn" aria-label="Open menu" class="sidebar-menu-btn">
        <span></span>
        <span></span>
        <span></span>
    </button>
    <div class="sidebar-overlay" id="sidebarOverlay" tabindex="-1" aria-hidden="true"></div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/Document_icon_%28white%29.svg" alt="Logo">
            <span>CBE Doc's Store</span>
        </div>
        
        <div class="sidebar-header">Navigation</div>
        <ul class="sidebar-menu">
            <li>
                <a href="admin_dashboard.php" data-tooltip="Dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="manage_users.php" data-tooltip="Manage Users">
                    <i class="fas fa-users-cog"></i>
                    <span>Manage Users</span>
                    <span class="badge">15</span>
                </a>
            </li>
            <li>
                <a href="manage_documents.php" data-tooltip="Documents">
                    <i class="fas fa-file-alt"></i>
                    <span>Documents</span>
                </a>
            </li>
            <li>
                <a href="manage_courses.php" data-tooltip="Courses">
                    <i class="fas fa-book-open"></i>
                    <span>Courses</span>
                </a>
            </li>
            <li>
                <a href="admin_register_stationery.php" data-tooltip="Stationary">
                    <i class="fas fa-pencil-alt"></i>
                    <span>Stationary</span>
                </a>
            </li>
        </ul>
        
        <div class="sidebar-header">Reports</div>
        <ul class="sidebar-menu">
            <li>
                <a href="print_requests.php" data-tooltip="Print Requests">
                    <i class="fas fa-print"></i>
                    <span>Print Requests</span>
                    <span class="badge">3</span>
                </a>
            </li>
        </ul>
        
        <div class="sidebar-header">System</div>
        <ul class="sidebar-menu">
            <li>
                <a href="system_settings.php" data-tooltip="Settings">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
            <li>
                <a href="logout.php" class="logout-link" data-tooltip="Logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
            </li>
        </ul>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const menuBtn = document.getElementById('sidebarMenuBtn');
            const mainContent = document.querySelector('.main-content');
            
            // Toggle sidebar function
            function toggleSidebar() {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('open');
                document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
                
                // Adjust main content margin when sidebar is open on mobile
                if (window.innerWidth <= 768) {
                    if (sidebar.classList.contains('open')) {
                        mainContent.style.marginLeft = '0';
                    }
                }
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
                    mainContent.style.marginLeft = '0';
                } else {
                    menuBtn.style.display = 'none';
                    sidebar.classList.remove('open');
                    overlay.classList.remove('open');
                    document.body.style.overflow = '';
                    
                    // Reset main content margin based on sidebar state
                    if (window.innerWidth <= 1024 && window.innerWidth > 768) {
                        mainContent.style.marginLeft = '80px';
                    } else if (window.innerWidth > 1024) {
                        mainContent.style.marginLeft = '280px';
                    }
                }
                
                // Handle automatic collapsing on medium screens
                if (window.innerWidth <= 1024 && window.innerWidth > 768) {
                    sidebar.classList.add('collapsed');
                    mainContent.style.marginLeft = '80px';
                } else {
                    sidebar.classList.remove('collapsed');
                    if (window.innerWidth > 1024) {
                        mainContent.style.marginLeft = '280px';
                    }
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
</body>
</html>
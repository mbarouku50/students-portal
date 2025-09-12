<?php
include("temperate/header.php");
include("../connection.php");

// Fetch all stationery items
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
    <title>Find Printing Stations - CBE Doc's Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #3b82f6;
            --secondary: #10b981;
            --secondary-dark: #059669;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --border: #e2e8f0;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
            --radius: 0.5rem;
            --radius-lg: 0.75rem;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: var(--gray-50);
            color: var(--gray-800);
            line-height: 1.6;
        }
        
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            display: inline-block;
        }
        
        .page-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: 2px;
        }
        
        .page-subtitle {
            color: var(--gray-600);
            font-size: 1.1rem;
            max-width: 700px;
            margin: 1.5rem auto 0;
        }
        
        .search-container {
            max-width: 800px;
            margin: 0 auto 3rem;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 1.25rem 1.5rem 1.25rem 3.5rem;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-lg);
            font-size: 1.1rem;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            background-color: white;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }
        
        .search-icon {
            position: absolute;
            left: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 1.2rem;
        }
        
        .filter-bar {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 0.75rem 1.5rem;
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: var(--shadow-sm);
        }
        
        .filter-btn:hover {
            background: var(--gray-100);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        
        .filter-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
        }
        
        .stationery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }
        
        .stationery-card {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            border: 1px solid var(--gray-200);
            position: relative;
        }
        
        .stationery-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }
        
        .card-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--secondary);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 10;
        }
        
        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-100);
            position: relative;
        }
        
        .card-header-flex {
            display: flex;
            align-items: center;
        }
        
        .card-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: var(--radius);
            margin-right: 1rem;
            border: 1px solid var(--gray-200);
            background: var(--gray-50);
            padding: 0.5rem;
            flex-shrink: 0;
        }
        
        .card-details {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        
        .card-title {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .card-title i {
            color: var(--primary);
        }
        
        .card-location {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray-600);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        
        .extra-info {
            font-size: 0.9rem;
            color: var(--gray-500);
            margin-bottom: 0.4rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .contact-info {
            margin-bottom: 1.5rem;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }
        
        .contact-item a {
            color: var(--gray-700);
            text-decoration: none;
            transition: color 0.2s ease;
        }
        
        .contact-item a:hover {
            color: var(--primary);
        }
        
        .price-tag {
            display: inline-block;
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary);
            padding: 0.6rem 1.2rem;
            border-radius: var(--radius);
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            box-shadow: var(--shadow-sm);
        }
        
        .card-description {
            color: var(--gray-600);
            margin-bottom: 1.5rem;
            line-height: 1.7;
            font-size: 0.95rem;
        }
        
        .card-footer {
            padding: 0 1.5rem 1.5rem;
        }
        
        .action-btn {
            display: block;
            text-align: center;
            padding: 0.9rem;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: var(--radius);
            font-weight: 600;
            transition: all 0.2s ease;
            box-shadow: var(--shadow);
        }
        
        .action-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .no-results {
            text-align: center;
            grid-column: 1 / -1;
            padding: 3rem;
            color: var(--gray-500);
            font-size: 1.2rem;
            display: none;
            background: var(--gray-50);
            border-radius: var(--radius-lg);
            border: 1px dashed var(--gray-300);
        }
        
        .no-results.show {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .no-results i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--gray-400);
        }
        
        /* Loading animation */
        .loading {
            display: none;
            text-align: center;
            padding: 2rem;
            grid-column: 1 / -1;
        }
        
        .loading.show {
            display: block;
        }
        
        .loading-spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 4px solid rgba(37, 99, 235, 0.1);
            border-radius: 50%;
            border-top-color: var(--primary);
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Responsive styles */
        @media (max-width: 1024px) {
            .stationery-grid {
                grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            
            .page-subtitle {
                font-size: 1rem;
            }
            
            .filter-bar {
                justify-content: flex-start;
                overflow-x: auto;
                padding-bottom: 0.5rem;
            }
            
            .filter-btn {
                flex-shrink: 0;
            }
            
            .search-input {
                padding: 1rem 1rem 1rem 3rem;
            }
        }
        
        @media (max-width: 640px) {
            .stationery-grid {
                grid-template-columns: 1fr;
            }
            
            .card-header-flex {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .card-logo {
                margin-bottom: 1rem;
                margin-right: 0;
            }
        }
        
        @media (max-width: 480px) {
            .main-container {
                padding: 1.5rem 1rem;
            }
            
            .page-title {
                font-size: 1.75rem;
            }
            
            .search-input {
                padding: 0.9rem 1rem 0.9rem 3rem;
                font-size: 1rem;
            }
            
            .search-icon {
                left: 1rem;
            }
            
            .filter-btn {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="page-header">
            <h1 class="page-title">Find Printing Stations</h1>
            <p class="page-subtitle">Browse our network of authorized printing stations near you. Compare prices and services to find the best option for your needs.</p>
        </div>

        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" id="search-input" placeholder="Search by name, location, or price...">
        </div>

        <div class="filter-bar">
            <button class="filter-btn active" data-filter="all">
                <i class="fas fa-layer-group"></i> All Stations
            </button>
            <button class="filter-btn" data-filter="available">
                <i class="fas fa-check-circle"></i> Available
            </button>
            <button class="filter-btn" data-filter="cheap">
                <i class="fas fa-money-bill-wave"></i> Under 500 Tsh
            </button>
            <button class="filter-btn" data-filter="premium">
                <i class="fas fa-crown"></i> Premium
            </button>
        </div>

        <div class="stationery-grid" id="stationery-grid">
            <?php if (empty($stationeryItems)): ?>
                <div class="no-results show">
                    <i class="fas fa-search"></i>
                    <p>No printing stations found. Please check back later.</p>
                </div>
            <?php else: ?>
                <?php foreach ($stationeryItems as $item): ?>
                    <div class="stationery-card" 
                         data-name="<?= strtolower(htmlspecialchars($item['name'])) ?>" 
                         data-location="<?= strtolower(htmlspecialchars($item['location'])) ?>"
                         data-price="<?= htmlspecialchars($item['price']) ?>">
                         
                        <?php if($item['price'] < 500): ?>
                            <div class="card-badge">Best Value</div>
                        <?php elseif($item['price'] > 1000): ?>
                            <div class="card-badge" style="background: var(--primary);">Premium</div>
                        <?php endif; ?>
                        
                        <div class="card-header card-header-flex">
                            <?php if(!empty($item['logo'])): ?>
                                <img src="<?= htmlspecialchars($item['logo']) ?>" alt="Logo" class="card-logo">
                            <?php else: ?>
                                <div class="card-logo" style="display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-store" style="font-size: 2rem; color: var(--gray-400);"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-details">
                                <h3 class="card-title">
                                    <i class="fas fa-store"></i>
                                    <?= htmlspecialchars($item['name']) ?>
                                </h3>
                                <div class="card-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?= htmlspecialchars($item['location']) ?>
                                </div>
                                <?php if(!empty($item['address'])): ?>
                                    <div class="extra-info">
                                        <i class="fas fa-home"></i> <?= htmlspecialchars($item['address']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if(!empty($item['opening_hours'])): ?>
                                    <div class="extra-info">
                                        <i class="fas fa-clock"></i> <?= htmlspecialchars($item['opening_hours']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="contact-info">
                                <?php if(!empty($item['phone'])): ?>
                                    <div class="contact-item">
                                        <i class="fas fa-phone"></i>
                                        <a href="tel:<?= htmlspecialchars($item['phone']) ?>"><?= htmlspecialchars($item['phone']) ?></a>
                                    </div>
                                <?php endif; ?>
                                <?php if(!empty($item['email'])): ?>
                                    <div class="contact-item">
                                        <i class="fas fa-envelope"></i>
                                        <a href="mailto:<?= htmlspecialchars($item['email']) ?>"><?= htmlspecialchars($item['email']) ?></a>
                                    </div>
                                <?php endif; ?>
                                <?php if(!empty($item['whatsapp'])): ?>
                                    <div class="contact-item">
                                        <i class="fab fa-whatsapp"></i>
                                        <a href="https://wa.me/<?= htmlspecialchars($item['whatsapp']) ?>" target="_blank">Chat on WhatsApp</a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="price-tag"><?= number_format($item['price'], 2) ?> Tsh per copy</div>

                            <?php if(!empty($item['description'])): ?>
                                <p class="card-description"><?= htmlspecialchars($item['description']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="card-footer">
                            <a href="print_option.php?stationery_id=<?= htmlspecialchars($item['stationery_id']) ?>" class="action-btn">
                                <i class="fas fa-print"></i> Print at <?= htmlspecialchars($item['name']) ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="no-results" id="no-results">
                <i class="fas fa-search"></i>
                <p>No results match your search criteria.</p>
            </div>
            
            <div class="loading" id="loading">
                <div class="loading-spinner"></div>
                <p style="margin-top: 1rem;">Searching stations...</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const stationeryGrid = document.getElementById('stationery-grid');
            const noResults = document.getElementById('no-results');
            const loading = document.getElementById('loading');
            const filterButtons = document.querySelectorAll('.filter-btn');
            let debounceTimer;

            // Search functionality with debounce
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                loading.classList.add('show');
                
                debounceTimer = setTimeout(function() {
                    const searchTerm = searchInput.value.toLowerCase().trim();
                    let hasResults = false;

                    document.querySelectorAll('.stationery-card').forEach(card => {
                        const name = card.getAttribute('data-name');
                        const location = card.getAttribute('data-location');
                        const price = card.getAttribute('data-price');

                        if (name.includes(searchTerm) || location.includes(searchTerm) || price.includes(searchTerm)) {
                            card.style.display = 'block';
                            hasResults = true;
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    loading.classList.remove('show');
                    noResults.classList.toggle('show', !hasResults);
                }, 300);
            });

            // Filter functionality
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    const filter = this.getAttribute('data-filter');
                    let hasResults = false;

                    document.querySelectorAll('.stationery-card').forEach(card => {
                        const price = parseFloat(card.getAttribute('data-price'));

                        if (filter === 'all' || 
                            (filter === 'available' && price > 0) ||
                            (filter === 'cheap' && price <= 500) ||
                            (filter === 'premium' && price > 500)) {
                            card.style.display = 'block';
                            hasResults = true;
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    noResults.classList.toggle('show', !hasResults);
                });
            });
        });
    </script>

    <?php include("temperate/footer.php"); ?>
</body>
</html>
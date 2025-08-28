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
            color: var(--dark);
            margin-bottom: 1rem;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .page-subtitle {
            color: var(--gray);
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .search-container {
            max-width: 800px;
            margin: 0 auto 3rem;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 1rem 1.5rem 1rem 3.5rem;
            border: 2px solid var(--border);
            border-radius: 0.5rem;
            font-size: 1.1rem;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        
        .search-icon {
            position: absolute;
            left: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
            font-size: 1.2rem;
        }
        
        .filter-bar {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 0.5rem 1.25rem;
            background: white;
            border: 1px solid var(--border);
            border-radius: 2rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .filter-btn:hover, .filter-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .stationery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }
        
        .stationery-card {
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            border: 1px solid var(--border);
        }
        
        .stationery-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        
        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            position: relative;
        }
        
        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .card-title i {
            color: var(--primary);
        }
        
        .card-location {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray);
            margin-bottom: 0.5rem;
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
            color: var(--dark);
            text-decoration: none;
            transition: color 0.2s ease;
        }
        
        .contact-item a:hover {
            color: var(--primary);
        }
        
        .price-tag {
            display: inline-block;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }
        
        .card-description {
            color: var(--gray);
            margin-bottom: 1.5rem;
            line-height: 1.7;
        }
        
        .card-footer {
            padding: 0 1.5rem 1.5rem;
        }
        
        .action-btn {
            display: block;
            text-align: center;
            padding: 0.75rem;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .action-btn:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
        }
        
        .no-results {
            text-align: center;
            grid-column: 1 / -1;
            padding: 3rem;
            color: var(--gray);
            font-size: 1.2rem;
            display: none;
        }
        
        .no-results.show {
            display: block;
        }
        .card-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 0.5rem;
            margin-right: 1rem;
            border: 1px solid var(--border);
            background: var(--light);
        }
        .card-header-flex {
            display: flex;
            align-items: center;
        }
        .card-details {
            display: flex;
            flex-direction: column;
        }
        .extra-info {
            font-size: 0.95rem;
            color: var(--gray);
            margin-bottom: 0.5rem;
        }
        /* Responsive styles */
        @media (max-width: 1024px) {
            .stationery-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
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
            }
        }
        
        @media (max-width: 480px) {
            .main-container {
                padding: 1.5rem 1rem;
            }
            
            .page-title {
                font-size: 1.75rem;
            }
            
            .stationery-grid {
                grid-template-columns: 1fr;
            }
            
            .search-input {
                padding: 0.9rem 1rem 0.9rem 3rem;
                font-size: 1rem;
            }
            
            .search-icon {
                left: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
    <div class="page-header">
        <h1 class="page-title">Find Printing Stations</h1>
        <p class="page-subtitle">Browse our network of authorized printing stations near you</p>
    </div>

    <div class="search-container">
        <i class="fas fa-search search-icon"></i>
        <input type="text" class="search-input" id="search-input" placeholder="Search by name, location, or price...">
    </div>

    <div class="filter-bar">
        <button class="filter-btn active" data-filter="all">All</button>
        <button class="filter-btn" data-filter="available">Available</button>
        <button class="filter-btn" data-filter="cheap">Under 500 Tsh</button>
        <button class="filter-btn" data-filter="premium">Premium</button>
    </div>

    <div class="stationery-grid" id="stationery-grid">
        <?php if (empty($stationeryItems)): ?>
            <div class="no-results show">No printing stations found. Please check back later.</div>
        <?php else: ?>
            <?php foreach ($stationeryItems as $item): ?>
                <div class="stationery-card" 
                     data-name="<?= strtolower(htmlspecialchars($item['name'])) ?>" 
                     data-location="<?= strtolower(htmlspecialchars($item['location'])) ?>"
                     data-price="<?= htmlspecialchars($item['price']) ?>">
                     
                    <div class="card-header card-header-flex">
                        <?php if(!empty($item['logo'])): ?>
                            <img src="<?= htmlspecialchars($item['logo']) ?>" alt="Logo" class="card-logo">
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
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <a href="tel:<?= htmlspecialchars($item['phone']) ?>"><?= htmlspecialchars($item['phone']) ?></a>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:<?= htmlspecialchars($item['email']) ?>"><?= htmlspecialchars($item['email']) ?></a>
                            </div>
                            <div class="contact-item">
                                <i class="fab fa-whatsapp"></i>
                                <a href="https://wa.me/<?= htmlspecialchars($item['whatsapp']) ?>" target="_blank">Chat on WhatsApp</a>
                            </div>
                        </div>

                        <div class="price-tag"><?= number_format($item['price'], 2) ?> Tsh per copy</div>

                        <p class="card-description"><?= htmlspecialchars($item['description']) ?></p>
                    </div>

                    <div class="card-footer">
                        <a href="print_option.php?stationery_id=<?= htmlspecialchars($item['stationery_id']) ?>" class="action-btn">
                            <i class="fas fa-print"></i> Print at <?= htmlspecialchars($item['name']) ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="no-results" id="no-results">No results match your search criteria.</div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const stationeryGrid = document.getElementById('stationery-grid');
        const noResults = document.getElementById('no-results');
        const filterButtons = document.querySelectorAll('.filter-btn');

        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
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

            noResults.classList.toggle('show', !hasResults);
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
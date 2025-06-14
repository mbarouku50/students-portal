<?php include("template/header.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Stationery</title>
    <!-- Responsive viewport for mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">



<style>
    body {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        background-color: #f3f4f6;
        margin: 0;
        color: #1f2937;
        font-size: 1rem;
        line-height: 1.6;
    }

    .stationery-section {
        padding: 5rem 1.5rem;
        background: linear-gradient(135deg, #e0e7ff 0%, #f1f5f9 100%);
        position: relative;
        overflow: hidden;
    }

    .stationery-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('https://source.unsplash.com/random/1920x1080/?pattern') no-repeat center center/cover;
        opacity: 0.05;
        z-index: 1;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1.5rem;
        position: relative;
        z-index: 2;
    }

    h2 {
        font-size: 2.5rem;
        font-weight: 800;
        color: #1f2937;
        text-align: center;
        margin-bottom: 2.5rem;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .search-section {
        margin-bottom: 2.5rem;
        text-align: center;
    }

    .search-input {
        width: 100%;
        max-width: 600px;
        padding: 0.85rem 1.5rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 1.1rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        background: #fff;
    }

    .search-input:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    .stationery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }

    .stationery-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
        backdrop-filter: blur(8px);
    }

    .stationery-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        background: rgba(255, 255, 255, 1);
    }

    .stationery-card h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.75rem;
    }

    .stationery-card p {
        color: #4b5563;
        font-size: 1.1rem;
        margin: 0.3rem 0;
    }

    .stationery-card .price {
        color: #7c3aed;
        font-weight: 600;
        font-size: 1.2rem;
    }

    .stationery-card .btn {
        display: block;
        width: 100%;
        padding: 0.85rem;
        background: linear-gradient(90deg, #4f46e5, #7c3aed);
        color: white;
        text-align: center;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1.1rem;
        margin-top: 1.5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stationery-card .btn:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
    }

    @media (max-width: 768px) {
        h2 {
            font-size: 2rem;
        }

        .stationery-grid {
            grid-template-columns: 1fr;
        }

        body {
            font-size: 1.1rem;
        }
    }

    @media (max-width: 480px) {
        .stationery-section {
            padding: 3rem 1rem;
        }

        .container {
            padding: 0 1rem;
        }

        h2 {
            font-size: 1.75rem;
        }

        body {
            font-size: 1.15rem;
        }

        .search-input {
            font-size: 1rem;
        }

        .stationery-card h3 {
            font-size: 1.4rem;
        }

        .stationery-card p {
            font-size: 1rem;
        }

        .stationery-card .btn {
            font-size: 1rem;
        }
    }
</style>
</head>

<section class="stationery-section">
    <div class="container">
        <h2>Search Stationery</h2>
        <div class="search-section">
            <input type="text" class="search-input" id="stationery-search" placeholder="Search for stationery items...">
        </div>
        <div class="stationery-grid" id="stationery-grid">
            <div class="stationery-card">
                <h3>Ubay Ubwela Shop</h3>
                <p>Location: Campus</p>
                <p><a href="tel:06897895" style="text-decoration: none; color: inherit;">Call Us <i class="fas fa-phone"></i></a></p>
                <p>Email: ubayubwelashop@gmail.com</p>
                <p><a href="https://wa.me/+255689118095" style="text-decoration: none; color: inherit;">Chat with Us <i class="fab fa-whatsapp"></i></a></p>
                <p class="price">25Tsh per copy</p>
                <p>Description: your welcome to print with us.</p>
                <a href="print_documents.php?station=station1" class="btn">Print at Ubay Ubwela Shop</a>
            </div>
            <div class="stationery-card">
                <h3>Mbuya Print</h3>
                <p>Location: kantini</p>
                <p><a href="tel:06897896" style="text-decoration: none; color: inherit;">Call Us <i class="fas fa-phone"></i></a></p>
                <p>Email: Mbuya@gmail.com</p>
                <p><a href="https://wa.me/+255689118095" style="text-decoration: none; color: inherit;">Chat with Us <i class="fab fa-whatsapp"></i></a></p>
                <p class="price">50Tsh per copy</p>
                <p>Description: Print chap with saving time.</p>
                <a href="print_documents.php?station=station2" class="btn">Print at Mbuya Print</a>
            </div>
            <div class="stationery-card">
                <h3>Hatuchezi Stationary</h3>
                <p>Location: Mihogo point near city mall</p>
                <p><a href="tel:06897897" style="text-decoration: none; color: inherit;">Call Us <i class="fas fa-phone"></i></a></p>
                <p>Email: Hatuchezi@gmail.com</p>
                <p><a href="https://wa.me/+255689118095" style="text-decoration: none; color: inherit;">Chat with Us <i class="fab fa-whatsapp"></i></a></p>
                <p class="price">100Tsh per copy</p>
                <p>Description: Print with high quality machine.</p>
                <a href="print_documents.php?station=station3" class="btn">Print at Hatuchezi Stationary</a>
            </div>
            <div class="stationery-card">
                <h3>Chapa Chap</h3>
                <p>Location: Near ATM</p>
                <p><a href="tel:06897898" style="text-decoration: none; color: inherit;">Call Us <i class="fas fa-phone"></i></a></p>
                <p>Email: chapachap@gmail.com</p>
                <p><a href="https://wa.me/+255689118095" style="text-decoration: none; color: inherit;">Chat with Us <i class="fab fa-whatsapp"></i></a></p>
                <p class="price">30Tsh per copy</p>
                <p>Description: Fast and reliable printing services.</p>
                <a href="print_documents.php?station=station4" class="btn">Print at Chapa Chap</a>
            </div>
            <div class="stationery-card">
                <h3>Fast Print</h3>
                <p>Location: DIT</p>
                <p><a href="tel:06897899" style="text-decoration: none; color: inherit;">Call Us <i class="fas fa-phone"></i></a></p>
                <p>Email: fastprint@gmail.com</p>
                <p><a href="https://wa.me/+255689118095" style="text-decoration: none; color: inherit;">Chat with Us <i class="fab fa-whatsapp"></i></a></p>
                <p class="price">40Tsh per copy</p>
                <p>Description: Quick and efficient printing services.</p>
                <a href="print_documents.php?station=station5" class="btn">Print at Fast Print</a>
            </div>
            <div class="stationery-card">
                <h3>Quality Prints</h3>
                <p>Location: Near Hostel</p>
                <p><a href="tel:06897900" style="text-decoration: none; color: inherit;">Call Us <i class="fas fa-phone"></i></a></p>
                <p>Email: qualityprints@gmail.com</p>
                <p><a href="https://wa.me/+255689118095" style="text-decoration: none; color: inherit;">Chat with Us <i class="fab fa-whatsapp"></i></a></p>
                <p class="price">50Tsh per copy</p>
                <p>Description: High-quality printing services for all your needs.</p>
                <a href="print_documents.php?station=station6" class="btn">Print at Quality Prints</a>
            </div>
        </div>
    </div>
</section>

<script>
    const searchInput = document.getElementById('stationery-search');
    const stationeryCards = document.querySelectorAll('.stationery-card');

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();

        stationeryCards.forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            const description = card.querySelector('p:not(.price)').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || description.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>

<?php include("template/footer.php"); ?>
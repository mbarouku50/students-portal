<?php
include("temperate/header.php")
?>
<style>
.hero-section {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%), url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1');
    background-blend-mode: overlay;
    background-size: cover;
    color: white;
    padding: 5rem 1.5rem 3rem 1.5rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.hero-section::before {
    content: '';
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(44, 62, 80, 0.5);
    z-index: 1;
}
.hero-content { position: relative; z-index: 2; }
.hero-content h1 { font-size: 2.8rem; font-weight: 800; margin-bottom: 1.2rem; text-shadow: 0 2px 8px rgba(0,0,0,0.15); }
.hero-content p { font-size: 1.3rem; max-width: 700px; margin: 0 auto 2.5rem; opacity: 0.95; }

.services-section { background: #f8f9fa; padding: 3rem 0 4rem 0; }
.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 2.5rem;
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 1.5rem;
}
.service-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 6px 32px rgba(52,152,219,0.10);
    padding: 2.5rem 2rem 2rem 2rem;
    text-align: center;
    transition: box-shadow 0.3s, transform 0.3s;
    position: relative;
    overflow: hidden;
}
.service-card:hover {
    box-shadow: 0 12px 40px rgba(52,152,219,0.18);
    transform: translateY(-6px) scale(1.03);
}
.service-card h3 {
    font-size: 1.5rem;
    color: #2c3e50;
    margin-bottom: 0.7rem;
    font-weight: 700;
}
.service-card p {
    color: #555;
    margin-bottom: 1.5rem;
    font-size: 1.08rem;
}
.service-card a {
    display: inline-block;
    background: linear-gradient(90deg, #4f46e5, #7c3aed);
    color: #fff;
    padding: 0.8rem 2.2rem;
    border-radius: 8px;
    font-size: 1.08rem;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(44,62,80,0.08);
    transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
}
.service-card a:hover {
    background: linear-gradient(90deg, #7c3aed, #4f46e5);
    box-shadow: 0 4px 16px rgba(44,62,80,0.13);
    transform: scale(1.04);
}
@media (max-width: 700px) {
    .hero-content h1 { font-size: 2rem; }
    .services-grid { grid-template-columns: 1fr; }
    .service-card { padding: 2rem 1rem; }
}
</style>
<section class="hero-section">
    <div class="hero-content">
        <h1>Welcome to Premium Printing & Stationery</h1>
        <p>Discover top-tier services for printing documents, browsing stationery, and crafting custom cover pages with ease.</p>
    </div>
</section>
<section class="services-section">
    <div class="container">
        <div class="services-grid">
            <div class="service-card">
                <h3>Print Documents</h3>
                <p>Upload your files, type, or edit your document for printing with our seamless service.</p>
                <a href="print_option.php"><i class="fas fa-print"></i> Get Started</a>
            </div>
            <div class="service-card">
                <h3>Search Stationery</h3>
                <p>Explore our premium stationery collection and print directly from your chosen station.</p>
                <a href="search_stationery.php"><i class="fas fa-search"></i> Explore Now</a>
            </div>
            <div class="service-card">
                <h3>Request Coverpage</h3>
                <p>Design and download stunning cover pages tailored to your academic or professional needs.</p>
                <a href="request_coverpage.php"><i class="fas fa-file-alt"></i> Create Coverpage</a>
            </div>
        </div>
    </div>
</section>
<?php
include("temperate/footer.php")
?>
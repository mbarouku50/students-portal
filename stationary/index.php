
<?php include("../temperate/header.php"); ?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Premium Printing & Stationery</title>
    <!-- Responsive viewport for mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            color: #1f2937;
            font-size: 1rem;
            line-height: 1.6;
        }

        .hero-section {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%), url('https://source.unsplash.com/random/1920x1080/?abstract');
            background-blend-mode: overlay;
            background-size: cover;
            color: white;
            padding: 6rem 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.25rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero-section p {
            font-size: 1.5rem;
            max-width: 700px;
            margin: 0 auto 2.5rem;
            opacity: 0.9;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 4rem auto;
            padding: 0 1.5rem;
        }

        .service-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
            backdrop-filter: blur(8px);
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 1);
        }

        .service-card h3 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .service-card p {
            color: #4b5563;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .service-card a {
            display: inline-block;
            padding: 0.85rem 2rem;
            background: linear-gradient(90deg, #4f46e5, #7c3aed);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .service-card a:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
        }

        @media (max-width: 1024px) {
            .hero-section h1 {
                font-size: 2.8rem;
            }
            .hero-section p {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 4rem 1rem;
            }
            .hero-section h1 {
                font-size: 2.2rem;
            }
            .hero-section p {
                font-size: 1rem;
            }
            .services-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            body {
                font-size: 1.05rem;
            }
        }

        @media (max-width: 480px) {
            .hero-section {
                padding: 2.5rem 0.5rem;
            }
            .hero-section h1 {
                font-size: 1.4rem;
            }
            .hero-section p {
                font-size: 0.95rem;
            }
            .service-card {
                padding: 1rem;
            }
            body {
                font-size: 1rem;
            }
            .service-card h3 {
                font-size: 1.1rem;
            }
            .service-card p {
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
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
                    <p>Upload your files and customize your print preferences with our seamless printing service.</p>
                    <a href="print_documents.php">Get Started</a>
                </div>
                <div class="service-card">
                    <h3>Search Stationery</h3>
                    <p>Explore our premium stationery collection and print directly from your chosen station.</p>
                    <a href="search_stationery.php">Explore Now</a>
                </div>
                <div class="service-card">
                    <h3>Request Coverpage</h3>
                    <p>Design and download stunning cover pages tailored to your academic or professional needs.</p>
                    <a href="request_coverpage.php">Create Coverpage</a>
                </div>
            </div>
        </div>
    </section>
</body>
</html>

<?php include("../temperate/footer.php");  ?>
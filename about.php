<?php
session_start();
require_once("includes/db.php");
$page_title = "About Us - Farmers Gateway";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        :root {
            --primary-green: #A7D49B;
            --accent-blue: #3B82F6;
            --accent-green: #10B981;
            --dark-green: #00796B;
            --text-gray: #6B7280;
            --light-gray: #F3F4F6;
            --white: #FFFFFF;
            --overlay-green: rgba(167, 212, 155, 0.95);
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--text-gray);
        }

        .navbar {
            padding: 1.2rem 0;
            transition: all 0.4s ease;
            background: var(--overlay-green);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .hero-section {
            min-height: 60vh;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                        url('assets/images/about-hero.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: var(--white);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            text-align: center;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--white);
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero-subtitle {
            font-size: clamp(1.1rem, 2vw, 1.3rem);
            opacity: 0.9;
            max-width: 800px;
            margin: 0 auto;
        }

        .feature-card {
            background: var(--white);
            padding: 2.5rem 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.4s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--accent-green);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1);
        }

        .section-title {
            font-size: clamp(2rem, 4vw, 2.5rem);
            color: var(--dark-green);
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
            padding-bottom: 1rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--accent-green);
        }

        .mission-section {
            background: var(--light-gray);
            padding: 6rem 0;
            position: relative;
            overflow: hidden;
        }

        .mission-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-green) 0%, transparent 100%);
            opacity: 0.1;
        }

        .stats-box {
            background: linear-gradient(135deg, var(--accent-green), var(--dark-green));
            padding: 2rem;
            border-radius: 15px;
            color: var(--white);
            text-align: center;
            transition: all 0.3s ease;
        }

        .stats-box:hover {
            transform: translateY(-5px);
        }

        .stats-number {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .team-member {
            text-align: center;
            margin-bottom: 3rem;
        }

        .team-member img {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            margin-bottom: 1.5rem;
            object-fit: cover;
            border: 5px solid var(--primary-green);
            transition: all 0.3s ease;
        }

        .team-member:hover img {
            transform: scale(1.05);
            border-color: var(--accent-green);
        }

        .contact-info {
            background: var(--white);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            background: var(--primary-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: var(--white);
            font-size: 1.2rem;
        }

        @media (max-width: 768px) {
            .hero-section {
                min-height: 50vh;
            }
            
            .feature-card {
                margin-bottom: 2rem;
            }
            
            .stats-box {
                margin-bottom: 2rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <?php include('shared-navbar.php'); ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container hero-content">
            <h1 class="hero-title animate__animated animate__fadeInDown">Welcome to Farmers Gateway</h1>
            <p class="hero-subtitle animate__animated animate__fadeInUp">Empowering farmers with technology and innovation for sustainable agriculture</p>
        </div>
    </section>

    <!-- Features Section -->
    <section class="container py-5">
        <h2 class="section-title">Our Features</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="feature-card animate__animated animate__fadeInUp">
                    <i class="fas fa-seedling feature-icon"></i>
                    <h3>Smart Farming</h3>
                    <p>Leverage AI and data analytics for optimal crop management and yield prediction</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                    <i class="fas fa-users feature-icon"></i>
                    <h3>Community Support</h3>
                    <p>Connect with fellow farmers, share experiences, and learn from each other</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card animate__animated animate__fadeInUp" style="animation-delay: 0.4s">
                    <i class="fas fa-chart-line feature-icon"></i>
                    <h3>Market Insights</h3>
                    <p>Access real-time market prices and trends to make informed decisions</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="mission-section">
        <div class="container">
            <h2 class="section-title">Our Mission</h2>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stats-box animate__animated animate__fadeInUp">
                        <div class="stats-number">5000+</div>
                        <div>Active Farmers</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stats-box animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                        <div class="stats-number">200+</div>
                        <div>Expert Advisors</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stats-box animate__animated animate__fadeInUp" style="animation-delay: 0.4s">
                        <div class="stats-number">50+</div>
                        <div>Districts Covered</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stats-box animate__animated animate__fadeInUp" style="animation-delay: 0.6s">
                        <div class="stats-number">95%</div>
                        <div>Satisfaction Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="container py-5">
        <h2 class="section-title">Get in Touch</h2>
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="contact-info animate__animated animate__fadeInLeft">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h4>Our Location</h4>
                            <p>123 Agriculture Street, Farming District, India</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h4>Phone Number</h4>
                            <p>+91 123 456 7890</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h4>Email Address</h4>
                            <p>contact@farmersgateway.com</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="contact-info animate__animated animate__fadeInRight">
                    <h3 class="mb-4">Our Vision</h3>
                    <p>To revolutionize agriculture through technology and empower farmers with knowledge and tools for sustainable farming practices. We aim to create a thriving ecosystem where traditional farming wisdom meets modern innovation.</p>
                    <p>Join us in our mission to transform agriculture and create a better future for farming communities across India.</p>
                </div>
            </div>
        </div>
    </section>

    <?php include("modern-footer.php"); ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                document.querySelector('.navbar').classList.add('scrolled');
            } else {
                document.querySelector('.navbar').classList.remove('scrolled');
            }
        });

        // Animate stats numbers
        function animateValue(obj, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                obj.innerHTML = Math.floor(progress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        // Trigger animations when elements come into view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                }
            });
        });

        document.querySelectorAll('.feature-card, .stats-box').forEach((el) => observer.observe(el));
    </script>
</body>
</html>

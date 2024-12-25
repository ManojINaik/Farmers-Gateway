<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Farmers Gateway</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/CustomEase.min.js"></script>
    <style>
        /* Enhanced Responsive Styles */
        :root {
            --primary-green: #A7D49B;
            --accent-blue: #3B82F6;
            --accent-green: #10B981;
            --dark-green: #00796B;
            --text-gray: #6B7280;
            --light-gray: #F3F4F6;
            --white: #FFFFFF;
            --overlay-green: rgba(167, 212, 155, 0.95);
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --hover-transform: translateY(-5px);
        }

        /* General Styles */
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--text-gray);
            background-color: var(--white);
            overflow-x: hidden;
        }

        /* Enhanced Navigation */
        .navbar {
            padding: 1.2rem 0;
            transition: all 0.4s ease;
            background: var(--overlay-green);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar.scrolled {
            padding: 0.8rem 0;
            background: rgba(255, 255, 255, 0.98);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.6rem;
            color: var(--dark-green) !important;
            letter-spacing: -0.5px;
        }

        .navbar-brand i {
            color: var(--accent-green);
            transform: rotate(-10deg);
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover i {
            transform: rotate(10deg);
        }

        .nav-link {
            font-weight: 500;
            color: var(--dark-green) !important;
            padding: 0.7rem 1.2rem !important;
            margin: 0 0.3rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--dark-green);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 80%;
        }

        /* Enhanced Hero Section */
        .hero-section {
            min-height: 100vh;
            padding-top: 76px;
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        .hero-content {
            padding: 4rem 0;
            text-align: center;
            width: 100%;
        }

        .hero-content h1 {
            font-size: clamp(2.5rem, 5vw, 4rem);
            line-height: 1.2;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            color: var(--white);
        }

        .hero-content p {
            font-size: clamp(1rem, 2vw, 1.25rem);
            opacity: 0.9;
            margin-bottom: 2.5rem;
            color: var(--white);
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .hero-buttons .btn {
            padding: 0.875rem 1.75rem;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            min-width: 160px;
            margin: 0.5rem;
            font-size: 0.9rem;
        }

        .btn-primary {
            background-color: var(--accent-blue);
            border: none;
            color: var(--white);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--white);
            color: var(--white);
        }

        .btn-outline:hover {
            background: var(--white);
            color: var(--dark-green);
        }

        @media (max-width: 767.98px) {
            .hero-content {
                padding: 3rem 1rem;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
                gap: 0.75rem;
            }

            .hero-buttons .btn {
                width: 100%;
                max-width: 280px;
                margin: 0;
            }
        }

        @media (max-width: 575.98px) {
            .hero-content {
                padding: 2rem 1rem;
            }

            .hero-content h1 {
                font-size: 2rem;
            }

            .hero-content p {
                font-size: 1rem;
            }

            .hero-buttons .btn {
                padding: 0.75rem 1.5rem;
                font-size: 0.875rem;
            }
        }

        /* Enhanced Feature Cards */
        .features-section {
            background: var(--light-gray);
            padding: 6rem 0;
        }

        .section-title {
            font-size: clamp(2rem, 4vw, 2.5rem);
            margin-bottom: 1rem;
            color: var(--dark-green);
            text-align: center;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            width: 80px;
            height: 3px;
            background: var(--accent-green);
            transform: translateX(-50%);
        }

        .feature-card {
            background: var(--white);
            padding: 2.5rem 2rem;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            transition: all 0.4s ease;
            position: relative;
            z-index: 1;
            overflow: hidden;
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
            transform-style: preserve-3d;
            perspective: 1000px;
        }

        .feature-card:hover {
            border-color: var(--accent-green);
            transform: translateY(-10px) rotateX(5deg);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--accent-blue), var(--dark-green));
            opacity: 0;
            z-index: -1;
            transition: opacity 0.4s ease;
        }

        .feature-card:hover {
            transform: var(--hover-transform);
            color: var(--white);
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-card:hover h4,
        .feature-card:hover p {
            color: var(--white);
        }

        .feature-icon {
            width: 90px;
            height: 90px;
            margin: 0 auto 25px;
            background: linear-gradient(135deg, var(--accent-blue), var(--dark-green));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.4s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(10deg);
            background: var(--white);
        }

        .feature-card:hover .feature-icon i {
            color: var(--dark-green);
        }

        /* Modern Login Cards */
        .login-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            padding: 3.5rem 2.5rem;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
            min-height: 320px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 1.5rem 0;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                135deg,
                rgba(167, 212, 155, 0.2) 0%,
                rgba(59, 130, 246, 0.2) 100%
            );
            opacity: 0;
            transition: all 0.4s ease;
            z-index: 1;
        }

        .login-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
            border-color: var(--primary-green);
        }

        .login-card:hover::before {
            opacity: 1;
        }

        .login-card * {
            position: relative;
            z-index: 2;
        }

        .login-card h3 {
            color: var(--dark-green);
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 1.8rem;
            transition: all 0.3s ease;
            background: linear-gradient(to right, var(--dark-green), var(--accent-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            opacity: 0.9;
        }

        .login-card:hover h3 {
            opacity: 1;
            transform: scale(1.05);
        }

        .login-card p {
            color: var(--text-gray);
            margin-bottom: 2rem;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            line-height: 1.7;
            max-width: 90%;
            margin-left: auto;
            margin-right: auto;
        }

        .login-card:hover p {
            color: var(--dark-green);
        }

        .login-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 4px;
            background: linear-gradient(to right, var(--primary-green), var(--accent-blue));
            transition: all 0.4s ease;
            transform: translateX(-50%);
        }

        .login-card:hover::after {
            width: 100%;
        }

        /* Add more spacing between cards on mobile */
        @media (max-width: 768px) {
            .login-card {
                margin: 2rem 0;
                padding: 3rem 2rem;
            }
        }

        /* Responsive Adjustments */
        @media (max-width: 991.98px) {
            .navbar-nav {
                background: var(--white);
                padding: 1rem;
                border-radius: 10px;
                box-shadow: var(--card-shadow);
            }

            .hero-content {
                text-align: center;
                padding: 3rem 0;
            }

            .hero-buttons .btn {
                display: block;
                margin: 1rem auto;
                max-width: 80%;
            }

            .feature-card, .login-card {
                margin-bottom: 2rem;
            }
        }

        @media (max-width: 767.98px) {
            .section-title {
                font-size: 1.8rem;
            }

            .feature-card, .login-card {
                padding: 2rem 1.5rem;
            }

            .card-icon {
                width: 80px;
                height: 80px;
            }
        }

        @media (max-width: 575.98px) {
            .hero-content h1 {
                font-size: 2rem;
            }

            .hero-content p {
                font-size: 1rem;
            }

            .hero-buttons .btn {
                padding: 0.8rem 1.5rem;
                font-size: 0.9rem;
            }
        }

        /* Loading Animation */
        .loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--white);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }
        
        .loader {
            width: 50px;
            height: 50px;
            border: 5px solid var(--light-gray);
            border-top: 5px solid var(--accent-green);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .hide-loader {
            opacity: 0;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <!-- Loading Animation -->
    <div class="loader-wrapper">
        <div class="loader"></div>
    </div>

    <div class="content-wrapper" style="opacity: 0">
        <!-- Custom Cursor -->
        <div class="cursor"></div>
        <div class="cursor-follower"></div>
        <!-- Navigation Bar -->
        <nav class="navbar navbar-expand-lg navbar-light fixed-top">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <i class="fas fa-leaf mr-2"></i>
                    Farmers Gateway
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#features">Features</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about.php">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact-script.php">Contact</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="hero-content">
                    <h1 class="animate__animated animate__fadeInDown">Welcome to Farmers Gateway</h1>
                    <p class="animate__animated animate__fadeInUp">Connecting farmers and customers directly for a sustainable future</p>
                    <div class="hero-buttons">
                        <a href="#features" class="btn btn-primary animate__animated animate__fadeInLeft">LEARN MORE</a>
                        <a href="#login" class="btn btn-outline animate__animated animate__fadeInRight">GET STARTED</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="features-section">
            <div class="container py-6">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-7 text-center">
                        <h2 class="section-title">Why Choose Us?</h2>
                        <p class="section-subtitle">Discover the benefits of our Farmers Gateway</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 mb-4">
                        <div class="feature-card animate__animated animate__fadeInUp">
                            <div class="feature-icon">
                                <i class="fas fa-tractor"></i>
                            </div>
                            <h4>For Farmers</h4>
                            <p>Direct market access, better prices, and simplified crop management</p>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <div class="feature-card animate__animated animate__fadeInUp" data-wow-delay="0.2s">
                            <div class="feature-icon">
                                <i class="fas fa-shopping-basket"></i>
                            </div>
                            <h4>For Customers</h4>
                            <p>Fresh produce, transparent pricing, and convenient shopping</p>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <div class="feature-card animate__animated animate__fadeInUp" data-wow-delay="0.4s">
                            <div class="feature-icon">
                                <i class="fas fa-leaf"></i>
                            </div>
                            <h4>Sustainable Farming</h4>
                            <p>Supporting local farmers and promoting sustainable agriculture</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Login Section -->
        <section id="login" class="login-section">
            <div class="container py-6">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="login-card farmer-card animate__animated animate__fadeInLeft">
                            <div class="card-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h3>Farmer Login</h3>
                            <p>Access your farmer dashboard</p>
                            <a href="farmer/flogin.php" class="btn btn-primary btn-block">Login as Farmer</a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="login-card customer-card animate__animated animate__fadeInUp">
                            <div class="card-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <h3>Customer Login</h3>
                            <p>Start shopping for fresh produce</p>
                            <a href="customer/clogin.php" class="btn btn-success btn-block">Login as Customer</a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="login-card admin-card animate__animated animate__fadeInRight">
                            <div class="card-icon">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <h3>Admin Login</h3>
                            <p>Manage the Farmers Gateway</p>
                            <a href="admin/alogin.php" class="btn btn-info btn-block">Login as Admin</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <?php include('footer.php'); ?>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>
        // Wait for all resources to load
        window.addEventListener('load', () => {
            // Hide loader
            document.querySelector('.loader-wrapper').classList.add('hide-loader');
            
            // Show content
            gsap.to('.content-wrapper', {
                opacity: 1,
                duration: 0.5,
                ease: 'power2.inOut'
            });

            // Initialize GSAP
            gsap.registerPlugin(ScrollTrigger, CustomEase);

            // Initialize animations after content is visible
            setTimeout(() => {
                // Custom cursor
                initCustomCursor();
                
                // Initialize other animations
                initAnimations();
            }, 100);
        });

        function initCustomCursor() {
            const cursor = document.querySelector('.cursor');
            const follower = document.querySelector('.cursor-follower');
            
            if (!cursor || !follower) return;

            let mouseX = 0;
            let mouseY = 0;
            let cursorX = 0;
            let cursorY = 0;
            let followerX = 0;
            let followerY = 0;

            gsap.to([cursor, follower], {
                duration: 1,
                opacity: 1,
                ease: 'power2.out'
            });

            document.addEventListener('mousemove', (e) => {
                mouseX = e.clientX;
                mouseY = e.clientY;
            });

            gsap.ticker.add(() => {
                const dt = 1.0 - Math.pow(0.8, gsap.ticker.deltaRatio());
                
                cursorX += (mouseX - cursorX) * dt;
                cursorY += (mouseY - cursorY) * dt;
                followerX += (mouseX - followerX) * (dt * 0.5);
                followerY += (mouseY - followerY) * (dt * 0.5);

                gsap.set(cursor, { x: cursorX - 10, y: cursorY - 10 });
                gsap.set(follower, { x: followerX - 20, y: followerY - 20 });
            });
        }

        function initAnimations() {
            // Text split animation
            document.querySelectorAll('.split-text').forEach(text => {
                if (!text.hasAttribute('data-split')) {
                    splitText(text);
                    text.setAttribute('data-split', 'true');
                }
            });

            // Custom ease for animations
            const customEase = CustomEase.create('custom', 'M0,0 C0.126,0.382 0.282,0.674 0.44,0.822 0.632,1.002 0.818,1.001 1,1');

            // Scroll animations
            ScrollTrigger.batch('.feature-card', {
                onEnter: (elements) => {
                    gsap.from(elements, {
                        y: 100,
                        opacity: 0,
                        duration: 1.2,
                        ease: customEase,
                        stagger: 0.15
                    });
                },
                once: true
            });

            // Initial animations
            const tl = gsap.timeline();
            
            tl.from('.navbar', {
                y: -100,
                opacity: 0,
                duration: 0.8,
                ease: customEase
            })
            .from('.hero-content > *', {
                y: 50,
                opacity: 0,
                duration: 0.8,
                stagger: 0.2,
                ease: customEase
            }, '-=0.4');
        }

        // Helper function for text splitting
        function splitText(element) {
            const text = element.textContent;
            const chars = text.split('');
            element.textContent = '';
            chars.forEach((char) => {
                const span = document.createElement('span');
                span.textContent = char;
                element.appendChild(span);
            });
        }
    </script>

    <!-- Three.js scene setup -->
    <script>
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
        
        // Set renderer size and add to DOM
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.getElementById('hero-canvas').appendChild(renderer.domElement);
        
        // Ambient light
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
        scene.add(ambientLight);
        
        // Directional light
        const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
        directionalLight.position.set(5, 5, 5);
        scene.add(directionalLight);

        // Camera position
        camera.position.set(0, 10, 20);
        camera.lookAt(0, 0, 0);

        // Create curved road
        const roadCurve = new THREE.CurvePath();
        const startPoint = new THREE.Vector3(-15, 0, -5);
        const controlPoint1 = new THREE.Vector3(-5, 0, -8);
        const controlPoint2 = new THREE.Vector3(5, 0, -8);
        const endPoint = new THREE.Vector3(15, 0, -5);
        
        const curve = new THREE.CubicBezierCurve3(
            startPoint,
            controlPoint1,
            controlPoint2,
            endPoint
        );
        
        roadCurve.add(curve);

        // Create road mesh
        const roadGeometry = new THREE.TubeGeometry(curve, 50, 1, 8, false);
        const roadMaterial = new THREE.MeshStandardMaterial({ 
            color: 0x333333,
            roughness: 0.8,
            metalness: 0.2
        });
        const road = new THREE.Mesh(roadGeometry, roadMaterial);
        scene.add(road);

        // Create grass plane
        const grassGeometry = new THREE.PlaneGeometry(50, 30);
        const grassMaterial = new THREE.MeshStandardMaterial({ 
            color: 0x90EE90,
            side: THREE.DoubleSide,
            roughness: 0.8
        });
        const grass = new THREE.Mesh(grassGeometry, grassMaterial);
        grass.rotation.x = -Math.PI / 2;
        grass.position.y = -0.1;
        scene.add(grass);

        // Load tractor model
        let tractor;
        const loader = new THREE.GLTFLoader();
        loader.load('https://raw.githubusercontent.com/mrdoob/three.js/dev/examples/models/gltf/Tractor/glTF/Tractor.gltf', (gltf) => {
            tractor = gltf.scene;
            tractor.scale.set(0.5, 0.5, 0.5);
            scene.add(tractor);
        });

        // Create tree function
        function createTree(x, z) {
            const trunkGeometry = new THREE.CylinderGeometry(0.2, 0.3, 1.5, 8);
            const trunkMaterial = new THREE.MeshStandardMaterial({ color: 0x8B4513 });
            const trunk = new THREE.Mesh(trunkGeometry, trunkMaterial);

            const leavesGeometry = new THREE.ConeGeometry(1, 2, 8);
            const leavesMaterial = new THREE.MeshStandardMaterial({ color: 0x228B22 });
            const leaves = new THREE.Mesh(leavesGeometry, leavesMaterial);
            leaves.position.y = 1.5;

            const tree = new THREE.Group();
            tree.add(trunk);
            tree.add(leaves);
            tree.position.set(x, 0, z);
            return tree;
        }

        // Add trees along the road
        for (let i = -15; i <= 15; i += 3) {
            const offsetZ = Math.sin((i + 15) * 0.2) * 2;
            scene.add(createTree(i, -8 + offsetZ));
            scene.add(createTree(i, -12 + offsetZ));
            scene.add(createTree(i, -4 + offsetZ));
            scene.add(createTree(i, 0 + offsetZ));
        }

        // Animation variables
        let progress = 0;
        const tractorSpeed = 0.001;

        // Animation loop
        function animate() {
            requestAnimationFrame(animate);

            if (tractor) {
                // Update tractor position along the curve
                progress = (progress + tractorSpeed) % 1;
                const point = curve.getPointAt(progress);
                const tangent = curve.getTangentAt(progress);

                tractor.position.copy(point);
                tractor.position.y += 0.5; // Lift tractor slightly above road

                // Calculate rotation to face direction of movement
                const rotation = new THREE.Matrix4();
                const up = new THREE.Vector3(0, 1, 0);
                const axis = new THREE.Vector3();

                axis.crossVectors(up, tangent).normalize();
                rotation.makeRotationAxis(axis, Math.acos(up.dot(tangent)));
                tractor.quaternion.setFromRotationMatrix(rotation);
                tractor.rotateY(Math.PI / 2); // Adjust tractor orientation
            }

            renderer.render(scene, camera);
        }

        // Handle window resize
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });

        // Start animation
        animate();
    </script>

    <!-- Add Navbar Scroll Effect -->
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>

    <!-- GSAP Initialization -->
    <script>
        gsap.registerPlugin(ScrollTrigger);

        // Magnetic button effect
        document.querySelectorAll('.magnetic-button').forEach(btn => {
            btn.addEventListener('mousemove', (e) => {
                const rect = btn.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const deltaX = (x - centerX) / centerX;
                const deltaY = (y - centerY) / centerY;
                
                gsap.to(btn, {
                    duration: 0.3,
                    x: deltaX * 20,
                    y: deltaY * 20,
                    rotateX: -deltaY * 20,
                    rotateY: deltaX * 20,
                    ease: 'power2.out'
                });
            });

            btn.addEventListener('mouseleave', () => {
                gsap.to(btn, {
                    duration: 0.6,
                    x: 0,
                    y: 0,
                    rotateX: 0,
                    rotateY: 0,
                    ease: 'elastic.out(1, 0.3)'
                });
            });
        });

        // Text split animation
        const splitText = (element) => {
            const text = element.textContent;
            const chars = text.split('');
            element.textContent = '';
            chars.forEach((char) => {
                const span = document.createElement('span');
                span.textContent = char;
                element.appendChild(span);
            });
        };

        document.querySelectorAll('.split-text').forEach(text => {
            splitText(text);
            gsap.from(text.querySelectorAll('span'), {
                scrollTrigger: {
                    trigger: text,
                    start: 'top 80%',
                    toggleActions: 'play none none reverse'
                },
                opacity: 0,
                rotateX: -90,
                stagger: 0.02,
                duration: 0.8,
                ease: 'power2.out'
            });
        });

        // Smooth page transitions
        const pageTransition = () => {
            const tl = gsap.timeline();
            
            tl.to('.page-transition', {
                duration: 0.8,
                scaleY: 1,
                transformOrigin: 'bottom',
                ease: 'power4.inOut'
            })
            .to('.page-transition', {
                duration: 0.8,
                scaleY: 0,
                transformOrigin: 'top',
                ease: 'power4.inOut'
            });
        };

        // Image reveal animation
        gsap.utils.toArray('.hover-reveal').forEach(image => {
            const tl = gsap.timeline({ paused: true });
            
            tl.from(image, {
                scale: 1.2,
                duration: 1.5,
                ease: 'power3.out'
            });

            image.addEventListener('mouseenter', () => tl.play());
            image.addEventListener('mouseleave', () => tl.reverse());
        });

        // Enhanced scroll animations
        const customEase = CustomEase.create('custom', 'M0,0 C0.126,0.382 0.282,0.674 0.44,0.822 0.632,1.002 0.818,1.001 1,1');

        ScrollTrigger.batch('.feature-card', {
            onEnter: (elements) => {
                gsap.from(elements, {
                    y: 100,
                    opacity: 0,
                    duration: 1.2,
                    ease: customEase,
                    stagger: 0.15
                });
            }
        });

        // Page load animation
        window.addEventListener('load', () => {
            const tl = gsap.timeline();

            tl.from('body', {
                opacity: 0,
                duration: 0.6,
                ease: 'power2.inOut'
            })
            .from('.navbar', {
                y: -100,
                opacity: 0,
                duration: 0.8,
                ease: customEase
            })
            .from('.hero-content > *', {
                y: 50,
                opacity: 0,
                duration: 0.8,
                stagger: 0.2,
                ease: customEase
            }, '-=0.4');
        });
    </script>
</body>
</html>
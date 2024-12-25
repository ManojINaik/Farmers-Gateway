<?php
session_start();
$error = '';

require('../sql.php');

if(isset($_POST['login'])) {
    $farmer_email = mysqli_real_escape_string($conn, $_POST['farmer_email']);
    $farmer_password = mysqli_real_escape_string($conn, $_POST['farmer_password']);
    
    $query = "SELECT * FROM farmerlogin WHERE email='$farmer_email' AND password='$farmer_password'";
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        $_SESSION['farmer_login_user'] = $farmer_email;
        $_SESSION['IS_LOGIN'] = $farmer_email;
        
        if(isset($_POST['remember']) && $_POST['remember'] == 'on') {
            setcookie('farmer_login', $farmer_email, time() + (86400 * 30), "/");
        }
        
        header("Location: fprofile.php");
        exit();
    } else {
        $error = "Username or Password is invalid";
    }
    
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Login - Farmers Gateway</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../assets/css/login.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
</head>
<body>
    <div class="animated-background"></div>
    <div id="particles"></div>

    <a href="../index.php" class="back-to-home">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Home</span>
    </a>

    <div class="page-wrapper">
        <div class="login-container">
            <div class="login-header">
                <h1><i class="fas fa-tractor"></i> Farmer Login</h1>
                <p>Welcome back! Please login to your account</p>
            </div>

            <?php if($error != '') { ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php } ?>

            <form method="POST" action="" id="loginForm">
                <div class="form-group">
                    <input type="email" class="form-control" id="farmer_email" name="farmer_email" placeholder=" " required>
                    <label class="form-label" for="farmer_email">Email Address</label>
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" id="farmer_password" name="farmer_password" placeholder=" " required>
                    <label class="form-label" for="farmer_password">Password</label>
                </div>

                <div class="remember-forgot">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
                </div>

                <button type="submit" name="login" class="btn-login">
                    <span>Login</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
                
                <div class="register-link mt-3 text-center">
                    <p>Don't have an account? <a href="fregister.php" class="text-success">Register here</a></p>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Initialize GSAP animations
        window.addEventListener('DOMContentLoaded', (event) => {
            // Create particles
            createParticles();

            // Animate login container
            gsap.to('.login-container', {
                opacity: 1,
                y: 0,
                duration: 1,
                ease: 'power3.out'
            });

            // Animate header elements
            gsap.to('.login-header h1', {
                opacity: 1,
                y: 0,
                duration: 1,
                delay: 0.3,
                ease: 'power3.out'
            });

            gsap.to('.login-header p', {
                opacity: 1,
                y: 0,
                duration: 1,
                delay: 0.4,
                ease: 'power3.out'
            });

            // Animate form groups
            gsap.to('.form-group', {
                opacity: 1,
                x: 0,
                duration: 1,
                delay: 0.5,
                stagger: 0.1,
                ease: 'power3.out'
            });

            // Animate remember-forgot section
            gsap.to('.remember-forgot', {
                opacity: 1,
                y: 0,
                duration: 1,
                delay: 0.7,
                ease: 'power3.out'
            });

            // Animate login button
            gsap.to('.btn-login', {
                opacity: 1,
                y: 0,
                duration: 1,
                delay: 0.8,
                ease: 'power3.out'
            });

            // Animate back to home link
            gsap.to('.back-to-home', {
                opacity: 1,
                x: 0,
                duration: 1,
                delay: 0.9,
                ease: 'power3.out'
            });

            // Animate error message if present
            if(document.querySelector('.error-message')) {
                gsap.to('.error-message', {
                    opacity: 1,
                    y: 0,
                    duration: 1,
                    delay: 0.6,
                    ease: 'power3.out'
                });
            }
        });

        // Create floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const numberOfParticles = 50;

            for(let i = 0; i < numberOfParticles; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                // Random size between 2 and 6 pixels
                const size = Math.random() * 4 + 2;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                
                // Random position
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.top = `${Math.random() * 100}%`;
                
                particlesContainer.appendChild(particle);

                // Animate each particle
                gsap.to(particle, {
                    y: `${Math.random() * 200 - 100}`,
                    x: `${Math.random() * 200 - 100}`,
                    duration: Math.random() * 3 + 2,
                    repeat: -1,
                    yoyo: true,
                    ease: 'power1.inOut'
                });
            }
        }

        // Form input animations
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', () => {
                gsap.to(input, {
                    scale: 1.02,
                    duration: 0.3,
                    ease: 'power2.out'
                });
            });

            input.addEventListener('blur', () => {
                gsap.to(input, {
                    scale: 1,
                    duration: 0.3,
                    ease: 'power2.out'
                });
            });
        });
    </script>
</body>
</html>

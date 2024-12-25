<!DOCTYPE html>
<?php
include('aloginScript.php'); // Includes Login Script
?>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" type="image/png" href="../assets/img/logo.png" />
  <title>Farmers Gateway - Admin Login</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  
  <!-- CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  
  <style>
    :root {
      --primary-color: #2ecc71;
      --secondary-color: #27ae60;
      --accent-color: #219653;
      --background-start: #134e5e;
      --background-end: #71b280;
    }

    body {
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      margin: 0;
      background: linear-gradient(135deg, var(--background-start) 0%, var(--background-end) 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
      position: relative;
      overflow-x: hidden;
    }

    body::before {
      content: '';
      position: absolute;
      width: 150%;
      height: 150%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
      top: -25%;
      left: -25%;
      animation: gradient-animation 15s ease infinite;
    }

    @keyframes gradient-animation {
      0% { transform: translate(0, 0) rotate(0deg); }
      50% { transform: translate(-5%, 5%) rotate(180deg); }
      100% { transform: translate(0, 0) rotate(360deg); }
    }

    .login-container {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 450px;
      opacity: 0;
      transform: translateY(30px);
    }

    .login-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
      padding: 3rem 2rem;
      transform: translateY(0);
      transition: all 0.3s ease;
    }

    .login-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.25);
    }

    .login-card:hover::after {
      transform: rotate(45deg) translate(50%, 50%);
    }

    .login-header {
      text-align: center;
      margin-bottom: 2.5rem;
    }

    .login-header img {
      width: 90px;
      margin-bottom: 1.5rem;
      opacity: 0;
      transform: scale(0.8);
      animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
      100% { transform: translateY(0px); }
    }

    .login-header h4 {
      color: var(--background-start);
      font-weight: 600;
      margin-bottom: 0.5rem;
      font-size: 1.75rem;
      opacity: 0;
      transform: translateY(20px);
    }

    .login-header p {
      color: #666;
      font-size: 0.95rem;
      opacity: 0;
      transform: translateY(20px);
    }

    .form-group {
      margin-bottom: 1.5rem;
      position: relative;
      opacity: 0;
      transform: translateX(-20px);
    }

    .form-group label {
      color: #444;
      font-weight: 500;
      margin-bottom: 0.5rem;
      font-size: 0.95rem;
    }

    .form-control {
      border-radius: 12px;
      padding: 0.75rem 1rem;
      border: 2px solid #e1e1e1;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: rgba(255, 255, 255, 0.9);
      transform: translateY(0);
    }

    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 4px rgba(46, 204, 113, 0.1);
      background: white;
      transform: translateY(-2px);
    }

    .input-icon {
      position: absolute;
      top: 38px;
      right: 15px;
      color: #666;
      transition: all 0.3s ease;
      transform: scale(1);
    }

    .form-control:focus + .input-icon {
      color: var(--primary-color);
      transform: scale(1.1);
    }

    .btn-login {
      opacity: 0;
      transform: translateY(20px);
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      font-size: 1rem;
      border-radius: 12px;
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
      border: none;
      width: 100%;
      margin-top: 1rem;
      position: relative;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
    }

    .btn-login:active {
      transform: translateY(0);
    }

    .btn-login::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(
        120deg,
        transparent,
        rgba(255, 255, 255, 0.2),
        transparent
      );
      transition: all 0.6s ease;
    }

    .btn-login:hover::before {
      left: 100%;
    }

    .back-to-home {
      color: white;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      margin-bottom: 1.5rem;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      background: rgba(255, 255, 255, 0.1);
      padding: 0.5rem 1rem;
      border-radius: 8px;
      backdrop-filter: blur(5px);
    }

    .back-to-home i {
      margin-right: 0.5rem;
      transition: transform 0.3s ease;
    }

    .back-to-home:hover {
      color: white;
      text-decoration: none;
      background: rgba(255, 255, 255, 0.2);
    }

    .back-to-home:hover i {
      transform: translateX(-4px);
    }

    .error-message {
      background: rgba(220, 53, 69, 0.1);
      color: #dc3545;
      font-size: 0.875rem;
      padding: 0.75rem 1rem;
      border-radius: 8px;
      margin-top: 0.5rem;
      backdrop-filter: blur(5px);
      border: 1px solid rgba(220, 53, 69, 0.2);
    }

    @media (max-width: 576px) {
      body {
        padding: 1rem;
      }
      
      .login-card {
        padding: 2rem 1.5rem;
      }
    }
  </style>
</head>

<body>
  <div class="login-container">
    <a href="../index.php" class="back-to-home" data-aos="fade-right">
      <i class="fas fa-arrow-left"></i>
      Back to Home
    </a>
    
    <div class="login-card" data-aos="zoom-in" data-aos-delay="100">
      <div class="login-header">
        <img src="../assets/img/logo.png" alt="Farmers Gateway Logo">
        <h4>Admin Login</h4>
        <p>Welcome back! Please login to your account.</p>
      </div>

      <form method="post" action="">
        <div class="form-group">
          <label for="admin_name">Admin ID</label>
          <input type="text" class="form-control" required id="admin_name" name="admin_name" placeholder="Enter your admin ID">
          <i class="fas fa-user input-icon"></i>
        </div>

        <div class="form-group">
          <label for="admin_password">Password</label>
          <input type="password" class="form-control" required id="admin_password" name="admin_password" placeholder="Enter your password">
          <i class="fas fa-lock input-icon"></i>
        </div>

        <?php if(isset($error) && !empty($error)) { ?>
          <div class="error-message text-center" data-aos="fade-up">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?php echo $error; ?>
          </div>
        <?php } ?>

        <button type="submit" name="adminlogin" class="btn btn-primary btn-login">
          Login
          <i class="fas fa-sign-in-alt ml-2"></i>
        </button>
      </form>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script>
    AOS.init({
      duration: 800,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });

    // GSAP Animations
    window.addEventListener('DOMContentLoaded', (event) => {
      // Main container animation
      gsap.to('.login-container', {
        opacity: 1,
        y: 0,
        duration: 1,
        ease: 'power3.out'
      });

      // Logo animation
      gsap.to('.login-header img', {
        opacity: 1,
        scale: 1,
        duration: 0.8,
        delay: 0.3,
        ease: 'back.out(1.7)'
      });

      // Header text animations
      gsap.to('.login-header h4', {
        opacity: 1,
        y: 0,
        duration: 0.8,
        delay: 0.5,
        ease: 'power3.out'
      });

      gsap.to('.login-header p', {
        opacity: 1,
        y: 0,
        duration: 0.8,
        delay: 0.7,
        ease: 'power3.out'
      });

      // Form groups animation
      gsap.to('.form-group', {
        opacity: 1,
        x: 0,
        duration: 0.8,
        delay: 0.9,
        stagger: 0.2,
        ease: 'power3.out'
      });

      // Button animation
      gsap.to('.btn-login', {
        opacity: 1,
        y: 0,
        duration: 0.8,
        delay: 1.3,
        ease: 'power3.out'
      });

      // Hover animation for login card
      const loginCard = document.querySelector('.login-card');
      loginCard.addEventListener('mousemove', (e) => {
        const rect = loginCard.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const xPercent = (x / rect.width - 0.5) * 20;
        const yPercent = (y / rect.height - 0.5) * 20;
        
        gsap.to(loginCard, {
          rotationY: xPercent,
          rotationX: -yPercent,
          duration: 0.5,
          ease: 'power2.out',
          transformPerspective: 1000
        });
      });

      loginCard.addEventListener('mouseleave', () => {
        gsap.to(loginCard, {
          rotationY: 0,
          rotationX: 0,
          duration: 0.5,
          ease: 'power2.out'
        });
      });
    });
  </script>
</body>
</html>
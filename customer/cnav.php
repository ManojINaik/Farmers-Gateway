    <?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize variables
$customer_name = isset($_SESSION['customer_login_name']) ? $_SESSION['customer_login_name'] : '';

// Check if user is logged in
if (!isset($_SESSION['customer_login_user'])) {
    header("Location: ../index.php");
    exit();
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('../includes/responsive-helper.php'); addResponsiveCSS(); ?>
</head>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="../index.php">
            <i class="fas fa-store text-success"></i>
            <span class="text-success">FARMERSGATEWAY</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="cbuy_crops.php">
                        <i class="fas fa-shopping-basket"></i> Buy Crops
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_cart.php">
                        <i class="fas fa-shopping-cart"></i> Cart
                        <?php 
                        if(isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0) {
                            echo '<span class="badge badge-pill badge-success">' . $_SESSION['cart_count'] . '</span>';
                        }
                        ?>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link px-3" href="cpurchase_history.php">
                        <i class="fas fa-history"></i>
                        Purchase History
                    </a>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-tools"></i>
                        Tools
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <!-- <li>
                            <a class="dropdown-item" href="cchatgpt.php">
                                <i class="fas fa-robot"></i>
                                Chat Bot
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="cweather_prediction.php">
                                <i class="fas fa-cloud-sun"></i>
                                Weather Forecast
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="cnewsfeed.php">
                                <i class="fas fa-newspaper"></i>
                                News Feed
                            </a>
                        </li> -->
                        <li>
                            <a class="dropdown-item" href="cfarmtube.php">
                                <i class="fas fa-video"></i>
                                FarmTube
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link px-3" href="cprofile.php">
                        <i class="fas fa-user"></i>
                        <?php echo $customer_name; ?>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link px-3" href="clogout.php">
                        <i class="fas fa-power-off"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
.navbar {
    background: #1a1a1a !important;
    padding: 0.5rem 0;
    height: 60px;
}

.container-fluid {
    padding-left: 1.5rem;
    padding-right: 1.5rem;
}

.navbar-brand {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.2rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    padding: 0;
}

.navbar-brand i {
    font-size: 1.4rem;
}

.navbar-nav {
    gap: 0.5rem;
}

.nav-link {
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    transition: color 0.2s ease;
}

.nav-link:hover {
    color: #2ECC71 !important;
}

.nav-link i {
    font-size: 1rem;
}

.dropdown-menu {
    background: #2c2c2c;
    border: none;
    border-radius: 4px;
    margin-top: 0.5rem;
    padding: 0.5rem;
}

.dropdown-item {
    color: rgba(255,255,255,0.85);
    padding: 0.5rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background: rgba(46, 204, 113, 0.1);
    color: #2ECC71;
}

.dropdown-item i {
    width: 16px;
    text-align: center;
}

@media (max-width: 991.98px) {
    .navbar {
        height: auto;
        padding: 0.5rem 1rem;
    }
    
    .navbar-collapse {
        background: #1a1a1a;
        position: fixed;
        top: 60px;
        left: 0;
        right: 0;
        padding: 1rem;
        max-height: calc(100vh - 60px);
        overflow-y: auto;
        z-index: 1000;
    }
    
    .navbar-nav {
        gap: 0;
    }
    
    .nav-item {
        width: 100%;
        margin: 0;
    }
    
    .nav-link {
        padding: 0.75rem 1rem;
        width: 100%;
    }
    
    .dropdown-menu {
        background: #242424;
        margin-top: 0;
        margin-bottom: 0.5rem;
        padding: 0;
        position: static !important;
        transform: none !important;
        border-radius: 0;
    }
    
    .dropdown-item {
        padding: 0.75rem 1.5rem;
    }
    
    .navbar-collapse {
        transition: transform 0.3s ease-in-out;
        transform: translateX(-100%);
    }
    
    .navbar-collapse.show {
        transform: translateX(0);
    }
    
    .navbar-toggler {
        border-color: rgba(255,255,255,0.1);
        padding: 0.5rem;
    }
    
    .navbar-toggler:focus {
        box-shadow: none;
        outline: none;
    }
    
    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.7%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }
}
</style>

<!-- Bootstrap 5 JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Add custom JavaScript for mobile menu -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const target = e.target;
        const isDropdownButton = target.matches('[data-bs-toggle="dropdown"]') || 
                               target.closest('[data-bs-toggle="dropdown"]');
        const isDropdownMenu = target.matches('.dropdown-menu') || 
                             target.closest('.dropdown-menu');
        const isNavbar = target.closest('.navbar');

        if (!isNavbar || (!isDropdownButton && !isDropdownMenu)) {
            const collapse = document.querySelector('.navbar-collapse');
            if (collapse && collapse.classList.contains('show')) {
                bootstrap.Collapse.getInstance(collapse).hide();
            }
        }
    });

    // Close collapsed menu only when clicking regular links (not dropdown toggles)
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link:not(.dropdown-toggle)');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            const collapse = document.querySelector('.navbar-collapse');
            if (collapse && collapse.classList.contains('show')) {
                bootstrap.Collapse.getInstance(collapse).hide();
            }
        });
    });
});
</script>
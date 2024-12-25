 <?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_login_user'])) {
    header("Location: ../index.php");
    exit();
}

$admin_name = isset($_SESSION['admin_login_user']) ? $_SESSION['admin_login_user'] : '';
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('../includes/responsive-helper.php'); addResponsiveCSS(); ?>
</head>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="../index.php">
            <i class="fas fa-leaf text-success"></i>
            <span class="text-success">FARMERSGATEWAY</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="usersDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="nav-link-inner--text">
                            <i class="fas fa-users"></i> Users
                        </span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="usersDropdown">
                        <a class="dropdown-item" href="afarmers.php">
                            <i class="fas fa-user-tie"></i> Farmers
                        </a>
                        <a class="dropdown-item" href="acustomers.php">
                            <i class="fas fa-user"></i> Customers
                        </a>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="managementDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="nav-link-inner--text">
                            <i class="fas fa-tasks"></i> Management
                        </span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="managementDropdown">
                        <a class="dropdown-item" href="aproducedcrop.php">
                            <i class="fas fa-store-alt"></i> Crop Stock
                        </a>
                        <a class="dropdown-item" href="aviewmsg.php">
                            <i class="fas fa-envelope"></i> Queries
                        </a>
                    </div>
                </li>

                <!-- <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="tradeDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="nav-link-inner--text">
                            <i class="fas fa-exchange-alt"></i> Trade
                        </span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="tradeDropdown">
                        <a class="dropdown-item" href="acrop_requests.php">
                            <i class="fas fa-seedling"></i> Crop Requests
                        </a>
                        <a class="dropdown-item" href="acrop_trades.php">
                            <i class="fas fa-handshake"></i> Crop Trades
                        </a>
                        <a class="dropdown-item" href="asales_history.php">
                            <i class="fas fa-history"></i> Sales History
                        </a>
                    </div>
                </li> -->

                <!-- <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="toolsDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="nav-link-inner--text">
                            <i class="fas fa-tools"></i> Tools
                        </span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="toolsDropdown">
                        <a class="dropdown-item" href="achatgpt.php">
                            <i class="fas fa-robot"></i> Chat Bot
                        </a>
                        <a class="dropdown-item" href="aweather_prediction.php">
                            <i class="fas fa-cloud-sun"></i> Weather Forecast
                        </a>
                        <a class="dropdown-item" href="anewsfeed.php">
                            <i class="fas fa-newspaper"></i> News Feed
                        </a>
                        <a class="dropdown-item" href="farmtube.php">
                            <i class="fas fa-video"></i> FarmTube
                        </a>
                    </div>
                </li> -->
                
                <li class="nav-item">
                    <a class="nav-link text-white" href="aprofile.php">
                        <i class="fas fa-user"></i>
                        <?php echo $admin_name; ?>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link text-white" href="alogout.php">
                        <i class="fas fa-power-off"></i> Logout
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
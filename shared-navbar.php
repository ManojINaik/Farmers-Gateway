<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-leaf mr-2"></i>
            Farmers Gateway
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">
                    <a class="nav-link" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact-script.php">Contact</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="farmer/flogin.php">Farmer Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="customer/clogin.php">Customer Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin/alogin.php">Admin Login</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

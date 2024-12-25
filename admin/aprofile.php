<?php
session_start();// Starting Session
require('../sql.php'); // Includes Login Script

// Storing Session
$user = $_SESSION['admin_login_user'];

if(!isset($_SESSION['admin_login_user'])){
header("location: ../index.php");} // Redirecting To Home Page
$query4 = "SELECT * from admin where admin_name ='$user'";
              $ses_sq4 = mysqli_query($conn, $query4);
              $row4 = mysqli_fetch_assoc($ses_sq4);
              $para1 = $row4['admin_id'];
              $para2 = $row4['admin_name'];
			  $para3 = $row4['admin_password'];
?>

<!DOCTYPE html>
<html>
<?php require ('aheader.php');  ?>

<head>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f8fafc;
    }

    .profile-section {
      padding: 3rem 0;
    }

    .profile-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      margin-bottom: 2rem;
    }

    .profile-header {
      background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%);
      padding: 2rem;
      color: white;
      text-align: center;
    }

    .profile-img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      border: 4px solid rgba(255, 255, 255, 0.2);
      margin-bottom: 1rem;
    }

    .profile-name {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    .profile-id {
      font-size: 0.9rem;
      opacity: 0.9;
    }

    .profile-body {
      padding: 2rem;
    }

    .info-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }

    .info-header {
      background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
      padding: 1.5rem;
      color: white;
    }

    .info-title {
      margin: 0;
      font-size: 1.25rem;
      font-weight: 600;
    }

    .info-body {
      padding: 1.5rem;
    }

    .info-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .info-item {
      display: flex;
      align-items: center;
      padding: 1rem;
      border-bottom: 1px solid #e2e8f0;
      color: #475569;
    }

    .info-item:last-child {
      border-bottom: none;
    }

    .info-item i {
      margin-right: 1rem;
      color: #3b82f6;
      font-size: 1.25rem;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
      margin-top: 2rem;
    }

    .stat-card {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      text-align: center;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
    }

    .stat-icon {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
      font-size: 1.5rem;
    }

    .stat-icon.blue {
      background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%);
      color: white;
    }

    .stat-icon.green {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      color: white;
    }

    .stat-icon.purple {
      background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
      color: white;
    }

    .stat-value {
      font-size: 1.5rem;
      font-weight: 600;
      color: #1e293b;
      margin-bottom: 0.5rem;
    }

    .stat-label {
      color: #64748b;
      font-size: 0.875rem;
    }
  </style>
</head>

<body class="bg-white" id="top">
  
<?php require ('anav.php');  ?>
 	
<div class="profile-section">
  <div class="container">
    <div class="row">
      <div class="col-lg-4">
        <!-- Profile Card -->
        <div class="profile-card">
          <div class="profile-header">
            <img src="../assets/img/admin.png" alt="Admin" class="profile-img">
            <h2 class="profile-name">Welcome <?php echo $para2 ?></h2>
            <div class="profile-id">Admin ID: <?php echo $para1 ?></div>
          </div>
          <div class="profile-body">
            <div class="stats-grid">
              <div class="stat-card">
                <div class="stat-icon blue">
                  <i class="fas fa-users"></i>
                </div>
                <div class="stat-value">Manage</div>
                <div class="stat-label">Users</div>
              </div>
              <div class="stat-card">
                <div class="stat-icon green">
                  <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value">View</div>
                <div class="stat-label">Analytics</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-8">
        <!-- Info Card -->
        <div class="info-card">
          <div class="info-header">
            <h3 class="info-title">
              <i class="fas fa-shield-alt mr-2"></i>
              Admin Privileges
            </h3>
          </div>
          <div class="info-body">
            <ul class="info-list">
              <li class="info-item">
                <i class="fas fa-database"></i>
                <span>Full access to all data in the Farmers Gateway</span>
              </li>
              <li class="info-item">
                <i class="fas fa-user-edit"></i>
                <span>Ability to modify and view all Customer details</span>
              </li>
              <li class="info-item">
                <i class="fas fa-users-cog"></i>
                <span>Management of farmer profiles and supply details</span>
              </li>
              <li class="info-item">
                <i class="fas fa-chart-bar"></i>
                <span>Access to comprehensive sales reports with sorting capabilities</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon blue">
              <i class="fas fa-user-shield"></i>
            </div>
            <div class="stat-value">Security</div>
            <div class="stat-label">Manage Access</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon green">
              <i class="fas fa-sync"></i>
            </div>
            <div class="stat-value">Updates</div>
            <div class="stat-label">System Status</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon purple">
              <i class="fas fa-cog"></i>
            </div>
            <div class="stat-value">Settings</div>
            <div class="stat-label">Configuration</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require("../modern-footer.php");?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
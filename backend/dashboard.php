<?php
include('db.php');
$link = 'dashboard';

$blogs = mysqli_query($con, "select count(id) from blogs");
if ($blogs->num_rows > 0) {
    $blogs = mysqli_fetch_assoc($blogs)['count(id)'];
} else {
    $blogs = 0;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - AILC Admin</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        .main-content {
            margin-left: 133px !important;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <button class="mobile-menu-btn" onclick="toggleSidebar()">☰</button>

        <!-- Sidebar -->
        <?php include('sidenav.php') ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Dashboard Section -->
            <div id="dashboard-section" class="section">
                <div class="header">
                    <h1>Welcome back, Admin</h1>
                    <p>Here's what's happening with your website today.</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <h3><?= $blogs ?></h3>
                        <p>Total Blogs Have Created</p>
                    </div>
                </div>
            </div>
</body>

</html>
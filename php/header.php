<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildPriceIQ - <?php echo $page_title ?? 'Construction Material Prices'; ?></title>
    <link rel="stylesheet" href="/buildpriceiq/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <i class="fas fa-hard-hat"></i>
                <span>BuildPriceIQ</span>
            </div>
            <div class="nav-menu" id="navMenu">
                  <a href="index.html" class="nav-link">Home</a>
                  <a href="compare.html" class="nav-link">Compare Prices</a>
                  <a href="estimator.html" class="nav-link">Cost Estimator</a>
                  <a href="trends.html" class="nav-link">Market Trends</a>
                  <a href="about.html" class="nav-link">About</a>
                </div>
            <div class="nav-buttons" id="navButtons">
                <button class="btn btn-outline" onclick="openModal('loginModal')">Login</button>
                <button class="btn btn-primary" onclick="openModal('registerModal')">Register</button>
            </div>
            <div class="user-menu" id="userMenu" style="display: none;">
                <span id="userName"></span>
                <button class="btn btn-outline-small" onclick="logout()">Logout</button>
            </div>
        </div>
    </nav>
<?php
session_start();

// Clear any existing user session
if (isset($_SESSION['user_id'])) {
    unset($_SESSION['user_id']);
    unset($_SESSION['role']);
}

// Set guest session
$_SESSION['is_guest'] = true;
$_SESSION['user_name'] = 'Guest';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Guest Mode - COM3 2O BUY</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/guest.css">
</head>
<body>

<div class="top-bar">
    <div class="logo">📚 COM3 2O BUY</div>
    <div class="nav">
        <span class="guest-badge">👤 Guest Mode</span>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
        <a href="logout.php" class="exit-guest-btn">🚪 Exit Guest</a>
    </div>
</div>

<div class="main">
    <div class="welcome-card">
        <h1>Welcome, Guest! 👋</h1>
        <p>You are browsing as a guest. Create an account to enjoy more features!</p>
        
        <a href="member_dashboard.php" class="btn">Browse Books</a>
        <a href="login.php" class="btn btn-outline">Login / Register</a>
        
        <div class="features">
            <div class="feature">
                <i class="fas fa-eye"></i>
                <h3>View Books</h3>
                <p>Browse all our collections</p>
            </div>
            <div class="feature">
                <i class="fas fa-shopping-cart"></i>
                <h3>Add to Cart</h3>
                <p>Try shopping experience</p>
            </div>
            <div class="feature">
                <i class="fas fa-heart"></i>
                <h3>Save Favorites</h3>
                <p>Login to save wishlist</p>
            </div>
        </div>
    </div>
</div>

<div class="footer">
    <p>© 2026 COM3 2O BUY</p>
</div>

</body>
</html>
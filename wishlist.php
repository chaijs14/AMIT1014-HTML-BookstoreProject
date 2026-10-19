<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get wishlist books
$stmt = $pdo->prepare("SELECT p.* FROM products p 
                       JOIN wishlist w ON p.product_id = w.product_id 
                       WHERE w.user_id = ?");
$stmt->execute([$user_id]);
$wishlist = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Wishlist</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/wishlist.css">
</head>
<body>

<div class="top-bar">
    <div class="logo">📚 COM3 2O BUY</div>
    <div class="nav">
        <a href="member_dashboard.php">Shop</a>
        <a href="wishlist.php" style="color: #e74c3c;">Wishlist</a>
        <a href="profile.php">Profile</a>
        <a href="order_history.php">Orders</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <div class="title">
        <i class="fas fa-heart"></i> My Wishlist
    </div>
    
    <?php if (empty($wishlist)): ?>
        <div class="empty">
            <i class="far fa-heart"></i>
            <h3>Your wishlist is empty</h3>
            <p>Click the heart ❤️ button on any book to save it here!</p>
            <a href="member_dashboard.php" class="browse-btn">Browse Books →</a>
        </div>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($wishlist as $book): ?>
                <div class="card">
                    <img src="images/<?php echo $book['image_url']; ?>" 
                         onerror="this.src='https://via.placeholder.com/200x220?text=Book'">
                    <div class="info">
                        <span class="cat"><?php echo $book['category']; ?></span>
                        <h4><?php echo $book['title']; ?></h4>
                        <div class="author"><?php echo $book['author']; ?></div>
                        <div class="price">RM <?php echo number_format($book['price'], 2); ?></div>
                        
                        <div class="btn-group">
                            <a href="add_to_cart.php?id=<?php echo $book['product_id']; ?>" class="cart-btn">
                                🛒 Add to Cart
                            </a>
                            <a href="remove_wishlist.php?id=<?php echo $book['product_id']; ?>" class="remove-btn" onclick="return confirm('Remove from wishlist?')">
                                ❌ Remove
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="footer">
    <p>© 2026 COM3 2O BUY</p>
</div>

</body>
</html>
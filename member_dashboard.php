<?php
session_start();
require 'config.php'; 

// Check if user is logged in as Member
$isLoggedIn = isset($_SESSION['user_id']) && $_SESSION['role'] === 'Member';
$isGuest = isset($_SESSION['is_guest']) && $_SESSION['is_guest'] === true;

// If not guest and not logged in, redirect to login
if (!$isGuest && !$isLoggedIn) {
    header("Location: login.php");
    exit();
}

// If logged in as Admin, redirect to admin panel
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'Admin') {
    header("Location: admin_dashboard.php");
    exit();
}

// Get all products (show to both guests and members)
$products = $pdo->query("SELECT * FROM products ORDER BY category, title")->fetchAll();

// Cart total
$cartTotal = 0;
$cartCount = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $prices = $pdo->query("SELECT product_id, price FROM products WHERE product_id IN ($ids)")->fetchAll(PDO::FETCH_KEY_PAIR);
    foreach ($_SESSION['cart'] as $id => $qty) {
        if (isset($prices[$id])) {
            $cartTotal += $prices[$id] * $qty;
            $cartCount += $qty;
        }
    }
}

$categories = $pdo->query("SELECT category_name FROM categories ORDER BY category_name")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html>
<head>
    <title>COM3 2O BUY</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/member_dashboard.css">
</head>
<body>

<div class="top-bar">
    <div class="logo">📚 COM3 2O BUY</div>
    
    <div class="search-sort">
        <input type="text" id="search" class="search" placeholder="🔍 Search books...">
        <select id="sort" class="sort">
            <option value="default">Sort by</option>
            <option value="low">Price: Low to High</option>
            <option value="high">Price: High to Low</option>
        </select>
    </div>
    
    <div class="nav">
        <a href="cart.php" class="cart-link">
            Cart 
            <?php if ($cartCount > 0): ?>
                <span class="cart-badge"><?php echo $cartCount; ?></span>
            <?php endif; ?>
        </a>
        
        <?php if ($isGuest): ?>
            <span class="guest-badge">👤 Guest Mode</span>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
            <a href="logout.php" class="exit-guest-btn">🚪 Exit Guest</a>
        <?php else: ?>
            <a href="wishlist.php">Wishlist</a>
            <a href="profile.php">Profile</a>
            <a href="order_history.php">Orders</a>
            <a href="logout.php">Logout</a>
        <?php endif; ?>
    </div>
</div>

<div class="main">
    
    <div class="sidebar">
        <h3>📖 Categories</h3>
        <button class="active" onclick="filterCat('all', this)">All Books</button>
        <?php foreach ($categories as $cat): ?>
            <button onclick="filterCat('<?php echo $cat; ?>', this)"><?php echo $cat; ?></button>
        <?php endforeach; ?>
        
        <div class="cart-box">
            <h3>🛒 Your Cart</h3>
            <p>Items: <?php echo $cartCount; ?></p>
            <div class="total">RM <?php echo number_format($cartTotal, 2); ?></div>
            <a href="checkout.php" class="btn">Checkout →</a>
            <a href="clear_cart.php" class="empty-link">Empty Cart</a>
        </div>
    </div>
    
    <div class="content">
        <div class="grid" id="grid">
            <?php foreach ($products as $p): ?>
                <div class="card" 
                     data-cat="<?php echo $p['category']; ?>"
                     data-title="<?php echo strtolower($p['title']); ?>"
                     data-price="<?php echo $p['price']; ?>">
                    
                    <div class="book-cover">
                        <?php 
                        $imagePath = 'images/' . $p['image_url'];
                        if (file_exists($imagePath) && !empty($p['image_url'])) {
                            echo '<img src="' . $imagePath . '" alt="' . $p['title'] . '">';
                        } else {
                            $colors = ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b', '#fa709a'];
                            $randomColor = $colors[array_rand($colors)];
                            echo '<div class="no-image" style="background: ' . $randomColor . ';">
                                    <i class="fas fa-book-open"></i>
                                    <span>' . htmlspecialchars($p['title']) . '</span>
                                  </div>';
                        }
                        ?>
                    </div>
                    
                    <div class="info">
                        <span class="cat"><?php echo $p['category']; ?></span>
                        <div class="title"><?php echo $p['title']; ?></div>
                        <div class="author"><?php echo $p['author']; ?></div>
                        <div class="price">RM <?php echo number_format($p['price'], 2); ?></div>
                        
                        <div class="btn-group">
                            <a href="add_to_cart.php?id=<?php echo $p['product_id']; ?>" class="add-btn">
                                🛒 Add
                            </a>
                            <?php if ($isGuest): ?>
                                <a href="login.php" class="wishlist-btn" onclick="alert('Please login to add to wishlist!')">
                                    ❤️
                                </a>
                            <?php else: ?>
                                <a href="add_to_wishlist.php?id=<?php echo $p['product_id']; ?>" class="wishlist-btn">
                                    ❤️
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <p>© 2026 COM3 2O BUY</p>
</div>

<!-- Javascript -->
<script>
    let allCards = Array.from(document.querySelectorAll('.card'));
    let currentCat = 'all';
    
    function update() {
        let search = document.getElementById('search').value.toLowerCase();
        let sortBy = document.getElementById('sort').value;
        
        let visible = allCards.filter(card => {
            let title = card.getAttribute('data-title');
            let cat = card.getAttribute('data-cat');
            return title.includes(search) && (currentCat === 'all' || cat === currentCat);
        });
        
        if (sortBy === 'low') {
            visible.sort((a, b) => a.getAttribute('data-price') - b.getAttribute('data-price'));
        } else if (sortBy === 'high') {
            visible.sort((a, b) => b.getAttribute('data-price') - a.getAttribute('data-price'));
        }
        
        allCards.forEach(c => c.style.display = 'none');
        let grid = document.getElementById('grid');
        visible.forEach(c => {
            c.style.display = 'block';
            grid.appendChild(c);
        });
    }
    
    function filterCat(cat, btn) {
        currentCat = cat;
        document.querySelectorAll('.sidebar button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        update();
    }
    
    document.getElementById('search').addEventListener('keyup', update);
    document.getElementById('sort').addEventListener('change', update);
</script>

</body>
</html>
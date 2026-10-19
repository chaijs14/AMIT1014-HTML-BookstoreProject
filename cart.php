<?php
session_start();
require 'config.php';

// Get cart items
$cart_items = [];
$subtotal = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();
    
    foreach ($products as $product) {
        $qty = $_SESSION['cart'][$product['product_id']];
        $total = $product['price'] * $qty;
        $subtotal += $total;
        
        $cart_items[] = [
            'product' => $product,
            'quantity' => $qty,
            'total' => $total
        ];
    }
}

// No discount - just use subtotal as total
$total = $subtotal;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart - COM3 2O BUY</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/cart.css">
</head>
<body>

<div class="top-bar">
    <div class="logo">📚 COM3 2O BUY</div>
    <div class="nav">
        <a href="member_dashboard.php">Continue Shopping</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h1 class="cart-title">🛒 Shopping Cart</h1>
    
    <?php if (empty($cart_items)): ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart" style="font-size: 60px; color: #ddd;"></i>
            <h3>Your cart is empty</h3>
            <p>Add some books to your cart and come back!</p>
            <a href="member_dashboard.php" class="continue-btn">← Continue Shopping</a>
        </div>
    <?php else: ?>
        
        <div class="cart-table">
            <div class="cart-header">
                <div class="col-product">Product</div>
                <div class="col-price">Price</div>
                <div class="col-quantity">Quantity</div>
                <div class="col-total">Total</div>
            </div>
            
            <!-- CART ITEMS -->
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <div class="col-product">
                        <div class="product-info">
                            <h4><?php echo $item['product']['title']; ?></h4>
                            <p><?php echo $item['product']['author']; ?></p>
                            <p>Category: <?php echo $item['product']['category']; ?></p>
                        </div>
                    </div>
                    <div class="col-price">
                        RM <?php echo number_format($item['product']['price'], 2); ?>
                    </div>
                    <div class="col-quantity">
                        <input type="number" class="quantity-input" 
                               value="<?php echo $item['quantity']; ?>"
                               min="1" max="99"
                               data-id="<?php echo $item['product']['product_id']; ?>">
                        <a href="remove_cart_item.php?id=<?php echo $item['product']['product_id']; ?>" 
                           class="remove-btn" onclick="return confirm('Remove item?')">
                            <i class="fas fa-trash"></i> Remove
                        </a>
                    </div>
                    <div class="col-total">
                        RM <?php echo number_format($item['total'], 2); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    
        <div class="cart-summary">
            <div class="summary-row">
                <span>Total items:</span>
                <span><?php echo count($cart_items); ?> item(s)</span>
            </div>
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>RM <?php echo number_format($subtotal, 2); ?></span>
            </div>
            <div class="summary-row total">
                <span>Total:</span>
                <span>RM <?php echo number_format($total, 2); ?></span>
            </div>
            
            <a href="checkout.php" class="pay-btn">
                <i class="fas fa-credit-card"></i> Proceed to Checkout
            </a>
            <a href="member_dashboard.php" class="continue-btn">← Continue Shopping</a>
        </div>
        
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    // Update quantity via AJAX
    $('.quantity-input').on('change', function() {
        var product_id = $(this).data('id');
        var quantity = $(this).val();
        
        $.ajax({
            url: 'update_cart.php',
            method: 'POST',
            data: { product_id: product_id, quantity: quantity },
            success: function() {
                location.reload();
            }
        });
    });
</script>

</body>
</html>
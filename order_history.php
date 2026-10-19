<?php
session_start();
require 'config.php'; 

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Where to go back
$backPage = ($role === 'Admin') ? 'admin_dashboard.php' : 'member_dashboard.php';

// Handle Order Cancellation
if (isset($_GET['cancel_id'])) {
    $cancel_id = (int)$_GET['cancel_id'];
    
    // Check if order belongs to this user and is Pending
    $check = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ? AND order_status = 'Pending'");
    $check->execute([$cancel_id, $user_id]);
    $order_to_cancel = $check->fetch();
    
    if ($order_to_cancel) {
        // Update order status to Cancelled
        $update = $pdo->prepare("UPDATE orders SET order_status = 'Rejected' WHERE order_id = ?");
        $update->execute([$cancel_id]);
        $cancel_success = "Order #$cancel_id has been cancelled successfully.";
    } else {
        $cancel_error = "Cannot cancel this order. It may not exist or has already been processed.";
    }
}

// Get all order
$query = "SELECT o.order_id, o.created_at, o.order_status, o.total_price, 
                 p.title, p.image_url, oi.quantity, oi.price_at_purchase
          FROM orders o
          JOIN order_items oi ON o.order_id = oi.order_id
          JOIN products p ON oi.product_id = p.product_id
          WHERE o.user_id = ?
          ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$results = $stmt->fetchAll();

// Group items for Order
$orders = [];
foreach ($results as $row) {
    $orders[$row['order_id']]['info'] = [
        'date' => $row['created_at'],
        'status' => $row['order_status'],
        'total' => $row['total_price']
    ];
    $orders[$row['order_id']]['items'][] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/orders.css">
</head>
<body>

<div class="top-bar">
    <div class="logo">COM3 2O BUY</div>
    <div class="nav">
        <a href="<?php echo $backPage; ?>">Dashboard</a>
        <a href="profile.php">Profile</a>
        <a href="wishlist.php">Wishlist</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- Main Content -->
<div class="container">
    <a href="<?php echo $backPage; ?>" class="back-btn">← Back to Shop</a>
    <div class="title">📦 My Orders</div>
    
    <?php if (isset($cancel_success)): ?>
        <div class="alert alert-success">✅ <?php echo $cancel_success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($cancel_error)): ?>
        <div class="alert alert-error">❌ <?php echo $cancel_error; ?></div>
    <?php endif; ?>
    
    <?php if (empty($orders)): ?>
        <div class="empty">
            <i class="fas fa-shopping-bag"></i>
            <h3>No orders yet</h3>
            <p>Start shopping to see your orders here!</p>
            <a href="<?php echo $backPage; ?>">Start Shopping →</a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $id => $order): ?>
            <div class="order">
                <!-- Order Header -->
                <div class="order-header">
                    <div>
                        <span class="order-id">Order #<?php echo $id; ?></span>
                        <div class="order-date"><?php echo date('d M Y, h:i A', strtotime($order['info']['date'])); ?></div>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                        <span class="status status-<?php echo $order['info']['status']; ?>">
                            <?php echo $order['info']['status']; ?>
                        </span>
                        <?php if ($order['info']['status'] == 'Pending'): ?>
                            <a href="?cancel_id=<?php echo $id; ?>" class="cancel-btn">Cancel Order</a>
                        <?php endif; ?>
                        <a href="generate_receipt.php?id=<?php echo $id; ?>" class="receipt-btn" target="_blank">
                            🧾 View Receipt
                        </a>
                    </div>
                </div>
                
                <!-- Order items -->
                <div class="items">
                    <?php foreach ($order['items'] as $item): ?>
                        <div class="item">
                            <img src="images/<?php echo $item['image_url']; ?>" 
                                 class="item-img"
                                 onerror="this.src='https://via.placeholder.com/60x75?text=Book'">
                            <div class="item-info">
                                <div class="item-title"><?php echo $item['title']; ?></div>
                                <div class="item-price">RM <?php echo number_format($item['price_at_purchase'], 2); ?> each</div>
                            </div>
                            <div class="item-qty">x <?php echo $item['quantity']; ?></div>
                            <div class="item-total">RM <?php echo number_format($item['price_at_purchase'] * $item['quantity'], 2); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Order footer -->
                <div class="order-footer">
                    <div class="grand-total">
                        Total: <span>RM <?php echo number_format($order['info']['total'], 2); ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
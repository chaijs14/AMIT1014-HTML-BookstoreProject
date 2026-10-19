<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

$order_id = $_GET['id'] ?? 0;

// Get order info
$stmt = $pdo->prepare("
    SELECT o.*, u.user_name, u.email 
    FROM orders o 
    JOIN user u ON o.user_id = u.user_id 
    WHERE o.order_id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: admin_orders.php");
    exit();
}

// Get order items
$stmt = $pdo->prepare("
    SELECT oi.*, p.title, p.author, p.image_url 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.product_id 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?php echo $order_id; ?> - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/admin_orders.css">
</head>
<body>
<div class="container">
    <a href="admin_orders.php" class="back-btn">← Back to Orders</a>
    
    <div class="card">
        <div class="order-header">
            <div>
                <h2>Order #<?php echo $order_id; ?></h2>
                <p style="color: #888; margin-top: 5px;">Placed on: <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['user_name']); ?> (<?php echo $order['email']; ?>)</p>
            </div>
            <div>
                <span class="status status-<?php echo $order['order_status']; ?>">
                    <?php echo $order['order_status']; ?>
                </span>
            </div>
        </div>

        <h3>Order Items</h3>
        <?php foreach ($items as $item): ?>
        <div class="item">
            <img src="images/<?php echo $item['image_url']; ?>" class="item-img" onerror="this.src='https://via.placeholder.com/60x80?text=Book'">
            <div class="item-info">
                <div class="item-title"><?php echo htmlspecialchars($item['title']); ?></div>
                <div class="item-price">by <?php echo htmlspecialchars($item['author']); ?></div>
            </div>
            <div class="item-qty">x <?php echo $item['quantity']; ?></div>
            <div class="item-total">RM <?php echo number_format($item['price_at_purchase'] * $item['quantity'], 2); ?></div>
        </div>
        <?php endforeach; ?>

        <div class="total-box">
            <h3>Total: RM <?php echo number_format($order['total_price'], 2); ?></h3>
        </div>
    </div>
</div>
</body>
</html>

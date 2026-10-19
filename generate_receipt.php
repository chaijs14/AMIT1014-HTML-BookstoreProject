<?php
session_start();
require 'config.php';

$order_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Get Order Info
$stmt = $pdo->prepare("SELECT o.*, u.user_name, u.email FROM orders o JOIN user u ON o.user_id = u.user_id WHERE o.order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

// Get Items Info
$itemStmt = $pdo->prepare("SELECT oi.*, p.title, p.author FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = ?");
$itemStmt->execute([$order_id]);
$items = $itemStmt->fetchAll();

if (!$order) die("Order not found.");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receipt #<?php echo $order_id; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/receipt.css">
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-card">
            <!-- Header -->
            <div class="receipt-header">
                <h1>COM3 2O BUY</h1>
                <p>3S: Secure. Simple. Shopping</p>
                <div class="receipt-badge">
                    <i class="fas fa-receipt"></i> RECEIPT
                </div>
            </div>

            <!-- Content -->
            <div class="receipt-content">
                <!-- Order Info -->
                <div class="order-info">
                    <div class="info-row">
                        <span class="info-label">Order ID:</span>
                        <span class="info-value">#<?php echo $order_id; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Customer:</span>
                        <span class="info-value"><?php echo htmlspecialchars($order['user_name']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo htmlspecialchars($order['email']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date:</span>
                        <span class="info-value"><?php echo date('d F Y, h:i A', strtotime($order['created_at'])); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="order-status status-<?php echo $order['order_status']; ?>">
                            <?php echo $order['order_status']; ?>
                        </span>
                    </div>
                </div>

                <!-- Items -->
                <div class="items-title">
                    <i class="fas fa-box-open"></i> Order Items
                </div>

                <?php foreach ($items as $item): ?>
                <div class="item-row">
                    <div class="item-details">
                        <div class="item-title"><?php echo htmlspecialchars($item['title']); ?></div>
                        <div class="item-author">by <?php echo htmlspecialchars($item['author'] ?? 'Unknown'); ?></div>
                    </div>
                    <div class="item-qty">x <?php echo $item['quantity']; ?></div>
                    <div class="item-price">RM <?php echo number_format($item['price_at_purchase'] * $item['quantity'], 2); ?></div>
                </div>
                <?php endforeach; ?>

                <!-- Total -->
                <div class="total-section">
                    <div class="total-row">
                        <span class="total-label">Subtotal:</span>
                        <span>RM <?php echo number_format($order['total_price'], 2); ?></span>
                    </div>
                    <div class="total-row">
                        <span class="total-label">Shipping:</span>
                        <span>RM 0.00</span>
                    </div>
                    <div class="total-row">
                        <span class="total-label grand-total">Total Paid:</span>
                        <span class="grand-total">RM <?php echo number_format($order['total_price'], 2); ?></span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
           <div class="receipt-footer">
    <div class="thankyou">
        Thank you for shopping with us!
    </div>
    <div>
        <a href="javascript:window.print()" class="btn-print">
            <i class="fas fa-print"></i> Print Receipt
        </a>
        <a href="member_dashboard.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Shop
        </a>
    </div>
</div>
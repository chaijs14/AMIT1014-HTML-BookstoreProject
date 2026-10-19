<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Check if order exists and belongs to this user AND is still Pending
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ? AND order_status = 'Pending'");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if ($order) {
    // Update order status to 'Rejected' (cancelled)
    $stmt = $pdo->prepare("UPDATE orders SET order_status = 'Rejected' WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $message = "Order #$order_id has been cancelled successfully.";
} else {
    $message = "Cannot cancel this order. It may not exist, not yours, or already processed.";
}

echo "<script>alert('$message'); window.location.href='order_history.php';</script>";
exit();
?>

<?php foreach ($orders as $id => $order): ?>
<div class="order">
    <div class="order-header">
        <div>
            <span class="order-id">Order #<?php echo $id; ?></span>
            <div class="order-date"><?php echo date('d M Y, h:i A', strtotime($order['info']['date'])); ?></div>
        </div>
        <div>
            <span class="status status-<?php echo $order['info']['status']; ?>">
                <?php echo $order['info']['status']; ?>
            </span>
            <?php if ($order['info']['status'] == 'Pending'): ?>
                <a href="cancel_order.php?id=<?php echo $id; ?>" 
                   onclick="return confirm('Are you sure you want to cancel this order?')"
                   style="display: inline-block; margin-left: 10px; padding: 4px 12px; background: #dc3545; color: white; text-decoration: none; border-radius: 20px; font-size: 11px; font-weight: bold;">
                    Cancel
                </a>
            <?php endif; ?>
        </div>
    </div>
    <!-- rest of your order display code -->
</div>
<?php endforeach; ?>




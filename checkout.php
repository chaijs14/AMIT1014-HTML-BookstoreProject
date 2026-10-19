<?php
session_start();
require 'config.php';

// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    echo "<script>alert('Your cart is empty!'); window.location.href='member_dashboard.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$total_price = 0;


$cart_items = [];
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $stmt = $pdo->prepare("SELECT price FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if ($product) {
        $price = $product['price'];
        $total_price += $price * $quantity;
        $cart_items[] = [
            'id' => $product_id,
            'qty' => $quantity,
            'price' => $price
        ];
    }
}

try {
    $pdo->beginTransaction();

    // Create Order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, order_status, created_at) VALUES (?, ?, 'Pending', NOW())");
    $stmt->execute([$user_id, $total_price]);
    $order_id = $pdo->lastInsertId();

    // Save file using array
    $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
    foreach ($cart_items as $item) {
        $stmtItem->execute([$order_id, $item['id'], $item['qty'], $item['price']]);
    }

    $pdo->commit();

    // Direct to PayPal
    echo "<script>
            alert('Order created! Proceeding to PayPal.');
            window.location.href='payment.php?order_id=$order_id&amount=$total_price';
          </script>";
    exit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Checkout Error: " . $e->getMessage());
}
?>
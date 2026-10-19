<?php
session_start();
require 'config.php';

// Get data from the URL
$order_id = $_GET['order_id'] ?? null;
$status = $_GET['status'] ?? null;

if ($order_id && $status === 'Completed') {
    try {
        $sql = "UPDATE orders SET order_status = 'Approved', payment_date = NOW() WHERE order_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$order_id]);
        
        // IMPORTANT: Clear the cart so the user doesn't pay for the same items twice
        unset($_SESSION['cart']);
        
        // Redirect to the receipt
        echo "<script>
                alert('Payment Received! Redirecting to your receipt.');
                window.location.href='generate_receipt.php?id=$order_id';
              </script>";
        exit();

    } catch (PDOException $e) {
        // If there is still an error, this will tell us exactly what it is
        die("Database Error: " . $e->getMessage());
    }
} else {
    header("Location: member_dashboard.php");
    exit();
}
?>
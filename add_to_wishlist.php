<?php
session_start();
require 'config.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_GET['id'];

// Add to wishlist
$stmt = $pdo->prepare("INSERT IGNORE INTO wishlist (user_id, product_id) VALUES (?, ?)");
$stmt->execute([$user_id, $product_id]);

// Go back
header("Location: member_dashboard.php");
exit();
?>
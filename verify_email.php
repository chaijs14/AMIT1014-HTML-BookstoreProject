<?php
session_start();
require 'config.php';

$token = $_GET['token'] ?? '';

if ($token == '') {
    die("No verification token provided.");
}

$stmt = $pdo->prepare("SELECT * FROM email_verification WHERE token = ? AND expires_at > NOW()");
$stmt->execute([$token]);
$verification = $stmt->fetch();

if (!$verification) {
    die("Invalid or expired verification link.");
}

$stmt = $pdo->prepare("UPDATE user SET is_verified = 1 WHERE user_id = ?");
$stmt->execute([$verification['user_id']]);

$stmt = $pdo->prepare("DELETE FROM email_verification WHERE token = ?");
$stmt->execute([$token]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Email Verified</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/verify.css">
</head>
<body>
    <div class="card">
        <i class="fas fa-check-circle" style="font-size: 64px; color: #27ae60; margin-bottom: 20px;"></i>
        <h2 style="color: #27ae60;">Email Verified!</h2>
        <p>Your email has been verified successfully.</p>
        <p>You can now login to your account.</p>
        <a href="login.php" class="btn">Login Now</a>
    </div>
</body>
</html>
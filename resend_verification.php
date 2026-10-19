<?php
session_start();
require 'config.php';

$email = $_GET['email'] ?? '';

if ($email == '') {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && $user['is_verified'] == 0) {
    // Delete old token
    $pdo->prepare("DELETE FROM email_verification WHERE user_id = ?")->execute([$user['user_id']]);
    
    // Create new token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    $stmt = $pdo->prepare("INSERT INTO email_verification (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$user['user_id'], $token, $expires]);
    
    $verify_link = "http://localhost/newpopular/verify_email.php?token=" . $token;
    
    echo "
    <!DOCTYPE html>
    <html>
    <head><title>Resend Verification</title></head>
    <body style='font-family:Arial; text-align:center; padding:50px; background:linear-gradient(135deg,#F7CAC9,#92A8D1);'>
        <div style='background:white; max-width:400px; margin:auto; padding:30px; border-radius:20px;'>
            <h2 style='color:#e74c3c;'>📧 Verification Link</h2>
            <p>Click the button below to verify your email:</p>
            <a href='$verify_link' style='display:inline-block; background:#e74c3c; color:white; padding:10px 20px; border-radius:8px; text-decoration:none; margin:15px 0;'>Verify Email</a>
            <p style='font-size:12px; color:#888;'>Or copy this link: $verify_link</p>
            <a href='login.php' style='color:#e74c3c;'>← Back to Login</a>
        </div>
    </body>
    </html>
    ";
    exit();
} else {
    echo "<script>alert('Account already verified or not found.'); window.location='login.php';</script>";
}
?>
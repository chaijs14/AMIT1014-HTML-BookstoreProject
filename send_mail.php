<?php
function sendVerificationEmail($email, $name, $token) {
    // Use your actual folder name - change 'newpopular' to your folder name
    $verification_link = "http://localhost/newpopular/verify_email.php?token=" . $token;
    
    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Verify Your Email</title>
    </head>
    <body>
        <div class='card'>
            <h2>📧 Verify Your Email</h2>
            <p>Hello <strong>$name</strong>,</p>
            <p>Please click the button below to verify your email address:</p>
            <a href='$verification_link' class='btn'>Verify Email</a>
            <div class='link'>
                <p>Or copy and paste this link:</p>
                <a href='$verification_link'>$verification_link</a>
            </div>
            <p style='font-size: 12px; color: #888;'>This link expires in 24 hours.</p>
        </div>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
        <link rel='stylesheet' href='css/common.css'>
        <link rel='stylesheet' href='css/verify.css'>
    </body>
    </html>
    ";
    exit(); // Stop further execution
}

function sendResetPasswordEmail($email, $name, $token) {
    $reset_link = "http://localhost/newpopular/reset_password.php?token=" . $token;
    
    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Reset Password</title>
    </head>
    <body>
        <div class='card'>
            <h2>🔐 Reset Your Password</h2>
            <p>Hello <strong>$name</strong>,</p>
            <p>Click the button below to reset your password:</p>
            <a href='$reset_link' class='btn'>Reset Password</a>
            <div class='link'>
                <p>Or copy and paste this link:</p>
                <a href='$reset_link'>$reset_link</a>
            </div>
            <p style='font-size: 12px; color: #888;'>This link expires in 5 minutes.</p>
        </div>
    </body>
    </html>
    ";
    exit();
}
?>
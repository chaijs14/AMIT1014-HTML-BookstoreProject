<?php
session_start();
require 'config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = trim($_POST['user_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($user_name) || empty($email) || empty($password)) {
        $error = "Please fill all fields";
    } else {
        $check = $pdo->prepare("SELECT email FROM user WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = "Email already registered!";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $role = (strpos($email, '@admin.com') !== false) ? 'Admin' : 'Member';
            
            $stmt = $pdo->prepare("INSERT INTO user (email, password_hash, user_name, role, is_verified) VALUES (?, ?, ?, ?, 0)");
            $stmt->execute([$email, $password_hash, $user_name, $role]);
            $user_id = $pdo->lastInsertId();
            
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
            
            $stmt = $pdo->prepare("INSERT INTO email_verification (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $token, $expires]);
            
            $verify_link = "http://localhost/newpopular/verify_email.php?token=" . $token;
            
            echo "
            <!DOCTYPE html>
            <html>
            <head>
                <title>Verify Your Email</title>
                <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
                <link rel='stylesheet' href='css/common.css'>
                <link rel='stylesheet' href='css/verify.css'>
            </head>
            <body>
                <div class='card'>
                    <h2>📧 Verify Your Email</h2>
                    <p>Click the button below to verify your email address:</p>
                    <a href='$verify_link' class='btn'>Verify Email</a>
                    <div class='link-box'>
                        <p style='font-size: 13px; margin-bottom: 10px;'>Or copy this link:</p>
                        <code style='font-size: 11px; word-break: break-all;'>$verify_link</code>
                    </div>
                    <p style='font-size: 12px; color: #888;'>⏰ This link expires in 24 hours</p>
                    <a href='login.php' class='back-link'>← Back to Login</a>
                </div>
            </body>
            </html>
            ";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - COM3 2O BUY</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="card">
        <h2>CREATE ACCOUNT</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="user_name" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required minlength="6">
            </div>
            <button type="submit">Register</button>
        </form>
        
        <hr>
        
        <div class="links">
            <a href="login.php">Already have an account? Login</a>
        </div>
    </div>
</body>
</html>
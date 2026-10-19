<?php
require 'config.php';

$message = "";
$reset_link = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(32)); 

        $stmt = $pdo->prepare("INSERT INTO password_reset (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
        $stmt->execute([$user['user_id'], $token]);

        $reset_link = "reset_password.php?token=" . $token;
        $message = "Link generated successfully!";
    } else {
        $message = "Error: Email address not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - COM3 2O BUY</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/request_reset.css">
</head>
<body>
    <div class="clouds"></div>
    <div class="card">
        <h2>Reset Access</h2>
        <p>Enter your account email to receive a password reset link.</p>
        <form method="POST">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="example@mail.com" required>
            <button type="submit" class="btn-submit">Generate Link</button>
        </form>
        <?php if ($reset_link): ?>
            <div class="test-box">
                <span style="font-size: 0.8rem; color: #2d3748;">Link created successfully!</span><br>
                <a href="<?php echo $reset_link; ?>" class="btn-test">Go to Reset Page →</a>
            </div>
        <?php elseif ($message): ?>
            <p style="color: #e53e3e; margin-top: 15px;"><?php echo $message; ?></p>
        <?php endif; ?>
        <div class="footer-links"><a href="login.php">Back to Login</a></div>
    </div>
</body>
</html>
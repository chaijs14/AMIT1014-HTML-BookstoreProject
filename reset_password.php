<?php
session_start();
require 'config.php';

$message = '';
$error = '';

// Get all users for dropdown
$users = $pdo->query("SELECT user_id, email, user_name FROM user")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
   
    if ($new_password != $confirm) {
        $error = "Passwords do not match!";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters!";
    } else {
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE user SET password_hash = ? WHERE email = ?");
        $stmt->execute([$hash, $email]);
        $message = "Password changed successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="card">
        <h2>Reset Password</h2>
       
        <?php if ($message): ?>
            <div class="message success">✅ <?php echo $message; ?></div>
            <div class="links"><a href="login.php">← Back to Login</a></div>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="message error">❌ <?php echo $error; ?></div>
            <?php endif; ?>
           
            <form method="POST">
                <div class="form-group">
                    <label>Select Your Email</label>
                    <select name="email" required>
                        <option value="">-- Select email --</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?php echo $u['email']; ?>"><?php echo $u['email']; ?> (<?php echo $u['user_name']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" required minlength="6">
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit">Change Password</button>
            </form>
            <div class="links">
                <a href="login.php">← Back to Login</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php 
require_once 'config.php';
require_once 'send_mail.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = isset($_POST['user_name']) ? trim($_POST['user_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($user_name) || empty($email) || empty($password)) {
        die("<script>alert('Please fill all fields'); window.history.back();</script>");
    }

    $checkEmail = $pdo->prepare("SELECT email FROM user WHERE email = ?");
    $checkEmail->execute([$email]);
    if ($checkEmail->fetch()) {
        die("<script>alert('Email already registered!'); window.history.back();</script>");
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $role = 'Member';
    if (str_ends_with($email, '@admin.com')) {
        $role = 'Admin';
    }

    try {
        // Make sure is_verified column exists! If not, remove it from INSERT
        $stmt = $pdo->prepare("INSERT INTO user (email, password_hash, user_name, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$email, $password_hash, $user_name, $role]);
        $user_id = $pdo->lastInsertId();
        
        // Generate verification token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $stmt = $pdo->prepare("INSERT INTO email_verification (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $token, $expires]);
        
        // Send verification email
        sendVerificationEmail($email, $user_name, $token);
        
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
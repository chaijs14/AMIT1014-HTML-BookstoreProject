<?php
session_start();
require_once 'config.php';

// IMPORTANT: Clear guest session when logging in
if (isset($_SESSION['is_guest'])) {
    unset($_SESSION['is_guest']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        echo "<script>alert('Please fill in all fields'); window.location.href='login.php';</script>";
        exit();
    }

    // Fetch user
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
       
        // Check If Account Is Locked
        if ($user['Lock_until'] && strtotime($user['Lock_until']) > time()) {
            $remaining = strtotime($user['Lock_until']) - time();
            echo "<script>alert('ACCOUNT LOCKED! Try again in " . ceil($remaining) . " seconds.'); window.location.href='login.php';</script>";
            exit();
        }
       
        // Check if email is verified (skip for Admin)
        if (isset($user['is_verified']) && $user['is_verified'] == 0 && $user['role'] !== 'Admin') {
            echo "<script>
                alert('Please verify your email first! Check your inbox.');
                window.location.href='resend_verification.php?email=" . urlencode($email) . "';
            </script>";
            exit();
        }
       
        // Check password
        if (password_verify($password, $user['password_hash'])) {
           
            // Reset failed attempts on successful login
            $update = $pdo->prepare("UPDATE user SET Fail_login = 0, Lock_until = NULL WHERE user_id = ?");
            $update->execute([$user['user_id']]);

            // Remember me
            if (isset($_POST['remember_me'])) {
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
               
                $stmt = $pdo->prepare("DELETE FROM remember_me WHERE user_id = ?");
                $stmt->execute([$user['user_id']]);
                
                $stmt = $pdo->prepare("INSERT INTO remember_me (user_id, token_hash, expires_at) VALUES (?, ?, ?)");
                $stmt->execute([$user['user_id'], hash('sha256', $token), $expires]);
               
                setcookie('remember_me', $token, time() + 86400 * 30, "/");
            }

            // Set sessions
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_name'] = $user['user_name'];
            
            // Make sure guest is cleared
            unset($_SESSION['is_guest']);

            // Redirect
            if ($user['role'] === 'Admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: member_dashboard.php");
            }
            exit();

        } else {
            // Wrong password - Increment failed attempts
            $failed_attempts = $user['Fail_login'] + 1;
            $lock_until = null;
            
            if ($failed_attempts >= 3) {
                // Lock account for 30 seconds
                $lock_until = date("Y-m-d H:i:s", strtotime("+30 seconds"));
                echo "<script>alert('3 FAILED ATTEMPTS! Account locked for 30 seconds.');</script>";
            } else {
                echo "<script>alert('Incorrect password! Attempt $failed_attempts of 3.');</script>";
            }
            
            // Update database
            $update = $pdo->prepare("UPDATE user SET Fail_login = ?, Lock_until = ? WHERE user_id = ?");
            $update->execute([$failed_attempts, $lock_until, $user['user_id']]);
            
            echo "<script>window.location.href='login.php';</script>";
            exit();
        }

    } else {
        echo "<script>alert('No account found with that email.'); window.location.href='login.php';</script>";
    }
}
?>
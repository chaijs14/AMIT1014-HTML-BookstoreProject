<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

// If not logged in, but the remember_me cookie exists
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    list($selector, $token) = explode(':', $_COOKIE['remember_me']);


    // Find the token in the database
    $stmt = $pdo->prepare("SELECT * FROM remember_me WHERE selector = ? AND expires_at > NOW()");
    $stmt->execute([$selector]);
    $record = $stmt->fetch();


    if ($record && hash_equals($record['token_hash'], hash('sha256', hex2bin($token)))) {
        $userStmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
        $userStmt->execute([$record['user_id']]);
        $user = $userStmt->fetch();


        if ($user) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_name'] = $user['user_name'];
        }
    }
}
?>

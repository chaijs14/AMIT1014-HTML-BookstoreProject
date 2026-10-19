<?php
session_start();
require_once 'config.php';

// Clear guest session
if (isset($_SESSION['is_guest'])) {
    unset($_SESSION['is_guest']);
}

// Delete the cookie from the browser
if (isset($_COOKIE['remember_me'])) {
    list($selector, $token) = explode(':', $_COOKIE['remember_me']);
    
    // Delete the token from the database
    $stmt = $pdo->prepare("DELETE FROM remember_me WHERE selector = ?");
    $stmt->execute([$selector]);
    
    // Clear the cookie
    setcookie('remember_me', '', time() - 3600, '/');
}

// Clear all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to login
header("Location: login.php");
exit();
?>
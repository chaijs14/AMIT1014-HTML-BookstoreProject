<?php
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'Admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: member_dashboard.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - COM3 2O BUY</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/login.css">

</head>
<body>
    <div class="login-card">
        <h2>LOGIN</h2>
        
        <form action="login_auth.php" method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <div class="remember">
                <input type="checkbox" name="remember_me" id="remember">
                <label for="remember">Remember Me</label>
            </div>
            
            <button type="submit">Login Now</button>
        </form>
        
        <a href="guest_dashboard.php" class="btn-guest">Continue as Guest</a>
        
        <hr>
        
        
        <div class="links">
            <a href="register.php">Create Account</a> | 
            <a href="reset_password.php">Forgot Password?</a>
        </div>
    </div>


</body>
</html>
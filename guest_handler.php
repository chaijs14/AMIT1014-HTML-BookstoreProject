<?php
session_start();

$_SESSION['user_id'] = 0; 
$_SESSION['user_name'] = 'Guest';
$_SESSION['role'] = 'Guest';
$_SESSION['is_verified'] = 1; 

header("Location: member_dashboard.php");
exit();
?>

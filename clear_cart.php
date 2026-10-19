<?php
session_start();
unset($_SESSION['cart']);
header("Location: member_dashboard.php");
exit();
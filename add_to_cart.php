<?php
session_start();
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
}
header("Location: member_dashboard.php");
exit();
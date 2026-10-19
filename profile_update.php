<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_name = $_POST['user_name'];
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];

    // Photo Upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $upload_dir = 'uploads/';
        
        // Create the folder if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = $_FILES['profile_photo']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        
        // Create a unique name to avoid browser caching issues
        $new_file_name = "user_" . $user_id . "_" . time() . "." . $file_ext;
        $target_path = $upload_dir . $new_file_name;

        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_path)) {
            // Update the database with the NEW filename
            $stmt = $pdo->prepare("UPDATE user SET profile_photo = ? WHERE user_id = ?");
            $stmt->execute([$new_file_name, $user_id]);
        }
    }

    // Update other info
    $stmt = $pdo->prepare("UPDATE user SET user_name = ?, email = ? WHERE user_id = ?");
    $stmt->execute([$user_name, $email, $user_id]);
    $_SESSION['user_name'] = $user_name;

    if (!empty($new_password)) {
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE user SET password_hash = ? WHERE user_id = ?");
        $stmt->execute([$hash, $user_id]);
    }

    echo "<script>alert('Update Successful!'); window.location.href='profile.php';</script>";
}
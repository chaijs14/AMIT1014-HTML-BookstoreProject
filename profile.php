<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data
$stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Where to go back
$backPage = ($_SESSION['role'] === 'Admin') ? 'admin_dashboard.php' : 'member_dashboard.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>

<!-- TOP BAR -->
<div class="top-bar">
    <div class="logo">COM3 2O BUY</div>
    <div class="nav">
        <a href="<?php echo $backPage; ?>">Back</a>
        <a href="order_history.php">Orders</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- MAIN -->
<div class="container">
    <div class="card">
        <h2>👤 My Profile</h2>
        
        <!-- PROFILE PICTURE -->
        <div class="picture">
            <img src="<?php 
                if (!empty($user['profile_photo']) && file_exists('uploads/'.$user['profile_photo'])) {
                    echo 'uploads/'.$user['profile_photo'];
                } else {
                    echo 'https://via.placeholder.com/120?text=User';
                }
            ?>" id="preview" alt="Profile">
        </div>
        
        <!-- FORM -->
        <form action="profile_update.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="user_name" value="<?php echo $user['user_name']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Profile Photo</label>
                <input type="file" name="profile_photo" id="photo" accept="image/*">
            </div>
            
            <div class="form-group">
                <label>Role</label>
                <input type="text" value="<?php echo $user['role']; ?>" disabled style="background:#f5f5f5;">
            </div>
            
            <hr>
            
            <div class="form-group">
                <label>New Password (leave blank to keep same)</label>
                <input type="password" name="new_password" placeholder="Enter new password">
            </div>
            
            <button type="submit">💾 Save Changes</button>
        </form>
        
        <a href="<?php echo $backPage; ?>" class="back-link">← Back to Dashboard</a>
    </div>
</div>

<script>
    // Preview image before upload
    document.getElementById('photo').onchange = function(e) {
        let file = e.target.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    };
</script>

</body>
</html>
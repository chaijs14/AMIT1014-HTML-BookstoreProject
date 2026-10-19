<?php
session_start();
require 'config.php';

// security check - Admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}
 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $edit_user_id = $_POST['edit_user_id'];
    $user_name = $_POST['user_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $new_password = $_POST['new_password'];
    
    if (!empty($new_password)) {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE user SET user_name = ?, email = ?, role = ?, password_hash = ? WHERE user_id = ?");
        $stmt->execute([$user_name, $email, $role, $password_hash, $edit_user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE user SET user_name = ?, email = ?, role = ? WHERE user_id = ?");
        $stmt->execute([$user_name, $email, $role, $edit_user_id]);
    }
    
    header("Location: manage_users.php?updated=1");
    exit();
}

// Batch Deletion Function
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['batch_delete'])) {
    if (!empty($_POST['user_ids'])) {
        $ids_to_delete = $_POST['user_ids'];
        $placeholders = str_repeat('?,', count($ids_to_delete) - 1) . '?';
        
        try {
            $stmt = $pdo->prepare("DELETE FROM user WHERE user_id IN ($placeholders)");
            $stmt->execute($ids_to_delete);
            header("Location: manage_users.php?msg=BatchDeleteSuccess");
            exit();
        } catch (PDOException $e) {
            header("Location: manage_users.php?error=CannotDeleteActiveUsers");
            exit();
        }
    } else {
        header("Location: manage_users.php?error=NoUserSelected");
        exit();
    }
}

// to block a user and unblock
if (isset($_GET['action']) && isset($_GET['id'])) {
    $target_id = $_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'block') {
        $stmt = $pdo->prepare("UPDATE user SET is_active = '0' WHERE user_id = ?");
        $stmt->execute([$target_id]);
        header("Location: manage_users.php?msg=User Blocked");
    } elseif ($action == 'unblock') {
        $stmt = $pdo->prepare("UPDATE user SET is_active = '1' WHERE user_id = ?");
        $stmt->execute([$target_id]);
        header("Location: manage_users.php?msg=User Unblocked");
    }
    exit();
}

// click "Edit" to get specific user data
$current_edit_user = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
    $stmt->execute([$_GET['edit_id']]);
    $current_edit_user = $stmt->fetch();
}

// get all user data
$stmt = $pdo->query("SELECT * FROM user ORDER BY user_id ASC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - COM3 2O BUY</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/manage_users.css">
</head>
<body>

<div class="sidebar">
    <h3>COM3 2O BUY</h3>
    <div class="section-label">Admin Control</div>
    <a href="admin_dashboard.php" class="nav-item"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="manage_users.php" class="nav-item active"><i class="fas fa-users-cog"></i> Manage Users</a>
    <a href="inventory.php" class="nav-item"><i class="fas fa-boxes-stacked"></i> Inventory</a>
    <a href="admin_orders.php" class="nav-item"><i class="fas fa-receipt"></i> Orders</a>
    <div class="section-label">My Account</div>
    <a href="profile.php" class="nav-item"><i class="fas fa-user-circle"></i> My Profile</a>
    <a href="order_history.php" class="nav-item"><i class="fas fa-clock-rotate-left"></i> My History</a>
    <a href="logout.php" class="nav-item logout-link">Logout</a>
</div>

<div class="main-content">
    <header style="display: flex; justify-content: space-between; align-items: center;">
        <h1><i class="fas fa-users-cog"></i> Manage Users</h1>
        <div class="search-sort">
            <input type="text" id="search" class="search" placeholder="🔍 Search Users...">
        </div>
    </header>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success" style="margin-top: 20px;">User details updated successfully!</div>
    <?php endif; ?>

    <?php if ($current_edit_user): ?>
    <div class="edit-panel">
        <h3 style="margin-top: 0; color: var(--serenity-dark);">Edit User #<?php echo $current_edit_user['user_id']; ?></h3>
        <form method="POST" action="manage_users.php">
            <input type="hidden" name="edit_user_id" value="<?php echo $current_edit_user['user_id']; ?>">
            
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="user_name" class="search" value="<?php echo htmlspecialchars($current_edit_user['user_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="search" value="<?php echo htmlspecialchars($current_edit_user['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Role</label>
                <select name="role" class="search">
                    <option value="Member" <?php echo $current_edit_user['role'] == 'Member' ? 'selected' : ''; ?>>Member</option>
                    <option value="Admin" <?php echo $current_edit_user['role'] == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label>New Password (Leave blank to keep current)</label>
                <input type="text" name="new_password" class="search" placeholder="Enter new password...">
            </div>

            <div style="margin-top: 15px;">
                <button type="submit" name="update_user" class="btn-update">Save Changes</button>
                <a href="manage_users.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <form id="batch-delete-form" method="POST" action="manage_users.php">
        <div style="margin-top: 20px; margin-bottom: 10px;">
            <button type="submit" name="batch_delete" class="delete-btn" onclick="return confirm('Are you sure you want to delete the selected users? This action cannot be undone!')">
                <i class="fas fa-trash-alt"></i> Delete Selected Users
            </button>
        </div>

        <div class="orders-table">
            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="select-all"></th>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><input type="checkbox" name="user_ids[]" value="<?php echo $u['user_id']; ?>" class="user-checkbox"></td>
                        <td>#<?php echo $u['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($u['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td>
                            <span class="status <?php echo $u['role'] == 'Admin' ? 'status-Approved' : 'status-Pending'; ?>">
                                <?php echo $u['role']; ?>
                            </span>
                        </td>
                        <td><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                        <td>
                            <?php if ($u['is_active'] == '1'): ?>
                                <span class="status status-Approved">Active</span>
                            <?php else: ?>
                                <span class="status status-Pending">Blocked</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="manage_users.php?edit_id=<?php echo $u['user_id']; ?>" class="btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <?php if ($u['is_active'] == '1'): ?>
                                <a href="manage_users.php?action=block&id=<?php echo $u['user_id']; ?>" 
                                   class="btn-edit btn-block" 
                                   style="background: #e57373;" 
                                   onclick="return confirm('Are You Sure You Want To Block This User?')">
                                    <i class="fas fa-ban"></i> Block
                                </a>
                            <?php else: ?>
                                <a href="manage_users.php?action=unblock&id=<?php echo $u['user_id']; ?>" 
                                   class="btn-edit btn-unblock" 
                                   style="background: #81c784;" 
                                   onclick="return confirm('Are You Sure You Want To Unblock This User?')">
                                    <i class="fas fa-check-circle"></i> Unblock
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    // Auto hide alert after 3 seconds
    setTimeout(function() {
        $('.alert').fadeOut(500);
    }, 3000);
    
    // Search filter function
    $("#search").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("table tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // Select all checkbox functionality
    $('#select-all').on('click', function() {
        $('.user-checkbox').prop('checked', this.checked);
    });

    // If a single checkbox is unchecked, uncheck the 'select all' box
    $('.user-checkbox').on('click', function() {
        if ($('.user-checkbox:checked').length == $('.user-checkbox').length) {
            $('#select-all').prop('checked', true);
        } else {
            $('#select-all').prop('checked', false);
        }
    });
});
</script>

</body>
</html>
<?php
session_start();
require 'config.php';

// Security check - Admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

// Handle Order Status Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['order_status'];
    
    $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    $stmt->execute([$new_status, $order_id]);
    
    header("Location: admin_orders.php?updated=1");
    exit();
}

// Get all orders
$stmt = $pdo->query("
    SELECT o.*, u.user_name, u.email 
    FROM orders o 
    JOIN user u ON o.user_id = u.user_id 
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Orders - COM3 2O BUY</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --rose-quartz: #F7CAC9;
            --serenity: #92A8D1;
            --serenity-dark: #7b94c4;
            --glass: rgba(255, 255, 255, 0.85);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', system-ui, sans-serif;
        }
        
        body {
            margin: 0;
            background: linear-gradient(135deg, var(--rose-quartz) 0%, var(--serenity) 100%);
            display: flex;
            min-height: 100vh;
        }
        
        /* SIDEBAR */
        .sidebar {
            width: 280px;
            background: var(--glass);
            backdrop-filter: blur(10px);
            margin: 20px;
            border-radius: 24px;
            padding: 25px;
            position: sticky;
            top: 20px;
            height: calc(100vh - 40px);
        }
        
        .sidebar h3 {
            color: var(--serenity-dark);
            text-align: center;
            margin-bottom: 25px;
        }
        
        .section-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #999;
            margin: 20px 0 10px 10px;
            font-weight: 800;
        }
        
        .nav-item {
            padding: 12px 20px;
            margin-bottom: 5px;
            border-radius: 12px;
            color: #555;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
        }
        
        .nav-item:hover {
            background: var(--rose-quartz);
            color: white;
        }
        
        .nav-item.active {
            background: var(--serenity);
            color: white;
        }
        
        .logout-link {
            margin-top: auto;
            color: #e57373;
            text-align: center;
            border: 2px solid #ffcdd2;
            border-radius: 12px;
        }
        
        .logout-link:hover {
            background: #e57373;
            color: white;
        }
        
        /* MAIN CONTENT */
        .main-content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }
        
        header h1 {
            color: white;
            margin: 0;
        }
        
        header p {
            color: rgba(255,255,255,0.8);
            margin-top: 5px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        /* ORDERS TABLE */
        .orders-table {
            background: var(--glass);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            overflow-x: auto;
            margin-top: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        th {
            background: rgba(146, 168, 209, 0.2);
            color: var(--serenity-dark);
            font-weight: 700;
        }
        
        tr:hover {
            background: rgba(255,255,255,0.5);
        }
        
        /* STATUS BADGES */
        .status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }
        
        .status-Pending {
            background: #ffe0b2;
            color: #e65100;
        }
        
        .status-Approved {
            background: #c8e6c9;
            color: #2e7d32;
        }
        
        .status-Rejected {
            background: #ffcdd2;
            color: #c62828;
        }
        
        /* ACTION BUTTONS */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .status-form {
            display: inline-flex;
            gap: 8px;
            align-items: center;
        }
        
        .status-form select {
            padding: 6px 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 12px;
        }
        
        .status-form button {
            padding: 6px 12px;
            border-radius: 8px;
            border: none;
            background: var(--serenity);
            color: white;
            cursor: pointer;
            font-size: 12px;
        }
        
        .status-form button:hover {
            background: var(--serenity-dark);
        }
        
        .btn-view {
            background: #6c757d;
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 12px;
            display: inline-block;
        }
        
        .btn-view:hover {
            background: #5a6268;
        }
        
        .btn-receipt {
            background: #17a2b8;
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 12px;
            display: inline-block;
        }
        
        .btn-receipt:hover {
            background: #138496;
        }
        
        @media (max-width: 900px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                width: auto;
                height: auto;
                margin: 10px;
            }
            .main-content {
                padding: 20px;
            }
            th, td {
                padding: 8px;
                font-size: 12px;
            }
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>COM3 2O BUY</h3>
    <div class="section-label">Admin Control</div>
    <a href="admin_dashboard.php" class="nav-item"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="manage_users.php" class="nav-item"><i class="fas fa-users-cog"></i> Manage Users</a>
    <a href="inventory.php" class="nav-item"><i class="fas fa-boxes-stacked"></i> Inventory</a>
    <a href="admin_orders.php" class="nav-item active"><i class="fas fa-receipt"></i> Orders</a>
    <div class="section-label">My Account</div>
    <a href="profile.php" class="nav-item"><i class="fas fa-user-circle"></i> My Profile</a>
    <a href="order_history.php" class="nav-item"><i class="fas fa-clock-rotate-left"></i> Order History</a>
    <a href="logout.php" class="nav-item logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">
    <header>
        <h1><i class="fas fa-receipt"></i> Order Management</h1>
        <p>Manage all customer orders</p>
    </header>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success">✅ Order status updated successfully!</div>
    <?php endif; ?>

    <div class="orders-table">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?php echo $order['order_id']; ?></td>
                    <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['email']); ?></td>
                    <td>RM <?php echo number_format($order['total_price'], 2); ?></td>
                    <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                    <td class="status-column">
                        <span class="status status-<?php echo $order['order_status']; ?>">
                            <?php echo $order['order_status']; ?>
                        </span>
                    </td>
                    <td class="action-buttons">
                        <form method="POST" class="status-form">
                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                            <select name="order_status">
                                <option value="Pending" <?php echo $order['order_status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Approved" <?php echo $order['order_status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="Rejected" <?php echo $order['order_status'] == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                            <button type="submit" name="update_status">Update</button>
                        </form>
                        <a href="admin_order_detail.php?id=<?php echo $order['order_id']; ?>" class="btn-view">View</a>
                        <a href="generate_receipt.php?id=<?php echo $order['order_id']; ?>" class="btn-receipt" target="_blank">🧾 Receipt</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
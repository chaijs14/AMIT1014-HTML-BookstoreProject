<?php
session_start();
require 'config.php';

// Check If Logged In As Admin
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get statistics
$totalUsers = $pdo->query("SELECT COUNT(*) FROM user WHERE role = 'Member'")->fetchColumn();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Pending'")->fetchColumn();
$approvedOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Approved'")->fetchColumn();
$rejectedOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Rejected'")->fetchColumn();

// Get sales data by month (last 6 months)
$salesData = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('M', strtotime("-$i months"));
    $yearMonth = date('Y-m', strtotime("-$i months"));
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(total_price), 0) as total FROM orders WHERE order_status = 'Approved' AND DATE_FORMAT(created_at, '%Y-%m') = ?");
    $stmt->execute([$yearMonth]);
    $salesData[$month] = $stmt->fetch()['total'];
}

// Get category distribution
$categoryCount = $pdo->query("SELECT category, COUNT(*) as count FROM products GROUP BY category ORDER BY count DESC LIMIT 5")->fetchAll();

// ========== TOP SELLING PRODUCTS ==========
$topProducts = $pdo->query("
    SELECT p.product_id, p.title, p.author, p.image_url, p.price, 
           COALESCE(SUM(oi.quantity), 0) as total_sold,
           COALESCE(SUM(oi.quantity * oi.price_at_purchase), 0) as total_revenue
    FROM products p
    LEFT JOIN order_items oi ON p.product_id = oi.product_id
    LEFT JOIN orders o ON oi.order_id = o.order_id AND o.order_status = 'Approved'
    GROUP BY p.product_id
    ORDER BY total_sold DESC
    LIMIT 5
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/admin_dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/main.js"></script>
</head>
<body>

<div class="top-bar">
    <div class="logo">👨🏻‍💼 ADMINISTRATION </div>
    <div class="nav">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="inventory.php">Products</a>
        <a href="admin_orders.php">Orders</a>
        <a href="manage_users.php">Users</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <div class="welcome">
        <h2>Welcome, <?php echo $_SESSION['user_name']; ?>! 👋</h2>
        <p>Manages the online bookstore system, including books, orders, users, and overall platform operations.</p>
    </div>
    
    <!-- STATS CARDS (TOP) -->
    <div class="stats">
        <div class="stat-card">
            <i class="fas fa-users"></i>
            <h3>Total Members</h3>
            <div class="number"><?php echo $totalUsers; ?></div>
        </div>
        <div class="stat-card">
            <i class="fas fa-book"></i>
            <h3>Total Products</h3>
            <div class="number"><?php echo $totalProducts; ?></div>
        </div>
        <div class="stat-card">
            <i class="fas fa-clock"></i>
            <h3>Pending Orders</h3>
            <div class="number"><?php echo $pendingOrders; ?></div>
        </div>
        <div class="stat-card">
            <i class="fas fa-check-circle"></i>
            <h3>Completed Orders</h3>
            <div class="number"><?php echo $approvedOrders; ?></div>
        </div>
    </div>
    
    //Charts
    <div class="charts-container">
        <div class="chart-card">
            <h3><i class="fas fa-chart-line"></i> Monthly Sales (RM)</h3>
            <canvas id="salesChart"></canvas>
        </div>
        <div class="chart-card">
            <h3><i class="fas fa-chart-pie"></i> Top Categories</h3>
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
    
    //Top Sellers + Order Status
    <div class="middle-section">
        
        <!-- TOP SELLING PRODUCTS (LEFT) -->
        <div class="top-sellers">
            <h3><i class="fas fa-trophy"></i> 🏆 Top Selling Products</h3>
            
            <?php if (empty($topProducts) || $topProducts[0]['total_sold'] == 0): ?>
                <div class="no-data">
                    <i class="fas fa-chart-simple"></i>
                    <p>No sales data yet. Complete some orders to see top sellers!</p>
                </div>
            <?php else: ?>
                <div class="sellers-grid">
                    <?php $rank = 1; ?>
                    <?php foreach ($topProducts as $product): ?>
                        <?php if ($product['total_sold'] > 0): ?>
                            <div class="seller-card">
                                <div class="seller-rank">
                                    <?php if ($rank == 1): ?>🥇
                                    <?php elseif ($rank == 2): ?>🥈
                                    <?php elseif ($rank == 3): ?>🥉
                                    <?php else: echo $rank; endif; ?>
                                </div>
                                <div class="seller-info">
                                    <div class="seller-title"><?php echo htmlspecialchars($product['title']); ?></div>
                                    <div class="seller-author">by <?php echo htmlspecialchars($product['author']); ?></div>
                                    <div class="seller-stats">
                                        <span class="seller-sold"><i class="fas fa-shopping-cart"></i> Sold: <?php echo $product['total_sold']; ?></span>
                                        <span class="seller-revenue"><i class="fas fa-dollar-sign"></i> RM <?php echo number_format($product['total_revenue'], 2); ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php $rank++; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        //Order Status Overview
        <div class="order-status">
            <h3><i class="fas fa-chart-bar"></i> Order Status Overview</h3>
            <div class="status-bars">
                <div class="status-item">
                    <div class="status-bar">
                        <div class="status-fill fill-pending"><?php echo $pendingOrders; ?></div>
                    </div>
                    <div class="status-count"><?php echo $pendingOrders; ?></div>
                    <div class="status-label">🟡 Pending</div>
                </div>
                <div class="status-item">
                    <div class="status-bar">
                        <div class="status-fill fill-approved"><?php echo $approvedOrders; ?></div>
                    </div>
                    <div class="status-count"><?php echo $approvedOrders; ?></div>
                    <div class="status-label">🟢 Approved</div>
                </div>
                <div class="status-item">
                    <div class="status-bar">
                        <div class="status-fill fill-rejected"><?php echo $rejectedOrders; ?></div>
                    </div>
                    <div class="status-count"><?php echo $rejectedOrders; ?></div>
                    <div class="status-label">🔴 Rejected</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="footer">
    <p>© 2026 COM3 2O BUY</p>
</div>

<script>
    // Sales Chart (Bar Chart)
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_keys($salesData)); ?>,
            datasets: [{
                label: 'Sales (RM)',
                data: <?php echo json_encode(array_values($salesData)); ?>,
                backgroundColor: '#92A8D1',
                borderColor: '#7b94c4',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: { callbacks: { label: function(context) { return 'RM ' + context.raw.toFixed(2); } } }
            },
            scales: { y: { beginAtZero: true, title: { display: true, text: 'Amount (RM)' } } }
        }
    });
    
    // Category Chart (Pie Chart)
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_column($categoryCount, 'category')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($categoryCount, 'count')); ?>,
                backgroundColor: ['#F7CAC9', '#92A8D1', '#e74c3c', '#27ae60', '#f39c12', '#3498db', '#9b59b6'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { position: 'right' } }
        }
    });
</script>

</body>
</html>
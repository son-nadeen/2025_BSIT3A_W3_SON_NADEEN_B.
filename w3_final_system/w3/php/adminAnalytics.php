<?php
include 'connection.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Total sales
$totalSalesResult = $conn->query("
    SELECT SUM(p.price) AS total_sales
    FROM orders o
    JOIN products p ON o.product_id = p.product_id
");
if (!$totalSalesResult) {
    die("Query Failed: " . $conn->error);
}
$totalSales = $totalSalesResult->fetch_assoc()['total_sales'] ?? 0;

// Total orders
$totalOrdersResult = $conn->query("SELECT COUNT(*) AS total_orders FROM orders");
if (!$totalOrdersResult) {
    die("Query Failed: " . $conn->error);
}
$totalOrders = $totalOrdersResult->fetch_assoc()['total_orders'] ?? 0;

// Status counts
$statusCountsResult = $conn->query("
    SELECT order_status, COUNT(*) AS count 
    FROM orders 
    GROUP BY order_status
");
$statusCounts = [];
if ($statusCountsResult) {
    while ($row = $statusCountsResult->fetch_assoc()) {
        $statusCounts[$row['order_status']] = $row['count'];
    }
}

// Top 5 best-selling products
$topProductsResult = $conn->query("
    SELECT p.PRODUCT_NAME, COUNT(*) AS total_sold
    FROM orders o
    JOIN products p ON o.product_id = p.PRODUCT_ID
    GROUP BY o.product_id
    ORDER BY total_sold DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analytics - ShopZone Admin</title>
    <link rel="stylesheet" href="../css/adminDashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .analytics-container {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .analytics-card {
            background-color: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            flex: 1 1 250px;
            text-align: center;
        }
        .analytics-card h2 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .analytics-card p {
            font-size: 1.1rem;
            color: #555;
        }
        table.analytics-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }
        table.analytics-table th, table.analytics-table td {
            padding: 0.8rem 1rem;
            border: 1px solid #ddd;
            text-align: left;
        }
        table.analytics-table th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
<div class="admin-container">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="logo">Shop<span>Zone</span> Admin</div>
        <nav class="admin-menu">
            <ul>
                <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="Products.php"><i class="fas fa-boxes"></i> Products</a></li>
                <li><a href="adminOrders.php"><i class="fas fa-receipt"></i> Orders</a></li>
                <li class="active"><a href="adminAnalytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-content">
            <h1>Sales Analytics</h1>

            <div class="analytics-container">
                <div class="analytics-card">
                    <h2><?= number_format((int)$totalOrders) ?></h2>
                    <p>Total Orders</p>
                </div>

                <div class="analytics-card">
                    <h2>₱ <?= number_format((float)$totalSales, 2) ?></h2>
                    <p>Total Sales</p>
                </div>

                <div class="analytics-card">
                    <h2><?= $statusCounts['Pending'] ?? 0 ?></h2>
                    <p>Pending Orders</p>
                </div>

                <div class="analytics-card">
                    <h2><?= $statusCounts['Shipped'] ?? 0 ?></h2>
                    <p>Shipped Orders</p>
                </div>

                <div class="analytics-card">
                    <h2><?= $statusCounts['Completed'] ?? 0 ?></h2>
                    <p>Completed Orders</p>
                </div>
            </div>

            <h2 style="margin-top: 3rem;">Top 5 Best-Selling Products</h2>
            <table class="analytics-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Units Sold</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($topProductsResult && $topProductsResult->num_rows > 0): ?>
                    <?php while ($row = $topProductsResult->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['PRODUCT_NAME']) ?></td>
                            <td><?= $row['total_sold'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" style="text-align: center;">No product sales data available.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>

<?php $conn->close(); ?>

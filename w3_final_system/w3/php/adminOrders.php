<?php
include 'connection.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['order_status'];
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM orders WHERE order_id = $delete_id");
    header("Location: adminOrders.php"); // <-- changed from manage_orders.php
    exit;
}

$query = "SELECT o.order_id, u.user_name, p.PRODUCT_NAME, p.IMAGE, o.user_address, o.order_status, o.order_date 
          FROM orders o
          JOIN user u ON o.user_id = u.user_id
          JOIN products p ON o.product_id = p.PRODUCT_ID
          ORDER BY o.order_date DESC";

$result = $conn->query($query);
if (!$result) {
    die("Query Failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders - ShopZone Admin</title>
    <link rel="stylesheet" href="../css/adminDashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="admin-container">
    <aside class="admin-sidebar">
        <div class="logo">Shop<span>Zone</span> Admin</div>
        <nav class="admin-menu">
            <ul>
                <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="Products.php"><i class="fas fa-boxes"></i> Products</a></li>
                <li class="active"><a href="adminOrder.php"><i class="fas fa-receipt"></i> Orders</a></li>
                <li><a href="adminAnalytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
            </ul>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-content">
            <h1>Manage Orders</h1>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Image</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['order_id'] ?></td>
                                <td><?= htmlspecialchars($row['user_name']) ?></td>
                                <td><?= htmlspecialchars($row['PRODUCT_NAME']) ?></td>
                                <td>
                                    <?php if (!empty($row['IMAGE'])): ?>
                                        <img src="<?= htmlspecialchars($row['IMAGE']) ?>" alt="Product Image" class="product-img" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['user_address']) ?></td>
                                <td>
                                    <form method="POST" style="display: flex; gap: 5px;">
                                        <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                                        <select name="order_status">
                                            <option> <?php echo $row['order_status']; ?></option>
                                            <option value="Pending" <?= $row['order_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Shipped" <?= $row['order_status'] == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                            <option value="Completed" <?= $row['order_status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                        </select>
                                        <button type="submit" name="update_order" class="add-button" style="padding: 0.3rem 0.6rem;">Update</button>
                                    </form>
                                </td>
                                <td><?= $row['order_date'] ?></td>
                                <td>
                                    <a href="?delete=<?= $row['order_id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this order?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>

<?php $conn->close(); ?>

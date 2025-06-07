<?php
session_start();
include 'connection.php';

// Ensure only admins can access this page
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user_admin') {
    echo "<script>alert('Access denied. Admins only.'); window.location.href='signin.php';</script>";
    exit();
}

// Fetch top 5 most recent orders
$recentOrders = [];
$sql = "SELECT o.order_id, u.user_name, o.order_date, o.order_status
        FROM orders o
        JOIN user u ON o.user_id = u.user_id
        ORDER BY o.order_date DESC
        LIMIT 5";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $recentOrders = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShopZone | Admin Panel</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="../css/adminDashboard.css">
</head>
<body>
  <div class="admin-container">
    <!-- Sidebar Navigation -->
    <aside class="admin-sidebar">
      <div class="logo">Shop<span>Zone</span> Admin</div>
      <nav class="admin-menu">
        <ul>
          <li class="active"><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li><a href="product.php"><i class="fas fa-boxes"></i> Products</a></li>
          <li><a href="adminOrders.php"><i class="fas fa-receipt"></i> Orders</a></li>
          <li><a href="adminAnalytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
        </ul>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
      <!-- Top Bar -->
      <header class="admin-topbar">
        <div class="search-bar">
          <input type="text" placeholder="Search admin...">
          <button><i class="fas fa-search"></i></button>
        </div>
        <div class="admin-actions">
          <button class="notification-bell">
            <i class="fas fa-bell"></i>
            <span class="notification-count">3</span>
          </button>
          <div class="admin-profile">
            <img src="https://via.placeholder.com/40" alt="Admin">
            <span>Admin User</span>
            <i class="fas fa-chevron-down"></i>
          </div>
        </div>
      </header>

      <!-- Dashboard Content -->
      <div class="admin-content">
        <h1>Admin Dashboard</h1>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-info">
              <h3>5</h3>
              <p>Total Orders</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
              <h3>6</h3>
              <p>Customers</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-box-open"></i>
            </div>
            <div class="stat-info">
              <h3>₱ 1,500</h3>
              <p>Revenue</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-star"></i>
            </div>
            <div class="stat-info">
              <h3>4.8</h3>
              <p>Avg. Rating</p>
            </div>
          </div>
        </div>

        <!-- Recent Orders -->
        <section class="admin-section">
          <div class="section-header">
            <h2>Recent Orders</h2>
            <a href="adminOrders.php" class="view-all">View All</a>
          </div>
          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Customer</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($recentOrders)): ?>
                  <?php foreach ($recentOrders as $order): ?>
                    <tr>
                      <td>#<?= htmlspecialchars($order['order_id']) ?></td>
                      <td><?= htmlspecialchars($order['user_name']) ?></td>
                      <td><?= date("M d, Y", strtotime($order['order_date'])) ?></td>
              
                      <td>
                        <span class="status <?= strtolower($order['order_status']) ?>">
                          <?= htmlspecialchars($order['order_status']) ?>
                        </span>
                      </td>
                      <td><a href="adminOrderDetails.php?order_id=<?= $order['order_id'] ?>" class="action-link">View</a></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="6">No recent orders found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Product Management -->
        <section class="admin-section">
          <div class="section-header">
            <h2>Product Management</h2>
            <button class="add-button"><i class="fas fa-plus"></i> Add Product</button>
          </div>
          <div class="product-grid">
            <div class="product-card">
              <img src="../image/shirt.jpg" alt="Classic White Tee">
              <div class="product-info">
                <h3>Classic White Tee</h3>
                <div class="product-meta">
                  <span class="price">$29.99</span>
                  <span class="stock">15 in stock</span>
                </div>
                <div class="product-actions">
                  <button class="edit-btn"><i class="fas fa-edit"></i></button>
                  <button class="delete-btn"><i class="fas fa-trash"></i></button>
                </div>
              </div>
            </div>
            <div class="product-card">
              <img src="../image/cap.jpg" alt="Baseball Cap">
              <div class="product-info">
                <h3>Baseball Cap</h3>
                <div class="product-meta">
                  <span class="price">$24.99</span>
                  <span class="stock">8 in stock</span>
                </div>
                <div class="product-actions">
                  <button class="edit-btn"><i class="fas fa-edit"></i></button>
                  <button class="delete-btn"><i class="fas fa-trash"></i></button>
                </div>
              </div>
            </div>
            <div class="product-card">
              <img src="../image/toteBag.jpg" alt="Canvas Tote Bag">
              <div class="product-info">
                <h3>Canvas Tote Bag</h3>
                <div class="product-meta">
                  <span class="price">$39.99</span>
                  <span class="stock">12 in stock</span>
                </div>
                <div class="product-actions">
                  <button class="edit-btn"><i class="fas fa-edit"></i></button>
                  <button class="delete-btn"><i class="fas fa-trash"></i></button>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </main>
  </div>
</body>
</html>

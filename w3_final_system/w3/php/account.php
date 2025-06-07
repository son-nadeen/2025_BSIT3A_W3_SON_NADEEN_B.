<?php
include 'connection.php';

// Dummy user ID for testing
$user_id = 1;

// Initialize variables
$user = null;
$order = null;

// Fetch user profile info
$user_query = $conn->query("SELECT * FROM user WHERE user_id = $user_id");
if ($user_query && $user_query->num_rows > 0) {
    $user = $user_query->fetch_assoc();
}

// Fetch recent order for this user
$order_query = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY order_date DESC LIMIT 1");
if ($order_query && $order_query->num_rows > 0) {
    $order = $order_query->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Account | ShopZone</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/account.css" />
</head>
<body>
<header class="account-header">
  <div class="header-container">
    <a href="dashboard.php" class="back-button">
      <i class="fas fa-arrow-left"></i>
    </a>
    <h1>My Account</h1>
    <div class="header-icons">
      <a href="wishlist.html" class="wishlist-icon"><i class="fas fa-heart"></i></a>
      <a href="cart.html" class="cart-icon">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-count">3</span>
      </a>
    </div>
  </div>
</header>

<main class="account-main">
  <div class="account-container">
    <!-- Profile Section -->
    <section class="profile-section">
      <div class="profile-card">
        <div class="profile-header">
          <div class="avatar">
            <img src="images/default-avatar.png" alt="User Avatar" />
            <button class="edit-avatar"><i class="fas fa-camera"></i></button>
          </div>
          <div class="profile-info">
            <h2><?= htmlspecialchars($user['user_name'] ?? 'Guest User') ?></h2>
            <p class="member-since">
              Member since
              <?= isset($user['date_registered']) ? date("F Y", strtotime($user['date_registered'])) : 'N/A' ?>
            </p>
          </div>
        </div>
        <div class="profile-stats">
          <div class="stat-item">
            <span class="stat-number">--</span>
            <span class="stat-label">Orders</span>
          </div>
          <div class="stat-item">
            <span class="stat-number">--</span>
            <span class="stat-label">Wishlist</span>
          </div>
          <div class="stat-item">
            <span class="stat-number">--</span>
            <span class="stat-label">Rating</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Account Navigation -->
    <nav class="account-nav">
      <ul>
        <li><a href="myOrders.php" class="nav-link active"><i class="fas fa-box-open"></i><span>My Orders</span><i class="fas fa-chevron-right"></i></a></li>
        <li><a href="address.php" class="nav-link"><i class="fas fa-map-marker-alt"></i><span>Saved Addresses</span><i class="fas fa-chevron-right"></i></a></li>
        <li><a href="wishlist.php" class="nav-link"><i class="fas fa-heart"></i><span>Wishlist</span><i class="fas fa-chevron-right"></i></a></li>
        <li><a href="payments.php" class="nav-link"><i class="fas fa-credit-card"></i><span>Payment Methods</span><i class="fas fa-chevron-right"></i></a></li>
        <li><a href="settings.php" class="nav-link"><i class="fas fa-cog"></i><span>Account Settings</span><i class="fas fa-chevron-right"></i></a></li>
      </ul>
    </nav>

    <!-- Recent Orders -->
    <section class="orders-section">
      <div class="section-header">
        <h2>Recent Orders</h2>
        <a href="orders.html" class="view-all">View All</a>
      </div>

      <?php if ($order): ?>
      <div class="orders-list">
        <div class="order-card">
          <div class="order-header">
            <span class="order-id">#<?= htmlspecialchars($order['order_id']) ?></span>
            <span class="order-date"><?= date("F d, Y", strtotime($order['order_date'])) ?></span>
            <span class="order-status"><?= htmlspecialchars($order['order_status']) ?></span>
          </div>
          <div class="order-details">
            <div class="order-items">
              <!-- Example static content for demo, replace with dynamic order items -->
              <div class="item-image">
                <img src="images/shirt.jpg" alt="Item 1" />
                <span class="item-quantity">1</span>
              </div>
              <div class="item-image">
                <img src="images/cap.jpg" alt="Item 2" />
              </div>
            </div>
            <div class="order-total">
              <span class="total-amount">--</span> <!-- Replace with actual total if available -->
            </div>
          </div>
          <div class="order-actions">
            <button class="btn track-order"><i class="fas fa-truck"></i> Track</button>
            <button class="btn reorder"><i class="fas fa-sync-alt"></i> Reorder</button>
          </div>
        </div>
      </div>
      <?php else: ?>
        <p>No recent orders found.</p>
      <?php endif; ?>
    </section>
  </div>
</main>

<footer class="account-footer">
  <div class="footer-container">
    <div class="footer-links">
      <a href="about.html">About Us</a>
      <a href="contact.html">Contact</a>
      <a href="privacy.html">Privacy Policy</a>
      <a href="terms.html">Terms of Service</a>
    </div>
    <div class="social-links">
      <a href="#"><i class="fab fa-facebook-f"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
    </div>
    <p class="copyright">© 2023 ShopZone. All rights reserved.</p>
  </div>
</footer>

<script src="js/account.js"></script>
</body>
</html>

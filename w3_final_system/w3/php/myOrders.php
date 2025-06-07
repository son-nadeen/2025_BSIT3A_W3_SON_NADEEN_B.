<?php
include 'connection.php';

// Dummy user ID for demo
$user_id = 1;

// Fetch user orders from DB with correct columns
$orders_query = $conn->prepare("
    SELECT order_id, product_id, user_address, order_date, order_time, order_status
    FROM orders
    WHERE user_id = ?
    ORDER BY order_date DESC, order_time DESC
");
$orders_query->bind_param("i", $user_id);
$orders_query->execute();
$orders_result = $orders_query->get_result();

$orders = $orders_result->fetch_all(MYSQLI_ASSOC);

$orders_query->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Orders | ShopZone</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/myOrders.css" />
</head>
<body>

  <!-- Fixed Header -->
  <header class="orders-header">
    <div class="header-container">
      <a href="account.php" class="back-button" title="Back to Shop">
        <i class="fas fa-arrow-left"></i>
      </a>
      <h1>My Orders</h1>
      <div class="header-icons">
        <a href="wishlist.html" class="wishlist-icon" title="Wishlist">
          <i class="fas fa-heart"></i>
        </a>
        <a href="account.html" class="account-icon" title="Account">
          <i class="fas fa-user"></i>
        </a>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="orders-main">
    <div class="orders-container">
      <section class="orders-list">
        <div class="section-header">
          <h2><?= count($orders) ?> Order<?= count($orders) != 1 ? 's' : '' ?></h2>
        </div>

        <?php if (count($orders) > 0): ?>
          <?php foreach ($orders as $order): ?>
            <div class="order-card <?= strtolower($order['order_status']) ?>">
              <div class="order-header">
                <div class="order-id">Order #<?= htmlspecialchars($order['order_id']) ?></div>
                <div class="order-date">
                  Placed on <?= date("F j, Y", strtotime($order['order_date'])) ?> at <?= date("g:i A", strtotime($order['order_time'])) ?>
                </div>
                <div class="order-status"><?= htmlspecialchars(ucfirst($order['order_status'])) ?></div>
              </div>

              <div class="order-items-preview">
                <p><strong>Product ID:</strong> <?= htmlspecialchars($order['product_id']) ?></p>
                <p><strong>Delivery Address:</strong> <?= htmlspecialchars($order['user_address']) ?></p>
              </div>

              <div class="order-actions">
                <?php if ($order['order_status'] === 'processing'): ?>
                  <button class="cancel-order-btn"><i class="fas fa-times-circle"></i> Cancel Order</button>
                  <button class="track-order-btn"><i class="fas fa-truck"></i> Track Order</button>
                <?php elseif ($order['order_status'] === 'shipped'): ?>
                  <button class="track-order-btn"><i class="fas fa-truck"></i> Track Order</button>
                  <button class="view-details-btn"><i class="fas fa-eye"></i> View Details</button>
                <?php elseif ($order['order_status'] === 'delivered'): ?>
                  <button class="return-order-btn"><i class="fas fa-undo"></i> Return Item</button>
                  <button class="buy-again-btn"><i class="fas fa-sync-alt"></i> Buy Again</button>
                <?php elseif ($order['order_status'] === 'cancelled'): ?>
                  <button class="buy-again-btn"><i class="fas fa-sync-alt"></i> Buy Again</button>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>You have no orders yet.</p>
        <?php endif; ?>
      </section>
    </div>
  </main>

  <!-- Fixed Footer -->
  <footer class="orders-footer">
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

  <script src="js/orders.js"></script>
</body>
</html>

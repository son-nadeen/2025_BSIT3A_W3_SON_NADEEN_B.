<?php
include 'connection.php';

// Get the order ID from the URL parameter, sanitize it to prevent SQL injection
$orderId = isset($_GET['orderId']) ? mysqli_real_escape_string($conn, $_GET['orderId']) : '';

$orderData = null;

if ($orderId) {
    // Query to get order info (adjust table and column names to your DB)
    $sql = "SELECT * FROM orders WHERE order_number = '$orderId' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $orderData = mysqli_fetch_assoc($result);
    } else {
        $orderData = null;  // Order not found
    }
} else {
    $orderData = null;  // No order ID provided
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Track Your Order | ShopZone</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/trackOrder.css" />
</head>
<body>
  <!-- Fixed Header -->
  <header class="track-header">
    <div class="header-container">
      <a href="dashboard.php" class="back-button">
        <i class="fas fa-arrow-left"></i>
      </a>
      <h1>Track Order</h1>
      <div class="header-icons">
        <a href="wishlist.html" class="wishlist-icon">
          <i class="fas fa-heart"></i>
        </a>
        <a href="account.html" class="account-icon">
          <i class="fas fa-user"></i>
        </a>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="track-main">
    <div class="track-container">
      <?php if ($orderData): ?>
      <!-- Order Summary -->
      <section class="order-summary">
        <div class="order-header">
          <span class="order-id">Order #<?php echo htmlspecialchars($orderData['order_number']); ?></span>
          <span class="order-date">Placed on <?php echo date('F j, Y', strtotime($orderData['order_date'])); ?></span>
        </div>

        <!-- Delivery Information Section -->
        <div class="delivery-info">
          <div class="info-box">
            <i class="fas fa-truck"></i>
            <div>
              <h3>Estimated Delivery</h3>
              <p><?php echo date('F j, Y', strtotime($orderData['estimated_delivery'])); ?></p>
            </div>
          </div>
          <div class="info-box">
            <i class="fas fa-money-bill-wave"></i>
            <div>
              <h3>Payment Method</h3>
              <p><?php echo htmlspecialchars($orderData['payment_method']); ?></p>
            </div>
          </div>
          <div class="info-box">
            <i class="fas fa-shipping-fast"></i>
            <div>
              <h3>Shipping</h3>
              <p><?php echo htmlspecialchars($orderData['shipping_method']); ?></p>
              <p class="tracking-number">Tracking #: <?php echo htmlspecialchars($orderData['tracking_number']); ?></p>
            </div>
          </div>
        </div>

        <div class="order-details">
          <div class="delivery-address">
            <h3>Delivery Address</h3>
            <p><?php echo htmlspecialchars($orderData['recipient_name']); ?></p>
            <p><?php echo nl2br(htmlspecialchars($orderData['address'])); ?></p>
            <p>Phone: <?php echo htmlspecialchars($orderData['phone']); ?></p>
          </div>

          <div class="order-items">
            <h3>Items in Order</h3>
            <?php
            // Assuming you have an order_items table linked by order_number
            $itemsSql = "SELECT * FROM order_items WHERE order_number = '$orderId'";
            $itemsResult = mysqli_query($conn, $itemsSql);

            if ($itemsResult && mysqli_num_rows($itemsResult) > 0):
                while ($item = mysqli_fetch_assoc($itemsResult)):
            ?>
            <div class="item">
              <div class="item-image">
                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" />
              </div>
              <div class="item-info">
                <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                <p>Quantity: <?php echo intval($item['quantity']); ?></p>
                <p class="price">$<?php echo number_format($item['price'], 2); ?></p>
              </div>
            </div>
            <?php
                endwhile;
            else:
                echo "<p>No items found for this order.</p>";
            endif;
            ?>
          </div>
        </div>

        <div class="order-total">
          <div class="total-row">
            <span>Subtotal (<?php echo intval($orderData['total_items']); ?> items)</span>
            <span>$<?php echo number_format($orderData['subtotal'], 2); ?></span>
          </div>
          <div class="total-row">
            <span>Shipping</span>
            <span>$<?php echo number_format($orderData['shipping_cost'], 2); ?></span>
          </div>
          <div class="total-row grand-total">
            <span>Total</span>
            <span>$<?php echo number_format($orderData['total'], 2); ?></span>
          </div>
        </div>
      </section>

      <!-- The rest of the page (tracking progress, delivery updates, help section) remains the same -->

      <?php else: ?>
        <p style="padding: 20px; text-align:center;">Order not found. Please check your order ID.</p>
      <?php endif; ?>
    </div>
  </main>

  <!-- Fixed Footer -->
  <footer class="track-footer">
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

  <script src="js/track-order.js"></script>
</body>
</html>

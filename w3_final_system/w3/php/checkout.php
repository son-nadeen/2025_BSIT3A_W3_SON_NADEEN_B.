<?php
// Start session and include DB connection
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: shop.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items
$sql = "SELECT ci.quantity, ci.product_id, p.product_name, p.price, p.image_path 
        FROM cart_items ci 
        JOIN products p ON ci.product_id = p.product_id 
        WHERE ci.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$subtotal = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $subtotal += $row['price'] * $row['quantity'];
}

$shipping_fee = 5.99;
$total = $subtotal + $shipping_fee;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Checkout | ShopZone</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/checkout.css" />
</head>
<body>

<header class="checkout-header">
  <div class="header-container">
    <a href="cart.php" class="back-button"><i class="fas fa-arrow-left"></i></a>
    <h1>Checkout</h1>
    <div class="header-icons">
      <a href="wishlist.php"><i class="fas fa-heart"></i></a>
      <a href="account.php"><i class="fas fa-user"></i></a>
    </div>
  </div>
</header>

<main class="checkout-main">
  <div class="checkout-container">

    <!-- Steps -->
    <div class="checkout-steps">
      <div class="step active"><span class="step-number">1</span><span class="step-name">Shipping</span></div>
      <div class="step"><span class="step-number">2</span><span class="step-name">Payment</span></div>
      <div class="step"><span class="step-number">3</span><span class="step-name">Confirmation</span></div>
    </div>

    <section class="shipping-section">
      <h2>Shipping Address</h2>
      <form class="address-form" method="POST" action="process_order.php">

        <div class="form-row">
          <div class="form-group">
            <label for="first-name">First Name</label>
            <input type="text" name="first_name" id="first-name" required />
          </div>
          <div class="form-group">
            <label for="last-name">Last Name</label>
            <input type="text" name="last_name" id="last-name" required />
          </div>
        </div>

        <div class="form-group">
          <label for="address">Address</label>
          <input type="text" name="address" id="address" required />
        </div>

        <div class="form-group">
          <label for="address2">Apartment, suite, etc. (optional)</label>
          <input type="text" name="address2" id="address2" />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="city">City</label>
            <input type="text" name="city" id="city" required />
          </div>
          <div class="form-group">
            <label for="zip">ZIP Code</label>
            <input type="text" name="zip" id="zip" required />
          </div>
        </div>

        <div class="form-group">
          <label for="phone">Phone Number</label>
          <input type="tel" name="phone" id="phone" required />
        </div>

        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" name="email" id="email" required />
        </div>

        <!-- Payment Method -->
        <section class="payment-section">
          <h2>Payment Method</h2>
          <div class="payment-options">
            <div class="payment-option">
              <input type="radio" name="payment_method" id="cod" value="COD" checked />
              <label for="cod"><i class="fas fa-money-bill-wave"></i><span>Cash on Delivery</span></label>
            </div>
            <div class="payment-option">
              <input type="radio" name="payment_method" id="gcash" value="GCash" />
              <label for="gcash"><img src="images/gcash-logo.png" alt="GCash" class="payment-logo" /><span>GCash</span></label>
            </div>
            <div class="payment-option">
              <input type="radio" name="payment_method" id="paymaya" value="PayMaya" />
              <label for="paymaya"><img src="images/paymaya-logo.png" alt="PayMaya" class="payment-logo" /><span>PayMaya</span></label>
            </div>
          </div>
        </section>

        <!-- Order Summary -->
        <section class="order-summary-section">
          <h2>Order Summary</h2>
          <div class="order-items">
            <?php if (count($cart_items) > 0): ?>
              <?php foreach ($cart_items as $item): ?>
                <div class="order-item">
                  <div class="item-image">
                    <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" />
                    <span class="quantity"><?= (int)$item['quantity'] ?></span>
                  </div>
                  <div class="item-details">
                    <h3><?= htmlspecialchars($item['product_name']) ?></h3>
                    <p class="item-price">$<?= number_format($item['price'], 2) ?></p>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p>Your cart is empty.</p>
            <?php endif; ?>
          </div>

          <div class="order-totals">
            <div class="total-row"><span>Subtotal</span><span>$<?= number_format($subtotal, 2) ?></span></div>
            <div class="total-row"><span>Shipping</span><span>$<?= number_format($shipping_fee, 2) ?></span></div>
            <div class="total-row grand-total"><span>Total</span><span>$<?= number_format($total, 2) ?></span></div>
          </div>
        </section>

        <div class="checkout-actions">
          <button type="submit" class="place-order-btn">Place Order</button>
        </div>
      </form>
    </section>
  </div>
</main>

<footer class="checkout-footer">
  <div class="footer-container">
    <div class="footer-links">
      <a href="about.php">About Us</a>
      <a href="contact.php">Contact</a>
      <a href="privacy.php">Privacy Policy</a>
      <a href="terms.php">Terms of Service</a>
    </div>
    <div class="social-links">
      <a href="#"><i class="fab fa-facebook-f"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
    </div>
    <p>&copy; 2023 ShopZone. All rights reserved.</p>
  </div>
</footer>

<script src="js/checkout.js"></script>
</body>
</html>

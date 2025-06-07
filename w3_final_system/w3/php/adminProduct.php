<?php
include 'connection.php';

// Dummy user ID for demo - replace with your session user ID in real app
$user_id = 1;

// Prepare and execute the query to fetch cart items safely
$stmt = $conn->prepare("
    SELECT ci.cart_item_id, ci.quantity, p.product_name, p.price, p.image, p.color, p.size
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.product_id
    WHERE ci.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items_result = $stmt->get_result();

// Initialize cart items array
$cart_items = [];
$subtotal = 0.0;

while ($row = $cart_items_result->fetch_assoc()) {
    $cart_items[] = $row;
    $subtotal += $row['price'] * $row['quantity'];
}

// Discount logic - example: apply 20% off for code "SUMMER20"
$discount_code = '';
$discount_value = 0;
$discount_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['discount_code'])) {
    $discount_code = strtoupper(trim($_POST['discount_code']));
    if ($discount_code === 'SUMMER20') {
        $discount_value = $subtotal * 0.20;
        $discount_message = 'Discount "SUMMER20" applied!';
    } else {
        $discount_message = 'Invalid discount code.';
    }
}

$total = $subtotal - $discount_value;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Your Cart | ShopZone</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/cart.css" />
</head>
<body>
  <!-- Fixed Header -->
  <header class="cart-header">
    <div class="header-container">
      <a href="shop.html" class="back-button">
        <i class="fas fa-arrow-left"></i>
      </a>
      <h1>Your Cart</h1>
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
  <main class="cart-main">
    <div class="cart-container">
      <!-- Cart Items Section -->
      <section class="cart-items">
        <div class="section-header">
          <h2><?= count($cart_items) ?> Item<?= count($cart_items) != 1 ? 's' : '' ?> in Cart</h2>
        </div>

        <?php if (count($cart_items) > 0): ?>
          <?php foreach ($cart_items as $item): ?>
            <div class="cart-item">
              <div class="item-image">
                <img src="images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" />
              </div>
              <div class="item-details">
                <div class="item-header">
                  <h3><?= htmlspecialchars($item['product_name']) ?></h3>
                  <form method="POST" action="remove_from_cart.php" style="display:inline;">
                    <input type="hidden" name="cart_item_id" value="<?= (int)$item['cart_item_id'] ?>" />
                    <button type="submit" class="remove-item" title="Remove item">
                      <i class="fas fa-times"></i>
                    </button>
                  </form>
                </div>
                <p class="item-color">Color: <?= htmlspecialchars($item['color'] ?? 'N/A') ?></p>
                <p class="item-size">Size: <?= htmlspecialchars($item['size'] ?? 'N/A') ?></p>
                <div class="item-price">$<?= number_format($item['price'], 2) ?></div>

                <div class="item-controls">
                  <form method="POST" action="update_quantity.php" class="quantity-form">
                    <input type="hidden" name="cart_item_id" value="<?= (int)$item['cart_item_id'] ?>" />
                    <div class="quantity-selector">
                      <button type="submit" name="action" value="minus" class="quantity-btn minus">-</button>
                      <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>" min="1" class="quantity-input" />
                      <button type="submit" name="action" value="plus" class="quantity-btn plus">+</button>
                    </div>
                  </form>
                  <button class="view-details">View Details</button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>Your cart is empty.</p>
        <?php endif; ?>
      </section>

      <!-- Order Summary Section -->
      <section class="order-summary">
        <div class="summary-card">
          <h2>Order Summary</h2>
          
          <!-- Discount Code -->
          <div class="discount-section">
            <form method="POST" action="">
              <div class="input-group">
                <input type="text" name="discount_code" placeholder="Discount code" class="discount-input" value="<?= htmlspecialchars($discount_code) ?>" />
                <button type="submit" class="apply-btn">Apply</button>
              </div>
            </form>
            <?php if ($discount_message): ?>
              <p class="discount-message <?= $discount_value > 0 ? 'success' : 'error' ?>"><?= htmlspecialchars($discount_message) ?></p>
            <?php endif; ?>
          </div>

          <!-- Order Totals -->
          <div class="totals-section">
            <div class="total-row">
              <span>Subtotal</span>
              <span>$<?= number_format($subtotal, 2) ?></span>
            </div>
            <div class="total-row">
              <span>Discount</span>
              <span class="discount-value">- $<?= number_format($discount_value, 2) ?></span>
            </div>
            <div class="total-row grand-total">
              <span>Total</span>
              <span>$<?= number_format($total, 2) ?></span>
            </div>
          </div>

          <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
          <a href="shop.html" class="continue-shopping">Continue Shopping</a>
        </div>
      </section>
    </div>
  </main>

  <!-- Fixed Footer -->
  <footer class="cart-footer">
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

  <script src="js/cart.js"></script>
</body>
</html>

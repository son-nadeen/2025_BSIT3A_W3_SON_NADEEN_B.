<?php
// Include your database connection file
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedItems = $_POST['return_items'] ?? [];
    $returnReasons = $_POST['return_reasons'] ?? [];
    $returnMethod = $_POST['return_method'] ?? '';
    $refundMethod = $_POST['refund_method'] ?? '';

    // Example: You can process or save the return request in your DB here
    // For example, just to illustrate:
    /*
    foreach ($selectedItems as $itemId) {
        $reason = $returnReasons[$itemId] ?? '';
        // Prepare and execute your DB insert here
    }
    */

    // For now, just a confirmation message
    $message = "Return request submitted.<br>";
    $message .= "Items to return:<br>";
    foreach ($selectedItems as $itemId) {
        $reason = $returnReasons[$itemId] ?? 'No reason selected';
        $message .= "- Item ID $itemId: Reason - $reason<br>";
    }
    $message .= "Return Method: $returnMethod<br>";
    $message .= "Refund Method: $refundMethod<br>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Return Item | ShopZone</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/return.css" />
</head>
<body>
  <!-- Fixed Header -->
  <header class="return-header">
    <div class="header-container">
      <a href="orders.html" class="back-button">
        <i class="fas fa-arrow-left"></i>
      </a>
      <h1>Return Item</h1>
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

  <main class="return-main">
    <div class="return-container">
      <?php if (!empty($message)): ?>
        <div class="confirmation-message" style="background:#d4edda; color:#155724; padding:10px; margin-bottom:20px; border-radius:5px;">
          <?php echo $message; ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="">
        <!-- Order Summary -->
        <section class="order-summary">
          <div class="order-header">
            <span class="order-id">Order #SHZ-98452</span>
            <span class="order-date">Delivered on June 15, 2023</span>
          </div>

          <div class="return-eligibility">
            <i class="fas fa-check-circle"></i>
            <p>This order is eligible for returns until <strong>June 22, 2023</strong></p>
          </div>

          <div class="order-items">
            <h3>Select Items to Return</h3>

            <div class="item-checkbox">
              <input type="checkbox" id="item1" name="return_items[]" value="item1" checked onchange="toggleReason('reason1', this.checked)" />
              <label for="item1" class="item">
                <div class="item-image">
                  <img src="images/shirt.jpg" alt="Classic White Tee" />
                </div>
                <div class="item-info">
                  <h4>Classic White Tee</h4>
                  <p>Quantity: 2</p>
                  <p class="price">$29.99</p>
                  <div class="return-reason">
                    <label for="reason1">Reason:</label>
                    <select id="reason1" name="return_reasons[item1]" required>
                      <option value="">Select reason</option>
                      <option value="wrong-item">Wrong item received</option>
                      <option value="damaged">Item damaged</option>
                      <option value="not-as-described">Not as described</option>
                      <option value="size-issue">Size doesn't fit</option>
                      <option value="change-mind">Changed my mind</option>
                    </select>
                  </div>
                </div>
              </label>
            </div>

            <div class="item-checkbox">
              <input type="checkbox" id="item2" name="return_items[]" value="item2" onchange="toggleReason('reason2', this.checked)" />
              <label for="item2" class="item">
                <div class="item-image">
                  <img src="images/cap.jpg" alt="Baseball Cap" />
                </div>
                <div class="item-info">
                  <h4>Baseball Cap</h4>
                  <p>Quantity: 1</p>
                  <p class="price">$24.99</p>
                  <div class="return-reason">
                    <label for="reason2">Reason:</label>
                    <select id="reason2" name="return_reasons[item2]" disabled required>
                      <option value="">Select reason</option>
                      <option value="wrong-item">Wrong item received</option>
                      <option value="damaged">Item damaged</option>
                      <option value="not-as-described">Not as described</option>
                      <option value="size-issue">Size doesn't fit</option>
                      <option value="change-mind">Changed my mind</option>
                    </select>
                  </div>
                </div>
              </label>
            </div>
          </div>
        </section>

        <!-- Return Details -->
        <section class="return-details">
          <h2>Return Details</h2>

          <div class="return-method">
            <h3>Return Method</h3>
            <div class="method-options">
              <label class="method-option">
                <input type="radio" name="return_method" value="pickup" checked />
                <div class="option-content">
                  <i class="fas fa-truck-pickup"></i>
                  <span>Schedule a pickup</span>
                  <p>We'll arrange a courier to collect the items</p>
                </div>
              </label>

              <label class="method-option">
                <input type="radio" name="return_method" value="dropoff" />
                <div class="option-content">
                  <i class="fas fa-map-marker-alt"></i>
                  <span>Drop-off at nearest location</span>
                  <p>Return to one of our partner stores or drop-off points</p>
                </div>
              </label>
            </div>
          </div>

          <div class="refund-method">
            <h3>Refund Method</h3>
            <div class="method-options">
              <label class="method-option">
                <input type="radio" name="refund_method" value="original" checked />
                <div class="option-content">
                  <i class="fas fa-undo"></i>
                  <span>Original payment method</span>
                  <p>Refund to your credit/debit card (5-7 business days)</p>
                </div>
              </label>

              <label class="method-option">
                <input type="radio" name="refund_method" value="wallet" />
                <div class="option-content">
                  <i class="fas fa-wallet"></i>
                  <span>ShopZone Wallet</span>
                  <p>Instant credit to your ShopZone account</p>
                </div>
              </label>
            </div>
          </div>

          <div class="return-instructions">
            <h3>Return Instructions</h3>
            <ul>
              <li>Items must be in original condition with all tags attached</li>
              <li>Please include the original packaging if possible</li>
              <li>Returns are processed within 3-5 business days after we receive your items</li>
              <li>Refunds may take 5-7 business days to reflect in your account</li>
            </ul>
          </div>
        </section>

        <!-- Return Summary -->
        <section class="return-summary">
          <h2>Return Summary</h2>
          <div class="summary-details">
            <div class="summary-row">
              <span>Items Subtotal</span>
              <span>$29.99</span>
            </div>
            <div class="summary-row">
              <span>Return Shipping</span>
              <span class="free">FREE</span>
            </div>
            <div class="summary-row total">
              <span>Estimated Refund</span>
              <span>$29.99</span>
            </div>
          </div>
          <button type="submit" class="submit-return">Submit Return Request</button>
        </section>
      </form>
    </div>
  </main>

  <!-- Fixed Footer -->
  <footer class="return-footer">
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

  <script>
    // Enable/disable reason select based on checkbox
    function toggleReason(reasonId, isChecked) {
      const reasonSelect = document.getElementById(reasonId);
      reasonSelect.disabled = !isChecked;
      if (!isChecked) {
        reasonSelect.value = '';
      }
    }
  </script>
</body>
</html>

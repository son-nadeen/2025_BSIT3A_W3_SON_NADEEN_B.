<?php
session_start();
include 'connection.php';

// Get logged in user ID - adjust accordingly
$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// Fetch wishlist items for the user
$wishlistItems = [];
if ($userId > 0) {
    $sql = "
      SELECT p.id, p.name, p.price, p.rating, p.stock, p.image_url
      FROM wishlist w
      JOIN products p ON w.product_id = p.id
      WHERE w.user_id = $userId
      ORDER BY w.added_at DESC
    ";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $wishlistItems[] = $row;
        }
    }
}

// Fetch cart count for the user
$cartCount = 0;
if ($userId > 0) {
    $cartSql = "SELECT COUNT(*) AS count FROM cart WHERE user_id = $userId";
    $cartResult = mysqli_query($conn, $cartSql);
    if ($cartResult) {
        $cartRow = mysqli_fetch_assoc($cartResult);
        $cartCount = intval($cartRow['count']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Wishlist | ShopZone</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/wishlist.css" />
</head>
<body>
  <!-- Navigation Header -->
  <header class="wishlist-header">
    <div class="container">
      <a href="dashboard.php" class="back-button">
        <i class="fas fa-arrow-left"></i>
      </a>
      <h1>My Wishlist</h1>
      <div class="header-icons">
        <a href="cart.php" class="cart-icon">
          <i class="fas fa-shopping-cart"></i>
          <span class="cart-count"><?php echo $cartCount; ?></span>
        </a>
      </div>
    </div>
  </header>

  <!-- Main Wishlist Content -->
  <main class="wishlist-container">
    <section class="wishlist-items">
      <div class="wishlist-header">
        <p class="items-count"><?php echo count($wishlistItems); ?> item<?php echo count($wishlistItems) !== 1 ? 's' : ''; ?> in wishlist</p>
        <form method="post" action="move_all_to_cart.php" style="display:inline;">
          <button type="submit" class="move-all-to-cart">Move All to Cart</button>
        </form>
      </div>

      <div class="wishlist-grid">
        <?php if (!empty($wishlistItems)): ?>
          <?php foreach ($wishlistItems as $item): ?>
            <div class="wishlist-item" data-product-id="<?php echo $item['id']; ?>">
              <div class="item-image">
                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" />
                <form method="post" action="remove_from_wishlist.php" class="remove-form">
                  <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>" />
                  <button type="submit" class="remove-item" title="Remove from wishlist">
                    <i class="fas fa-times"></i>
                  </button>
                </form>
              </div>
              <div class="item-info">
                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                <div class="item-meta">
                  <span class="price">$<?php echo number_format($item['price'], 2); ?></span>
                  <span class="rating">
                    <i class="fas fa-star"></i> <?php echo number_format($item['rating'], 1); ?>
                  </span>
                </div>
                <?php
                  // Stock status logic example
                  $stockClass = "in-stock";
                  $stockText = "In Stock";
                  if ($item['stock'] <= 2) {
                      $stockClass = "low-stock";
                      $stockText = "Only {$item['stock']} left";
                  } elseif ($item['stock'] == 0) {
                      $stockClass = "out-of-stock";
                      $stockText = "Out of Stock";
                  }
                ?>
                <p class="stock <?php echo $stockClass; ?>"><?php echo $stockText; ?></p>
                <div class="item-actions">
                  <form method="post" action="add_to_cart.php" style="display:inline;">
                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>" />
                    <button type="submit" class="add-to-cart" <?php echo $stockClass === 'out-of-stock' ? 'disabled' : ''; ?>>Add to Cart</button>
                  </form>
                  <a href="product.php?id=<?php echo $item['id']; ?>" class="quick-view">Quick View</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="padding: 20px;">Your wishlist is empty.</p>
        <?php endif; ?>
      </div>

      <!-- Recently Viewed Section (Optional: You can also make this dynamic similarly) -->
      <div class="recently-viewed">
        <h2>Recently Viewed</h2>
        <div class="recent-items">
          <div class="recent-item">
            <img src="../image/sneakers.jpg" alt="Running Sneakers" />
            <h4>Running Sneakers</h4>
            <span class="price">$79.99</span>
          </div>
          <div class="recent-item">
            <img src="../image/watch.jpg" alt="Smart Watch" />
            <h4>Smart Watch</h4>
            <span class="price">$129.99</span>
          </div>
          <div class="recent-item">
            <img src="../image/backpack.jpg" alt="Travel Backpack" />
            <h4>Travel Backpack</h4>
            <span class="price">$49.99</span>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="shop-footer">
    <div class="footer-links">
      <a href="#">About Us</a>
      <a href="#">Contact</a>
      <a href="#">Privacy Policy</a>
      <a href="#">Terms of Service</a>
    </div>
    <div class="social-links">
      <a href="#"><i class="fab fa-facebook-f"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
    </div>
    <p class="copyright">© 2023 ShopZone. All rights reserved.</p>
  </footer>

  <script>
    // JS to update item count on client-side removal can be added here if needed
  </script>
</body>
</html>

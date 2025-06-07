<?php
include 'connection.php';

// Get product_id from URL query string (e.g. productDetail.php?product_id=123)
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

// Prepare and execute query to get product info from DB
$product_stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$product_stmt->bind_param("i", $product_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();

if ($product_result->num_rows === 0) {
    // Product not found, you could redirect or show an error
    die("Product not found.");
}

$product = $product_result->fetch_assoc();

// Close statement and connection
$product_stmt->close();
$conn->close();

// Example product fields (adjust according to your DB schema):
// $product['name'], $product['sku'], $product['price'], $product['original_price'],
// $product['discount_percent'], $product['stock'], $product['description'],
// $product['rating'], $product['reviews_count'], $product['image_main'], etc.
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($product['name']) ?> | YourBrand</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/productDetail.css" />
</head>
<body>
  <!-- Top Navigation Bar with Back Button -->
  <header class="product-detail-header">
    <div class="container">
      <a href="dashboard.html" class="back-button">
        <i class="fas fa-arrow-left"></i>
      </a>
      <h1>Product Details</h1>
      <div class="header-icons">
        <a href="#" class="cart-icon">
          <i class="fas fa-shopping-cart"></i>
          <span class="cart-count">3</span>
        </a>
      </div>
    </div>
  </header>

  <!-- Main Product Detail Section -->
  <main>
    <div class="product-gallery">
      <div class="main-image">
        <img src="<?= htmlspecialchars($product['image_main']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" id="main-product-image" />
      </div>
      <div class="thumbnail-grid">
        <?php
        // Assuming you have multiple product images in your DB or a separate images table
        // For demonstration, just show main image and some placeholders:
        $thumbnails = [
          $product['image_main'],
          '../image/shirt-alt.jpg',
          '../image/shirt-back.jpg',
          '../image/shirt-detail.jpg',
        ];
        foreach ($thumbnails as $index => $img) {
          $activeClass = $index === 0 ? 'active-thumbnail' : '';
          echo '<img src="' . htmlspecialchars($img) . '" alt="Thumbnail ' . ($index + 1) . '" class="' . $activeClass . '" onclick="changeImage(this)" />';
        }
        ?>
      </div>
    </div>

    <div class="product-info">
      <h1><?= htmlspecialchars($product['name']) ?></h1>
      <div class="product-meta">
        <span class="rating">
          <i class="fas fa-star"></i> <?= number_format($product['rating'], 1) ?> (<?= intval($product['reviews_count']) ?> reviews)
        </span>
        <span class="sku">SKU: <?= htmlspecialchars($product['sku']) ?></span>
      </div>

      <div class="price-section">
        <span class="current-price">$<?= number_format($product['price'], 2) ?></span>
        <?php if ($product['original_price'] > $product['price']): ?>
          <span class="original-price">$<?= number_format($product['original_price'], 2) ?></span>
          <span class="discount"><?= intval($product['discount_percent']) ?>% OFF</span>
        <?php endif; ?>
      </div>

      <p class="stock <?= $product['stock'] > 0 ? 'in-stock' : 'out-of-stock' ?>">
        <?= $product['stock'] > 0 ? "In Stock ({$product['stock']} available)" : "Out of Stock" ?>
      </p>

      <div class="product-description">
        <h3>Description</h3>
        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        <!-- Add bullet points if available -->
      </div>

      <div class="size-selector">
        <h3>Size</h3>
        <div class="size-options">
          <!-- Example static sizes, can be dynamic if your DB supports -->
          <button onclick="selectSize(this)">S</button>
          <button class="selected" onclick="selectSize(this)">M</button>
          <button onclick="selectSize(this)">L</button>
          <button onclick="selectSize(this)">XL</button>
          <button onclick="selectSize(this)">XXL</button>
        </div>
      </div>

      <div class="quantity-selector">
        <h3>Quantity</h3>
        <div class="quantity-control">
          <button onclick="adjustQuantity(-1)">-</button>
          <span id="quantity">1</span>
          <button onclick="adjustQuantity(1)">+</button>
        </div>
      </div>

      <div class="action-buttons">
        <button class="add-to-cart">Add to Cart</button>
        <button class="wishlist-btn"><i class="far fa-heart"></i> Wishlist</button>
      </div>
    </div>

    <div class="product-reviews">
      <h2>Customer Reviews</h2>
      <div class="review-summary">
        <div class="average-rating">
          <span><?= number_format($product['rating'], 1) ?></span>
          <div class="stars">
            <?php
            $fullStars = floor($product['rating']);
            $halfStar = ($product['rating'] - $fullStars) >= 0.5;
            for ($i = 0; $i < $fullStars; $i++) {
                echo '<i class="fas fa-star"></i>';
            }
            if ($halfStar) {
                echo '<i class="fas fa-star-half-alt"></i>';
            }
            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
            for ($i = 0; $i < $emptyStars; $i++) {
                echo '<i class="far fa-star"></i>';
            }
            ?>
          </div>
          <p><?= intval($product['reviews_count']) ?> reviews</p>
        </div>
        <div class="rating-bars">
          <!-- You can dynamically build rating bars here -->
        </div>
      </div>

      <div class="review-list">
        <!-- Static example review; you can fetch from DB as well -->
        <div class="review-card">
          <div class="review-header">
            <span class="reviewer">Alex Johnson</span>
            <span class="review-date">October 12, 2023</span>
            <div class="review-rating">
              <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
            </div>
          </div>
          <h3 class="review-title">Perfect fit and quality</h3>
          <p class="review-content">This is my third purchase of this shirt. The quality is excellent and it fits perfectly. Highly recommend!</p>
        </div>
        <!-- Add more reviews dynamically as needed -->
      </div>
    </div>
  </main>

  <!-- Footer (same as dashboard) -->

  <script>
    function changeImage(element) {
      const mainImage = document.getElementById('main-product-image');
      mainImage.src = element.src;
      mainImage.alt = element.alt;

      document.querySelectorAll('.thumbnail-grid img').forEach(img => {
        img.classList.remove('active-thumbnail');
      });
      element.classList.add('active-thumbnail');
    }

    function selectSize(element) {
      document.querySelectorAll('.size-options button').forEach(btn => {
        btn.classList.remove('selected');
      });
      element.classList.add('selected');
    }

    function adjustQuantity(change) {
      const quantityElement = document.getElementById('quantity');
      let quantity = parseInt(quantityElement.textContent);
      quantity += change;
      if (quantity < 1) quantity = 1;
      if (quantity > 10) quantity = 10;
      quantityElement.textContent = quantity;
    }

    document.querySelector('.add-to-cart').addEventListener('click', function() {
      const size = document.querySelector('.size-options .selected').textContent;
      const quantity = document.getElementById('quantity').textContent;
      alert(`Added ${quantity} ${size} size item(s) to cart!`);
      // Real add to cart logic goes here
    });

    document.querySelector('.wishlist-btn').addEventListener('click', function() {
      const btn = this;
      btn.classList.toggle('active');
      if (btn.classList.contains('active')) {
        btn.innerHTML = '<i class="fas fa-heart"></i> In Wishlist';
        btn.style.color = 'white';
        btn.style.backgroundColor = 'var(--primary)';
        btn.style.borderColor = 'var(--primary)';
      } else {
        btn.innerHTML = '<i class="far fa-heart"></i> Wishlist';
        btn.style.color = 'var(--dark)';
        btn.style.backgroundColor = 'white';
        btn.style.borderColor = '#ddd';
      }
    });
  </script>
</body>
</html>

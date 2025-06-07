<?php
include 'php/connection.php'; // Include database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="ShopZone - Premium custom fashion that tells your story">
  <title>YourBrand | Unique Custom Fashion</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="index.css">
</head>
<body>
  <!-- Header -->
  <header class="header">
    <div class="navbar">
      <div class="logo">Your<span>Brand</span></div>
      <nav class="nav-links">
        <a href="#features">Features</a>
        <a href="#products">Products</a>
        <a href="#testimonials">Reviews</a>
        <a href="php/signin.php" class="cta-button">Shop Now</a>
      </nav>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-content">
      <h1>Wear Your Story With Pride</h1>
      <p>Discover our unique collection of custom-designed apparel that celebrates your individuality. Premium quality, perfect fit, and designs that speak to you.</p>
      <div class="hero-buttons">
        <a href="#products" class="cta-button">Shop Collection</a>
        <a href="#features" class="secondary-button">Learn More</a>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features" id="features">
    <div class="section-title">
      <h2>Why Choose YourBrand</h2>
      <p>We combine premium materials with thoughtful design to create pieces you'll love wearing every day.</p>
    </div>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fas fa-tshirt"></i>
        </div>
        <h3>Premium Materials</h3>
        <p>Only the finest fabrics that feel as good as they look, designed to last through seasons.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fas fa-palette"></i>
        </div>
        <h3>Unique Designs</h3>
        <p>Original artwork and limited edition collections you won't find anywhere else.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fas fa-heart"></i>
        </div>
        <h3>Ethically Made</h3>
        <p>Responsibly sourced materials and fair labor practices at every step.</p>
      </div>
    </div>
  </section>

  <!-- Products Section -->
  <section class="products" id="products">
    <div class="section-title">
      <h2>Our Featured Collection</h2>
      <p>Discover this season's most loved pieces from our community.</p>
    </div>
    <div class="products-grid">
      <?php
      $sql = "SELECT * FROM product";
      $result = $conn->query($sql);

      if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo '<div class="product-card">';
              echo '<img src="image/' . $row["image"] . '" alt="' . $row["product_name"] . '" class="product-image">';
              echo '<div class="product-info">';
              echo '<h3>' . $row["product_name"] . '</h3>';
              echo '<p>' . $row["description"] . '</p>';
              echo '<span class="price">P' . number_format($row["price"], 2) . '</span>';
              echo '<a href="#" class="cta-button">Add to Cart</a>';
              echo '</div>';
              echo '</div>';
          }
      } else {
          echo "<p>No products available.</p>";
      }
      ?>
    </div>
  </section>

  <!-- (The rest of your HTML remains unchanged) -->

  <footer class="footer">
    <!-- Footer content here -->
    <div class="footer-bottom">
      <p>&copy; 2025 YourBrand. All rights reserved. | <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </div>
  </footer>
</body>
</html>

<?php
$conn->close(); // Close DB connection
?>
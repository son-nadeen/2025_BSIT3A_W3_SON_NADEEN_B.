<?php
session_start();
include 'connection.php';

// Optional: You could fetch user data from session or database here
// Example:
$name = isset($_SESSION['user']['fullname']) ? $_SESSION['user']['firstname'] : 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>YourBrand | Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/dashboard.css" />
  <style>
    .logout-button {
      text-decoration: none;
      color: #fff;
      margin-left: 20px;
      font-weight: bold;
      padding: 8px 12px;
      border-radius: 4px;
      background-color: #e74c3c;
      transition: background-color 0.3s ease;
    }
    .logout-button:hover {
      background-color: #c0392b;
    }
  </style>
</head>
<body>
  <!-- Navigation -->
  <header class="dashboard-header">
    <div class="logo">Shop<span>Zone</span></div>
    <div class="search-bar">
      <input type="text" placeholder="Search products..." />
      <button><i class="fas fa-search"></i></button>
      <div class="filters">
        <select>
          <option>All Categories</option>
          <option>T-Shirts</option>
          <option>Accessories</option>
          <option>Bags</option>
        </select>
        <select>
          <option>Price Range</option>
          <option>Under $25</option>
          <option>$25 - $50</option>
          <option>Over $50</option>
        </select>
        <select>
          <option>Rating</option>
          <option>4+ Stars</option>
          <option>3+ Stars</option>
        </select>
      </div>
    </div>
    <nav class="dashboard-nav">
      <a href="#"><i class="fas fa-home"></i> Home</a>
      <a href="shop.php"><i class="fas fa-shopping-bag"></i> Shop</a>
      <a href="account.php"><i class="fas fa-user"></i> Account</a>
      <a href="cart.php" class="cart-icon">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-count">3</span>
      </a>
      <!-- Logout button -->
      <a href="logout.php" class="logout-button">
        <i class="fas fa-sign-out-alt"></i> Logout
      </a>
    </nav>
  </header>
  <!-- Main Content -->
  <main class="dashboard-container">
    <!-- Welcome Section -->
    <section class="welcome-section">
      <div class="welcome-message">
        <h1>Good <span id="time-of-day">Morning</span>, <?php echo htmlspecialchars($name); ?>!</h1>
        <p>Your last login was yesterday at 3:45 PM</p>
        <div class="quick-stats">
          <div class="stat-item">
            <i class="fas fa-box"></i>
            <span>2 orders in progress</span>
          </div>
          <div class="stat-item">
            <i class="fas fa-star"></i>
            <span>Gold Member (1500 pts)</span>
          </div>
        </div>
      </div>
      <div class="promo-banner">
        <h3>SPECIAL OFFER!</h3>
        <p>Get 20% off your next order with code: WELCOME20</p>
        <button class="promo-button">Shop Now</button>
      </div>
    </section>

    <!-- Quick Actions -->
    <section class="quick-actions">
      <h2>Quick Actions</h2>
      <div class="action-grid">
        <div class="action-card">
          <i class="fas fa-box-open"></i>
          <h3>My Orders</h3>
          <p>Track your recent purchases</p>
          <a href="myOrders.php" class="action-button">View</a>
        </div>
        <div class="action-card">
          <i class="fas fa-map-marker-alt"></i>
          <h3>Addresses</h3>
          <p>2 saved addresses</p>
          <a href="address.php" class="action-button">Manage</a>
        </div>
        <div class="action-card">
          <i class="fas fa-cog"></i>
          <h3>Account Settings</h3>
          <p>Update your profile</p>
          <a href="account.php" class="action-button">Edit</a>
        </div>
      </div>
    </section>

    <!-- Product Categories -->
    <section class="product-categories">
      <h2>Shop by Category</h2>
      <div class="category-grid">
        <a href="#" class="category-card">
          <img src="../image/shirt.jpg" alt="T-Shirts">
          <h3>T-Shirts</h3>
        </a>
        <a href="#" class="category-card">
          <img src="../image/cap.jpg" alt="Accessories">
          <h3>Accessories</h3>
        </a>
        <a href="#" class="category-card">
          <img src="../image/toteBag.jpg" alt="Bags">
          <h3>Bags</h3>
        </a>
      </div>
    </section>

    <!-- Recent Orders -->
    <section class="recent-orders">
      <div class="section-header">
        <h2>Recent Orders</h2>
        <a href="#" class="view-all">View All</a>
      </div>
      <div class="orders-list">
        <div class="order-card">
          <div class="order-info">
            <span class="order-number">#ORD-2023-4567</span>
            <span class="order-date">Placed on Oct 15, 2023</span>
            <span class="order-status delivered">Delivered</span>
          </div>
          <div class="order-products">
            <img src="../image/cap.jpg" alt="Product 1">
            <img src="../image/shirt.jpg" alt="Product 2">
            <span class="more-items">+2 more</span>
          </div>
          <div class="order-total">$189.00</div>
          <a href="track.php" class="order-action">Track</a>
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products">
      <div class="section-header">
        <h2>Featured Products</h2>
        <a href="#" class="view-all">Browse All</a>
      </div>
      <div class="product-grid">
        <div class="product-card">
          <div class="product-badge">BESTSELLER</div>
          <img src="../image/shirt.jpg" alt="Classic White Tee">
          <div class="product-info">
            <h3>Classic White Tee</h3>
            <div class="product-meta">
              <span class="price">$29.99</span>
              <span class="rating">
                <i class="fas fa-star"></i> 4.8
              </span>
            </div>
            <p class="stock in-stock">In Stock (15 available)</p>
            <button class="add-to-cart">Add to Cart</button>
            <a href="../php/productDetail.html" class="view-details">View Details</a>
          </div>
        </div>
        <div class="product-card">
          <div class="product-badge">NEW</div>
          <img src="../image/cap.jpg" alt="Baseball Cap">
          <div class="product-info">
            <h3>Baseball Cap</h3>
            <div class="product-meta">
              <span class="price">$24.99</span>
              <span class="rating">
                <i class="fas fa-star"></i> 4.5
              </span>
            </div>
            <p class="stock in-stock">In Stock (8 available)</p>
            <button class="add-to-cart">Add to Cart</button>
            <a href="../php/productDetail.html" class="view-details">View Details</a>
          </div>
        </div>
        <div class="product-card">
          <img src="../image/toteBag.jpg" alt="Canvas Tote Bag">
          <div class="product-info">
            <h3>Canvas Tote Bag</h3>
            <div class="product-meta">
              <span class="price">$39.99</span>
              <span class="rating">
                <i class="fas fa-star"></i> 4.2
              </span>
            </div>
            <p class="stock in-stock">In Stock (12 available)</p>
            <button class="add-to-cart">Add to Cart</button>
            <a href="../php/productDetail.html" class="view-details">View Details</a>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="dashboard-footer">
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
    <p class="copyright">© 2023 YourBrand. All rights reserved.</p>
  </footer>

  <script>
    // Time-based greeting
    const timeGreeting = document.getElementById('time-of-day');
    const hour = new Date().getHours();
    
    if (hour < 12) timeGreeting.textContent = 'Morning';
    else if (hour < 18) timeGreeting.textContent = 'Afternoon';
    else timeGreeting.textContent = 'Evening';
  </script>
</body>
</html>
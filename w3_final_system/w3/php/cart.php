<?php
// cart.php
include 'connection.php'; // Make sure this connects to your DB
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shopping Cart | ShopZone</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/cart.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<header class="cart-header">
  <div class="container">
    <a href="shop.php" class="back-button"><i class="fas fa-arrow-left"></i></a>
    <h1>Your Cart</h1>
  </div>
</header>

<main class="cart-container">
  <section class="cart-items" id="cartItems">
    <!-- Cart items rendered here -->
  </section>

  <section class="cart-summary">
    <h2>Order Summary</h2>
    <div class="summary-details">
      <p><strong>Total Items:</strong> <span id="totalItems">0</span></p>
      <p><strong>Total Price:</strong> ₱<span id="totalPrice">0.00</span></p>
    </div>
    <button class="checkout-btn">Proceed to Checkout</button>
  </section>
</main>

<footer class="cart-footer">
  <p>© 2023 ShopZone. All rights reserved.</p>
</footer>

<script>
function renderCart() {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  const cartContainer = document.getElementById("cartItems");
  const totalItemsElement = document.getElementById("totalItems");
  const totalPriceElement = document.getElementById("totalPrice");

  cartContainer.innerHTML = "";
  let totalItems = 0;
  let totalPrice = 0;

  if (cart.length === 0) {
    cartContainer.innerHTML = "<p>Your cart is empty.</p>";
    totalItemsElement.textContent = "0";
    totalPriceElement.textContent = "0.00";
    return;
  }

  cart.forEach((item, index) => {
    const itemTotal = item.price * item.quantity;
    totalItems += item.quantity;
    totalPrice += itemTotal;

    const cartItem = document.createElement("div");
    cartItem.className = "cart-item";
    cartItem.innerHTML = `
      <div class="item-image">
        <img src="${item.image}" alt="${item.product_name}">
      </div>
      <div class="item-details">
        <h3>${item.product_name}</h3>
        <p>Color: ${item.color}</p>
        <p>Size: ${item.size}</p>
        <p>Price: ₱${parseFloat(item.price).toFixed(2)}</p>
        <div class="quantity-controls">
          <button class="decrease" data-index="${index}">-</button>
          <span>${item.quantity}</span>
          <button class="increase" data-index="${index}">+</button>
        </div>
        <button class="remove-item" data-index="${index}">Remove</button>
      </div>
    `;
    cartContainer.appendChild(cartItem);
  });

  totalItemsElement.textContent = totalItems;
  totalPriceElement.textContent = totalPrice.toFixed(2);
}

document.addEventListener("click", function(event) {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  const index = parseInt(event.target.getAttribute("data-index"));

  if (event.target.classList.contains("increase")) {
    cart[index].quantity += 1;
  } else if (event.target.classList.contains("decrease")) {
    if (cart[index].quantity > 1) {
      cart[index].quantity -= 1;
    }
  } else if (event.target.classList.contains("remove-item")) {
    cart.splice(index, 1);
  } else {
    return;
  }

  localStorage.setItem("cart", JSON.stringify(cart));
  renderCart();
});

document.addEventListener("DOMContentLoaded", renderCart);

// ✅ Checkout button click handler: store cart to DB and redirect
document.querySelector(".checkout-btn").addEventListener("click", async () => {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  if (cart.length === 0) {
    alert("Your cart is empty.");
    return;
  }

  const response = await fetch('save_order.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(cart)
  });

  const result = await response.text();
  if (result === 'success') {
    localStorage.removeItem("cart");
    window.location.href = "checkout.php";
  } else {
    alert("Failed to save order. Please try again.");
  }
});
</script>

</body>
</html>

<?php
session_start();
include 'connection.php'; // adjust path if needed

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect shipping and payment details
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'] . ', ' . $_POST['city'] . ' ' . $_POST['zip'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $payment_method = $_POST['payment_method'];
    $full_address = $first_name . ' ' . $last_name . ', ' . $address . ', ' . $phone . ', ' . $email;

    // Get cart items
    $cart_sql = "SELECT * FROM cart_items WHERE user_id = ?";
    $stmt = $conn->prepare($cart_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_result = $stmt->get_result();

    if ($cart_result->num_rows > 0) {
        // Insert each cart item into orders table
        $insert_sql = "INSERT INTO orders (user_id, product_id, user_address, order_status) VALUES (?, ?, ?, 'A')";
        $insert_stmt = $conn->prepare($insert_sql);

        while ($item = $cart_result->fetch_assoc()) {
            $product_id = $item['product_id'];
            $insert_stmt->bind_param("iis", $user_id, $product_id, $full_address);
            $insert_stmt->execute();
        }

        // Clear cart after placing order
        $delete_sql = "DELETE FROM cart_items WHERE user_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $user_id);
        $delete_stmt->execute();

        // Redirect to dashboard with success message
        header("Location: dashboard.php?order=success");
        exit;
    } else {
        // No cart items
        header("Location: cart.php?error=empty_cart");
        exit;
    }
} else {
    // Invalid request method
    header("Location: checkout.php");
    exit;
}
?>

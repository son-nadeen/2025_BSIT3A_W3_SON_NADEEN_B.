<?php
include 'connection.php';

// For demo only; replace with actual session-based user_id
$user_id = 1;

// Fetch all orders for the user (no JOIN)
$sql = "
    SELECT order_id, product_id, user_address, order_date, order_time, order_status
    FROM orders
    WHERE user_id = ?
    ORDER BY order_date DESC, order_time DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Track Orders</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 2rem; }
    h1 { margin-bottom: 1rem; }
    .order { border: 1px solid #ccc; padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px; }
    .status { padding: 0.2rem 0.5rem; border-radius: 4px; color: white; font-weight: bold; display: inline-block; }
    .status.A { background-color: green; }
    .status.N { background-color: red; }
  </style>
</head>
<body>

<h1>Your Orders (<?= count($orders) ?>)</h1>

<?php if (count($orders) > 0): ?>
  <?php foreach ($orders as $order): ?>
    <div class="order">
      <h3>Product ID: <?= $order['product_id'] ?></h3>
      <p><strong>Order ID:</strong> <?= $order['order_id'] ?></p>
      <p><strong>Placed On:</strong> <?= date('F j, Y g:i A', strtotime($order['order_date'])) ?></p>
      <p><strong>Address:</strong> <?= htmlspecialchars($order['user_address']) ?></p>
      <p><strong>Status:</strong> <span class="status <?= $order['order_status'] ?>"><?= $order['order_status'] === 'A' ? 'Available' : 'Not Available' ?></span></p>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <p>No orders found.</p>
<?php endif; ?>

</body>
</html>

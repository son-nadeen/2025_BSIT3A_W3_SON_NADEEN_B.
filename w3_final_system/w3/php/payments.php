<?php
include 'connection.php';

// === Payment functions ===
function getPayments($conn, $user_id) {
    $stmt = $conn->prepare("SELECT * FROM payments WHERE user_id = ? ORDER BY payment_date DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $payments = [];
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
    $stmt->close();
    return $payments;
}

function addPayment($conn, $user_id, $data) {
    $amount = isset($data['amount']) ? floatval($data['amount']) : 0;
    $method = isset($data['payment_method']) ? trim($data['payment_method']) : '';
    if ($amount <= 0 || $method === '') return false;

    $stmt = $conn->prepare("INSERT INTO payments (user_id, amount, payment_method) VALUES (?, ?, ?)");
    $stmt->bind_param("ids", $user_id, $amount, $method);
    $res = $stmt->execute();
    $stmt->close();
    return $res;
}
// === End of payment functions ===

// For demo only; replace with session user_id
$user_id = 1;

$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_payment'])) {
    if (!empty($_POST['amount']) && !empty($_POST['payment_method'])) {
        if (addPayment($conn, $user_id, $_POST)) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $error_message = "Invalid payment details.";
        }
    } else {
        $error_message = "Please fill out all fields.";
    }
}

// Get payments
$payments = getPayments($conn, $user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Your Payments</title>
  <link rel="stylesheet" href="../css/payments.css">
  <style>
    /* Back button styles */
    .back-button {
      display: inline-block;
      margin-bottom: 1rem;
      text-decoration: none;
      color: #007BFF;
      font-weight: bold;
      padding: 0.4rem 0.8rem;
      background-color: #f0f0f0;
      border-radius: 4px;
      transition: background-color 0.3s;
    }

    .back-button:hover {
      background-color: #e0e0e0;
    }
  </style>
</head>
<body>

<a href="dashboard.php" class="back-button">← Back</a>

<h1>Your Payments (<?= count($payments) ?>)</h1>

<?php if ($error_message): ?>
  <div class="error"><?= htmlspecialchars($error_message) ?></div>
<?php endif; ?>

<table>
  <thead>
    <tr>
      <th>Amount</th>
      <th>Method</th>
      <th>Date</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($payments as $p): ?>
      <tr>
        <td>₱<?= number_format($p['amount'], 2) ?></td>
        <td><?= htmlspecialchars($p['payment_method']) ?></td>
        <td><?= htmlspecialchars($p['payment_date']) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h2>Add Payment</h2>
<form method="POST">
  <label for="amount">Amount (₱):</label>
  <input type="number" step="0.01" name="amount" id="amount" required />

  <label for="payment_method">Payment Method:</label>
  <select name="payment_method" id="payment_method" required>
    <option value="">-- Select --</option>
    <option value="GCash">GCash</option>
    <option value="PayMaya">PayMaya</option>
    <option value="Bank Transfer">Bank Transfer</option>
    <option value="Cash">Cash</option>
  </select>

  <button type="submit" name="add_payment">Add Payment</button>
</form>

</body>
</html>

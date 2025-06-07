<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['email'])) {
    echo "Please log in first.<br><a href='signin.php'>Go to Login</a>";
    exit();
}

$email = $_SESSION['user']['email'];
$feedback = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');

    if (!empty($address)) {
        $updateSql = "UPDATE user SET user_address = ? WHERE email = ?";
        $stmt = $conn->prepare($updateSql);

        if ($stmt) {
            $stmt->bind_param("ss", $address, $email);
            if ($stmt->execute()) {
                $_SESSION['user']['user_address'] = $address;
                $feedback = "<p style='color: green;'>Address updated successfully.</p>";
            } else {
                $feedback = "<p style='color: red;'>Failed to update address.</p>";
            }
            $stmt->close();
        } else {
            $feedback = "<p style='color: red;'>SQL error: " . $conn->error . "</p>";
        }
    } else {
        $feedback = "<p style='color: red;'>Address field cannot be empty.</p>";
    }
}

$sql = "SELECT * FROM user WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

if (!$userData) {
    echo "Failed to retrieve user data.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Address</title>
    <link rel="stylesheet" href="address.css">
</head>

<body>
    <h2>Your Address Details</h2>

    <?= $feedback ?>

    <form method="POST" action="address.php">
        <p><strong>Name:</strong> <?= htmlspecialchars($userData['user_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($userData['email']) ?></p>

        <label for="address">Address:</label>
        <input type="text" name="address" id="address" value="<?= htmlspecialchars($userData['user_address']) ?>" required>

        <button type="submit">Save Address</button>
    </form>

    <br>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
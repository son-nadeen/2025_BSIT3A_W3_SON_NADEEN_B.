<?php
// save_order.php
include 'connection.php'; // Make sure this contains your DB connection

session_start();
$user_id = $_SESSION['user_id'] ?? 1; // Replace 1 with actual user logic if session is missing
$user_address = $_SESSION['user_address'] ?? 'Libon, Albay'; // Replace with real user data

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data)) {
    echo "no data";
    exit;
}

$stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, user_address, order_status) VALUES (?, ?, ?, 'A')");

foreach ($data as $item) {
    $product_id = $item['product_id'] ?? 0;

    if ($product_id > 0) {
        $stmt->bind_param("iis", $user_id, $product_id, $user_address);
        $stmt->execute();
    }
}

$stmt->close();
$conn->close();

echo "success";

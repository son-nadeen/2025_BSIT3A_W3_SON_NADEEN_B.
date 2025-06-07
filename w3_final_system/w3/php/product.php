<?php
session_start();
include 'connection.php';

// Check if admin is logged in
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user_admin') {
    header('Location: signin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $color = trim($_POST['color'] ?? '');
    $size = trim($_POST['size'] ?? '');

    $image_url = '';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['product_image']['tmp_name'];
        $fileName = $_FILES['product_image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadDir = './uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $image_url = $destPath;
            } else {
                $error = 'Error moving uploaded file.';
            }
        } else {
            $error = 'Allowed file types: ' . implode(', ', $allowedfileExtensions);
        }
    }

    if ($name === '' || $price <= 0) {
        $error = "Product name and valid price are required.";
    }

    if (!isset($error)) {
        $stmt = $conn->prepare("INSERT INTO products (product_name, price, image, color, size) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsss", $name, $price, $image_url, $color, $size);
        if ($stmt->execute()) {
            $success = "Product added successfully.";
        } else {
            $error = "Failed to add product.";
        }
        $stmt->close();
    }
}

// Fetch products
$products = [];
$result = $conn->query("SELECT * FROM products ORDER BY date_added DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Panel - Products</title>
<link rel="stylesheet" href="../css/products.css" />
<style>
    body {
    font-family: Arial, sans-serif;
    background: #f7f9fc;
    margin: 0;
    padding: 0;
    }
    .admin-container {
    max-width: 1000px;
    margin: 40px auto;
    background: #fff;
    padding: 30px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    border-radius: 8px;
    }
    h1 {
    margin-bottom: 20px;
    color: #2c3e50;
    }
    form {
    margin-bottom: 30px;
    background: #ecf0f1;
    padding: 20px;
    border-radius: 6px;
    }
    form .form-group {
    margin-bottom: 15px;
    }
    label {
    display: block;
    font-weight: bold;
    margin-bottom: 6px;
    color: #34495e;
    }
    input[type="text"],
    input[type="number"],
    textarea,
    input[type="file"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #bdc3c7;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
    }
    textarea {
    resize: vertical;
    min-height: 70px;
    }
    button {
    background: #2980b9;
    color: white;
    border: none;
    padding: 12px 20px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
    }
    button:hover {
    background: #3498db;
    }
    .message {
    margin-bottom: 15px;
    padding: 10px;
    border-radius: 5px;
    }
    .error {
    background: #e74c3c;
    color: white;
    }
    .success {
    background: #27ae60;
    color: white;
    }
    table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    }
    th, td {
    padding: 12px 15px;
    border-bottom: 1px solid #ddd;
    text-align: left;
    }
    th {
    background: #2980b9;
    color: white;
    }
    img.product-img {
    max-width: 80px;
    max-height: 60px;
    object-fit: contain;
    border-radius: 4px;
    border: 1px solid #ccc;
    }
    .back-button {
      display: inline-flex;
      align-items: center;
      margin-bottom: 15px;
      font-weight: bold;
      text-decoration: none;
      color: #2980b9;
      font-size: 16px;
    }
    .back-button i {
      margin-right: 8px;
    }
    .back-button:hover {
      text-decoration: underline;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body>
<div class="admin-container">
    <a href="admin.php" class="back-button"><i class="fas fa-arrow-left"></i> Back</a>
    <h1>Manage Products</h1>

    <?php if (isset($error)) : ?>
    <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif (isset($success)) : ?>
    <div class="message success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="product.php" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Product Name *</label>
            <input type="text" id="name" name="name" required />
        </div>
            <div class="form-group">
            <label for="price">Price (₱) *</label>
            <input type="number" id="price" name="price" step="0.01" min="0" required />
        </div>
        <div class="form-group">
            <label for="color">Color</label>
            <input type="text" id="color" name="color" />
        </div>
        <div class="form-group">
            <label for="size">Size</label>
            <input type="text" id="size" name="size" />
        </div>
        <div class="form-group">
            <label for="product_image">Product Image</label>
            <input type="file" id="product_image" name="product_image" accept="image/*" />
        </div>
        <button type="submit" name="add_product">Add Product</button>
    </form>

    <h2>Existing Products</h2>
    <?php if (count($products) === 0): ?>
        <p>No products found.</p>
    <?php else: ?>
    <table>
        <thead>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Price (₱)</th>
            <th>Date Added</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td>
                    <?php if (!empty($p['image'])): ?>
                        <img class="product-img" src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['product_name']); ?>" />
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($p['product_name']); ?></td>
                <td><?php echo number_format($p['price'], 2); ?></td>
                <td><?php echo htmlspecialchars($p['date_added']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
</body>
</html>

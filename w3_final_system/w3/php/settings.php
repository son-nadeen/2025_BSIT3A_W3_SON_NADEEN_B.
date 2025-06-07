<?php
include 'connection.php';
session_start();

// Replace with actual logged-in user ID from session
$user_id = 1;

// Fetch user info
function getUser($conn, $user_id) {
    $stmt = $conn->prepare("SELECT user_name, email FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

// Update profile info
function updateProfile($conn, $user_id, $name, $email) {
    $stmt = $conn->prepare("UPDATE user SET user_name = ?, email = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $name, $email, $user_id);
    $res = $stmt->execute();
    $stmt->close();
    return $res;
}

// Update password
function updatePassword($conn, $user_id, $new_password) {
    $hashed = password_hash($new_password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE user SET password = ? WHERE user_id = ?");
    $stmt->bind_param("si", $hashed, $user_id);
    $res = $stmt->execute();
    $stmt->close();
    return $res;
}

// Initial values
$user = getUser($conn, $user_id);
$success = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = trim($_POST['user_name']);
        $email = trim($_POST['email']);

        if ($name && $email) {
            if (updateProfile($conn, $user_id, $name, $email)) {
                $success = "Profile updated successfully.";
                $user = getUser($conn, $user_id); // refresh
            } else {
                $error = "Failed to update profile.";
            }
        } else {
            $error = "All fields are required.";
        }
    }

    if (isset($_POST['update_password'])) {
        $new_pass = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];

        if ($new_pass && $confirm) {
            if ($new_pass === $confirm) {
                if (strlen($new_pass) >= 6) {
                    if (updatePassword($conn, $user_id, $new_pass)) {
                        $success = "Password updated successfully.";
                    } else {
                        $error = "Failed to update password.";
                    }
                } else {
                    $error = "Password must be at least 6 characters.";
                }
            } else {
                $error = "Passwords do not match.";
            }
        } else {
            $error = "Please fill in both password fields.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Settings</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: Arial, sans-serif; margin: 2rem; }
    .form-box { max-width: 400px; margin-bottom: 2rem; }
    label { display: block; margin-top: 1rem; }
    input[type=text], input[type=email], input[type=password] {
        width: 100%; padding: 0.5rem; margin-top: 0.3rem; box-sizing: border-box;
    }
    button { margin-top: 1rem; padding: 0.5rem 1rem; }
    .success { color: green; margin-bottom: 1rem; }
    .error { color: red; margin-bottom: 1rem; }
  </style>
</head>
<body>

<h1>Account Settings</h1>

<?php if ($success): ?>
  <div class="success"><?= htmlspecialchars($success) ?></div>
<?php elseif ($error): ?>
  <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="form-box">
  <h2>Update Profile</h2>
  <form method="POST">
    <label>Full Name:</label>
    <input type="text" name="user_name" value="<?= htmlspecialchars($user['user_name']) ?>" required>

    <label>Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <button type="submit" name="update_profile">Save Changes</button>
  </form>
</div>

<div class="form-box">
  <h2>Change Password</h2>
  <form method="POST">
    <label>New Password:</label>
    <input type="password" name="new_password" required>

    <label>Confirm Password:</label>
    <input type="password" name="confirm_password" required>

    <button type="submit" name="update_password">Update Password</button>
  </form>
</div>

</body>
</html>

<?php
session_start();
include 'connection.php';

// LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'user_name' => $user['user_name'],
                'email' => $user['email'],
                'user_address' => $user['user_address'],
                'status' => $user['status'],
                'role' => $user['role']
            ];

            if ($user['role'] === 'user_admin') {
                header("Location: admin.php");
                exit();
            } else {
                header("Location: dashboard.php");
                exit();
            }
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "No account found with this email.";
    }

    $stmt->close();
}

// SIGNUP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $address = trim($_POST['address']);

    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        // Check if email already exists
        $check_sql = "SELECT * FROM user WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            echo "<script>alert('An account with this email already exists.');</script>";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insert_sql = "INSERT INTO user (user_name, password, role, status, date_registered, email, user_address) 
                           VALUES (?, ?, 'user', 'Offline', CURDATE(), ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssss", $name, $hashedPassword, $email, $address);

            if ($insert_stmt->execute()) {
                echo "<script>alert('Account created successfully. Please log in.'); window.location.href='signin.php';</script>";
            } else {
                echo "<script>alert('Something went wrong during signup.');</script>";
            }

            $insert_stmt->close();
        }

        $check_stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>YourBrand | Sign In</title>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
  />
  <link rel="stylesheet" href="../styles.css" />
  <link rel="stylesheet" href="../css/signin.css" />
</head>
<body>
  <div class="auth-container">
    <div class="auth-header">
      <h1>Welcome Back</h1>
      <p>Sign in to access your account and start shopping</p>
    </div>

    <div class="auth-tabs">
      <button class="auth-tab active" onclick="openTab(event, 'signin')">Sign In</button>
      <button class="auth-tab" onclick="openTab(event, 'signup')">Create Account</button>
    </div>

    <div id="signin" class="auth-form active">
      <?php if (isset($error_message)) : ?>
        <div style="color: red; margin-bottom: 15px;"><?php echo htmlspecialchars($error_message); ?></div>
      <?php endif; ?>

      <form method="POST" action="signin.php">
        <div class="form-group">
          <label for="email">Email</label>
          <input
            type="email"
            name="email"
            id="email"
            placeholder="Enter your email"
            required
          />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input
            type="password"
            name="password"
            id="password"
            placeholder="Enter your password"
            required
          />
        </div>
        <a href="#" class="forgot-password">Forgot password?</a>
        <button type="submit" name="login" class="auth-button">Sign In</button>
      </form>

      <div class="social-auth">
        <p>Or sign in with</p>
        <div class="social-buttons">
          <button class="social-button google">
            <i class="fab fa-google"></i> Google
          </button>
          <button class="social-button facebook">
            <i class="fab fa-facebook-f"></i> Facebook
          </button>
        </div>
      </div>
    </div>

    <div id="signup" class="auth-form">
      <form action="signin.php" method="POST">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input
            type="text"
            name="name"
            id="name"
            placeholder="Enter your full name"
            required
          />
        </div>
        <div class="form-group">
          <label for="new-email">Email</label>
          <input
            type="email"
            name="email"
            id="new-email"
            placeholder="Enter your email"
            required
          />
        </div>
        <div class="form-group">
          <label for="address">Address</label>
          <input
            type="text"
            name="address"
            id="address"
            placeholder="Enter your address"
            required
          />
        </div>
        <div class="form-group">
          <label for="new-password">Password</label>
          <input
            type="password"
            name="password"
            id="new-password"
            placeholder="Create a password"
            required
          />
        </div>
        <div class="form-group">
          <label for="confirm-password">Confirm Password</label>
          <input
            type="password"
            name="confirm_password"
            id="confirm-password"
            placeholder="Confirm your password"
            required
          />
        </div>
        <button type="submit" name="signup" class="auth-button">Create Account</button>
      </form>
    </div>

    <p class="auth-footer">
      By continuing, you agree to our
      <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
    </p>
  </div>

  <script>
    function openTab(event, tabName) {
      document.querySelectorAll(".auth-form").forEach((form) =>
        form.classList.remove("active")
      );
      document.querySelectorAll(".auth-tab").forEach((tab) =>
        tab.classList.remove("active")
      );
      document.getElementById(tabName).classList.add("active");
      event.currentTarget.classList.add("active");
    }
  </script>
</body>
</html>

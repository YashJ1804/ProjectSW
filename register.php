<?php
session_start();
require 'includes/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "Email already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $username, $email, $password);
        if ($stmt->execute()) {
            $message = "Registration successful. <a href='login.php'>Login here</a>";
        } else {
            $message = "Registration failed.";
        }
        $stmt->close();
    }
    $check->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register | Invoice Generator</title>
  <style>
  :root {
    --primary: #4a63d9;
    --primary-dark: #2f4ac1;
    --light-bg: #f1f5ff;
    --card-bg: #ffffff;
    --text: #212529;
    --border: #d1d9e6;
    --error: #dc3545;
    --success: #198754;
  }

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body, html {
    height: 100%;
    font-family: 'Poppins', sans-serif;
    background-color: var(--light-bg);
    color: var(--text);
  }

  .container {
    display: flex;
    flex-direction: column;
    height: 100%;
  }

  header {
    background-color: var(--primary);
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 40px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
  }

  .brand {
    font-size: 1.6rem;
    font-weight: 600;
    color: white;
  }

  .nav-links {
    display: flex;
    gap: 20px;
  }

  .nav-links a {
    color: #fff;
    text-decoration: none;
    font-weight: 500;
  }

  .nav-links a:hover {
    text-decoration: underline;
  }

  .main-content {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .card {
    background-color: var(--card-bg);
    padding: 35px 30px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    width: 100%;
    max-width: 400px;
  }

  h2 {
    text-align: center;
    color: var(--primary);
    margin-bottom: 25px;
  }

  form input {
    width: 100%;
    padding: 12px;
    margin-bottom: 18px;
    border: 1px solid var(--border);
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
  }

  form input:focus {
    border-color: var(--primary);
    outline: none;
    box-shadow: 0 0 0 3px rgba(74, 99, 217, 0.2);
  }

  .btn {
    width: 100%;
    background: linear-gradient(to right, #4a63d9, #5d7cfb);
    color: white;
    padding: 12px;
    font-size: 1rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
  }

  .btn:hover {
    background: linear-gradient(to right, #3c51b7, #4d6af0);
    transform: translateY(-1px);
  }

  .error-message {
    color: var(--error);
    font-size: 0.9rem;
    text-align: center;
    margin-top: 10px;
  }

  .success-message {
    color: var(--success);
    font-size: 0.9rem;
    text-align: center;
    margin-top: 10px;
  }

  p {
    text-align: center;
    margin-top: 10px;
  }

  footer {
    text-align: center;
    padding: 20px;
    font-size: 0.9rem;
    color: #6c757d;
  }
</style>

</head>
<body>

<div class="container">
<header class="main-header">
  <div class="brand">Invoice Generator</div>
  <nav class="nav-links">
    <a href="login.php">Login</a>
    <a href="register.php">Register</a>
  </nav>
</header>

  <div class="main-content">
    <div class="card">
      <h2>Register</h2>
      <form method="POST" action="register.php">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="btn">Register</button>
      </form>

      <?php if (!empty($message)): ?>
        <p class="<?= strpos($message, 'successful') !== false ? 'success-message' : 'error-message' ?>">
          <?php echo $message; ?>
        </p>
      <?php endif; ?>

      <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
  </div>

  <?php include 'includes/footer.php'; ?>
</div>

</body>
</html>

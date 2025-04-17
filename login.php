<?php
session_start();
require 'includes/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;
            header("Location: home.php");
            exit();
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "No account found with that email.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | Invoice Generator</title>
  <style>
    :root {
      --primary: #4a63d9;
      --primary-dark: #3248c0;
      --accent: #f1f5ff;
      --white: #ffffff;
      --error: #dc3545;
      --border: #dce1f5;
    }

    * {
      box-sizing: border-box;
      padding: 0;
      margin: 0;
    }

    body, html {
      height: 100%;
      font-family: 'Poppins', sans-serif;
      background-color: var(--accent);
    }

    .container {
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 40px;
      background-color: transparent;
    }

    .header-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
    }

    .brand {
      font-size: 1.8rem;
      font-weight: 600;
      color: var(--primary);
      flex: 1;
      text-align: center;
    }

    .nav-links {
      display: flex;
      gap: 20px;
    }

    .nav-links a {
      color: var(--primary);
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
      padding: 20px;
    }

    .card {
      background-color: var(--white);
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
      width: 100%;
      max-width: 420px;
      animation: fadeIn 0.4s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
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
    }

    form input:focus {
      border-color: var(--primary);
      outline: none;
      box-shadow: 0 0 0 2px rgba(74, 99, 217, 0.2);
    }

    .btn {
      width: 100%;
      background: linear-gradient(145deg, var(--primary), var(--primary-dark));
      color: white;
      padding: 12px;
      font-size: 1rem;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: transform 0.2s ease, background 0.3s ease;
    }

    .btn:hover {
      transform: translateY(-1px);
    }

    p {
      text-align: center;
      margin-top: 15px;
    }

    .error-message {
      color: var(--error);
      font-size: 0.9rem;
      text-align: center;
      margin-top: -10px;
      margin-bottom: 10px;
    }

    footer {
      text-align: center;
      padding: 20px;
      font-size: 0.9rem;
      color: #6c757d;
    }
    .main-header {
  background-color: #4a63d9;
  color: white;
  padding: 16px 40px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.brand {
  font-size: 1.6rem;
  font-weight: 600;
  color: white;
  text-decoration: none;
}

.nav-links {
  display: flex;
  gap: 20px;
}

.nav-links a {
  color: white;
  text-decoration: none;
  font-weight: 500;
}

.nav-links a:hover {
  text-decoration: underline;
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
      <h2>Login</h2>
      <form method="POST" action="login.php">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="btn">Login</button>
      </form>

      <?php if (!empty($message)): ?>
        <p class="error-message"><?php echo $message; ?></p>
      <?php endif; ?>

      <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
  </div>
  <?php include 'includes/footer.php'; ?>
</div>

</body>
</html>
